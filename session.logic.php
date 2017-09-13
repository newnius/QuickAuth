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

	/* Manage user sessions, including list current sessions, tickout a session etc. */

	/**/
	function users_online($rule)
	{
		if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'get_online_users')){
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['users'] = Session::listGroup($rule);
		return $res;
	}


	/**/
	function user_sessions($rule)
	{
		if($rule->get('group')!==Session::get('username')){
			if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'get_online_users')){
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
				return $res;
			}
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['sessions'] = Session::listSession($rule);
		return $res;
	}


	/**/
	function tick_out($rule)
	{
		if(Session::get('username')!==$rule->get('username')){
			if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'tick_out_user')){
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
				return $res;
			}
			$user_arr = UserManager::getByUsername($rule->get('username'));
			if($user_arr===null){
				$res['errno'] = CRErrorCode::USER_NOT_EXIST;
				return $res;
			}
			if(!AccessController::hasAccess(Session::get('role', 'visitor'), "tick_out_{$user_arr['role']}")){
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
				return $res;
			}
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		Session::expireByGroup($rule->get('username'), $rule->getInt('_index'));

		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'tickout');
		$content = array('username' => $rule->get('username'), 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}
