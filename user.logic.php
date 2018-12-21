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
require_once('email.logic.php');

require_once('config.inc.php');
require_once('init.inc.php');

/**/
function validate_username($username)
{
	if ($username === null || !is_string($username)) {
		return false;
	}
	$reserved_names = array('system', '管理员', 'admin');
	if (in_array(strtolower($username), $reserved_names)) {
		return false;
	}
	if (preg_match('/^[0-9]+$/', $username)) {//reserve for phone number
		return false;
	}
	$blacklist = array("[", "]", "@", "<", ">", "'", "\"");
	foreach ($blacklist as $s) {
		if (stripos($username, $s) !== false)
			return false;
	}
	return mb_strlen($username, 'utf8') > 0 && mb_strlen($username, 'utf8') <= 12;
}

/**/
function validate_email($email)
{
	if ($email === null || !is_string($email)) {
		return false;
	}
	if (strlen($email) > 45) {
		return false;
	}
	return Validator::isEmail($email);
}

/**/
function user_register(CRObject $user)
{
	$username = $user->get('username');
	$email = $user->get('email');
	$user->set('role', 'normal');
	if (!validate_username($username)) {
		$res['errno'] = Code::INVALID_USERNAME;
	} else if (!validate_email($email)) {
		$res['errno'] = Code::INVALID_EMAIL;
	} else if (!ALLOW_REGISTER) {
		$res['errno'] = Code::REGISTRATION_CLOSED;
	} else if (UserManager::getByUsername($username) !== null) {
		$res['errno'] = Code::USERNAME_OCCUPIED;
	} else if (UserManager::getByEmail($email) !== null) {
		$res['errno'] = Code::EMAIL_OCCUPIED;
	} else {
		$password = password_hash($user->get('password', ''), PASSWORD_DEFAULT);
		$user->set('password', $password);
		$success = UserManager::add($user);
		$res['errno'] = $success ? Code::SUCCESS : Code::UNKNOWN_ERROR;
	}
	$log = new CRObject();
	$log->set('scope', $username);
	$log->set('tag', 'user.signup');
	$content = array('username' => $username, 'email' => $email, 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function user_login(CRObject $user)
{
	$account = $user->get('account', '');// can be username, email etc.
	$password = $user->get('password', '');
	$remember_me = $user->getBool('remember_me', false);
	if (strpos($account, '@') !== false) {
		$user_arr = UserManager::getByEmail($account);
	} else {
		$user_arr = UserManager::getByUsername($account);
	}
	if ($user_arr === null) {
		$res['errno'] = Code::USER_NOT_EXIST;
	} else if ($user_arr['role'] === 'removed') { //removed
		$res['errno'] = Code::USER_IS_REMOVED;
	} else if ($user_arr['role'] === 'blocked') { //blocked
		$res['errno'] = Code::USER_IS_BLOCKED;
	} else if (!password_verify($password, $user_arr['password'])) {
		$res['errno'] = Code::WRONG_PASSWORD;
	} else if (FORCE_VERIFY && $user_arr['email_verified'] === '0') {
		$res['errno'] = Code::EMAIL_IS_NOT_VERIFIED;
	} else {
		if (!ENABLE_MULTIPLE_LOGIN) {
			Session::expireByGroup($user_arr['username']);
		}
		Session::put('username', $user_arr['username']);
		Session::put('role', $user_arr['role']);
		Session::attach($user_arr['username']);
		if (ENABLE_COOKIE && $remember_me) {
			Session::persist(604800);// 7 days
		}
		$res['errno'] = Code::SUCCESS;
	}
	$log = new CRObject();
	if ($user_arr !== null) {
		$log->set('scope', $user_arr['username']);
	} else {
		$log->set('scope', '[nobody]');
	}
	$log->set('tag', 'user.login');
	$content = array('account' => $account, 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/*
 * clear session and cookie
 */
function user_signout()
{
	Session::expire();
	$res['errno'] = Code::SUCCESS;
	return $res;
}

/**/
function user_update(CRObject $user)
{
	$user_arr = UserManager::getByUsername($user->get('username'));
	if ($user_arr === null) {
		$res['errno'] = Code::USER_NOT_EXIST;
		return $res;
	}
	if ($user->get('email') !== null && $user_arr['email'] !== $user->get('email')) {
		if (!validate_email($user->get('email'))) {
			$res['errno'] = Code::INVALID_EMAIL;
		} else if (UserManager::getByEmail($user->get('email')) !== null) {
			$res['errno'] = Code::EMAIL_OCCUPIED;
		} else {
			$user_arr['email'] = $user->get('email');
			$user_arr['email_verified'] = 0;
			verify_email($user);//expire verify_email code
		}
	}
	if ($user_arr['username'] !== Session::get('username')) {
		if ($user->get('password') !== null) {
			$user_arr['password'] = password_hash($user->get('password', ''), PASSWORD_DEFAULT);
		}
		if (AccessController::hasAccess(Session::get('role'), 'user.update_' . $user_arr['role'])// can update role
			&& AccessController::hasAccess(Session::get('role'), 'user.update_' . $user->get('role', '')))// can update to role
		{
			$user_arr['role'] = $user->get('role');
		} else {
			$res['errno'] = Code::NO_PRIVILEGE;
		}
	}
	if (!isset($res)) {
		$success = UserManager::update(new CRObject($user_arr));
		$res['errno'] = $success ? Code::SUCCESS : Code::UNKNOWN_ERROR;
	}
	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'user.update');
	$content = array(
		'username' => $user->get('username'),
		'email' => $user->get('email'),
		'role' => $user->get('role'),
		'response' => $res['errno']
	);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function user_update_pwd(CRObject $user)
{
	$user_arr = UserManager::getByUsername(Session::get('username'));
	if ($user_arr === null) {
		$res['errno'] = Code::USER_NOT_EXIST;
		return $res;
	}
	if (!password_verify($user->get('old_pwd', ''), $user_arr['password'])) { /* verify old password first */
		$res['errno'] = Code::WRONG_PASSWORD;
		return $res;
	}
	$password = password_hash($user->get('password', ''), PASSWORD_DEFAULT);
	$user_arr['password'] = $password;
	$success = UserManager::update(new CRObject($user_arr));
	$res['errno'] = $success ? Code::SUCCESS : Code::UNKNOWN_ERROR;
	$log = new CRObject();
	$log->set('scope', Session::get('username'));
	$log->set('tag', 'user.update_pwd');
	$content = array('response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function user_get(CRObject $rule)
{
	if (Session::get('username') === $rule->get('username')
		|| AccessController::hasAccess(Session::get('role', 'visitor'), 'user.get_others')) {
		$res['errno'] = Code::SUCCESS;
		$user_arr = UserManager::getByUsername($rule->get('username'));
		if ($user_arr === null) {
			$res['errno'] = Code::USER_NOT_EXIST;
		} else {
			unset($user_arr['password']);
			$res['user'] = $user_arr;
		}
		return $res;
	}
	$res['errno'] = Code::NO_PRIVILEGE;
	return $res;
}

/**/
function users_get(CRObject $rule)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'user.get_others')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$res['errno'] = Code::SUCCESS;
	$res['count'] = UserManager::getCount($rule);
	$res['users'] = UserManager::gets($rule);
	return $res;
}

/**/
function user_get_log(CRObject $rule)
{
	if ($rule->get('username') === null || $rule->get('username') !== Session::get('username')) {
		if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'log.get_others')) {
			$res['errno'] = Code::NO_PRIVILEGE;
			return $res;
		}
	} else {// view self login log
		$rule->set('scope', $rule->get('username'));
		$rule->set('tag', 'user.login');
	}
	$res['errno'] = Code::SUCCESS;
	$res['count'] = CRLogger::getCount($rule);
	$res['logs'] = CRLogger::search($rule);
	return $res;
}

/**/
function reset_pwd_send_code(CRObject $user)
{
	$user_arr = UserManager::getByUsername($user->get('username'));
	if ($user_arr === null) {
		$res['errno'] = Code::USER_NOT_EXIST;
		return $res;
	}
	if ($user_arr['email'] !== $user->get('email')) {
		$res['errno'] = Code::USERNAME_MISMATCH_EMAIL;
		return $res;
	}
	$code = Random::randomInt(100000, 999999);
	$res['errno'] = Code::SUCCESS;
	$redis = RedisDAO::instance();
	if ($redis === null) {
		$res['errno'] = Code::UNABLE_TO_CONNECT_REDIS;
		return $res;
	}
	$redis->set('resetpwd:code:' . $user_arr['username'], $code, 'EX', 300);
	$redis->disconnect();

	$email = new CRObject();
	$email->set('email', $user_arr['email']);
	$email->set('username', $user_arr['username']);
	$email->set('subject', '[QuickAuth] Reset your password');
	$content = file_get_contents('templates/resetpwd_en.tpl');
	$content = str_replace('<%username%>', $user_arr['username'], $content);
	$content = str_replace('<%email%>', $user_arr['email'], $content);
	$content = str_replace('<%auth_key%>', $code, $content);
	$content = str_replace('<%base_url%>', BASE_URL, $content);
	$email->set('content', $content);
	$res = email_send($email);

	$log = new CRObject();
	$log->set('scope', $user_arr['username']);
	$log->set('tag', 'email.send');
	$content = array('username' => $user->get('username'), 'type' => 'reset_pwd', 'email' => $user->get('email'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function verify_email_send_code(CRObject $user)
{
	$user_arr = UserManager::getByUsername($user->get('username'));
	if ($user_arr === null) {
		$res['errno'] = Code::USER_NOT_EXIST;
		return $res;
	}
	if ($user_arr['email_verified'] === '1') {
		$res['errno'] = Code::EMAIL_ALREADY_VERIFIED;
		return $res;
	}
	$code = Random::randomInt(100000, 999999);
	$redis = RedisDAO::instance();
	if ($redis === null) {
		$res['errno'] = Code::UNABLE_TO_CONNECT_REDIS;
		return $res;
	}
	$redis->set('verify:code:' . $user_arr['username'], $code, 'EX', 300);
	$redis->disconnect();

	$email = new CRObject();
	$email->set('email', $user_arr['email']);
	$email->set('username', $user_arr['username']);
	$email->set('subject', '[QuickAuth] Verify your email');
	$content = file_get_contents('templates/verify_en.tpl');
	$content = str_replace('<%username%>', $user_arr['username'], $content);
	$content = str_replace('<%email%>', $user_arr['email'], $content);
	$content = str_replace('<%auth_key%>', $code, $content);
	$content = str_replace('<%base_url%>', BASE_URL, $content);
	$email->set('content', $content);
	$res = email_send($email);

	$log = new CRObject();
	$log->set('scope', $user_arr['username']);
	$log->set('tag', 'email.send');
	$content = array('username' => $user_arr['username'], 'type' => 'verify_email', 'email' => $user_arr['email'], 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function reset_pwd(CRObject $user)
{
	$user_arr = UserManager::getByUsername($user->get('username'));
	if ($user_arr === null) {
		$res['errno'] = Code::USER_NOT_EXIST;
		return $res;
	}
	$redis = RedisDAO::instance();
	if ($redis === null) {
		$res['errno'] = Code::UNABLE_TO_CONNECT_REDIS;
		return $res;
	}
	$code = $redis->get('resetpwd:code:' . $user_arr['username']);
	$redis->del(array('resetpwd:code:' . $user_arr['username']));//expire code immediately
	$redis->disconnect();
	if ($code !== null && $code === $user->get('code')) {
		$user_arr['password'] = password_hash($user->get('password', ''), PASSWORD_DEFAULT);
		$success = UserManager::update(new CRObject($user_arr));
		$res['errno'] = $success ? Code::SUCCESS : Code::UNKNOWN_ERROR;
	} else {
		$res['errno'] = Code::CODE_EXPIRED;
	}

	$log = new CRObject();
	$log->set('scope', $user_arr['username']);
	$log->set('tag', 'user.resetpwd');
	$content = array('username' => $user_arr['username'], 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

/**/
function verify_email(CRObject $user)
{
	$user_arr = UserManager::getByUsername($user->get('username'));
	if ($user_arr === null) {
		$res['errno'] = Code::USER_NOT_EXIST;
		return $res;
	}
	$redis = RedisDAO::instance();
	if ($redis === null) {
		$res['errno'] = Code::UNABLE_TO_CONNECT_REDIS;
		return $res;
	}
	$code = $redis->get('verify:code:' . $user_arr['username']);
	$redis->del(array('verify:code:' . $user_arr['username']));
	$redis->disconnect();
	if ($code !== null && $code === $user->get('code')) {
		$user_arr['email_verified'] = 1;
		$success = UserManager::update(new CRObject($user_arr));
		$res['errno'] = $success ? Code::SUCCESS : Code::UNKNOWN_ERROR;
	} else {
		$res['errno'] = Code::CODE_EXPIRED;
	}
	$log = new CRObject();
	$log->set('scope', $user_arr['username']);
	$log->set('tag', 'user.verify_email');
	$content = array('username' => $user_arr['username'], 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}