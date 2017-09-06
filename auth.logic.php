<?php
	require_once('predis/autoload.php');
	require_once('util4p/CRObject.class.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/Validator.class.php');
	require_once('util4p/ReSession.class.php');
	require_once('util4p/CRLogger.class.php');
	require_once('util4p/AccessController.class.php');
	require_once('util4p/Random.class.php');

	require_once('UserManager.class.php');
	require_once('SiteManager.class.php');

	require_once('config.inc.php');
	require_once('init.inc.php');

	/* SSO or OAuth */
	function auth_get_site($rule)
	{
		$r = new CRObject();
		$r->set('id', $rule->getInt('app_id'));
		$site = SiteManager::get($r);
		if($site===null){
			$res['errno'] = CRErrorCode::SITE_NOT_EXIST;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['auto_grant'] = $site['level']==='99'?1:0;
		return $res;
	}

	/*
	 * array('response_type'='code', 'app_id', 'redirect_uri', 'state', 'scope')
	 * return array('errno', 'code', 'state');
	 */
	function auth_grant($rule)
	{
		$app_id = $rule->getInt('app_id');
		$r = new CRObject();
		$r->set('id', $app_id);
		$site = SiteManager::get($r);
		if($site===null){
			$res['errno'] = CRErrorCode::SITE_NOT_EXIST;
			return $res;
		}
		if($rule->get('response_type')!=='code'){
			$res['errno'] = CRErrorCode::INVALID_PARAM;
			return $res;
		}
		if(!Validator::isURL($rule->get('redirect_uri'))){
			$res['errno'] = CRErrorCode::INVALID_URL;
			return $res;
		}
		$arr = parse_url($rule->get('redirect_uri'));
		if($arr['host'] !== $site['domain']){
			$res['errno'] = CRErrorCode::DOMAIN_MISMATCH;
			return $res;
		}
		if($rule->get('state')===null){
			$res['errno'] = CRErrorCode::INCOMPLETE_CONTENT;
			return $res;
		}
		$scope = array_filter(explode(',', $rule->get('scope', '')), 'strlen');

		$code = Random::randomString(64);
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$data = array(
			'app_id' => $app_id,
			'app_key' => $site['key'],
			'redirect_uri' => $rule->get('redirect_uri'),
			'uid' => Session::get('username'),
			'scope' => $scope
		);
		$redis->set("auth:code:$code", json_encode($data), 'EX', 300);
		$redis->disconnect();

		$res['errno'] = CRErrorCode::SUCCESS;
		$res['code'] = $code;
		$res['state'] = $rule->get('state');
		
		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'auth_grant');
		$content = array('app_id' => $app_id, 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/* array('grant_type'=>'authorization_code', 'app_id', 'app_key', 'code', 'redirect_uri')
	 * return array('errno', 'token', 'expires_in'=>3600);
	 */
	function auth_get_token($rule)
	{
		if($rule->get('grant_type')!=='authorization_code'){
			$res['errno'] = CRErrorCode::INVALID_PARAM;
			return $res;
		}
		$app_id = $rule->getInt('app_id', '');
		$code = $rule->get('code', '');
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$str = $redis->get("auth:code:$code");
		if($str===null){
			$res['errno'] = CRErrorCode::CODE_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		$data = json_decode($str, true);
		if($app_id!==$data['app_id'] || $rule->get('redirect_uri')!==$data['redirect_uri'] || $rule->get('app_key')!==$data['app_key']){
			$res['errno'] = CRErrorCode::INVALID_URL;
			$redis->disconnect();
			return $res;
		}
		$redis->del("auth:code:$code");
		$token = Random::randomString(64);
		$token = $data['uid'].'$'.$token;
		$arr = array('expires' => time() + 3600*24*30, 'app_id' => $data['app_id'], 'app_key' => $data['app_key'], 'scope' => $data['scope']);
		$data2 = json_encode($arr);
		$redis->hset("auth:token:{$data['uid']}", $token, $data2);
		$code = Random::randomString(64);
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['token'] = $token;
		$res['expires_in'] = 3600*24*30;
		$redis->disconnect();
		return $res;
	}

	/*
	 * array('grant_type'=>'refresh_token', 'app_id', 'app_key', 'token')
	 * return array('errno', 'token', 'expires_in'=>3600);
	 */
	function auth_refresh_token($rule)
	{
		if($rule->get('grant_type')!=='refresh_token'){
			$res['errno'] = CRErrorCode::INVALID_PARAM;
			return $res;
		}
		$token = $rule->get('token', '');
		$vals = explode('$', $token);
		if(count($vals)!==2){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			return $res;
		}
		$uid = $vals[0];
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$str = $redis->hget("auth:token:$uid", $token);
		if($str===null){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		$data = json_decode($str, true);
		if($data['expires'] < time()){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		$redis->hdel("auth:token:$uid", $token);
		$token = Random::randomString(64);
		$token = $uid.'$'.$token;
		$arr = array('expires' => time() + 3600*24*30, 'app_id' => $data['app_id'], 'app_key' => $data['app_key'], 'scope' => $data['scope']);
		$data2 = json_encode($arr);
		$redis->hset("auth:token:$uid", $token, $data2);
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['token'] = $token;
		$res['expires_in'] = 3600*24*30;
		$redis->disconnect();
		return $res;
	}


	/* query user info */
	function auth_get_info($rule)
	{
		$token = $rule->get('token', '');
		$vals = explode('$', $token);
		if(count($vals)!==2){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			return $res;
		}
		$uid = $vals[0];
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$str = $redis->hget("auth:token:$uid", $token);
		if($str===null){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		$data = json_decode($str, true);
		if($data['expires'] < time()){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		$scope = $data['scope'];
		$user = UserManager::getByUsername($uid);
		$info = array();
		$info['uid'] = $user['username'];
		$allowed_scopes = array('email', 'email_verified', 'role', 'nickname');
		foreach($allowed_scopes as $s){
			if(in_array($s, $scope)){
				$info[$s] = $user[$s];
			}
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['info'] = $info;
		return $res;
	}

	/* */
	function auth_revoke($rule)
	{
		$app_id = $rule->getInt('app_id', '');
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$success = $redis->hdel("auth:token:$uid", $app_id);
		$res['errno'] = $success>0?CRErrorCode::SUCCESS:CRErrorCode::FAIL;

		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'auth_revoke');
		$content = array('app_id' => $app_id, 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/* */
	function auth_list($rule)
	{
		$uid = Session::get('username');
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$list = $redis->hgetall("auth:token:$uid");
		$redis->disconnect();
		$sites = array();
		foreach($list as $key => $value){
			$data = json_decode($value, true);
			$sites[] = array(
				'app_id' => $data['app_id'],
				'expires' => $data['expires'],
				'scope' => join(',', $data['scope'])
			);
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['list'] = $sites;
		return $res;
	}
