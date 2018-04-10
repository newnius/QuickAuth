<?php
	require_once('global.inc.php');
	require_once('auth.logic.php');


	$action = cr_get_GET('action');
	$res['errno'] = CRErrorCode::UNKNOWN_REQUEST;
	switch($action){
		case 'get_token':
			$rule = new CRObject();
			$rule->set('grant_type', cr_get_POST('grant_type'));
			$rule->set('app_id', cr_get_POST('client_id'));
			$rule->set('app_key', cr_get_POST('client_secret'));
			$rule->set('code', cr_get_POST('code'));
			$rule->set('redirect_uri', cr_get_POST('redirect_uri'));
			$res = auth_get_token($rule);
			break;

		case 'refresh_token':
			$rule = new CRObject();
			$rule->set('grant_type', cr_get_POST('grant_type'));
			$rule->set('app_id', cr_get_POST('client_id'));
			$rule->set('app_key', cr_get_POST('client_secret'));
			$rule->set('token', cr_get_POST('token'));
			$res = auth_refresh_token($rule);
			break;

		case 'get_info':
			$rule = new CRObject();
			$rule->set('api_name', cr_get_POST('api_name'));
			$rule->set('app_id', cr_get_POST('client_id'));
			$rule->set('app_key', cr_get_POST('client_secret'));
			$rule->set('token', cr_get_POST('token'));
			$res = auth_get_info($rule);
			break;
	}

	if(!isset($res['msg']))
		$res['msg'] = CRErrorCode::getErrorMsg($res['errno']);
	$json = json_encode($res);
	header('Content-type: application/json');
	echo $json;
