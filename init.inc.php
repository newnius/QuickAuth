<?php
	require_once('predis/autoload.php');
	require_once('config.inc.php');
	require_once('util4p/MysqlPDO.class.php');
	require_once('util4p/RedisDAO.class.php');
	require_once('util4p/CRLogger.class.php');
	require_once('util4p/Session.class.php');
	require_once('util4p/CRObject.class.php');
	require_once('util4p/AccessController.class.php');

	init_mysql();
	init_redis();
	init_logger();
	init_Session();
	init_accessMap();

	function init_mysql(){
		$config = new CRObject();
		$config->set('host', DB_HOST);
		$config->set('port', DB_PORT);
		$config->set('db', DB_NAME);
		$config->set('user', DB_USER);
		$config->set('password', DB_PASSWORD);
		MysqlPDO::configure($config);
	}

	function init_redis(){
		$config = new CRObject();
		$config->set('scheme', REDIS_SCHEME);
		$config->set('host', REDIS_HOST);
		$config->set('port', REDIS_PORT);
		$config->set('show_error', REDIS_SHOW_ERROR);
		RedisDAO::configure($config);
	}

	function init_logger(){
		$config = new CRObject();
		$config->set('db_table', 'qa_log');
		CRLogger::configure($config);
	}

	function init_Session(){
		$config = new CRObject();
		$config->set('time_out', SESSION_TIME_OUT);
		$config->set('bind_ip', BIND_SESSION_WITH_IP);
		Session::configure($config);
	}

	function init_accessMap(){
		// $operation => arrayof roles
		$map = array(
			/* user */
      'user_get_self' => array('root', 'admin', 'developer', 'normal'),
      'user_get_others' => array('root', 'admin'),
      'user_update_admin' => array('root'),
      'user_update_developer' => array('root', 'admin'),
      'user_update_normal' => array('root', 'admin'),
      'user_update_blocked' => array('root', 'admin'),
      'user_update_removed' => array('root', 'admin'),
      'get_logs_self' => array('root', 'admin', 'developer', 'normal'),
      'get_logs_others' => array('root', 'admin'),

			/* ucenter entry show control */
      'show_ucenter_home' => array('root', 'admin', 'developer', 'normal'),
      'show_ucenter_profile' => array('admin', 'developer', 'normal'),
      'show_ucenter_changepwd' => array('root', 'admin', 'developer', 'normal'),
      'show_ucenter_logs' => array('root', 'admin', 'developer', 'normal'),
      'show_ucenter_admin' => array('root', 'admin'),
      'show_ucenter_signout' => array('root', 'admin', 'developer', 'normal'),

      'show_ucenter_users' => array('root', 'admin'),
      'show_ucenter_logs_all' => array('root', 'admin'),

		);
		AccessController::setMap($map);
	}
