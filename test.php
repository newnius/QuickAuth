<?php
	require_once('predis/autoload.php');
	require_once('util4p/CRObject.class.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/Validator.class.php');
	require_once('util4p/RedisDAO.class.php');
	//require_once('util4p/Session.class.php');
	require_once('util4p/ReSession.class.php');
	require_once('util4p/CRLogger.class.php');
	require_once('util4p/AccessController.class.php');
	require_once('util4p/Random.class.php');
	//require_once('UserManager.class.php');
	
	require_once('guzzle/autoloader.php');

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

/*
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
*/

/*
	setcookie('sid', 'sid', time()-1);	
	if(isset($_COOKIE['sid'])){
		var_dump($_COOKIE['sid']);
	}
	setcookie('sid', 'sid');	
	var_dump($_COOKIE['sid']);
*/

/*
session_start();
var_dump(session_id());
//$_SESSION['flag'] = '1';
var_dump($_SESSION['flag']);
*/

//$redis = RedisDAO::instance();
//var_dump($redis->exists('cookie:token:newnius'));

//$rule = new CRObject();
//$rule->set('group', 'newnius');
//var_dump(Session::listGroup($rule));
//var_dump(Session::get('username'));


//echo time();

//test_guzzle();
function test_guzzle(){
	$client = new \GuzzleHttp\Client(['timeout' => 3, 'headers' => ['User-Agent' => 'QuickAuth Bot']]);
	$form_params = array(
		'username' => 'newnius',
		'email' => 'i@newnius.com'
	);
	$data = array('form_params' => $form_params);
	try{
		$res = $client->request('POST', 'http://192.168.56.227/service?action=reset_pwd_send_code', $data);
		echo $res->getStatusCode()."\n";
		// 200
		
		echo $res->getBody();
	}catch(Exception $e){
		//pass
	}

	//echo $res->getHeaderLine('content-type');
	// 'application/json; charset=utf8'

	//echo $res->getBody();
	// '{"id": 1420053, "name": "guzzle", ...}'

	//use Psr\Http\Message\ResponseInterface;
	//use GuzzleHttp\Exception\RequestException;

	$promise = $client->requestAsync('POST', 'http://google.com/192.168.56.227/service?action=login', $data);
	$promise->then(
    function (ResponseInterface $res) {
        echo $res->getStatusCode() . "\n";
    },
    function (RequestException $e) {
        echo $e->getMessage() . "\n";
        echo $e->getRequest()->getMethod();
    }
	);
	//$promise->wait();

	// Send an asynchronous request.
	//$request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
	/*
	$promise = $client->sendAsync($request)->then(function ($response) {
		echo 'I completed! '; //. $response->getBody();
	});
	*/
	//$promise->wait();
}

//test_spam();
function test_spam(){
		$rule = new CRObject();
		$rule->set('time_begin', time()-86400*2);
		$rule->set('scope', 'newnius');
		$rule->set('tag', 'send_email');//last 24 hours
		$res['errno'] = CRErrorCode::SUCCESS;
		$logs = CRLogger::search($rule);
		return count($logs) > MAX_EMAIL_PER_EMAIL;
}
