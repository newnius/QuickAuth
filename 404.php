<?php
require_once('Code.class.php');
require_once('secure.inc.php');

$error = '404 Not Found';
header('HTTP/1.1 404 Not Found');
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<?php require('head.php'); ?>
	<title>404 | QuickAuth</title>
</head>

<body>
<div class="wrapper">
	<?php require('header.php'); ?>
	<div class="container">
		<div class="container">
			<h2 style="text-align: center"><?= $error ?></h2>
		</div>
	</div> <!-- /container -->
	<!--This div exists to avoid footer from covering main body-->
	<div class="push"></div>
</div>
<?php require('footer.php'); ?>
</body>
</html>
