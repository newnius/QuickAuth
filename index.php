<?php
require_once('config.inc.php');
require_once('secure.inc.php');
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<?php require('head.php') ?>
	<title>QuickAuth | A user system which supports OAuth</title>
</head>
<body>
<div class="wrapper">
	<?php require('header.php'); ?>
	<?php require('modals.php'); ?>
	<div class="container">
		<div class="jumbotron">
			<h2>What is QuickAuth</h2>
			<p><a href="<?= BASE_URL ?>/">QuickAuth</a> is an open-source user system which supports OAuth. By using
				QuickAuth, you can log in to some websites without creating new accounts, which will most likely be used
				only once. Frankly speaking, only my websites would use this system.(hahah)</p>
			<p><a class="btn btn-primary btn-lg" href="<?= BASE_URL ?>/register?getstarted">Get started</a></p>
		</div>
		<div class="jumbotron">
			<h2>Why will I use QuickAuth</h2>
			<p>&nbsp;&nbsp;You don't have to own a Google account, but when you access some Google products, you need
				one, right?</p>
			<p><a class="btn btn-primary btn-lg" href="<?= BASE_URL ?>/register?tryitnow">Try It Now</a></p>
		</div>
		<div class="jumbotron">
			<h2>FAQs</h2>
			<div>
				<h4><span class="glyphicon glyphicon-question-sign"></span>What kind of information can be accessed?
				</h4>
				<p>&nbsp;&nbsp;In short,information such as username, email, verified or not, and some personal
					information(granted by the user) can be accessed.</p>
			</div>
			<div>
				<h4><span class="glyphicon glyphicon-question-sign"></span>I am a site manager, how to grantee the auth?
				</h4>
				<p>&nbsp;&nbsp;This is an implementation of OAuth, and it is proved safe, look at <a
							href="<?= BASE_URL ?>/help#qid-6">documents</a> to learn more.</p>
			</div>
		</div>
	</div> <!-- /container -->
	<!--This div exists to avoid footer from covering main body-->
	<div class="push"></div>
</div>
<?php require('footer.php'); ?>
</body>
</html>