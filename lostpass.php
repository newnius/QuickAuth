<?php
require_once('config.inc.php');
require_once('secure.inc.php');
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<?php require('head.php') ?>
	<title>Lost password | QuickAuth</title>
</head>
<body>
<div class="wrapper">
	<?php require('header.php'); ?>
	<?php require('modals.php'); ?>
	<div class="container">
		<div id="lostpass-error">
			<strong>Error:</strong>
			<span id="lostpass-error-msg"></span>
		</div>
		<div id="lostpass">
			<form class="form-lostpass">
				<h2>Lost password</h2>
				<div class="form-group">
					<label class="sr-only" for="inputUsername">Username</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
						</div>
						<input type="text" class="form-control" id="form-lostpass-username" placeholder="Username"
						       required/>
					</div>
				</div>
				<div class="form-group">
					<label class="sr-only" for="inputEmail">Email</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
						</div>
						<input type="email" class="form-control" id="form-lostpass-email" placeholder="Email" required/>
					</div>
				</div>
				<button id="form-lostpass-submit" class="btn btn-lg btn-primary btn-block" type="submit">Send Email
				</button>
			</form>
		</div>
	</div> <!-- /container -->
	<!--This div exists to avoid footer from covering main body-->
	<div class="push"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/blueimp-md5@2.10.0/js/md5.min.js"></script>
<?php require('footer.php') ?>
</body>
</html>