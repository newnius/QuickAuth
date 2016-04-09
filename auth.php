<?php
  require_once('qa-config.php');
  require_once('account-functions.php');
  require_once('auth-functions.php');
  $errorno = 0;
  if(isset($_GET['userid']) && isset($_GET['access_token']) && isset($_GET['url'])){
    echo finish_auth($_GET['userid'], $_GET['access_token'], $_GET['url']);
  }else{
    echo json_encode(array('errorno'=>5));
  }


?>
