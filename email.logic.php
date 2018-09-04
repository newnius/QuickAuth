<?php

require_once('util4p/CRObject.class.php');
require_once('util4p/Validator.class.php');
require_once('util4p/CRLogger.class.php');
require_once('util4p/AccessController.class.php');
require_once('util4p/Random.class.php');

require_once("sendgrid/sendgrid-php.php");

require_once('Code.class.php');
require_once('config.inc.php');
require_once('init.inc.php');

/**/
function email_send(CRObject $email)
{
	if (!can_send($email)) {
		$res['errno'] = Code::TOO_FAST;
		return $res;
	}
	$res['errno'] = Code::SUCCESS;
	$from = new SendGrid\Email('QuickAuth', EMAIL_FROM);
	$to = new SendGrid\Email($email->get('username'), $email->get('email'));
	$subject = $email->get('subject');
	$content = new SendGrid\Content("text/html", $email->get('content'));

	$mail = new SendGrid\Mail($from, $subject, $to, $content);
	$sg = new SendGrid(SENDGRID_API_KEY);
	$response = $sg->client->mail()->send()->post($mail);
	//if($response->statusCode()==202)
	//echo 'success';
	$json = $response->body();
	if (strlen($json) > 0) {
		$msg = json_decode($json, true);
		$res['errno'] = Code::FAIL;
		$res['msg'] = $msg['errors'][0]['message'];
	}
	$res['errno'] = Code::SUCCESS;
	return $res;
}

/* count send stats and reduce spam */
function can_send(CRObject $email)
{
	/* here we only check by username(email) and leave ip check to RateLimiter */
	$rule = new CRObject();
	$rule->set('time_begin', time() - 86400);//last 24 hours
	$rule->set('scope', $email->get('username'));
	$rule->set('tag', 'email.send');
	$res['errno'] = Code::SUCCESS;
	$cnt = CRLogger::getCount($rule);
	return $cnt < MAXIMUM_EMAIL_PER_EMAIL;
}