<?php 
	require_once('predis/autoload.php');
	require_once('util4p/ReSession.class.php');
	require_once('config.inc.php');
	require_once('init.inc.php');
	require_once('global.inc.php');
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<meta name="keywords" content="QuickAuth, free, quick, OAuth, User System"/>
		<meta name="description" content="QuickAuth is a user system and an implement of OAuth. By using QuickAuth, you can log in to some websites without sign up for another account, which most likely will be used only once. Also ,it is totally free!" />
		<meta name="author" content="Newnius"/>
		<link rel="icon" href="favicon.ico"/>
		<title>Sign in | QuickAuth</title>
		<link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
		<link href="style.css" rel="stylesheet"/>
		<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
	</head>
	<body>
		<div class="wrapper">
			<?php require_once('header.php'); ?>
			<div class="container">
				<div id="signin-error">
					<strong>Error:</strong>
					<span id="signin-error-msg"></span>
				</div>
			<?php if(!Session::get('username')){ ?>
				<div id="login">
					<form class="form-signin" action="javascript:void(0)">
						<h2 class="form-signin-heading">Please sign in</h2>
						<label for="Account" class="sr-only">Account</label>
						<input type="text" id="account" class="form-control " placeholder="Username/Email" required autofocus>
						<label for="Password" class="sr-only">Password</label>
						<input type="password" id="password" class="form-control" placeholder="Password" required>
						<div class="checkbox">
							<label>
								<input type="checkbox" id="rememberme" value="remember-me"> Remember me
							</label>
							<label>
								<span><a class="text-right" href="<?=BASE_URL?>/lostpass">Forget?</a></span>
							</label>        
						</div>
						<button id="btn-login" class="btn btn-lg btn-primary btn-block" type="submit" >Sign in</button>
						<p class="msg">
							<a href="javascript:window.location.pathname='/register'" >Register</a>
						</p>
						<div class="alert alert-danger alert-dismissable my-info"  style="display:none">
							<button type="button" class="close" data-dismiss="alert"  aria-hidden="true">&times;</button>
							<span id="msg-info"></a>
						</div>
					</form>
				</div>
			<?php }else{ ?>
				<div id="haveloged" class="panel panel-default">
					<h4>You have loged in</h4>
					<div><input type="radio" checked="checked"/>&nbsp;&nbsp;<?php echo Session::get('username') ?></div><br/>
					<a href="javascript:void(0)" class="btn btn-primary" id="btn-login-continue">Continue</a>
					&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="btn-signout">Sign out</a>
				</div>
			<?php } ?>

			</div> <!-- /container -->
			<div class="push"></div>
		</div>
		<?php require_once('footer.php') ?>

		<script src="js/util.js"></script>
		<script src="js/script.js"></script>
		<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script src="//cdn.bootcss.com/blueimp-md5/1.1.1/js/md5.min.js"></script>
		<script src="//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.js"></script> 
	</body>
</html>
