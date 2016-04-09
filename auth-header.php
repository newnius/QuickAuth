<?php
   //session_start();
   require_once('account-functions.php');

   if(!isset($_SESSION['redirect'])){
     $_SESSION['redirect'] = '';
   }
   if($_SESSION['redirect'] != '' && isset($_SESSION['username'])){
     header('location:auth-grant.php');
     exit;
   }
   if(isset($_GET['redirect'])){
     $_SESSION['redirect'] = $_GET['redirect'];
   }
    
   
  /*
   * return client auth key
   */
  function start_auth($username, $url)
  {
    $result = get_user_information($username);
    if($result == null){
      return '(null)';
    }
    $auth_key = $result['auth_key'];
    $last_time = $result['last_time'];
    $auth_key = process_auth_key($auth_key, $last_time, $url);
    return $auth_key;
  }
    
?>
