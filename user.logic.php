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

	require_once('config.inc.php');
	require_once('init.inc.php');

	/**/
	function validate_username($username)
	{
		$reserved_names = array('system', 'SYSTEM', '管理员', 'admin', 'ADMIN');
		if(in_array($username, $reserved_names)){
			return false;
		}
		if(strpos($username, '@') !== false){
			return false;
		}
		return mb_strlen($username, 'utf8') > 0 && mb_strlen($username, 'utf8') <= 12;
	}

	/**/
	function validate_email($email){
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
			return $res;
		}
		if(!validate_email($email)){
			$res['errno'] = CRErrorCode::INVALID_EMAIL;
			return $res;
		}
		if(UserManager::getByUsername($username) !== null){ 
			$res['errno'] = CRErrorCode::USERNAME_OCCUPIED;
			return $res;
		}
		if(UserManager::getByEmail($email) !== null){ 
			$res['errno'] = CRErrorCode::EMAIL_OCCUPIED;
			return $res;
		}
		$password = password_hash($user->get('password'), PASSWORD_DEFAULT);
		$user->set('password', $password);
		$success = UserManager::add($user);
		$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		
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
		$account = $user->get('account');// may be username, email or pnone number etc.
		$password = $user->get('password');
		$remember_me = $user->getBool('remember_me');
		if(strpos($account, '@') !== false){
			$user_arr = UserManager::getByEmail($account);
		}else{
			$user_arr = UserManager::getByUsername($account);
		}
		if($user_arr === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res;
		}
		if($user_arr['role']==='removed'){ //removed
			$res['errno'] = CRErrorCode::USER_IS_REMOVED;
			return $res;
		}
		if($user_arr['role']==='blocked'){ //blocked
			$res['errno'] = CRErrorCode::USER_IS_BLOCKED;
			return $res;
		}
		if(password_verify($password, $user_arr['password'])){
			Session::put('username', $user_arr['username']);
			Session::put('role', $user_arr['role']);
			if(ENABLE_COOKIE && $remember_me){
				setcookie('username', $username, time() + 604800);// 7 days
				setcookie('token', session_id(), time() + 604800);//7 days
			}
			$res['errno'] = CRErrorCode::SUCCESS;
		}else{
			$res['errno'] = CRErrorCode::WRONG_PASSWORD;
		}

		$log = new CRObject();
		$log->set('scope', $user_arr['username']);
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
		setcookie('sid', '', time() - 3600);
		setcookie('token', '', time() - 3600);
		Session::clear();
		$res['errno'] = CRErrorCode::SUCCESS;
		return $res;
	}

	/**/
	function user_update($user_new)
	{
		$user_arr = UserManager::getByUsername($user_new->get('username'));
		if($user_arr === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res; 
		}
		if($user_new->get('email') !== null){
			if(!validate_email($user_new->get('email'))){
				$res['errno'] = CRErrorCode::INVALID_EMAIL;
				return $res;
			}
			if($user_arr['email'] !== $user_new->get('email')){
				if(UserManager::getByEmail($user_new->get('email')) !== null){
					$res['errno'] = CRErrorCode::EMAIL_OCCUPIED;
					return $res;
				}
				$user_arr['email_verified'] = 0;
			}
			$user_arr['email'] = $user_new->get('email');
		}
		if($user_new->get('password')!==null){
			$user_arr['password'] = password_hash($user_new->get('password'), PASSWORD_DEFAULT);
		}
		if($user_arr['username']!==Session::get('username')) { //update self is not allowed
			if(!AccessController::hasAccess(Session::get('role'), 'user_update_'.$user_arr['role'])){// can update
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
				return $res;
			}
			if(!AccessController::hasAccess(Session::get('role'), 'user_update_'.$user_new->get('role', ''))){// can update to role
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
				return $res;
			}
			$user_arr['role'] = $user_new->get('role');
		}else{
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}

		$success = UserManager::update(new CRObject($user_arr));
		$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'update');
		$content = array(
			'username' => $user_arr['username'],
			'email' => $user_arr['email'],
			'role' => $user_arr['role'],
			'response' => $res['errno']
		);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/**/
	function user_update_pwd($user)
	{
		$user_arr = UserManager::getByUsername($user->get('username'));
		if($user_arr === null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res; 
		}
		if(!password_verify($user->get('old_pwd'), $user_arr['password']))
		{ /* need provide old password */
			$res['errno'] = CRErrorCode::WRONG_PASSWORD;
			return $res;
		}
		$password = password_hash($user->get('password'), PASSWORD_DEFAULT);
		$user_arr['password'] = $password;
		$success = UserManager::update(new CRObject($user_arr));
		$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'updatepwd');
		$content = array(
			'username' => $user_arr['username'],
			'response' => $res['errno']
		);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/*
	 * check if cookie is valid, if so, log in the user
	 */
	function login_from_cookie($username, $password)
	{
		if(!ENABLE_COOKIE){
			return false;
		}
		if(!validate_username($username)){
			return false;
		}

		$user = UserManager::getByUsername($username);
		if($user === null){
			signout();
			return false;
		}
		if($password === UserManager::cryptPwdForCookie($user['password'])){
			Session::put('username', $user['username']);
			Session::put('role', $user['role']);
			Session::put('loged', false);
			$log = new CRObject();
			$log->set('scope', $username);
			$log->set('tag', 'signin');
			$content = array('method' => 'cookie', 'response' => $res['errno']);
			$log->set('content', json_encode($content));
			CRLogger::log2db($log);
			return true;
		}
		signout();
		return false;
	}


	/**/
	function user_get($rule)
	{
		if(Session::get('username') === $rule->get('username') || AccessController::hasAccess(Session::get('role', 'visitor'), 'user_get_others')){ //access control. potential BUG: admin can view root
			$res['errno'] = CRErrorCode::SUCCESS;
			$user = UserManager::getByUsername($rule->get('username'));
			if($user === null){
				$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			}else{
				unset($user['password']);
			}
			$res['user'] = $user;
			return $res;
		}
		$res['errno'] = CRErrorCode::NO_PRIVILEGE;
		return $res;
	}

	/**/
	function users_get($rule)
	{
		if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'user_get_others')){ //access control
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
		if(is_null($rule->get('username')) || $rule->get('username') !== Session::get('username')){
			if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'get_logs_others')){ //access control
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
				return $res;
			}
		}else{
			if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'get_logs_self')){ //access control
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
				return $res;
			}
		}
		$rule->set('scope', $rule->get('username'));
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
		$res['msg'] = $code;

		$log = new CRObject();
		$log->set('scope', $user->get('username'));
		$log->set('tag', 'send_email');
		$content = array('type' => 'reset_pwd', 'email' => $user->get('email'), 'response' => $res['errno']);
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
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['msg'] = $code;

		$log = new CRObject();
		$log->set('scope', $user->get('username'));
		$log->set('tag', 'send_email');
		$content = array('type' => 'verify_email', 'email' => $user_arr['email'], 'response' => $res['errno']);
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
		$code = '123456';
		if($code !== $user->get('code')){
			$res['errno'] = CRErrorCode::CODE_EXPIRED;
			return $res;
		}
		$password = password_hash($user->get('password'), PASSWORD_DEFAULT);
		$user_arr['password'] = $password;
		$success = UserManager::update(new CRObject($user_arr));

		$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		$log = new CRObject();
		$log->set('scope', $user->get('username'));
		$log->set('tag', 'resetpwd');
		$content = array('code' => $code, 'response' => $res['errno']);
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
		$code = '123456';
		if($code !== $user->get('code')){
			$res['errno'] = CRErrorCode::CODE_EXPIRED;
			return $res;
		}
		$user_arr['email_verified'] = 1;
		$success = UserManager::update(new CRObject($user_arr));
		$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		$log = new CRObject();
		$log->set('scope', $user->get('username'));
		$log->set('tag', 'verify_email');
		$content = array('code' => $code, 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/**/
	function users_online($rule)
	{
		if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'get_online_users')){
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['users'] = Session::listOnline();
		return $res;
	}

	/**/
	function tick_out($rule)
	{
		if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'tick_out_user')){
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}
		$user = UserManager::getByUsername($rule->get('username'));
		if($user===null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res;
		}
		if(!AccessController::hasAccess(Session::get('role', 'visitor'), "tick_out_{$user['role']}")){
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$username = $rule->get('username');
		Session::tickOut($username);
		return $res;
	}

	/**/
	function block($rule)
	{
		$res['errno'] = CRErrorCode::SUCCESS;
		return $res;
	}

	/**/
	function unblock($rule)
	{
		$res['errno'] = CRErrorCode::SUCCESS;
		return $res;
	}

