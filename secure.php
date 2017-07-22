<?php
  require_once('config.inc.php');
  require_once('util4p/util.php');
  if(BIND_SESSION_WITH_IP){
    if(!isset($_SESSION['ip'])){
      $_SESSION['ip'] = cr_get_client_ip();
    }else{
      if( cr_get_client_ip() != $_SESSION['ip'] ){
        $_SESSION = array();
      }
    }
  }
?>
