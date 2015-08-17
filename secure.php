<?php
  if(BIND_SESSION_WITH_IP){
    if(!isset($_SESSION['ip'])){
      $_SESSION['ip'] = time();
    }else{
      $_SESSION = array();
    }
  }
?>
