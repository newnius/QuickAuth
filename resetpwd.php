<?php
require_once('config.inc.php');
require_once('secure.inc.php');
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<?php require('head.php') ?>
	<title>Reset Password | QuickAuth</title>
</head>
<body>
<div class="wrapper">
	<?php require('header.php'); ?>
	<?php require('modals.php'); ?>
	<div class="container">
		<div id="resetpwd">
			<form class="form-resetpwd" action="javascript:void(0)">
				<h2>Reset Password</h2>
				<div class="form-group">
					<label class="sr-only" for="form-resetpwd-username">Username</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
						</div>
						<input type="text" class="form-control" id="form-resetpwd-username" placeholder="Username"
						       required/>
					</div>
				</div>
				<div class="form-group">
					<label class="sr-only" for="form-resetpwd-password">Password</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
						</div>
						<input type="password" class="form-control" id="form-resetpwd-password"
						       placeholder="New Password" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="sr-only" for="form-resetpwd-repeat">Repeat</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
						</div>
						<input type="password" class="form-control" id="form-resetpwd-repeat"
						       placeholder="Repeat new password" required/>
					</div>
				</div>
				<button id="form-resetpwd-submit" class="btn btn-lg btn-primary btn-block" type="submit">Reset</button>
			</form>
		</div>
	</div> <!-- /container -->
	<div class="push"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/blueimp-md5@2.10.0/js/md5.min.js"></script>
<?php require('footer.php'); ?>
</body>
</html>