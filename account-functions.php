<?php
  if(file_exists('email-functions.php')){
    require_once('email-functions.php');
  }else{
    die('Email functions file not exist !');
  }
	
  if(file_exists('dao-mysql.php')){
    require_once('dao-mysql.php');
  }else{
    die('Mysql dao file not exist !');
  }
  
  /*
   * get client side ip
   */
  function get_ip(){ 
	$ip=false; 
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){ 
		$ip=$_SERVER['HTTP_CLIENT_IP']; 
	}
	if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){ 
		$ips=explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']); 
		if($ip){ array_unshift($ips, $ip); $ip=FALSE; }
		for ($i=0; $i < count($ips); $i++){
			if(!preg_match ('/^(10│172.16│192.168)./i', $ips[$i])){
				$ip=$ips[$i];
				break;
			}
		}
	}
	return ($ip ? $ip : $_SERVER['REMOTE_ADDR']); 
}

  function is_ip($str){
    $ip=explode('.',$str);
    for($i=0;$i<count($ip);$i++){  
      if($ip[$i]>255){  
        return false;  
      }  
    }  
    return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$str);  
  } 

  /*
   * generate random string of length $length
   */
  function rand_string($length = 32)
  {
    $dictionary='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-*/?!%`~@#^&(){}';
    $str='';
    for($i=0;$i<$length;$i++){
      $str.=$dictionary[rand(0,strlen($dictionary)-1)];
    }
    return $str;
  }
 

  /*
   * process pwd submitted from client to store in server
   */
  function crypt_pwd($pwd , $salt)
  {
    $salt = hash('sha256',$salt);
    $data = hash('sha256',$pwd.$salt);
    $data = hash('sha256',$data);
    $data = mb_substr($data,0,50,'utf-8');
    $data = hash('sha256',$data);
    return $data;
  }
  

 /*
  * process server pwd to be stored in cookie
  * be available no more than 1 month
  */
  function crypt_pwd_client($pwd)
  {
    $this_month = mktime(0, 0, 0, date('n'),0);
    $pwd = hash('sha256', $pwd);
    $pwd = hash('sha256', $this_month.$pwd);
    $pwd = mb_substr($pwd, 0, 32, 'utf-8');
    return $pwd;
  }


  /*
   * process auth_key to auth reset pwd or 3rd site
   * available no more than 1 day, unavavilable when loged, or used
   */
  function process_auth_key($auth_key, $last_time, $url = 'account.newnius.com')
  {
    $auth_key = md5($auth_key.$last_time);
    $today = mktime(0, 0, 0);
    $auth_key = md5($today.$auth_key);
    $auth_key = hash('sha256', $url.$auth_key);
    $auth_key = md5($auth_key);
    return $auth_key;
  }
  

  /*
   * store errors which will "impossible" happen, 
   * for example a log process can not be executed
   */
  function error_report($msg ){
    //store in db or file
    return ;
  }

	
  /*
   * search whether username is occupied
   */
  function is_name_reged($username)
  {
    $sql="SELECT 1 FROM `account` WHERE username= ? LIMIT 1";
    $params = array($username);
    $param_types = 's';
    $result = (new MysqlDAO())->executeQuery($sql, $params, $param_types);
    $count = count($result);
    return $count > 0;
  }

	
  /*
   * search whether email is occupied
   */
  function is_email_reged($email)
  {
    $sql="SELECT 1 FROM `account` WHERE email= ? LIMIT 1";
    $params = array($email);
    $param_types = 's';
    $result = (new MysqlDAO())->executeQuery($sql, $params, $param_types);
    $count = count($result);
    return $count>0;
  }
 

  /*
   * do signup process
   */
  function Reg($username, $email, $pwd)
  {
    if(!ENABLE_REGISTER){
      return 'Registeration closed';
    }
    if(mb_strlen($username, 'utf8') < 1 || mb_strlen($username, 'utf8') > 12){
      return 'Username length should <= 12';
    }
    if(strlen($pwd)!=32){
      return 'Invalid password';
    }
    if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $email)){ 
      return 'Unacceptable email';
    }
		
    if(!is_name_reged($username) && !is_email_reged($email))
    {
      $salt = rand_string(64);
      $pwd=crypt_pwd($pwd, $salt);
      $auth_key = rand_string();
      $reg_time = time();
      $reg_ip = ip2long(get_ip());
      $sql = "INSERT INTO `account`(`username`, `email`,`password`,`auth_key`, `salt`, `reg_time`, `reg_ip`) VALUES ( ?, ?, ?, ?, ?, ?, ?)";
      $params = array($username, $email, $pwd, $auth_key, $salt, $reg_time, $reg_ip);
      $param_types = 'sssssii';
      $count = (new MysqlDAO())->execute($sql, $params, $param_types);
      if($count == 0){
        return 'Unable to signup (errorno:1001)';
      }else if($count==1){
        send_welcome_mail($username, $email, '', $reg_ip);
        return '1';
      }else{
        return 'Unable to signup, sth is wrong with server';
      }
    }else{
      return 'Username or email have been occupied';
    }
  }
	
	
  function loginByName($username, $pwd, $remember_me=false)
  {
    if(mb_strlen($username, 'utf8') < 1 || mb_strlen($username, 'utf8') > 12){
      return 'User not exist';
    }
    
    if(mb_strlen($pwd, 'utf8') != 32){
      return 'Invalid password, please refresh your browser';
    }

    $sql = "SELECT username, password, salt, verified FROM `account` WHERE username= ? LIMIT 1";
    $result = (new MysqlDAO())->executeQuery($sql, array($username),'s');
    if(count($result) != 1){
      add_signin_log($username, 'f', time(), ip2long(get_ip()));
      return 'User not exist';
    }
    if($result[0]['verified'] == 'b'){
      add_signin_log($username, 'b', time(), ip2long(get_ip()));
      return 'Your account is blocked';
    }

    if($result[0]['password'] != crypt_pwd($pwd, $result[0]['salt'])){
      add_signin_log($username, 'f', time(), ip2long(get_ip()));
      return 'Wrong password';
    }
    
    $_SESSION['username'] = $username;
    $_SESSION['loged'] = true;
    if(ENABLE_COOKIE && $remember_me){
      setcookie('username', $result[0]['username'], time() + 604800);// 7 days
      setcookie('sid', crypt_pwd_client($result[0]['password']), time()+604800);//7 days
    }

    $last_time = time();
    $last_ip = ip2long(get_ip());
    $sql = "UPDATE `account` SET `last_time`= ?, `last_ip`=? WHERE username=? LIMIT 1";
    $params = array($last_time, $last_ip, $username);
    $cnt = (new MysqlDAO())->execute($sql, $params, 'iis');	

    add_signin_log($username, 't', $last_time, $last_ip);
    return '1';
  }
	

  function loginByEmail($email, $pwd, $remember_me = false)
  {
    if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$email)){ 
      return 'User not exist';
    }
    if(mb_strlen($pwd, 'utf8') != 32){
      return 'Invalid password, please refresh your browser';
    }
		
    $sql = "SELECT username, password, salt, verified FROM `account` WHERE email = ? LIMIT 1";
    $result = (new MysqlDAO())->executeQuery($sql,array($email),'s');
    $last_time = time();
    $last_ip = ip2long(get_ip());
    if(count($result)!=1){
      add_signin_log($email, 'f', $last_time, $last_ip);
      return 'User not exist';
    }
    if($result[0]['verified'] == 'b'){
      add_signin_log($username, 'b', $last_time, $last_ip);
      return 'Your account is blocked';
    }

    if($result[0]['password'] != crypt_pwd($pwd, $result[0]['salt'])){
      add_signin_log($email, 'f', $last_time, $last_ip);
      return 'Wrong password';
    }

    $_SESSION['username'] = $result[0]['username'];
    $_SESSION['loged'] = true;// sign in by password or session

    if(ENABLE_COOKIE && $remember_me){
      setcookie('username', $result[0]['username'], time() + 604800);// 7 days
      setcookie('sid', crypt_pwd_client($result[0]['password']), time()+604800);//7 days
    }

    $sql="UPDATE `account` SET `last_time`= ?, `last_ip`=? WHERE email=? LIMIT 1";
    $params = array($last_time, $last_ip, $email);
    $cnt = (new MysqlDAO())->execute($sql, $params, 'iis');	
    add_signin_log($email, 't', $last_time, $last_ip);
    return '1';
  }
  
  /*
   * clear session and cookie
   */
  function signout(){
    $_SESSION=array();
    setcookie('sid', '', time() - 42000);
    setcookie('username', '', time() - 42000);
    session_destroy();  
  }


  /*
   * send reset password email
   */
  function forget_password($username, $email)
  {
    if(mb_strlen($username, 'utf8') < 1 || mb_strlen($username, 'utf8') > 12){
      return 'User not exist';
    }
    if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $email)){ 
      return 'User not exist';
    }
    $sql="SELECT username, verified, auth_key, last_time FROM `account` WHERE email=? AND username =? LIMIT 1";
    $params = array($email, $username);
    $result = (new MysqlDAO())->executeQuery($sql,$params,'ss');
    if(count($result)!=1){
      return 'User not exist';
    }
    if($result[0]['verified'] == 'b'){
      return 'Your account is blocked';
    }
    if($result[0]['verified'] == 'f'){
      return 'Email is not verified';
    }

    $auth_key = $result[0]['auth_key'];
    $last_time = $result[0]['last_time'];
    $ip = ip2long(get_ip());
    $auth_key = process_auth_key($auth_key, $last_time);
    $res = send_forget_pass_mail($username, $email, $auth_key, $ip);
    return $res;
  } 
     

  /*
   * verify email while user is online
   */
  function verify_online($username )
  {
    $result = get_user_information($username);
    if($result == null){
      return 'User not exist';
    }
    $auth_key = $result['auth_key'];
    $last_time = $result['last_time'];
    $auth_key = process_auth_key($auth_key, $last_time);
    $res = send_verify_mail($username, $result['email'], $auth_key, ip2long(get_ip()));
    return $res;
  }


  /*
   * change password
   */
  function change_pwd($username, $old_pwd, $new_pwd)
  {
    if(mb_strlen($username, 'utf-8') < 1 || mb_strlen($username, 'utf-8') > 12){
      return 'User not exist';
    }
    if(strlen($new_pwd) != 32 || strlen($old_pwd) != 32){
      return 'Invalid password, refresh your browser.';
    }

    $result = get_user_information($username);
    if($result == null){
      return 'User not exist';
    }

    if(crypt_pwd($old_pwd, $result['salt']) != $result['password']){
      return 'Wrong password';
    }

    $new_salt = rand_string(64);
    $new_pwd=crypt_pwd($new_pwd, $new_salt);
    $new_auth_key=rand_string();

    $sql="UPDATE `account` SET `auth_key`= ?, `password`=?, `salt`=? WHERE username= ? LIMIT 1";
    $params = array($new_auth_key, $new_pwd, $new_salt, $username);
    $count = (new MysqlDAO())->execute($sql, $params, 'ssss');
    if($count == 1){
      unset($_SESSION['username']);
      unset($_SESSION['loged']);
      return '1';
    }else{
      echo 'Sth is wrong with server';
    }
  }


  /*
   */
  function get_user_information( $username )
  {
    if(mb_strlen($username, 'utf-8') < 1 || mb_strlen($username, 'utf-8') > 12){
      return null;
    }
		
    $sql="SELECT * FROM `account` WHERE username=? LIMIT 1";
    $result = (new MysqlDAO())->executeQuery($sql, array($username),'s');
    if(count($result) != 1){
      return null;
    }
    return $result[0];
  }
	

  /*
   * get signin log
   */
  function get_log_by_username( $username )
  {
    $result = get_user_information($username);
    if($result == null ){
      return null;
    }
    $sql = 'SELECT * FROM `signin_log` WHERE `account` = ? OR `account` = ? ORDER BY `log_id` DESC LIMIT 20';
    $params = array($result['username'], $result['email']);
    /* the following line aims to make php happy
     * when the table has few rows, db may choose to scan the whole table,which is fater
     *while php force us to use index
     */
    mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
    $result = (new MysqlDAO())->executeQuery($sql, $params, 'ss');
    return $result;
  }


  /*
   * reset password by auth_key
   */
  function reset_pwd($username, $auth_key, $new_pwd)
  {
    if(mb_strlen($username, 'utf8') < 1 || mb_strlen($username, 'utf8') > 12){
      return 'User not exist';
    }
    if(strlen($new_pwd) !=32 ){
      return 'Invalid password, please refresh your browser';
    }
    if(strlen($auth_key) !=32 ){
      return 'Invalid auth key';
    }
		
    $result = get_user_information($username);
    if($result == null){
      return 'User not exist';
    }
    if(process_auth_key($result['auth_key'], $result['last_time']) != $auth_key){
      return 'Link is out of data';
    }

    $new_salt = rand_string(64);
    $new_pwd = crypt_pwd($new_pwd, $new_salt);
    $new_auth_key = rand_string();
		
    $sql="UPDATE `account` SET `auth_key`= ?, `password`=?, `salt`=? WHERE username= ? LIMIT 1";
    $params = array($new_auth_key, $new_pwd, $new_salt, $username);
    $count = (new MysqlDAO())->execute($sql, $params, 'ssss');
    if($count == 1){
      return '1';
    }else{
      return 'Sth is wrong with server';
    }
  }


  /*
   * verify by auth_key and username
   */
  function verify($username, $auth_key)
  {
    if(mb_strlen($username,'utf-8') < 1 || mb_strlen($username, 'utf-8') > 12){
      return 'User not exist';
    }
    if(strlen($auth_key) !=32 ){
      return 'Invalid auth key';
    }
		
    $result = get_user_information($username);
    if($result == null){
      return 'User not exist';
    }
    if(process_auth_key($result['auth_key'], $result['last_time']) != $auth_key){
      return 'Link is out of data';
    }

    $new_auth_key=rand_string();
    $sql="UPDATE `account` SET `auth_key`= ?, verified='t' WHERE username= ? LIMIT 1";
    $params = array($new_auth_key, $username);
    $count = (new MysqlDAO())->execute($sql, $params, 'ss');
    if($count == 1){
      return '1';
    }else{
      return 'Sth is wrong with server';
    }
  }


  /*
   * check if cookie is valid, if so, log in the user
   */
  function chk_cookie($username, $pwd)
  {
    if(!ENABLE_COOKIE){
      return;
    }
    if(mb_strlen($username,'utf8')<1 || mb_strlen($username,'utf8')>12){
      return;
    }

    $result = get_user_information($username);
    if($result == null){
      setcookie('username', '', time() - 1);
      setcookie('sid', '', time() - 1);
      return;
    }else{
      if(crypt_pwd_client($result['password']) == $pwd){
        $_SESSION['username'] = $result['username'];
        $_SESSION['loged'] = false;
        return ;
      }else{
        setcookie('username', '', time() - 1);
        setcookie('sid', '', time() - 1);
        return;
      }
      return ;
    }
  }



  /*
   * track signin infoformation
   */
  function add_signin_log($account, $accepted, $time, $ip)
  {
    $sql = 'INSERT INTO `signin_log`(`account`, `accepted`, `time`, `ip`)    VALUES(?, ?, ?, ?)';
    $params = array($account, $accepted, $time, $ip);
    $cnt = (new MysqlDAO())->execute($sql, $params, 'ssii');
    if($cnt != 1){
      error_report('store sign in log error');
    }
    return ;
  }
	
?>
