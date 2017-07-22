<?php
	require_once('util4p/CRObject.class.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/Validator.class.php');
	require_once('util4p/Session.class.php');
	require_once('util4p/CRLogger.class.php');
	require_once('util4p/AccessController.class.php');
	require_once('UserManager.class.php');
	
	require_once('config.inc.php');
	require_once('init.inc.php');

	/**/
	function validate_username($username)
	{
		if(strpos($username, '@') !== false){
			return false;
		}
		return mb_strlen($username, 'utf8') > 0 && mb_strlen($username, 'utf8') <= 12;
	}

	/**/
	function validate_email($email){
		return Validator::isEmail($email);
	}

	/**/
	function is_username_exists($username)
	{
		if(!validate_username($username)){
			return CRErrorCode::USERNAME_OCCUPIED;
		}
		$occupied = UserManager::isUsernameExists($username);
		$res['errno'] = $occupied?CRErrorCode::USERNAME_OCCUPIED:CRErrorCode::SUCCESS;
		return $res;
	}

	/**/
	function is_email_exists($email)
	{
		if(!validate_email($email)){
			return CRErrorCode::EMAIL_OCCUPIED;
		}
		$occupied = UserManager::isEmailExists($email);
		$res['errno'] = $occupied?CRErrorCode::EMAIL_OCCUPIED:CRErrorCode::SUCCESS;
		return $res;
	}

	/**/
	function user_register($user)
	{
		$username = $user->get('username');
		$email = $user->get('email');
		$user->set('role', 'normal');

		$res['errno'] = CRErrorCode::SUCCESS;
		if(!validate_username($username)){
			$res['errno'] = CRErrorCode::INVALID_USERNAME;
			return $res;
		}
		if(!validate_email($email)){
			$res['errno'] = CRErrorCode::INVALID_EMAIL;
			return $res;
		}
		if(UserManager::isUsernameExists($username)){ 
			$res['errno'] = CRErrorCode::USERNAME_OCCUPIED;
			return $res;
		}
		if(UserManager::isEmailExists($email)){ 
			$res['errno'] = CRErrorCode::EMAIL_OCCUPIED;
			return $res;
		}
		$success = UserManager::addUser($user);
		if(!$success){
			$res['errno'] = CRErrorCode::UNKNOWN_ERROR;
			return $res;
		}
		return $res;
	}

	function user_login($user){
		$account = $user->get('account');// may be username, email or pnone number etc.
		$pwd = $user->get('pwd');
		$remember_me = $user->get('remember_me', false);

		$user = null;
		if(strpos($username, '@') !== false){
			$user = UserManager::getUserByEmail($account);
		}else{
			$user = UserManager::getUserByUsername($account);
		}

		if($user == null){
			$res['errno'] = CRErrorCode::USER_NOT_EXIST;
			return $res;
		}
		if($user['role']=='removed'){ //removed
			$res['errno'] = CRErrorCode::USER_IS_REMOVED;
			return $res;
		}
		if($user['role']=='blocked'){ //blocked
			$res['errno'] = CRErrorCode::USER_IS_BLOCKED;
			return $res;
		}
		if(password_verify($pwd, $user['password'])){
			Session::put('username', $user['username']);
			Session::put('role', $user['role']);
			if(ENABLE_COOKIE && $remember_me){
				setcookie('username', $username, time() + 604800);// 7 days
				setcookie('sid', session_id(), time()+604800);//7 days
			}
			$res['errno'] = CRErrorCode::SUCCESS;
		}else{
			$res['errno'] = CRErrorCode::WRONG_PASSWORD;
		}

		$log = new CRObject();
		$log->set('scope', $user['username']);
		$log->set('tag', 'signin');
		$content = array('account' => $account, 'response' => null);
		$log->set('content', $content);
		CRLogger::log2db($log);
		return $res;
	}


  /*
   * clear session and cookie
   */
  function signout(){
    setcookie('sid', '', time() - 42000);
    setcookie('username', '', time() - 42000);
    Session::clear();
    $res['errno'] = CRErrorCode::SUCCESS;
    return $res;
  }
  

  function user_update($user_new){
    $user = UserManager::getUserByUsername($user_new->get('username'));
    if($user == null){
      $res['errno'] = CRErrorCode::USER_NOT_EXIST;
      return $res; 
    }
    /* fill user properties */
    $user_new->set('email', $user_new->get('email', $user['email']));
    $user_new->set('pwd', $user_new->get('pwd', $user['password']));
    $user_new->set('role', $user_new->get('role', $user['role']));
    if(!validate_email($user_new->get('email'))){
      $res['errno'] = CRErrorCode::INVALID_EMAIL;
      return $res;
    }

    if($user['username']==Session::get('username'))
    { /* update self */
      if(!AccessController::hasAccess(Session::get('role'), 'user_update_self')){
        $res['errno'] = CRErrorCode::NO_PRIVILEGE;
        return $res;
      }
      if( !AccessController::hasAccess(Session::get('role'), 'user_update_teacher') //admin changes self group
        && !($user_arr['password']==UserManager::cryptPwdForStore($user_new->get('old_pwd'), $user_arr['salt'])))
      { /* need provide old password */
        $res['errno'] = CRErrorCode::WRONG_PASSWORD;
        return $res;
      }
      $user_new->set('role', $user_arr['role']); //update self role is not allowed
    }else
    {  /* update others */
      if(!AccessController::hasAccess(Session::get('role'), 'user_update_'.$user['role'])){// can update
        $res['errno'] = CRErrorCode::NO_PRIVILEGE;
        return $res;
      }
      if(!AccessController::hasAccess(Session::get('role'), 'user_add_'.$user_new->get('role', ''))){// can update role
        $res['errno'] = CRErrorCode::NO_PRIVILEGE;
        return $res;
      }
    }

    $success = UserManager::updateUser($user_new);
    if($success){
      $log = new CRObject();
      $log->set('tag', "user-".Session::get('username'));
      $log->set('content', "更新帐号信息:{$user->get('username')}");
      CRLogger::log2db($log);
      $res['errno'] = CRErrorCode::SUCCESS;
      return $res;
    }
    $res['errno'] = CRErrorCode::UNKNOWN_ERROR;
    return $res;
  }

  /*
   * check if cookie is valid, if so, log in the user
   */
  function login_from_cookie($username, $pwd)
  {
    if(!ENABLE_COOKIE){
      return false;
    }
    if(!validate_username($username)){
      return false;
    }

    $user = UserManager::getUserByUsername($username);
    if($user == null){
      signout();
      return false;
    }
    if($pwd == UserManager::cryptPwdForCookie($user['password'])){
      Session::put('username', $user['username']);
      Session::put('role', $user['role']);
      Session::put('loged', false);
      $log = new CRObject();
      $log->set('tag', "signin-{$user['username']}");
      $log->set('content', 'success(Cookie)');
      CRLogger::log2db($log);
      return true;
    }
    signout();
    return false;
  }


  function user_get_users($rule){
    if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'user_get_others')){ //access control
      $res['errno'] = CRErrorCode::NO_PRIVILEGE;
      return $res;
    }
    $res['errno'] = CRErrorCode::SUCCESS;
    $res['users'] = UserManager::getUsers($rule);
    return $res;
  }

  function user_get_signin_log($rule){
    $rule->set('tag', 'nobody');
    if(empty($rule->get('username')) || $rule->get('username')!==Session::get('username')){
      if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'get_signin_log_others')){ //access control
        $res['errno'] = CRErrorCode::NO_PRIVILEGE;
        return $res;
      }
    }else{
      if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'get_signin_log_self')){ //access control
        $res['errno'] = CRErrorCode::NO_PRIVILEGE;
        return $res;
      }
    }
    if($rule->get('username')){
      $rule->set('tag', 'signin-'.$rule->get('username', ''));
    }else{
      $rule->set('tag', '%');
      $rule->set('time_begin', mktime(0,0,0,date('m'),date('d')-6,date('Y')));
      $rule->set('limit', '-1');
    }
    $res['errno'] = CRErrorCode::SUCCESS;
    $res['logs'] = CRLogger::search($rule);
    return $res;
  }

  function user_remove($user){
    $user_arr = UserManager::getUserByUsername($user->get('username'));
    if($user_arr == null){
      $res['errno'] = CRErrorCode::USER_NOT_EXIST;
      return $res; 
    }
    if(!AccessController::hasAccess(Session::get('role'), 'user_delete_'.$user_arr['role'])){// can update
      $res['errno'] = CRErrorCode::NO_PRIVILEGE;
      return $res;
    }
    $success = UserManager::remove($user);
    if(!$success){
      $res['errno'] = CRErrorCode::UNKNOWN_ERROR;
      return $res;
    }else{
      $log = new CRObject();
      $log->set('tag', "user-".Session::get('username'));
      $log->set('content', "删除用户:{$user->get('username')}");
      CRLogger::log2db($log);
    }
    $info = new CRObject();
    $info->set('username', $user->get('username'));
    return userinfo_remove($info);
  }
