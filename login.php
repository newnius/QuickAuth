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
	<?php require('modals.php'); ?>
	<title>Sign in | QuickAuth</title>
</head>
<body>
<div class="wrapper">
	<?php require('header.php'); ?>
	<div class="container">
		<?php if (!Session::get('username')) { ?>
			<div id="login">
				<form id="form-login" action="javascript:void(0)">
					<h2 id="form-login-heading">Please sign in</h2>
					<label for="form-login-account" class="sr-only">Account</label>
					<input type="text" id="form-login-account" class="form-control" placeholder="Username/Email"
					       required autofocus>
					<label for="form-login-password" class="sr-only">Password</label>
					<input type="password" id="form-login-password" class="form-control" placeholder="Password"
					       minlength="6" required>
					<div>
						<label>
							<input type="checkbox" id="form-login-remember"/> Remember me
						</label>
						<span class="pull-right"><a class="text-infp" href="<?= BASE_URL ?>/lostpass">Forget?</a></span>
					</div>
					<button id="form-login-submit" class="btn btn-lg btn-primary btn-block" type="submit">Sign in
					</button>
					<p class="msg">
						<a href="javascript:window.location.pathname='register'">Register</a>
					</p>
				</form>
			</div>
		<?php } else { ?>
			<div id="havelogged" class="panel panel-default" style="padding: 25px">
				<h4>You have logged in</h4>
				<div>
					<input type="radio" checked="checked"
					       title="username"/>&nbsp;&nbsp;<?= htmlspecialchars(Session::get('username')) ?>
				</div>
				<br/>
				<a href="javascript:void(0)" class="btn btn-primary" id="btn-login-continue">Continue</a>
				&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="btn-signout">Switch</a>
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
