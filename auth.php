<?php
	require_once('util4p/util.php');
	require_once('predis/autoload.php');
	require_once('util4p/ReSession.class.php');
	require_once('util4p/AccessController.class.php');

	require_once('auth.logic.php');

	require_once('config.inc.php');
	require_once('init.inc.php');


	//TODO GET to POST
	$action = cr_get_GET('action');
	$res['errno'] = CRErrorCode::UNKNOWN_REQUEST;
	switch($action){
		case 'get_token':
			$rule = new CRObject();
			$rule->set('grant_type', cr_get_GET('grant_type'));
			$rule->set('app_id', cr_get_GET('client_id'));
			$rule->set('app_key', cr_get_GET('client_secret'));
			$rule->set('code', cr_get_GET('code'));
			$rule->set('redirect_uri', cr_get_GET('redirect_uri'));
			$res = auth_get_token($rule);
			break;

		case 'refresh_token':
			$rule = new CRObject();
			$rule->set('grant_type', cr_get_GET('grant_type'));
			$rule->set('app_id', cr_get_GET('client_id'));
			$rule->set('app_key', cr_get_GET('client_secret'));
			$rule->set('token', cr_get_GET('token'));
			$res = auth_refresh_token($rule);
			break;

		case 'get_info':
			$rule = new CRObject();
			$rule->set('api_name', cr_get_GET('api_name'));
			$rule->set('app_id', cr_get_GET('client_id'));
			$rule->set('app_key', cr_get_GET('client_secret'));
			$rule->set('token', cr_get_GET('token'));
			$res = auth_get_info($rule);
			break;
	}
	var_dump($res);
/*
  $errorno = 0;
  if(isset($_GET['userid']) && isset($_GET['access_token']) && isset($_GET['url'])){
    echo finish_auth($_GET['userid'], $_GET['access_token'], $_GET['url']);
  }else{
    echo json_encode(array('errorno'=>5));
  }
*/
