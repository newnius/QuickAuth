<?php
  /* 0 success
   * 1 maximun deliver per ip per day exceeded
   * 2 maximun deliver per email per day exceeded
   * 3 unable to deliver, maybe email not exist, or blocked by email provider
   */
	
  require_once('email-class.php');

  function send_forget_pass_mail($username, $email, $auth_key, $ip){
    $tmp = can_send_to($email, $ip);
    if($tmp !=0 ){
      return $tmp;
    }
    
    $smtpemailto = $email; //send to whom
    $subject = 'Link to reset your QuickAuth password';//subject
    $content =  file_get_contents('templates/resetpwd_en.tpl');
    $content = str_replace('<%username%>', $username, $content);
    $content = str_replace('<%email%>', $email, $content);
    $content = str_replace('<%auth_key%>', $auth_key, $content);

    $smtp = new smtp(SMTPSERVER,SMTPSERVERPORT,true,SMTPUSER,SMTPPASS);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
    $smtp->debug = false;//是否显示发送的调试信息
    if($smtp->sendmail($smtpemailto, SMTPUSERMAIL, $subject, $content, MAILTYPE)){
      return 0;
    }else{
      return 3;
    }
  }

  function send_welcome_mail($username, $email, $auth_key, $ip){
    return 0;//time considered, change in the future
    $tmp = can_send_to($email, $ip);
    if($tmp !=0 ){
      return $tmp;
    }

    $smtpemailto = $email;//发送给谁
    $subject = 'Welcome to QuickAuth';//邮件主题
    $content = file_get_contents('templates/welcome_en.tpl');//邮件内容
    $content = str_replace('<%username%>', $username, $content);
    $content = str_replace('<%email%>', $email, $content);
    $content = str_replace('<%auth_key%>', $auth_key, $content);
  
    $smtp = new smtp(SMTPSERVER,SMTPSERVERPORT,true,SMTPUSER,SMTPPASS);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
    $smtp->debug = false;//是否显示发送的调试信息
    if($smtp->sendmail($smtpemailto, SMTPUSERMAIL, $subject, $content, MAILTYPE)){
      return 0;
    }else{
      return 3;
    }
  }

  function send_verify_mail($username, $email, $auth_key, $ip){
    $tmp = can_send_to($email, $ip);
    if($tmp !=0 ){
      return $tmp;
    }
    
    $smtpemailto = $email; //send to whom
    $subject = 'Verify your email address | QuickAuth';//subject
    $content =  file_get_contents('templates/verify_en.tpl');
    $content = str_replace('<%username%>', $username, $content);
    $content = str_replace('<%email%>', $email, $content);
    $content = str_replace('<%auth_key%>', $auth_key, $content);

    $smtp = new smtp(SMTPSERVER,SMTPSERVERPORT,true,SMTPUSER,SMTPPASS);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
    $smtp->debug = false;//是否显示发送的调试信息
    if($smtp->sendmail($smtpemailto, SMTPUSERMAIL, $subject, $content, MAILTYPE)){
      return 0;
    }else{
      return 3;
    }
  }

  function can_send_to($email, $ip){
    if(!ENABLE_EMAIL_ANTISPAM){
      return 0;
    }
    
    $sql = 'SELECT 1 FROM email_log WHERE ip = ? AND time >= ? ';
    $result = (new MysqlDAO())->executeQuery($sql, array($ip, mktime(0,0,0)), 'ii');
    $cnt = count($result);
    if($cnt >= MAXIMUM_EMAIL_PER_IP){
      return 1;//
    }

    $sql = 'SELECT 1 FROM email_log WHERE email = ? AND time >= ? ';
    $result = (new MysqlDAO())->executeQuery($sql, array($email, mktime(0,0,0)), 'si');
    $cnt = count($result);
    if($cnt >= MAXIMUM_EMAIL_PER_EMAIL){
      return 2;//
    }
    $sql = 'INSERT INTO email_log(email, time, ip) VALUES(?, ?, ?) ';
    $params = array($email, time(), $ip);
    $cnt = (new MysqlDAO())->execute($sql, $params, 'sii');
 
    return 0;
  }

  function send_undelivered_mail(){
    return 0;
  }
?>
