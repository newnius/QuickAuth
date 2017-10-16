<?php
	require_once('predis/autoload.php');
	require_once('util4p/CRObject.class.php');
	require_once('util4p/CRErrorCode.class.php');
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
		$res['host'] = $site['domain'];
		$res['auto_grant'] = $site['level']==='99'?1:0;
		return $res;
	}


	/*
	 * array('response_type'='code', 'app_id', 'redirect_uri', 'state', 'scope')
	 * return array('errno', 'code', 'state');
	 */
	function auth_grant($rule)
	{
		if(Session::get('username')===null){
			$res['errno'] = CRErrorCode::NOT_LOGED;
			return $res;
		}
		$app_id = $rule->getInt('app_id');
		$r = new CRObject();
		$r->set('id', $app_id);
		$site = SiteManager::get($r);
		if($site===null){
			$res['errno'] = CRErrorCode::SITE_NOT_EXIST;
		}else if($rule->get('response_type')!=='code'){
			$res['errno'] = CRErrorCode::INVALID_PARAM;
		}else if(!Validator::isURL($rule->get('redirect_uri'))){
			$res['errno'] = CRErrorCode::INVALID_URL;
		}else{
			$arr = parse_url($rule->get('redirect_uri'));
			if($arr['host'] !== $site['domain']){
				$res['errno'] = CRErrorCode::DOMAIN_MISMATCH;
			}else if($rule->get('state')===null){
				$res['errno'] = CRErrorCode::INCOMPLETE_CONTENT;
			}else{
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
					'revoke_url' => $site['revoke_url'],
					'redirect_uri' => $rule->get('redirect_uri'),
					'uid' => Session::get('username'),
					'scope' => json_encode($scope)
				);
				$redis->hmset("auth:code:$code", $data);
				$redis->expire("auth:code:$code", 300);
				$redis->disconnect();

				$res['errno'] = CRErrorCode::SUCCESS;
				$res['code'] = $code;
				$res['state'] = $rule->get('state');
			}
		}
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
		$app_id = $rule->get('app_id', '');
		$code = $rule->get('code', '');
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$data = $redis->hgetall("auth:code:$code");
		if(count($data)===null){
			$res['errno'] = CRErrorCode::CODE_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		if($app_id!==$data['app_id'] || $rule->get('redirect_uri')!==$data['redirect_uri'] || $rule->get('app_key')!==$data['app_key']){
			$res['errno'] = CRErrorCode::INVALID_URL;
			$redis->disconnect();
			return $res;
		}
		$redis->del("auth:code:$code");
		$token = Random::randomString(64);
		$data2 = array(
			'expires' => time() + 3600*24*30,
			'app_id' => $data['app_id'],
			'app_key' => $data['app_key'],
			'revoke_url' => $data['revoke_url'],
			'uid' => $data['uid'],
			'scope' => $data['scope']
		);
		$redis->hmset("auth:token:$token", $data2);
		$redis->expire("auth:token:$token", 3600*24*30);
		/* remove old token */
		$t = $redis->hget("auth:group:{$data['uid']}", $app_id);
		if($t !== null){
			$redis->del("auth:token:$t");
		}
		$redis->hset("auth:group:{$data['uid']}", $app_id, $token);
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
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$data = $redis->hgetall("auth:token:$token");
		if(count($data)===0){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		if($data['expires'] < time()){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		$redis->del("auth:token:$token");
		$t = $redis->hget("auth:group:{$data['uid']}", $data['app_id']);
		if($t !== $token){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		$token = Random::randomString(64);
		$redis->hset("auth:group:{$data['uid']}", $data['app_id'], $token);
		$data2 = array(
			'expires' => time() + 3600*24*30,
			'app_id' => $data['app_id'],
			'app_key' => $data['app_key'],
			'revoke_url' => $data['revoke_url'],
			'uid' => $data['uid'],
			'scope' => $data['scope']
		);
		$redis->hmset("auth:token:$token", $data2);
		$redis->expire("auth:token:$token", 3600*24*30);
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
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$data = $redis->hgetall("auth:token:$token");
		if(count($data)===0){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		if($data['expires'] < time()){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->del("auth:token:$token");
			$redis->disconnect();
			return $res;
		}
		$t = $redis->hget("auth:group:{$data['uid']}", $data['app_id']);
		if($t !== $token){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		$scope = json_decode($data['scope']);
		$user = UserManager::getByUsername($data['uid']);
		if($user === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res;
		}
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
		if(Session::get('username')===null){
			$res['errno'] = CRErrorCode::NOT_LOGED;
			return $res;
		}
		$app_id = $rule->getInt('app_id', '');
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$token = $redis->hget('auth:group:'.Session::get('username'), $app_id);
		$redis->hdel("auth:group:".Session::get('username'), $app_id);
		$data = $redis->hgetall("auth:token:$token");
		if(count($data)===0){
			$res['errno'] = CRErrorCode::TOKEN_EXPIRED;
			$redis->disconnect();
			return $res;
		}
		$success = $redis->del("auth:token:$token");
		$res['errno'] = $success>0?CRErrorCode::SUCCESS:CRErrorCode::FAIL;

		/* notify site if revoke_url is set */
		$url = $data['revoke_url'];
		if($url !== null && strlen($url)>10){
			$form_params = array(
				'uid' => $data['uid'],
				'token' => $token
			);
			$post_data = array('form_params' => $form_params);
			$client = new \GuzzleHttp\Client(['timeout' => 3, 'headers' => ['User-Agent' => 'QuickAuth Bot']]);
			try{
				$client->request('POST', $url, $post_data);
			}catch(Exception $e){//pass
			}
		}

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
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$list = $redis->hgetall('auth:group:'.Session::get('username', ''));
		$redis->disconnect();
		$sites = array();
		foreach($list as $app_id => $token){
			$data = $redis->hgetall("auth:token:$token");
			if(count($data)===0){
				$redis->hdel('auth:group:'.Session::get('username', ''), $app_id);
				continue;
			}
			$sites[] = array(
				'app_id' => $app_id,
				'expires' => $data['expires'],
				'scope' => $data['scope']
			);
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['list'] = $sites;
		return $res;
	}


	/**/
	function site_add($site)
	{
		if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'site_add_'.$site->getInt('level', 1))){
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}
		$site->set('owner', Session::get('username'));
		$site->set('key', Random::randomString(64));
		$site->set('level', $site->getInt('level', 1));
		$success = SiteManager::add($site);
		$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;

		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'site_add');
		$content = array('domain' => $site->get('domain'), 'revoke_url' => $site->get('revoke_url'), 'level' => $site->getInt('level', 1), 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}


	/**/
	function site_update($site)
	{
		$site_arr = SiteManager::get($site);
		if($site_arr === null){
			$res['errno'] = CRErrorCode::RECORD_NOT_EXIST;
		}else if($site_arr['owner']!==Session::get('username') && !AccessController::hasAccess(Session::get('role', 'visitor'), 'site_update_others')){
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
		}else if(!AccessController::hasAccess(Session::get('role'), 'site_add_'.$site->getInt('level', 1))){// can update to level
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
		}else{
			$site_arr['level'] = $site->getInt('level', 1);
			$site_arr['domain'] = $site->get('domain');
			$site_arr['revoke_url'] = $site->get('revoke_url');

			$success = SiteManager::update(new CRObject($site_arr));
			$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
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
		CRLogger::log2db($log);
		return $res;
	}

	/* */
	function sites_get($rule)
	{
		if($rule->get('owner') === null || $rule->get('owner') !== Session::get('username')){
			if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'sites_get_others')){
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
				return $res;
			}
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['count'] = SiteManager::getCount($rule);
		$res['sites'] = SiteManager::gets($rule);
		return $res;
	}
