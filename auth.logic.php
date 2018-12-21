<?php

require_once('predis/autoload.php');
require_once('util4p/CRObject.class.php');
require_once('util4p/Validator.class.php');
require_once('util4p/ReSession.class.php');
require_once('util4p/CRLogger.class.php');
require_once('util4p/AccessController.class.php');
require_once('util4p/Random.class.php');

require_once('Code.class.php');
require_once('UserManager.class.php');
require_once('SiteManager.class.php');
require_once('OAuth2.class.php');

require_once('config.inc.php');
require_once('init.inc.php');

/* get OAuth client domain */
function auth_get_site(CRObject $rule)
{
	$r = new CRObject();
	$r->set('client_id', $rule->get('client_id'));
	$site = SiteManager::get($r);
	if ($site === null) {
		$res['errno'] = Code::SITE_NOT_EXIST;
		return $res;
	}
	$res['errno'] = Code::SUCCESS;
	$res['host'] = $site['domain'];
	return $res;
}

/*
 * array('response_type'='code', 'client_id', 'redirect_uri', 'state', 'scope')
 * return array('errno', 'code', 'state');
 */
function auth_grant(CRObject $rule)
{
	$client = new CRObject();
	$client->set('client_id', $rule->get('client_id'));
	$site = SiteManager::get($client);

	if (Session::get('username') === null) {
		$res['errno'] = Code::NOT_LOGGED;
	} else if ($rule->get('response_type') !== 'code') {
		$res['errno'] = Code::INVALID_PARAM;
	} else if ($site === null) {
		$res['errno'] = Code::SITE_NOT_EXIST;
	} else if (!Validator::isURL($rule->get('redirect_uri'))) {
		$res['errno'] = Code::INVALID_URL;
	} else if (parse_url($rule->get('redirect_uri'))['host'] !== $site['domain']) {
		$res['errno'] = Code::DOMAIN_MISMATCH;
	} else if ($rule->get('state') === null) {
		$res['errno'] = Code::INCOMPLETE_CONTENT;
	} else if (!ENABLE_OAUTH) {
		$res['errno'] = Code::OAUTH_DISABLED;
	} else {
		$open_id = OAuth2::getOpenID(Session::get('username'), $rule->get('client_id'));
		$rule->set('open_id', $open_id);
		$code = OAuth2::getCode($rule);

		$res['errno'] = $code !== null ? Code::SUCCESS : Code::FAIL;
		$res['code'] = $code;
		$res['state'] = $rule->get('state');
		$res['scope'] = $rule->get('scope');
	}
	$log = new CRObject();
	$log->set('scope', Session::get('username', '[nobody]'));
	$log->set('tag', 'oauth.grant');
	$content = array('client_id' => $rule->get('client_id'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/*
 * array('grant_type'=>'authorization_code', 'client_id', 'client_secret', 'code', 'redirect_uri')
 * return array('errno', 'token', 'expires_in'=>3600);
 */
function auth_get_token(CRObject $rule)
{
	if ($rule->get('grant_type') !== 'authorization_code') {
		$res['errno'] = Code::INVALID_PARAM;
	} else if (!Validator::isURL($rule->get('redirect_uri'))) {
		$res['errno'] = Code::INVALID_URL;
	} else {
		$token = OAuth2::getToken($rule);
		$res['errno'] = $token !== null ? Code::SUCCESS : Code::CODE_EXPIRED;
		$res['token'] = $token;
		$res['expires_in'] = OAUTH_TOKEN_TIMEOUT;
	}
	$log = new CRObject();
	$log->set('scope', '[oauth]');
	$log->set('tag', 'oauth.getToken');
	$content = array('client_id' => $rule->get('client_id'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/*
 * array('grant_type'=>'refresh_token', 'client_id', 'client_secret', 'token')
 * return array('errno', 'token', 'expires_in'=>3600);
 */
function auth_refresh_token(CRObject $rule)
{
	if ($rule->get('grant_type') !== 'refresh_token') {
		$res['errno'] = Code::INVALID_PARAM;
	} else {
		$token = OAuth2::refreshToken($rule);
		$res['errno'] = $token !== null ? Code::SUCCESS : Code::TOKEN_EXPIRED;
		$res['token'] = $token;
		$res['expires_in'] = OAUTH_TOKEN_TIMEOUT;
	}
	$log = new CRObject();
	$log->set('scope', '[oauth]');
	$log->set('tag', 'oauth.refreshToken');
	$content = array('client_id' => $rule->get('client_id'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/* query user info */
/*
 * array('api_name'=>'basic', 'client_id', 'client_secret', 'token')
 * return array('errno', 'info');
 */
function auth_get_info(CRObject $rule)
{
	if (!OAuth2::validateToken($rule)) {
		$res['errno'] = Code::TOKEN_EXPIRED;
		return $res;
	}
	$auth = OAuth2::getAuth($rule);
	$scope = json_decode($auth['scope']);
	$user = UserManager::getByUsername(OAuth2::getUID($auth['open_id'], $auth['client_id']));
	if ($user === null) {
		$res['errno'] = Code::USER_NOT_EXIST;
		return $res;
	}
	$info = array();
	$info['nickname'] = $user['username'];
	$info['open_id'] = $auth['open_id'];
	$allowed_scopes = array('email', 'email_verified', 'role');
	foreach ($allowed_scopes as $s) {
		if (in_array($s, $scope)) {
			$info[$s] = $user[$s];
		}
	}
	$res['errno'] = Code::SUCCESS;
	$res['info'] = $info;
	$log = new CRObject();
	$log->set('scope', '[oauth]');
	$log->set('tag', 'oauth.getInfo');
	$content = array('client_id' => $rule->get('client_id'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/*
 *
 * array('client_id')
 * */
function auth_revoke(CRObject $rule)
{
	if (Session::get('username') === null) {
		$res['errno'] = Code::NOT_LOGGED;
		return $res;
	}
	$open_id = OAuth2::getOpenID(Session::get('username'), $rule->get('client_id'));
	$success = OAuth2::revoke($open_id, $rule->get('client_id'));
	$res['errno'] = $success ? Code::SUCCESS : Code::FAIL;

	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'auth.revoke');
	$content = array('client_id' => $rule->get('client_id'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function auth_list(CRObject $rule)
{
	if (Session::get('username') === null) {
		$res['errno'] = Code::NOT_LOGGED;
		return $res;
	}
	$list = OAuth2::getAuthListByUID(Session::get('username'));
	$res['errno'] = Code::SUCCESS;
	$res['list'] = $list;
	return $res;
}

/**/
function site_add(CRObject $site)
{
	if (Session::get('username') === null) {
		$res['errno'] = Code::NOT_LOGGED;
		return $res;
	}
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'site.add')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$client_id = Random::randomString(16);
	for ($i = 0; $i < 10; $i++) {// try at most 10 times
		$site->set('client_id', $client_id);
		if (SiteManager::get($site) === null) {
			break;
		}
		$client_id = Random::randomString(16);
	}
	$site->set('owner', Session::get('username'));
	$site->set('client_secret', Random::randomString(64));
	$success = SiteManager::add($site);
	$res['errno'] = $success ? Code::SUCCESS : Code::UNKNOWN_ERROR;

	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'client.add');
	$content = array('domain' => $site->get('domain'), 'client_id' => $site->get('client_id'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function sites_get(CRObject $rule)
{
	if ($rule->get('owner') === null || $rule->get('owner') !== Session::get('username')) {
		if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'site.get_others')) {
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
function site_remove(CRObject $site)
{
	if (Session::get('username') === null) {
		$res['errno'] = Code::NOT_LOGGED;
		return $res;
	}
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'site.remove')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$site_arr = SiteManager::get($site);
	if ($site_arr === null) {
		$res['errno'] = Code::SITE_NOT_EXIST;
		return $res;
	}
	if (Session::get('username') !== $site_arr['owner'] && !AccessController::hasAccess(Session::get('role', 'visitor'), 'site.remove_others')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$success = SiteManager::remove($site);
	$res['errno'] = $success ? Code::SUCCESS : Code::UNKNOWN_ERROR;

	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'client.remove');
	$content = array('domain' => $site->get('domain'), 'client_id' => $site->get('client_id'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}
