<?php
	require_once('util4p/util.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/Session.class.php');

	require_once('user.logic.php');
  
	require_once('config.inc.php');
	require_once('cookie.php');

	$res['errno'] = CRErrorCode::UNKNOWN_REQUEST;
	$action = cr_get_GET('action');

	switch($action){
		/* account */
		case 'login':
			$user = new CRObject();
			$user->set('account', cr_get_POST('account'));
			$user->set('password', cr_get_POST('password'));
			$user->set('remember_me', cr_get_POST('rememberme', 'false')=='true');
			$res = user_login($user);
			break;

		case 'users_get':
			$rule = new CRObject();
			$rule->set('offset', 0);
			$rule->set('limit', 20);
			$rule->set('order', 'latest');
			$res = users_get($rule);
			break;

		case 'user_get':
			$rule = new CRObject();
			$rule->set('username', cr_get_GET('username', Session::get('username')));
			$res = user_get($rule);
			break;

		case 'user_register':
			$user = new CRObject();
			$user->set('username', cr_get_POST('username'));
			$user->set('email', cr_get_POST('email'));
			$user->set('password', cr_get_POST('password'));
			$res = user_register($user);
			break;

		case 'user_update':
			$user = new CRObject();
			$user->set('username', cr_get_POST('username', Session::get('username')));
			$user->set('email', cr_get_POST('email'));
			$user->set('old_pwd', cr_get_POST('oldpwd'));
			$user->set('password', cr_get_POST('password'));
			$user->set('role', cr_get_POST('role'));
			$res = user_update($user);
			break;

		case 'user_remove':
			$user = new CRObject();
			$user->set('username', cr_get_POST('username'));
			$res = user_remove($user);
			break;

		case 'get_log':
			$rule = new CRObject();
			if(cr_get_GET('scope')=='self'){
				$rule->set('username', cr_get_GET('username', Session::get('username')));
			}
			$rule->set('offset', 0);
			$rule->set('limit', 20);
			$rule->set('order', 'latest');
			$res = user_get_log($rule);
			break;

		case 'reset_pwd_send_code':
			$user = new CRObject();
			$user->set('username', cr_get_POST('username'));
			$user->set('email', cr_get_POST('email'));
			$res = reset_pwd_send_code($user);
			break;

		case 'reset_pwd':
			$user = new CRObject();
			$user->set('username', cr_get_POST('username'));
			$user->set('email', cr_get_POST('email'));
			$user->set('password', cr_get_POST('password'));
			$user->set('code', cr_get_POST('code'));
			$res = reset_pwd($user);
			break;

		case 'verify_email_send_code':
			$user = new CRObject();
			$user->set('username', Session::get('username'));
			$res = verify_email_send_code($user);
			break;

		case 'verify_email':
			$user = new CRObject();
			$user->set('username', cr_get_POST('username'));
			$user->set('email', cr_get_POST('email'));
			$user->set('code', cr_get_POST('code'));
			$res = reset_pwd($user);
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
