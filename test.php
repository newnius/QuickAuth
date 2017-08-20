<?php
	require_once('predis/autoload.php');
	require_once('util4p/CRObject.class.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/Validator.class.php');
	//require_once('util4p/Session.class.php');
	require_once('util4p/ReSession.class.php');
	require_once('util4p/CRLogger.class.php');
	require_once('util4p/AccessController.class.php');
	require_once('util4p/Random.class.php');
	//require_once('UserManager.class.php');

	require_once('config.inc.php');
	require_once('init.inc.php');

/*
	$rule = new CRObject();
	$rule->set('scope', 'scope');
	$rule->set('tag', 'tag');
	$rule->set('level_min', 0);
	$rule->set('ip', '192.168.56.1');
	$rule->set('time_begin', 0);
	$rule->set('time_end', 0);
	$rule->set('offset', 0);
	$rule->set('limit', 0);
	$rule->set('order', 'latest');
	var_dump(CRLogger::search($rule));
*/

/*
    var_dump(Session::put('username', 'newnius', 'namespace1'));
    var_dump(Session::clear());
    var_dump(Session::put('username', 'newnius'));
    var_dump(Session::put('role', 'root'));
    var_dump(Session::remove('username'));
    var_dump(Session::clear());
    var_dump(Session::clearAll());
*/


	require("sendgrid/sendgrid-php.php");
	$from = new SendGrid\Email("Example User", "support@newnius.com");
	$to = new SendGrid\Email("Example User", "me@newnius.com");

	$subject = '[QuickAuth] Verify your email';
	$content = new SendGrid\Content("text/plain", "and easy to do anywhere, even with PHP");

	$mail = new SendGrid\Mail($from, $subject, $to, $content);
	$sg = new SendGrid(SENDGRID_API_KEY);
	$response = $sg->client->mail()->send()->post($mail);
	if($response->statusCode()==202)
		echo 'success';
	$json = $response->body();
	if(strlen($json)>0){
		$msg = json_decode($json, true);
		echo $msg['errors'][0]['message'];
	}
