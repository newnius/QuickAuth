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

require_once('config.inc.php');
require_once('init.inc.php');

/* Manage user sessions, including list current sessions, tickout a session etc. */

/**/
function users_online(CRObject $rule)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'session.get_online')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$res['errno'] = Code::SUCCESS;
	$res['users'] = Session::listGroup($rule);
	return $res;
}

/**/
function user_sessions(CRObject $rule)
{
	if ($rule->get('group') === null || $rule->get('group') !== Session::get('username')) {
		if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'session.get_online')) {
			$res['errno'] = Code::NO_PRIVILEGE;
			return $res;
		}
	}
	$res['errno'] = Code::SUCCESS;
	$res['sessions'] = Session::listSession($rule);
	return $res;
}

/**/
function tick_out(CRObject $rule)
{
	if ($rule->get('username') === null || Session::get('username') !== $rule->get('username')) {
		if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'session.tick_out_user')) {
			$res['errno'] = Code::NO_PRIVILEGE;
			return $res;
		}
		$user_arr = UserManager::getByUsername($rule->get('username'));
		if ($user_arr === null) {
			$res['errno'] = Code::USER_NOT_EXIST;
			return $res;
		}
		if (!AccessController::hasAccess(Session::get('role', 'visitor'), "session.tick_out_{$user_arr['role']}")) {
			$res['errno'] = Code::NO_PRIVILEGE;
			return $res;
		}
	}
	$res['errno'] = Code::SUCCESS;
	Session::expireByGroup($rule->get('username'), $rule->getInt('_index'));

	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'session.tickout');
	$content = array('username' => $rule->get('username'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}