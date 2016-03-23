<?php
  session_start();
  if(file_exists('qa-config.php')){
    require_once('qa-config.php');
  }else{
    die('config file not exist !');
  }

  if(file_exists('account-functions.php')){
    require_once('account-functions.php');
  }else{
    die('functions(account) file not exist !');
  }

  $action='';
  if(isset($_GET['action'])){
    $action=$_GET['action'];
  }
  switch($action){
    case 'login':
      if(isset($_POST['account']) && isset($_POST['password']))
      {
        $account=$_POST['account'];
        $pwd=$_POST['password'];
        $remember_me = false;
        if(isset($_POST['rememberme']) && $_POST['rememberme'] == 'true'){
          $remember_me = true;
        }
        
        if(strpos($account,'@')===false){
          echo loginByName($account, $pwd, $remember_me);
        }else{
          echo loginByEmail($account, $pwd, $remember_me);
        }
      }else{
        echo 'Invalid request';
      }
      break;


      /*
      case 'loginByName':
        if(isset($_POST['username']) && isset($_POST['password']))
        {
          $username = $_POST['username'];
          $pwd = $_POST['password'];
          echo loginByName($username,$pwd);
        }else{
          echo 'Invalid request';
        }
        break;


      case 'loginByEmail':
        if(isset($_POST['email']) && isset($_POST['password']))
        {
          $email = $_POST['email'];
          $pwd = $_POST['password'];
          echo loginByEmail($email,$pwd);
        }else{
          echo 'Invalid request';
        }
        break;
      */


      case 'reg':
        if(!isset($_POST['username'])){
          exit('Invalid requset');
        }
        if(!isset($_POST['email'])){
          exit('Invalid request');
        }
        if(!isset($_POST['password'])){
          exit('Invalid request');
        }
        if(strpos($_POST['username'],'@') === true){
          exit('@ can not be included in username');
        }
        echo reg($_POST['username'], $_POST['email'], $_POST['password']);
        break;


      case 'isNameReged':
        if(isset($_GET['username'])){
          if(strpos($_GET['username'],'@')===true){
            echo 'true';
            exit; 
          }
          if(is_name_reged($_GET['username'])){
            echo 'true';
          }else{
            echo 'false';
          }
        }else{
          echo 'Invalid request';
        }
        break;	


      case 'isEmailReged':
        if(isset($_GET['email'])){
          if(is_email_reged($_GET['email'])){
            echo 'true';
          }else{
            echo 'false';
          }
        }else{
          echo 'Invalid request';
        }
        break;


      case 'lostpass':
        if(isset($_POST['username']) && isset($_POST['email'])){
          echo forget_password($_POST['username'], $_POST['email']);
        }else{
          echo 'Invalid request';
        }
        break;


      case 'changePwd':
        if(!isset($_SESSION['username'])){
          echo 'You are offline';
	  exit;
	}
        if(isset($_POST['oldpwd']) && isset($_POST['newpwd']))
        {
          echo change_pwd($_SESSION['username'], $_POST['oldpwd'], $_POST['newpwd']);
        }else{
          echo 'Invalid request';
        }
        break;

      /*
      case 'logout':
        unset($_SESSION['username']);
        echo 'true';
      break;
      */

      case 'verifyon':
        if(isset($_SESSION['username'])){
          echo verify_online($_SESSION['username']);
        }else{
          echo 'You are offline';
        }
        break;


      case 'resetpwd':
        if(isset($_POST['auth_key']) && isset($_POST['username']) && isset($_POST['password'])){
          echo reset_pwd($_POST['username'], $_POST['auth_key'], $_POST['password']);
        }else{
          exit('Invalid request');
        }
        break;


      case 'verify':
        if(isset($_POST['auth_key']) && isset($_POST['username'])){
          echo verify($_POST['username'], $_POST['auth_key']);
        }else{
          exit('Invalid request');
        }
        break;


      default:echo 'Invalid request';
  
  }

?>
