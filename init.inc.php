<?php
	require_once('predis/autoload.php');
	require_once('config.inc.php');
	require_once('util4p/MysqlPDO.class.php');
	require_once('util4p/RedisDAO.class.php');
	require_once('util4p/CRLogger.class.php');
	require_once('util4p/ReSession.class.php');
	require_once('util4p/RateLimiter.class.php');
	require_once('util4p/CRObject.class.php');
	require_once('util4p/AccessController.class.php');

	init_mysql();
	init_redis();
	init_logger();
	init_Session();
	init_accessMap();
	init_RateLimiter();

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
		$config->set('PK', 'username');
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

			/* site */
			'sites_get_self' => array('root', 'admin', 'developer'),
			'sites_get_all' => array('root', 'admin'),
			'site_add_0' => array('root', 'admin', 'developer'),
			'site_add_1' => array('root', 'admin'),
			'site_add_99' => array('root', 'admin'),
			'site_update_others' => array('root', 'admin'),

			/* session */
			'get_online_users' => array('root', 'admin'),
			'tick_out_user' => array('root', 'admin'),
			'tick_out_removed' => array('root', 'admin'),
			'tick_out_blocked' => array('root', 'admin'),
			'tick_out_normal' => array('root', 'admin'),
			'tick_out_developer' => array('root', 'admin'),
			'tick_out_admin' => array('root'),

			/* rate limit */
			


			/* ucenter entry show control */
      'show_ucenter_home' => array('root', 'admin', 'developer', 'normal'),
      'show_ucenter_profile' => array('admin', 'developer', 'normal'),
      'show_ucenter_changepwd' => array('root', 'admin', 'developer', 'normal'),
      'show_ucenter_logs' => array('root', 'admin', 'developer', 'normal'),
      'show_ucenter_admin' => array('root', 'admin'),
      'show_ucenter_signout' => array('root', 'admin', 'developer', 'normal'),

      'show_ucenter_users' => array('root', 'admin'),
      'show_ucenter_sites_all' => array('root', 'admin'),
      'show_ucenter_users_online' => array('root', 'admin'),
      'show_ucenter_logs_all' => array('root', 'admin'),

		);
		AccessController::setMap($map);
	}

	function init_RateLimiter()
	{
		$rules = array(
			array('interval' => 300, 'degree' => 100),
			array('interval' => 3600, 'degree' => 500),
			array('interval' => 86400, 'degree' => 1000)
		);
		$config = new CRObject();
		$config->set('key_prefix', RATE_LIMIT_KEY_PREFIX);
		$config->set('rules', $rules);
		RateLimiter::configure($config);
	}
