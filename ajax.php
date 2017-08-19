<?php
	require_once('predis/autoload.php');
	require_once('util4p/util.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/ReSession.class.php');
	require_once('util4p/RateLimiter.class.php');

	require_once('user.logic.php');
	require_once('site.logic.php');

	require_once('config.inc.php');
	require_once('cookie.php');

	$res['errno'] = CRErrorCode::UNKNOWN_REQUEST;
	
	$action = cr_get_GET('action');

	switch($action){
		/* account */
		case 'login':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$user = new CRObject();
			$user->set('account', cr_get_POST('account'));
			$user->set('password', cr_get_POST('password'));
			$user->set('remember_me', cr_get_POST('rememberme', 'false')==='true');
			$res = user_login($user);
			break;

		case 'users_get':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$rule = new CRObject();
			$rule->set('search', cr_get_GET('search'));
			$rule->set('offset', cr_get_GET('offset'));
			$rule->set('limit', cr_get_GET('limit'));
			$rule->set('order', 'latest');
			$res = users_get($rule);
			break;

		case 'user_get':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$rule = new CRObject();
			$rule->set('username', Session::get('username'));
			$res = user_get($rule);
			break;

		case 'user_register':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$user = new CRObject();
			$user->set('username', cr_get_POST('username'));
			$user->set('email', cr_get_POST('email'));
			$user->set('password', cr_get_POST('password'));
			$res = user_register($user);
			break;

		case 'user_update':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$user = new CRObject();
			$user->set('username', cr_get_POST('username'));
			$user->set('email', cr_get_POST('email'));
			$user->set('password', cr_get_POST('password'));
			$user->set('role', cr_get_POST('role'));
			$res = user_update($user);
			break;

		case 'update_pwd':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$user = new CRObject();
			$user->set('username', Session::get('username'));
			$user->set('old_pwd', cr_get_POST('oldpwd'));
			$user->set('password', cr_get_POST('password'));
			$res = user_update_pwd($user);
			break;

		case 'get_logs':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$rule = new CRObject();
			if(cr_get_GET('who')!=='all'){
				$rule->set('username', cr_get_GET('username', Session::get('username')));
			}
			$rule->set('search', cr_get_GET('search'));
			$rule->set('offset', cr_get_GET('offset'));
			$rule->set('limit', cr_get_GET('limit'));
			$rule->set('order', 'latest');
			$res = user_get_log($rule);
			break;

		case 'reset_pwd_send_code':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$user = new CRObject();
			$user->set('username', cr_get_POST('username'));
			$user->set('email', cr_get_POST('email'));
			$res = reset_pwd_send_code($user);
			break;

		case 'reset_pwd':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$user = new CRObject();
			$user->set('username', cr_get_POST('username'));
			$user->set('email', cr_get_POST('email'));
			$user->set('password', cr_get_POST('password'));
			$user->set('code', cr_get_POST('code'));
			$res = reset_pwd($user);
			break;

		case 'verify_email_send_code':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$user = new CRObject();
			$user->set('username', Session::get('username'));
			$res = verify_email_send_code($user);
			break;

		/* oauth */
		case 'auth_grant':
			break;

		case 'auth_revoke':
			break;

		case 'auth_list':
			break;

		case 'site_add':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$site = new CRObject();
			$site->set('domain', cr_get_POST('domain'));
			$site->set('revoke_url', cr_get_POST('revoke_url'));
			$site->set('level', cr_get_POST('level'));
			$res = site_add($site);
			break;

		case 'site_update':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$site = new CRObject();
			$site->set('id', cr_get_POST('id'));
			$site->set('domain', cr_get_POST('domain'));
			$site->set('revoke_url', cr_get_POST('revoke_url'));
			$site->set('level', cr_get_POST('level'));
			$res = site_update($site);
			break;

		case 'site_remove':
			break;

		case 'sites_get':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$rule = new CRObject();
			$rule->set('offset', cr_get_GET('offset'));
			$rule->set('limit', cr_get_GET('limit'));
			$res = sites_get($rule);
			break;

		/* session */
		case 'users_online':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$rule = new CRObject();
			$res = users_online($rule);
			break;

		case 'tick_out':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$rule = new CRObject();
			$rule->set('username', cr_get_POST('username'));
			$res = tick_out($rule);
			break;

		/* rate control */
		case 'block':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$rule = new CRObject();
			$rule->set('username', cr_get_POST('username'));
			$rule->set('duration', cr_get_POST('duration'));
			$res = block($rule);
			break;

		case 'unblock':
			if(RateLimiter::getFreezeTime()>0){
				$res['errno'] = CRErrorCode::TOO_FAST;
				break;
			}
			RateLimiter::increase(1);
			$rule = new CRObject();
			$rule->set('username', cr_get_POST('username'));
			$res = unblock($rule);
			break;
	}

	if(!isset($res['msg']))
		$res['msg'] = CRErrorCode::getErrorMsg($res['errno']);
	$json = json_encode($res);
//	if(isset($_GET['callback']))
	//	$json = $_GET['callback'].'('.$json.')';
//else
	//$json = 'Void('.$json.')';
	echo $json;
