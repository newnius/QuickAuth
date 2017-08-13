<?php
	require_once('util4p/CRObject.class.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/Validator.class.php');
	require_once('util4p/CRLogger.class.php');
	require_once('util4p/AccessController.class.php');
	require_once('util4p/Random.class.php');
	require_once('SiteManager.class.php');

	require_once('config.inc.php');
	require_once('init.inc.php');

	/**/
	function site_add($site)
	{
		$site->set('owner', Session::get('username'));
		$site->set('key', Random::randomString(64));
		$success = SiteManager::add($site);
		$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;

		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'site_add');
		$content = array('domain' => $site->get('domain'), 'revoke_url' => $site->get('revoke_url'), 'level' => $site->getInt('level'));
		$log->set('content', json_encode($site));
		CRLogger::log2db($log);
		return $res;
	}


	/**/
	function site_update($site)
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
	function sites_get($rule)
	{
		if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'sites_get_all')){
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['count'] = SiteManager::getCount($rule);
		$res['sites'] = SiteManager::gets($rule);
		return $res;
	}
