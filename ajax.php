<?php

require_once('predis/autoload.php');
require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');
require_once('util4p/ReSession.class.php');
require_once('util4p/RateLimiter.class.php');

require_once('Code.class.php');
require_once('Securer.class.php');

require_once('config.inc.php');
require_once('user.logic.php');
require_once('session.logic.php');
require_once('secure.logic.php');
require_once('auth.logic.php');

function csrf_check($action)
{
	/* check referer, just in case I forget to add the method to $post_methods */
	$referer = cr_get_SERVER('HTTP_REFERER', '');
	$url = parse_url($referer);
	$host = isset($url['host']) ? $url['host'] : '';
	$host .= isset($url['port']) && $url['port'] !== 80 ? ':' . $url['port'] : '';
	if ($host !== cr_get_SERVER('HTTP_HOST')) {
		return false;
	}

	$post_methods = array(
		'login',
		'signout',
		'user_register',
		'user_update',
		'update_pwd',
		'reset_pwd_send_code',
		'reset_pwd',
		'verify_email_send_code',
		'verify_email',
		'auth_grant',
		'auth_revoke',
		'site_add',
		'site_update',
		'site_remove',
		'tick_out',
		'block',
		'unblock'
	);
	if (in_array($action, $post_methods)) {
		return Securer::validate_csrf_token();
	}
	return true;
}

function print_response($res)
{
	if (!isset($res['msg']))
		$res['msg'] = Code::getErrorMsg($res['errno']);
	$json = json_encode($res);
	header('Content-type: application/json');
	echo $json;
}

if (ENABLE_RATE_LIMIT && RateLimiter::getFreezeTime() > 0) {
	$res['errno'] = Code::TOO_FAST;
	print_response($res);
	exit(0);
}


$res['errno'] = Code::UNKNOWN_REQUEST;
$action = cr_get_GET('action');

if (!csrf_check($action)) {
	$res['errno'] = 99;
	$res['msg'] = 'invalid csrf_token';
	print_response($res);
	exit(0);
}

