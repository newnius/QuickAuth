<?php 
  session_start();
  require_once('qa-config.php');
  require_once('secure.php');
  require_once('cookie.php');
  require_once('auth-functions.php');
  require_once('account-functions.php');
  if(!isset($_SESSION['username'])){
    header('location:login.php?notloged');
    exit;
  }

  if(!isset($_SESSION['redirect']) || $_SESSION['redirect']==''){
    header('location:ucenter.php?nothingtoauth');
    exit;
  }

  $redirect_url =  $_SESSION['redirect'];
  $userid = $_SESSION['username'];
  $access_token = start_auth($userid, $redirect_url);
  $redirect_to = $redirect_url.'?userid='.$userid.'&access_token='.$access_token;
  unset($_SESSION['redirect']);
 
?>
<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="keywords" content="QuickAuth, free, quick, OAuth"/>
    <meta name="description" content="QuickAuth is an implement of authorization. By using QuickAuth, you can log in to some websites without sign up for another account, which most likely will be used only once. Also,it is totally free!" />
    <meta name="author" content="Newnius"/>
    <link rel="icon" href="favicon.ico"/>
    <title>Grant auth | QuickAuth</title>
    <!-- Bootstrap core CSS -->
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
    <!-- Custom styles for this template -->
    <link href="style.css" rel="stylesheet"/>
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
  </head>

  <body>
    <?php require_once('header.php'); ?>
    <div class="container">
      <div id="auth_grant" class="panel panel-default">
        <h4>Grant following site to access your account</h4>
        <span class="text-info"><?php echo htmlspecialchars($redirect_url) ?></span>
        <p>Information can be accessed:<br/>* Your username<br/>* Your email address<br/>* Some personal information<br/><a href="<?php echo DOMAIN ?>/help.php#qid-5" target="_blank">In detail</a></p>
        <a href="<?php echo $redirect_to ?>" class="btn btn-success ">Accept</a>
        <a href="<?php echo DOMAIN ?>/ucenter.php?declineautu" class="btn btn-info">Decline</a>
      </div>
    </div> <!-- /container -->
    <?php require_once('footer.php'); ?>

    <script src="script.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="//cdn.bootcss.com/blueimp-md5/1.1.1/js/md5.min.js"></script>
    <script src="//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.js"></script> 
  </body>
</html>


