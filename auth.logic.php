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
		$r->set('id', $rule->getInt('client_id'));
		$site = SiteManager::get($r);
		if($site===null){
			$res['errno'] = CRErrorCode::SITE_NOT_EXIST;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['domain'] = $site['domain'];
		$res['auto_grant'] = $site['level']==='99'?1:0;
		return $res;
	}

	/* 
	 * array('response_type'='code', 'client_id'=APP_ID, 'redirect_uri', 'state', 'scope')
	 * return array('errno', 'code', 'state');
	 */
	function auth_grant($rule)
	{
		$r = new CRObject();
		$r->set('id', $rule->getInt('client_id'));
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
			$res['errno'] = CRErrorCode::INVALID_URL;
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
			'client_id' => $rule->getInt('client_id'),
			'client_secret' => $site['key'],
			'redirect_uri' => $rule->get('redirect_uri'),
			'username' => Session::get('username'),
			'scope' => $scope
		);
		$redis->set("auth:code:$code", json_encode($data), 'EX', 300);
		$redis->disconnect();

		$res['errno'] = CRErrorCode::SUCCESS;
		$res['code'] = $code;
		$res['state'] = $rule->get('state');
		$res['uid'] = Session::get('username');
		
		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'auth_grant');
		$content = array('client_id' => $rule->getInt('client_id'), 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/* array('grant_type'=>'authorization_code', 'client_id'=>APP_ID, 'client_secret'=>APP_KEY, 'code', 'redirect_uri')
	 * return array('errno', 'access_token', 'expires_in'=>3600);
	 */
	function auth_get_token($rule)
	{
		if($rule->get('grant_type')!=='authorization_code'){
			$res['errno'] = CRErrorCode::INVALID_PARAM;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$code = $rule->get('code');
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
		if($rule->getInt('client_id')!==$data['client_id'] || $rule->get('redirect_uri')!==$data['redirect_uri'] || $rule->get('client_secret')!==$data['client_secret']){
			$res['errno'] = CRErrorCode::INVALID_URL;
			$redis->disconnect();
			return $res;
		}
		$redis->del("auth:code:$code");
		$token = Random::randomString(64);
		$arr = array('expires' => time() + 3600*24*30, 'client_id' => $data['client_id'], 'client_secret' => $data['client_secret'], 'scope' => $data['scope']);
		$data2 = json_encode($arr);
		$redis->hset("auth:token:{$data['username']}", $token, $data2);
		$code = Random::randomString(64);
		$res['token'] = $token;
		$redis->disconnect();
		return $res;
	}

	/*
	 * array('grant_type'=>'refresh_token', 'client_id', 'client_secret', 'refresh_token', 'uid')
	 * return array('errno', 'access_token', 'expires_in'=>3600, 'refresh_token');
	 */
	function auth_refresh_token($rule)
	{
		if($rule->get('grant_type')!=='refresh_token'){
			$res['errno'] = CRErrorCode::INVALID_PARAM;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$uid = $rule->get('uid', '');
		$token = $rule->get('token', '');
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
		$arr = array('expires' => time() + 3600*24*30, 'client_id' => $data['client_id'], 'client_secret' => $data['client_secret'], 'scope' => $data['scope']);
		$data2 = json_encode($arr);
		$redis->hset("auth:token:$uid", $token, $data2);
		$res['token'] = $token;
		$redis->disconnect();
		return $res;
	}


	/* query user info */
	function auth_get_info($rule)
	{
		$uid = $rule->get('uid', '');
		$token = $rule->get('token', '');
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