<?php
	require_once('util4p/util.php');
	require_once('predis/autoload.php');
	require_once('util4p/ReSession.class.php');
	require_once('util4p/AccessController.class.php');

	require_once('user.logic.php');
	require_once('UserManager.class.php');

	require_once('config.inc.php');
	require_once('init.inc.php');


	$action = cr_get_GET('action');
	switch($action){
		case 'get_access_token':
			$rule = new CRObject();
			$rule->set('code', cr_get_GET('code'));
			$res = get_access_token($rule);
			break;
		case 'refresh_access_token':
			$rule = new CRObject();
			$rule->set('uid', cr_get_GET('uid'));
			$rule->set('token', cr_get_GET('token'));
			$res = refresh_access_token($rule);
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
