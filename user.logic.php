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
	require_once('email.logic.php');

	require_once('config.inc.php');
	require_once('init.inc.php');

	/**/
	function validate_username($username)
	{
		if($username===null || !is_string($username)){
			return false;
		}
		$reserved_names = array('system', '管理员', 'admin');
		if(in_array(strtolower($username), $reserved_names)){
			return false;
		}
		if(preg_match('/^[0-9]+$/', $username)){//reserve for phone number
			return false;
		}
		$blacklist = array("[", "]", "@");
		foreach($blacklist as $s){
			if(stripos($username, $s) !== false)
				return false;
		}
		return mb_strlen($username, 'utf8') > 0 && mb_strlen($username, 'utf8') <= 12;
	}

	/**/
	function validate_email($email)
	{
		if($email===null || !is_string($email)){
			return false;
		}
		if(strlen($email) > 45){
			return false;
		}
		return Validator::isEmail($email);
	}

	/**/
	function user_register($user)
	{
		$username = $user->get('username');
		$email = $user->get('email');
		$user->set('role', 'normal');
		if(!validate_username($username)){
			$res['errno'] = CRErrorCode::INVALID_USERNAME;
		}else if(!validate_email($email)){
			$res['errno'] = CRErrorCode::INVALID_EMAIL;
		}else if(UserManager::getByUsername($username) !== null){ 
			$res['errno'] = CRErrorCode::USERNAME_OCCUPIED;
		}else if(UserManager::getByEmail($email) !== null){ 
			$res['errno'] = CRErrorCode::EMAIL_OCCUPIED;
		}else{
			$password = password_hash($user->get('password'), PASSWORD_DEFAULT);
			$user->set('password', $password);
			$success = UserManager::add($user);
			$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		}
		$log = new CRObject();
		$log->set('scope', $username);
		$log->set('tag', 'signup');
		$content = array('username' => $username, 'email' => $email, 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/**/
	function user_login($user)
	{
		$account = $user->get('account', '');// can be username, email etc.
		$password = $user->get('password');
		$remember_me = $user->getBool('remember_me', false);
		if(strpos($account, '@') !== false){
			$user_arr = UserManager::getByEmail($account);
		}else{
			$user_arr = UserManager::getByUsername($account);
		}
		if($user_arr === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
		}else if(!password_verify($password, $user_arr['password'])){
			$res['errno'] = CRErrorCode::WRONG_PASSWORD;
		}else if($user_arr['role']==='removed'){ //removed
			$res['errno'] = CRErrorCode::USER_IS_REMOVED;
		}else if($user_arr['role']==='blocked'){ //blocked
			$res['errno'] = CRErrorCode::USER_IS_BLOCKED;
		}else if(FORCE_VERIFY && $user_arr['email_verified']==='0'){
			$res['errno'] = CRErrorCode::EMAIL_IS_NOT_VERIFIED;
		}else{
			if(!ENABLE_MULTIPLE_LOGIN){
				Session::expireByGroup($user_arr['username']);
			}
			Session::put('username', $user_arr['username']);
			Session::put('role', $user_arr['role']);
			Session::attach($user_arr['username']);
			if(ENABLE_COOKIE && $remember_me){
				Session::persist(604800);// 7 days
			}
			$res['errno'] = CRErrorCode::SUCCESS;
		}
		$log = new CRObject();
		if(isset($user_arr['username'])){
			$log->set('scope', $user_arr['username']);
		}else{
			$log->set('scope', '[nobody]');
		}
		$log->set('tag', 'signin');
		$content = array('account' => $account, 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/*
	 * clear session and cookie
	 */
	function signout()
	{
		Session::expire();
		$res['errno'] = CRErrorCode::SUCCESS;
		return $res;
	}

	/**/
	function user_update($user)
	{
		$user_arr = UserManager::getByUsername($user->get('username'));
		if($user_arr === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res;
		}
		if($user->get('email') !== null && $user_arr['email'] !== $user->get('email')){
			if(!validate_email($user->get('email'))){
				$res['errno'] = CRErrorCode::INVALID_EMAIL;
			}else if(UserManager::getByEmail($user->get('email')) !== null){
				$res['errno'] = CRErrorCode::EMAIL_OCCUPIED;
			}else{
				$user_arr['email'] = $user->get('email');
				$user_arr['email_verified'] = 0;
				verify_email($user);//expire verify_email code
			}
		}
		if($user_arr['username']!==Session::get('username')){
			if($user->get('password')!==null){
				$user_arr['password'] = password_hash($user->get('password'), PASSWORD_DEFAULT);
			}
			if(AccessController::hasAccess(Session::get('role'), 'user_update_'.$user_arr['role'])// can update role
				&& AccessController::hasAccess(Session::get('role'), 'user_update_'.$user->get('role', '')))// can update to role
			{
				$user_arr['role'] = $user->get('role');
			}else{
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			}
		}
		if(!isset($res)){
			$success = UserManager::update(new CRObject($user_arr));
			$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		}
		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'update_user');
		$content = array(
			'username' => $user->get('username'),
			'email' => $user->get('email'),
			'role' => $user->get('role'),
			'response' => $res['errno']
		);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/**/
	function user_update_pwd($user)
	{
		$user_arr = UserManager::getByUsername(Session::get('username'));
		if($user_arr === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res; 
		}
		if(!password_verify($user->get('old_pwd'), $user_arr['password']))
		{ /* verify old password first */
			$res['errno'] = CRErrorCode::WRONG_PASSWORD;
			return $res;
		}
		$password = password_hash($user->get('password'), PASSWORD_DEFAULT);
		$user_arr['password'] = $password;
		$success = UserManager::update(new CRObject($user_arr));
		$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'update_pwd');
		$content = array( 'response' => $res['errno'] );
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/**/
	function user_get($rule)
	{
		if(Session::get('username') === $rule->get('username') || AccessController::hasAccess(Session::get('role', 'visitor'), 'user_get_others')){
			$res['errno'] = CRErrorCode::SUCCESS;
			$user_arr = UserManager::getByUsername($rule->get('username'));
			if($user_arr === null){
				$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			}else{
				unset($user_arr['password']);
				$res['user'] = $user_arr;
			}
			return $res;
		}
		$res['errno'] = CRErrorCode::NO_PRIVILEGE;
		return $res;
	}

	/**/
	function users_get($rule)
	{
		if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'user_get_others')){
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['count'] = UserManager::getCount($rule);
		$res['users'] = UserManager::gets($rule);
		return $res;
	}

	/**/
	function user_get_log($rule)
	{
		if($rule->get('username')===null || $rule->get('username') !== Session::get('username')){
			if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'get_logs_others')){
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
				return $res;
			}
		}else{// view self signin log
			$rule->set('scope', $rule->get('username'));
			$rule->set('tag', 'signin');
		}
		$rule->set('time_begin', mktime(0, 0, 0, date('m'), date('d')-6, date('Y')));//last 7 days
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['logs'] = CRLogger::search($rule);
		return $res;
	}

	/**/
	function reset_pwd_send_code($user)
	{
		$user_arr = UserManager::getByUsername($user->get('username'));
		if($user_arr === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res;
		}
		if($user_arr['email'] !== $user->get('email')){
			$res['errno'] = CRErrorCode::USERNAME_MISMATCH_EMAIL;
			return $res;
		}
		$code = Random::randomInt(100000, 999999);
		$res['errno'] = CRErrorCode::SUCCESS;
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$redis->set('resetpwd:code:'.$user_arr['username'], $code, 'EX', 300);
		$redis->disconnect();

		$email = new CRObject();
		$email->set('email', $user_arr['email']);
		$email->set('username', $user_arr['username']);
		$email->set('subject', '[QuickAuth] Reset your password');
		$content = file_get_contents('templates/resetpwd_en.tpl');
		$content = str_replace('<%username%>', $user_arr['username'], $content);
		$content = str_replace('<%email%>', $user_arr['email'], $content);
		$content = str_replace('<%auth_key%>', $code, $content);
		$email->set('content', $content);
		$res = email_send($email);

		$log = new CRObject();
		$log->set('scope', $user_arr['username']);
		$log->set('tag', 'send_email');
		$content = array('username' => $user->get('username'), 'type' => 'reset_pwd', 'email' => $user->get('email'), 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/**/
	function verify_email_send_code($user)
	{
		$user_arr = UserManager::getByUsername($user->get('username'));
		if($user_arr === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res;
		}
		if($user_arr['email_verified'] === '1'){
			$res['errno'] = CRErrorCode::EMAIL_ALREADY_VERIFIED;
			return $res;
		}
		$code = Random::randomInt(100000, 999999);
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$redis->set('verify:code:'.$user_arr['username'], $code, 'EX', 300);
		$redis->disconnect();

		$email = new CRObject();
		$email->set('email', $user_arr['email']);
		$email->set('username', $user_arr['username']);
		$email->set('subject', '[QuickAuth] Verify your email');
		$content = file_get_contents('templates/verify_en.tpl');
		$content = str_replace('<%username%>', $user_arr['username'], $content);
		$content = str_replace('<%email%>', $user_arr['email'], $content);
		$content = str_replace('<%auth_key%>', $code, $content);
		$email->set('content', $content);
		$res = email_send($email);

		$log = new CRObject();
		$log->set('scope', $user_arr['username']);
		$log->set('tag', 'send_email');
		$content = array('username' => $user_arr['username'], 'type' => 'verify_email', 'email' => $user_arr['email'], 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/**/
	function reset_pwd($user)
	{
		$user_arr = UserManager::getByUsername($user->get('username'));
		if($user_arr === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res;
		}
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$code = $redis->get('resetpwd:code:'.$user_arr['username']);
		$redis->del('resetpwd:code:'.$user_arr['username']);//expire code immediatelly
		$redis->disconnect();
		if($code !== null && $code === $user->get('code')){
			$user_arr['password'] = password_hash($user->get('password'), PASSWORD_DEFAULT);
			$success = UserManager::update(new CRObject($user_arr));
			$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		}else{
			$res['errno'] = CRErrorCode::CODE_EXPIRED;
		}

		$log = new CRObject();
		$log->set('scope', $user_arr['username']);
		$log->set('tag', 'resetpwd');
		$content = array('username' => $user_arr['username'], 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/**/
	function verify_email($user)
	{
		$user_arr = UserManager::getByUsername($user->get('username'));
		if($user_arr === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res;
		}
		$redis = RedisDAO::instance();
		if($redis===null){
			$res['errno'] = CRErrorCode::UNABLE_TO_CONNECT_REDIS;
			return $res;
		}
		$code = $redis->get('verify:code:'.$user_arr['username']);
		$redis->del('verify:code:'.$user_arr['username']);
		$redis->disconnect();
		if($code !== null && $code === $user->get('code')){
			$user_arr['email_verified'] = 1;
			$success = UserManager::update(new CRObject($user_arr));
			$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		}else{
			$res['errno'] = CRErrorCode::CODE_EXPIRED;
		}
		$log = new CRObject();
		$log->set('scope', $user_arr['username']);
		$log->set('tag', 'verify_email');
		$content = array('username' => $user_arr['username'], 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}
