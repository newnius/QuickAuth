<?php

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


  /*
   * chk auth key and retur info
   */
  function finish_auth($username, $auth_key, $url)
  { 
    // not a good design
    if(mb_strlen($auth_key,'utf-8') != 32){
      return json_encode(array('errorno'=>1));
    }

    $result = get_user_information($username);
    if($result == null){
      return json_encode(array('errorno'=>2));
    }
		
    if(process_auth_key($result['auth_key'], $result['last_time'], $url) != $auth_key){
      return json_encode(array('errorno'=>3));
    }
    
    // not good design +1
    $new_auth_key = rand_string();
    $sql = "UPDATE `account` SET `auth_key`= ? WHERE username= ? LIMIT 1";
    $params = array($new_auth_key, $username);
    $count = (new MysqlDAO())->execute($sql,$params,'ss');

    $username = $result['username'];
    $email = $result['email'];
    $verified = $result['verified'];
    $reg_time = $result['reg_time'];
    $res = array('errorno'=>0, 'user'=>array('username'=>$username, 'email'=>$email, 'verified'=>$verified, 'reg_time'=>$reg_time));
    return json_encode($res);
  }
?>
