<?php
	require_once('config.inc.php');
	require_once('predis/autoload.php');
	require_once('util4p/ReSession.class.php');
	require_once('user.logic.php');
	/*
	 * check if cookie is set
	 */
	if(!Session::get('username') && ENABLE_COOKIE){
		if(isset($_COOKIE['username']) && isset($_COOKIE['token'])){
			$res = login_from_cookie($_COOKIE['username'], $_COOKIE['token']);
		}
	}
