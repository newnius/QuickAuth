<?php
	require_once('util4p/CRObject.class.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/Validator.class.php');
	require_once('util4p/Session.class.php');
	require_once('util4p/CRLogger.class.php');
	require_once('util4p/AccessController.class.php');
	require_once('util4p/Random.class.php');
	require_once('UserManager.class.php');

	require_once('config.inc.php');
	require_once('init.inc.php');

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

