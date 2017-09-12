<?php
	require_once('util4p/CRObject.class.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/Validator.class.php');
	require_once('util4p/CRLogger.class.php');
	require_once('util4p/AccessController.class.php');
	require_once('util4p/Random.class.php');
	
	require_once("sendgrid/sendgrid-php.php");

	require_once('config.inc.php');
	require_once('init.inc.php');

	function email_send($email)
	{
		if(!can_send($email)){
			$res['errno'] = CRErrorCode::TOO_FAST;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$from = new SendGrid\Email('QuickAuth', 'support@newnius.com');
		$to = new SendGrid\Email($email->get('username'), $email->get('email'));
		$subject = $email->get('subject');
		$content = new SendGrid\Content("text/html", $email->get('content'));

		$mail = new SendGrid\Mail($from, $subject, $to, $content);
		$sg = new SendGrid(SENDGRID_API_KEY);
		$response = $sg->client->mail()->send()->post($mail);
		//if($response->statusCode()==202)
			//echo 'success';
		$json = $response->body();
		if(strlen($json)>0){
			$msg = json_decode($json, true);
			$res['errno'] = CRErrorCode::FAIL;
			$res['msg'] = $msg['errors'][0]['message'];
		}
		return $res;
	}

	/* count send stats and reduce spam */
	function can_send($email){
		return true;
	}
