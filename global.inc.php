<?php
	require_once('util4p/Random.class.php');

	/* set csrf token */
	if(!isset($_COOKIE['csrf_token'])){
		setcookie('csrf_token', Random::randomString(32));
	}

	/* set no iframe */
	header('X-FRAME-OPTIONS:DENY');
