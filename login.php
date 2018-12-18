<?php
require_once('predis/autoload.php');
require_once('util4p/ReSession.class.php');
require_once('config.inc.php');
require_once('secure.inc.php');
require_once('init.inc.php');
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<?php require('head.php') ?>
	<title>Sign in | QuickAuth</title>
</head>
<body>
<div class="wrapper">
	<?php require('header.php'); ?>
	<div class="container">
		<div id="signin-error">
			<strong>Error:</strong>
			<span id="signin-error-msg"></span>
		</div>
		<?php if (!Session::get('username')) { ?>
			<div id="login">
				<form class="form-signin" action="javascript:void(0)">
					<h2 class="form-signin-heading">Please sign in</h2>
					<label for="Account" class="sr-only">Account</label>
					<input type="text" id="account" class="form-control " placeholder="Username/Email" required
					       autofocus>
					<label for="Password" class="sr-only">Password</label>
					<input type="password" id="password" class="form-control" placeholder="Password" required>
					<div class="checkbox">
						<label>
							<input type="checkbox" id="rememberme" value="remember-me"> Remember me
						</label>
						<label>
							<span><a class="text-right" href="<?= BASE_URL ?>/lostpass">Forget?</a></span>
						</label>
					</div>
					<button id="btn-login" class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
					<p class="msg">
						<a href="javascript:window.location.pathname='/register'">Register</a>
					</p>
					<div class="alert alert-danger alert-dismissable my-info" style="display:none">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<span id="msg-info"></span>
					</div>
				</form>
			</div>
		<?php } else { ?>
			<div id="haveloged" class="panel panel-default">
				<h4>You have loged in</h4>
				<div><input type="radio" checked="checked"/>&nbsp;&nbsp;<?php echo Session::get('username') ?></div>
				<br/>
				<a href="javascript:void(0)" class="btn btn-primary" id="btn-login-continue">Continue</a>
				&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="btn-signout">Sign out</a>
			</div>
		<?php } ?>

	</div> <!-- /container -->
	<!--This div exists to avoid footer from covering main body-->
	<div class="push"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/blueimp-md5@2.10.0/js/md5.min.js"></script>
<?php require('footer.php') ?>
</body>
</html>
