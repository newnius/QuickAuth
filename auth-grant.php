<?php 
	require_once('config.inc.php');
	require_once('predis/autoload.php');
	require_once('util4p/ReSession.class.php');
	require_once('init.inc.php');
	require_once('secure.php');
	require_once('cookie.php');

	if(Session::get('username')==null){
		header('location:login.php?a=notloged');
		exit;
	}
/*
	if(!isset($_SESSION['redirect']) || $_SESSION['redirect']==''){
		header('location:ucenter.php?nothingtoauth');
		exit;
  }
*/
$redirect = 'http://example.com';
//  $redirect_url =  $_SESSION['redirect'];
  $userid = Session::get('username');
//  $redirect_to = $redirect_url.'?userid='.$userid.'&access_token='.$access_token;
//  unset($_SESSION['redirect']);
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
		<?php require_once('modals.php'); ?>
    <div class="container">
      <div id="auth_grant" class="panel panel-default">
        <h4>Grant following site to access your account</h4>
        <span class="text-info"><?php echo htmlspecialchars($redirect) ?></span>
				<form id="form-auth" action="javascript:void(0)">
        	<p>Information can be accessed:</p>
					* <input type="checkbox" id="form-auth-username" class="form-group" checked disabled/><span>Username</span><br/>
					* <input type="checkbox" id="form-auth-email" class="form-group"/><span>Email</span><br/>
					* <input type="checkbox" id="form-auth-verified" class="form-group"/><span>Verified</span><br/>
					* <input type="checkbox" id="form-auth-role" class="form-group"/><span>Role</span><br/>
				<button id="form-auth-accept" type="button" class="btn btn-primary">&nbsp;Accept&nbsp;</button>
				<button id="form-auth-decline" type="button" class="btn btn-default">&nbsp;Decline&nbsp;</button>
				</form>
      </div>
    </div> <!-- /container -->
    <?php require_once('footer.php'); ?>

    <script src="js/util.js"></script>
    <script src="js/script.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="//cdn.bootcss.com/blueimp-md5/1.1.1/js/md5.min.js"></script>
    <script src="//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.js"></script> 
  </body>
</html>
