<?php

/* This file aims to avoid spam in many different ways such as rate limit. */
require_once('predis/autoload.php');
require_once('util4p/CRObject.class.php');
require_once('Code.class.php');
require_once('util4p/Validator.class.php');
require_once('util4p/ReSession.class.php');
require_once('util4p/CRLogger.class.php');
require_once('util4p/AccessController.class.php');
require_once('util4p/Random.class.php');

require_once('UserManager.class.php');

require_once('config.inc.php');
require_once('init.inc.php');


/* list IPs being blocked */
function list_blocked()
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'rc_list')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$res['errno'] = Code::SUCCESS;
	$res['list'] = RateLimiter::listPunished();
	return $res;
}

/**/
function get_blocked_time($ip)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'rc_list')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$res['errno'] = Code::SUCCESS;
	$res['time'] = RateLimiter::getFreezeTime($ip);
	return $res;
}

/**/
function block(CRObject $rule)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'rc_block')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$res['errno'] = Code::SUCCESS;
	$ip = $rule->get('ip');
	if ($ip === null) {
		$res['errno'] = Code::FAIL;
		return $res;
	}
	$r = array('degree' => 9999, 'interval' => $rule->getInt('time', 3600));
	RateLimiter::punish($r, $ip);
	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'block');
	$content = array('ip' => $ip, 'time' => $rule->getInt('time', 3600), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function unblock(CRObject $rule)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'rc_unblock')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	RateLimiter::clear($rule->get('ip'));
	$res['errno'] = Code::SUCCESS;
	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'unblock');
	$content = array('ip' => $rule->get('ip'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}