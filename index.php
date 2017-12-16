<?php
	require_once('predis/autoload.php');
	require_once('util4p/ReSession.class.php');
	require_once('config.inc.php');
	require_once('init.inc.php');
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
		<title>QuickAuth | A user system which supports OAuth</title>
		<link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
		<link href="style.css" rel="stylesheet"/>
		<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
	</head>
	<body>
		<div class="wrapper">
			<?php require_once('header.php'); ?>
			<div class="container">
				<div class="jumbotron">
					<h2>What is QuickAuth</h2>
					<p><a href="<?=BASE_URL?>/">QuickAuth</a> is an open-source user system which supports OAuth. By using QuickAuth, you can log in to some websites without sign up for another account, which most likely will be used only once. Frankly speaking, only my websites would use this system.(hahah)</p>
					<p><a class="btn btn-primary btn-lg" href="<?=BASE_URL?>/register?getstarted">Get started</a></p>
				</div>
				<div class="jumbotron">
					<h2>Why will I use QuickAuth</h2>
					<p>&nbsp;&nbsp;You don't have to own a Google account, but when you access some Google products, you need one, right?</p>
					<p><a class="btn btn-primary btn-lg" href="<?=BASE_URL?>/register?tryitnow">Try It Now</a></p>
				</div>
				<div class="jumbotron">
					<h2>FAQs</h2>
					<div>
						<h4><span class="glyphicon glyphicon-question-sign"></span>What kind of information can be accessed?</h4>
						<p>&nbsp;&nbsp;In short,information such as username, email, verified or not, and some personal information(granted by the user) can be accessed.</p>
					</div>
					<div>
						<h4><span class="glyphicon glyphicon-question-sign"></span>I am a site manager, how to garanteen the auth?</h4>
						<p>&nbsp;&nbsp;This is an implemention of OAuth, and it is proved safe, look at <a href="<?=BASE_URL?>/help#qid-6">Doc</a> to learn more.</p>
					</div>
				</div>
			</div> <!-- /container -->
			<div class="push"></div>
		</div>
		<?php require_once('footer.php'); ?>

		<script src="js/util.js"></script>
		<script src="js/script.js"></script>
		<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script src="//cdn.bootcss.com/blueimp-md5/1.1.1/js/md5.min.js"></script>
		<script src="//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.js"></script>
	</body>
</html>