switch ($action) {
	/* account */
	case 'login':
		RateLimiter::increase(10);
		$user = new CRObject();
		$user->set('account', cr_get_POST('account'));
		$user->set('password', cr_get_POST('password'));
		$user->set('remember_me', cr_get_POST('remember_me', 'false') === 'true');
		$res = user_login($user);
		break;

	case 'signout':
		$res = user_signout();
		break;

	case 'users_get':
		$rule = new CRObject();
		$rule->set('search', cr_get_GET('search'));
		$rule->set('offset', cr_get_GET('offset'));
		$rule->set('limit', cr_get_GET('limit'));
		$rule->set('order', 'latest');
		$res = users_get($rule);
		break;

	case 'user_get':
		RateLimiter::increase(1);
		$rule = new CRObject();
		$rule->set('username', Session::get('username'));
		$res = user_get($rule);
		break;

	case 'user_register':
		RateLimiter::increase(20);
		$user = new CRObject();
		$user->set('username', cr_get_POST('username'));
		$user->set('email', cr_get_POST('email'));
		$user->set('password', cr_get_POST('password'));
		$res = user_register($user);
		break;

	case 'user_update':
		RateLimiter::increase(1);
		$user = new CRObject();
		$user->set('username', cr_get_POST('username', Session::get('username')));
		$user->set('email', cr_get_POST('email'));
		$user->set('password', cr_get_POST('password'));
		$user->set('role', cr_get_POST('role'));
		$res = user_update($user);
		break;

	case 'update_pwd':
		RateLimiter::increase(5);
		$user = new CRObject();
		$user->set('old_pwd', cr_get_POST('oldpwd'));
		$user->set('password', cr_get_POST('password'));
		$res = user_update_pwd($user);
		break;

	case 'get_logs':
		RateLimiter::increase(1);
		$rule = new CRObject();
		if (cr_get_GET('who') !== 'all') {
			$rule->set('username', cr_get_GET('username', Session::get('username')));
		}
		$rule->set('search', cr_get_GET('search'));
		$rule->set('offset', cr_get_GET('offset'));
		$rule->set('limit', cr_get_GET('limit'));
		$rule->set('order', 'latest');
		$res = user_get_log($rule);
		break;

	case 'reset_pwd_send_code':
		RateLimiter::increase(5);
		$user = new CRObject();
		$user->set('username', cr_get_POST('username'));
		$user->set('email', cr_get_POST('email'));
		$res = reset_pwd_send_code($user);
		break;

	case 'reset_pwd':
		RateLimiter::increase(5);
		$user = new CRObject();
		$user->set('username', cr_get_POST('username'));
		$user->set('email', cr_get_POST('email'));
		$user->set('password', cr_get_POST('password'));
		$user->set('code', cr_get_POST('code'));
		$res = reset_pwd($user);
		break;

	case 'verify_email_send_code':
		RateLimiter::increase(5);
		$user = new CRObject();
		$user->set('username', Session::get('username'));
		$res = verify_email_send_code($user);
		break;

	case 'verify_email':
		RateLimiter::increase(5);
		$user = new CRObject();
		$user->set('username', Session::get('username'));
		$user->set('code', cr_get_POST('code'));
		$res = verify_email($user);
		break;

	/* oauth */
	case 'auth_get_site':
		$rule = new CRObject();
		$rule->set('client_id', cr_get_GET('client_id'));
		$res = auth_get_site($rule);
		break;

	case 'auth_grant':
		$rule = new CRObject();
		$rule->set('response_type', cr_get_POST('response_type'));
		$rule->set('client_id', cr_get_POST('client_id'));
		$rule->set('redirect_uri', cr_get_POST('redirect_uri'));
		$rule->set('state', cr_get_POST('state'));
		$rule->set('scope', cr_get_POST('scope'));
		$res = auth_grant($rule);
		break;

	case 'auth_revoke':
		$rule = new CRObject();
		$rule->set('client_id', cr_get_POST('client_id'));
		$res = auth_revoke($rule);
		break;

	case 'auth_list':
		$rule = new CRObject();
		$res = auth_list($rule);
		break;

	case 'site_add':
		RateLimiter::increase(5);
		$site = new CRObject();
		$site->set('domain', cr_get_POST('domain'));
		$res = site_add($site);
		break;

	case 'site_remove':
		RateLimiter::increase(1);
		$site = new CRObject();
		$site->set('client_id', cr_get_POST('client_id'));
		$res = site_remove($site);
		break;

	case 'sites_get':
		RateLimiter::increase(1);
		$rule = new CRObject();
		if (cr_get_GET('who') !== 'all') {
			$rule->set('owner', cr_get_GET('owner', Session::get('username')));
		}
		$rule->set('offset', cr_get_GET('offset'));
		$rule->set('limit', cr_get_GET('limit'));
		$res = sites_get($rule);
		break;

	/* session */
	case 'users_online':
		$rule = new CRObject();
		$res = users_online($rule);
		break;

	case 'user_sessions':
		$rule = new CRObject();
		$rule->set('group', cr_get_GET('username', Session::get('username')));
		$res = user_sessions($rule);
		break;

	case 'tick_out':
		$rule = new CRObject();
		$rule->set('username', cr_get_POST('username', Session::get('username')));
		$rule->set('_index', cr_get_POST('_index'));
		$res = tick_out($rule);
		break;

	/* rate control */
	case 'list_blocked':
		$res = list_blocked();
		break;

	case 'get_blocked_time':
		$ip = cr_get_POST('ip');
		$res = get_blocked_time($ip);
		break;

	case 'block':
		$rule = new CRObject();
		$rule->set('ip', cr_get_POST('ip'));
		$rule->set('time', cr_get_POST('time'));
		$res = block($rule);
		break;

	case 'unblock':
		$rule = new CRObject();
		$rule->set('ip', cr_get_POST('ip'));
		$res = unblock($rule);
		break;

}

print_response($res);
