<?php
require_once('config.inc.php');
require_once('secure.inc.php');
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<?php require('head.php') ?>
	<title>Grant auth | QuickAuth</title>
</head>

<body>
<div class="wrapper">
	<?php require('header.php'); ?>
	<?php require('modals.php'); ?>
	<div class="container">
		<div id="auth_grant" class="panel panel-default">
			<h3>Grant following site to access your account</h3>
			<span class="text-info" id="auth-grant-host">example.com</span>
			<form id="form-auth" action="javascript:void(0)">
				<h4>Information to be accessed</h4>
				* <label for="form-auth-openid"></label><input type="checkbox" id="form-auth-openid" class="form-group"
				                                               checked disabled/>&nbsp;<span>OpenID</span><br/>
				* <label for="form-auth-email"></label><input type="checkbox" id="form-auth-email" class="form-group"/>&nbsp;<span>Email</span><br/>
				* <label for="form-auth-verified"></label><input type="checkbox" id="form-auth-verified"
				                                                 class="form-group"/>&nbsp;<span>Verified</span><br/>
				* <label for="form-auth-role"></label><input type="checkbox" id="form-auth-role" class="form-group"/>&nbsp;<span>Role</span><br/>
				<br/>
				<button id="form-auth-accept" type="button" class="btn btn-primary">&nbsp;Accept&nbsp;</button>
				<button id="form-auth-decline" type="button" class="btn btn-default">&nbsp;Decline&nbsp;</button>
			</form>
		</div>
	</div> <!-- /container -->
	<!--This div exists to avoid footer from covering main body-->
	<div class="push"></div>
</div>
<?php require('footer.php'); ?>
</body>
</html>