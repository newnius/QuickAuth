<?php
require_once('config.inc.php');
require_once('secure.inc.php');
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<?php require('head.php') ?>
	<title>Sign up | QuickAuth</title>
</head>
<body>
<div class="wrapper">
	<?php require('header.php'); ?>
	<?php require('modals.php'); ?>
	<div class="container">
		<div id="register">
			<form class="form-signup" action="javascript:void(0)">
				<h2>Sign up</h2>
				<div class="form-group">
					<label class="sr-only" for="inputUsername">Username</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
						</div>
						<input type="text" class="form-control" id="form-signup-username" placeholder="Username"
						       required/>
					</div>
				</div>
				<div class="form-group">
					<label class="sr-only" for="inputEmail">Email</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
						</div>
						<input type="email" class="form-control" id="form-signup-email" placeholder="Email" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="sr-only" for="inputPassword">Password</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
						</div>
						<input type="password" class="form-control" id="form-signup-password" placeholder="Password"
						       required/>
					</div>
				</div>
				<button id="btn-register" class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
			</form>
		</div>
	</div> <!-- /container -->
	<div class="push"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/blueimp-md5@2.10.0/js/md5.min.js"></script>
<?php require('footer.php'); ?>
</body>
</html>