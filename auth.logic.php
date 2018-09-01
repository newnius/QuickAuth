<?php

require_once('predis/autoload.php');
require_once('util4p/CRObject.class.php');
require_once('Code.class.php');
require_once('util4p/Validator.class.php');
require_once('util4p/ReSession.class.php');
require_once('util4p/CRLogger.class.php');
require_once('util4p/AccessController.class.php');
require_once('util4p/Random.class.php');
require_once('guzzle/autoloader.php');

require_once('UserManager.class.php');
require_once('SiteManager.class.php');

require_once('config.inc.php');
require_once('init.inc.php');

/* detect SSO or OAuth */
function auth_get_site(CRObject $rule)
{
	$r = new CRObject();
	$r->set('id', $rule->getInt('app_id'));
	$site = SiteManager::get($r);
	if ($site === null) {
		$res['errno'] = Code::SITE_NOT_EXIST;
		return $res;
	}
	$res['errno'] = Code::SUCCESS;
	$res['host'] = $site['domain'];
	$res['auto_grant'] = $site['level'] === '99' ? 1 : 0;
	return $res;
}

/*
 * array('response_type'='code', 'app_id', 'redirect_uri', 'state', 'scope')
 * return array('errno', 'code', 'state');
 */
function auth_grant(CRObject $rule)
{
	if (Session::get('username') === null) {
		$res['errno'] = Code::NOT_LOGED;
		return $res;
	}
	$app_id = $rule->getInt('app_id');
	$r = new CRObject();
	$r->set('id', $app_id);
	$site = SiteManager::get($r);
	if ($site === null) {
		$res['errno'] = Code::SITE_NOT_EXIST;
	} else if ($rule->get('response_type') !== 'code') {
		$res['errno'] = Code::INVALID_PARAM;
	} else if (!Validator::isURL($rule->get('redirect_uri'))) {
		$res['errno'] = Code::INVALID_URL;
	} else {
		$arr = parse_url($rule->get('redirect_uri'));
		if ($arr['host'] !== $site['domain']) {
			$res['errno'] = Code::DOMAIN_MISMATCH;
		} else if ($rule->get('state') === null) {
			$res['errno'] = Code::INCOMPLETE_CONTENT;
		} else {
			$scope = array_filter(explode(',', $rule->get('scope', '')), 'strlen');
			$code = Random::randomString(64);
			$redis = RedisDAO::instance();
			if ($redis === null) {
				$res['errno'] = Code::UNABLE_TO_CONNECT_REDIS;
				return $res;
			}
			$data = array(
				'app_id' => $app_id,
				'app_key' => $site['key'],
				'revoke_url' => $site['revoke_url'],
				'redirect_uri' => $rule->get('redirect_uri'),
				'uid' => Session::get('username'),
				'scope' => json_encode($scope)
			);
			$redis->hmset("auth:code:$code", $data);
			$redis->expire("auth:code:$code", 300);
			$redis->disconnect();

			$res['errno'] = Code::SUCCESS;
			$res['code'] = $code;
			$res['state'] = $rule->get('state');
		}
	}
	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'auth_grant');
	$content = array('app_id' => $app_id, 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/*
 * array('grant_type'=>'authorization_code', 'app_id', 'app_key', 'code', 'redirect_uri')
 * return array('errno', 'token', 'expires_in'=>3600);
 */
function auth_get_token(CRObject $rule)
{
	if ($rule->get('grant_type') !== 'authorization_code') {
		$res['errno'] = Code::INVALID_PARAM;
		return $res;
	}
	$app_id = $rule->get('app_id', '');
	$code = $rule->get('code', '');
	$redis = RedisDAO::instance();
	if ($redis === null) {
		$res['errno'] = Code::UNABLE_TO_CONNECT_REDIS;
		return $res;
	}
	$data = $redis->hgetall("auth:code:$code");
	if (count($data) === 0) {
		$res['errno'] = Code::CODE_EXPIRED;
		$redis->disconnect();
		return $res;
	}
	if ($app_id !== $data['app_id'] || $rule->get('redirect_uri') !== $data['redirect_uri'] || $rule->get('app_key') !== $data['app_key']) {
		$res['errno'] = Code::INVALID_URL;
		$redis->disconnect();
		return $res;
	}
	$redis->del(array("auth:code:$code"));
	$token = Random::randomString(64);
	$data2 = array(
		'expires' => time() + 3600 * 24 * 30,
		'app_id' => $data['app_id'],
		'app_key' => $data['app_key'],
		'revoke_url' => $data['revoke_url'],
		'uid' => $data['uid'],
		'scope' => $data['scope']
	);
	$redis->hmset("auth:token:$token", $data2);
	$redis->expire("auth:token:$token", AUTH_TOKEN_TIMEOUT);
	/* remove old token */
	$t = $redis->hget("auth:group:{$data['uid']}", $app_id);
	if ($t !== null) {
		$redis->del(array("auth:token:$t"));
	}
	$redis->hset("auth:group:{$data['uid']}", $app_id, $token);
	$res['errno'] = Code::SUCCESS;
	$res['token'] = $token;
	$res['expires_in'] = AUTH_TOKEN_TIMEOUT;
	$redis->disconnect();
	return $res;
}

/*
 * array('grant_type'=>'refresh_token', 'app_id', 'app_key', 'token')
 * return array('errno', 'token', 'expires_in'=>3600);
 */
function auth_refresh_token(CRObject $rule)
{
	if ($rule->get('grant_type') !== 'refresh_token') {
		$res['errno'] = Code::INVALID_PARAM;
		return $res;
	}
	$token = $rule->get('token', '');
	$redis = RedisDAO::instance();
	if ($redis === null) {
		$res['errno'] = Code::UNABLE_TO_CONNECT_REDIS;
		return $res;
	}
	$data = $redis->hgetall("auth:token:$token");
	if (count($data) === 0) {
		$res['errno'] = Code::TOKEN_EXPIRED;
		$redis->disconnect();
		return $res;
	}
	if ($data['expires'] < time()) {
		$res['errno'] = Code::TOKEN_EXPIRED;
		$redis->disconnect();
		return $res;
	}
	$redis->del(array("auth:token:$token"));
	$t = $redis->hget("auth:group:{$data['uid']}", $data['app_id']);
	if ($t !== $token) {
		$res['errno'] = Code::TOKEN_EXPIRED;
		$redis->disconnect();
		return $res;
	}
	$token = Random::randomString(64);
	$redis->hset("auth:group:{$data['uid']}", $data['app_id'], $token);
	$data2 = array(
		'expires' => time() + 3600 * 24 * 30,
		'app_id' => $data['app_id'],
		'app_key' => $data['app_key'],
		'revoke_url' => $data['revoke_url'],
		'uid' => $data['uid'],
		'scope' => $data['scope']
	);
	$redis->hmset("auth:token:$token", $data2);
	$redis->expire("auth:token:$token", AUTH_TOKEN_TIMEOUT);
	$res['errno'] = Code::SUCCESS;
	$res['token'] = $token;
	$res['expires_in'] = AUTH_TOKEN_TIMEOUT;
	$redis->disconnect();
	return $res;
}

/* query user info */
function auth_get_info(CRObject $rule)
{
	$token = $rule->get('token', '');
	$redis = RedisDAO::instance();
	if ($redis === null) {
		$res['errno'] = Code::UNABLE_TO_CONNECT_REDIS;
		return $res;
	}
	$data = $redis->hgetall("auth:token:$token");
	if (count($data) === 0) {
		$res['errno'] = Code::TOKEN_EXPIRED;
		$redis->disconnect();
		return $res;
	}
	if ($data['expires'] < time()) {
		$res['errno'] = Code::TOKEN_EXPIRED;
		$redis->del(array("auth:token:$token"));
		$redis->disconnect();
		return $res;
	}
	$t = $redis->hget("auth:group:{$data['uid']}", $data['app_id']);
	if ($t !== $token) {
		$res['errno'] = Code::TOKEN_EXPIRED;
		$redis->disconnect();
		return $res;
	}
	$scope = json_decode($data['scope']);
	$user = UserManager::getByUsername($data['uid']);
	if ($user === null) {
		$res['errno'] = Code::USER_NOT_EXIST;
		return $res;
	}
	$info = array();
	$info['uid'] = $user['username'];
	$allowed_scopes = array('email', 'email_verified', 'role', 'nickname');
	foreach ($allowed_scopes as $s) {
		if (in_array($s, $scope)) {
			$info[$s] = $user[$s];
		}
	}
	$res['errno'] = Code::SUCCESS;
	$res['info'] = $info;
	return $res;
}

/**/
function auth_revoke(CRObject $rule)
{
	if (Session::get('username') === null) {
		$res['errno'] = Code::NOT_LOGED;
		return $res;
	}
	$app_id = $rule->getInt('app_id', '');
	$redis = RedisDAO::instance();
	if ($redis === null) {
		$res['errno'] = Code::UNABLE_TO_CONNECT_REDIS;
		return $res;
	}
	$token = $redis->hget('auth:group:' . Session::get('username'), $app_id);
	$redis->hdel("auth:group:" . Session::get('username'), array($app_id));
	$data = $redis->hgetall("auth:token:$token");
	if (count($data) === 0) {
		$res['errno'] = Code::TOKEN_EXPIRED;
		$redis->disconnect();
		return $res;
	}
	$success = $redis->del(array("auth:token:$token"));
	$res['errno'] = $success > 0 ? Code::SUCCESS : Code::FAIL;

	/* notify site if revoke_url is set */
	$url = $data['revoke_url'];
	if ($url !== null && strlen($url) > 10) {
		$form_params = array(
			'uid' => $data['uid'],
			'token' => $token
		);
		$post_data = array('form_params' => $form_params);
		$client = new \GuzzleHttp\Client(['timeout' => 3, 'headers' => ['User-Agent' => 'QuickAuth Bot']]);
		try {
			$client->request('POST', $url, $post_data);
		} catch (\GuzzleHttp\Exception\GuzzleException $e) {
		} catch (Exception $e) {//pass
		}
	}

	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'auth_revoke');
	$content = array('app_id' => $app_id, 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function auth_list(CRObject $rule)
{
	$redis = RedisDAO::instance();
	if ($redis === null) {
		$res['errno'] = Code::UNABLE_TO_CONNECT_REDIS;
		return $res;
	}
	$list = $redis->hgetall('auth:group:' . Session::get('username', ''));
	$redis->disconnect();
	$sites = array();
	foreach ($list as $app_id => $token) {
		$data = $redis->hgetall("auth:token:$token");
		if (count($data) === 0) {
			$redis->hdel('auth:group:' . Session::get('username', ''), $app_id);
			continue;
		}
		$sites[] = array(
			'app_id' => $app_id,
			'expires' => $data['expires'],
			'scope' => $data['scope']
		);
	}
	$res['errno'] = Code::SUCCESS;
	$res['list'] = $sites;
	return $res;
}

/**/
function site_add(CRObject $site)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'site_add_' . $site->getInt('level', 1))) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}

	$revoke_url = $site->get('revoke_url');
	if ($revoke_url !== null && strlen($revoke_url) > 0) {
		$arr = parse_url($revoke_url);
		if ($arr['host'] !== $site->get('domain')) {
			$res['errno'] = Code::DOMAIN_MISMATCH;
			return $res;
		}
	}

	$site->set('owner', Session::get('username'));
	$site->set('key', Random::randomString(64));
	$site->set('level', $site->getInt('level', 1));
	$success = SiteManager::add($site);
	$res['errno'] = $success ? Code::SUCCESS : Code::UNKNOWN_ERROR;

	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'site_add');
	$content = array('domain' => $site->get('domain'), 'revoke_url' => $site->get('revoke_url'), 'level' => $site->getInt('level', 1), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function site_update(CRObject $site)
{
	$site_arr = SiteManager::get($site);
	if ($site_arr === null) {
		$res['errno'] = Code::RECORD_NOT_EXIST;
	} else if ($site_arr['owner'] !== Session::get('username') && !AccessController::hasAccess(Session::get('role', 'visitor'), 'site_update_others')) {
		$res['errno'] = Code::NO_PRIVILEGE;
	} else if (!AccessController::hasAccess(Session::get('role'), 'site_add_' . $site->getInt('level', 1))) {// can update to level
		$res['errno'] = Code::NO_PRIVILEGE;
	} else {
		$revoke_url = $site->get('revoke_url');
		if ($revoke_url !== null && strlen($revoke_url) > 0) {
			$arr = parse_url($revoke_url);
			if ($arr['host'] !== $site->get('domain')) {
				$res['errno'] = Code::DOMAIN_MISMATCH;
				return $res;
			}
		}

		$site_arr['level'] = $site->getInt('level', 1);
		$site_arr['domain'] = $site->get('domain');
		$site_arr['revoke_url'] = $site->get('revoke_url');

		$success = SiteManager::update(new CRObject($site_arr));
		$res['errno'] = $success ? Code::SUCCESS : Code::UNKNOWN_ERROR;
	}
	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'update_site');
	$content = array(
		'id' => $site->getInt('id'),
		'domain' => $site->get('domain'),
		'revoke_url' => $site->get('revoke_url'),
		'level' => $site->getInt('level', 1),
		'response' => $res['errno']
	);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function sites_get(CRObject $rule)
{
	if ($rule->get('owner') === null || $rule->get('owner') !== Session::get('username')) {
		if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'sites_get_others')) {
			$res['errno'] = Code::NO_PRIVILEGE;
			return $res;
		}
	}
	$res['errno'] = Code::SUCCESS;
	$res['count'] = SiteManager::getCount($rule);
	$res['sites'] = SiteManager::gets($rule);
	return $res;
}

/**/
function site_remove($site)
{
	$res['errno'] = Code::IN_DEVELOP;
	return $res;
}
