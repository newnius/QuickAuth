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
		<title>Lost password | QuickAuth</title>
		<link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
		<link href="style.css" rel="stylesheet"/>
		<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
	</head>
	<body>
		<div class="wrapper">
		<?php require_once('header.php'); ?>
		<?php require_once('modals.php'); ?>
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
								<input type="text" class="form-control" id="form-lostpass-username" placeholder="Username" required />
							</div>
						</div>
						<div class="form-group">
							<label class="sr-only" for="inputEmail">Email</label>
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
								</div>
								<input type="email" class="form-control" id="form-lostpass-email" placeholder="Email" required />
							</div>
						</div>
						<button id="form-lostpass-submit" class="btn btn-lg btn-primary btn-block" type="submit" >Send Email</button>
					</form>
				</div>
			</div> <!-- /container -->
			<div class="push"></div>
		</div>
		<?php require_once('footer.php')?>

		<script src="js/script.js"></script>
		<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script src="//cdn.bootcss.com/blueimp-md5/1.1.1/js/md5.min.js"></script>
		<script src="//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.js"></script> 
	</body>
</html>
