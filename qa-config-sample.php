<?php

  // database  information 
  define('DB_HOST', 'localhost');
  define('DB_PORT', 3307);
  define('DB_NAME', 'account');
  define('DB_USER', 'root');
  define('DB_PASSWORD', '123456');

  //set to forbidden direct access to some function files
  define('IN_QA', true);

  //make absolute url for SEO and avoid hijack
  define('DOMAIN', 'http://localhost/newnius.github.io');//网站地址
  
  //email service information
  define('SMTPSERVER', '');//SMTP服务器
  define('SMTPSERVERPORT', 25);//SMTP服务器端口
  define('SMTPUSERMAIL', '');//SMTP服务器的用户邮箱
  define('SMTPUSER', '');//SMTP服务器的用户帐号
  define('SMTPPASS', '');//SMTP服务器的用户密码
  define('MAILTYPE', 'HTML');//邮件格式（HTML/TXT）,TXT为文本邮件

  //support cookie or not
  define('ENABLE_COOKIE', false);
 
  //email module
  define('ENABLE_EMAIL_ANTISPAM', true);
  define('MAXIMUM_EMAIL_PER_IP', 8);	
  define('MAXIMUM_EMAIL_PER_EMAIL', 3);
  
  
  // if not vefified, not allowed to login	
  define('FORCE_VERIFY', false);
 
  // secure module
  // protect from CC, mostly in those sites provide vague search
  define('ENABLE_SECURE_MODULE', false);// open
  define('MAXIMUM_REQUEST_PER_MIN', 200);// maximun request per minute per ip
  define('BIND_COOKIE_WITH_IP', false);
  define('BIND_SESSION_WITH_IP', true);//can not be false when BIND_COOKIE_WITH_IP is true;
  define('SESSION_TIME_OUT', 300000);// 5 minutes 5*60*1000=300,000
