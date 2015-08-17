<?php
  require_once('account-functions.php');
  /*
   * check if cookie is set
   */
  if(!isset($_SESSION['username']) && ENABLE_COOKIE){
    if(isset($_COOKIE['username']) && isset($_COOKIE['sid'])){
      chk_cookie($_COOKIE['username'], $_COOKIE['sid']);
    }
  }
?>
