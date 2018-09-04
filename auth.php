<?php

require_once('global.inc.php');
require_once('auth.logic.php');

/*
 *
 *
 * GET /auth?response_type=code&client_id=1&redirect_uri=http://127.0.0.1/test.php&state=ssr&scope=email,email_verified,role
 *
 * /redirect?code=&state=&scope=
 *
 * POST /api?action=get_token  array(grant_type=authorization_code&app_id=&app_key=&code=&redirect_uri=)
 *
 * POST /api?action=get_token array(grant_type=refresh_token&client_id=&client_secret=&token=)
 *
 * POST /api?action=get_info array(api_name=basic&client_id=&client_secret=&token=)
 *
 * */

$action = cr_get_GET('action');
$res['errno'] = Code::UNKNOWN_REQUEST;
switch ($action) {
	case 'get_token':
		$rule = new CRObject();
		$rule->set('grant_type', cr_get_POST('grant_type'));
		$rule->set('client_id', cr_get_POST('client_id'));
		$rule->set('client_secret', cr_get_POST('client_secret'));
		$rule->set('code', cr_get_POST('code'));
		$rule->set('redirect_uri', cr_get_POST('redirect_uri'));
		$res = auth_get_token($rule);
		break;

	case 'refresh_token':
		$rule = new CRObject();
		$rule->set('grant_type', cr_get_POST('grant_type'));
		$rule->set('client_id', cr_get_POST('client_id'));
		$rule->set('client_secret', cr_get_POST('client_secret'));
		$rule->set('token', cr_get_POST('token'));
		$res = auth_refresh_token($rule);
		break;

	case 'get_info':
		$rule = new CRObject();
		$rule->set('api_name', cr_get_POST('api_name'));
		$rule->set('client_id', cr_get_POST('client_id'));
		$rule->set('client_secret', cr_get_POST('client_secret'));
		$rule->set('token', cr_get_POST('token'));
		$res = auth_get_info($rule);
		break;
}

if (!isset($res['msg']))
	$res['msg'] = Code::getErrorMsg($res['errno']);
$json = json_encode($res);
header('Content-type: application/json');
echo $json;