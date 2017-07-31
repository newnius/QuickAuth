<?php 
  require_once('util4p/util.php');
  require_once('util4p/Session.class.php');
  require_once('config.inc.php');
  require_once('init.inc.php');
  require_once('secure.php');
  require_once('cookie.php');

	require_once('user.logic.php');

  if(Session::get('username')===null){
    header("location:login.php?callback=verify.php?code={$code}");
    exit;
  }

	$code = cr_get_GET('code', '');

	$user = new CRObject();
	$user->set('username', Session::get('username'));
	$user->set('code', $code);
	$res = verify_email($user);
	if($res['errno']===0){
    header('ucenter.php');
	}else{
    header("location:login.php?callback=verify.php?code={$code}");
	}
?>
