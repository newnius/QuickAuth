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

require_once('OAuth2.class.php');

init_mysql();
init_redis();
init_logger();
init_Session();
init_accessMap();
init_RateLimiter();
init_OAuth();

function init_mysql()
{
	$config = new CRObject();
	$config->set('host', DB_HOST);
	$config->set('port', DB_PORT);
	$config->set('db', DB_NAME);
	$config->set('user', DB_USER);
	$config->set('password', DB_PASSWORD);
	$config->set('show_error', DB_SHOW_ERROR);
	MysqlPDO::configure($config);
}

function init_redis()
{
	$config = new CRObject();
	$config->set('scheme', REDIS_SCHEME);
	$config->set('host', REDIS_HOST);
	$config->set('port', REDIS_PORT);
	$config->set('show_error', REDIS_SHOW_ERROR);
	RedisDAO::configure($config);
}

function init_logger()
{
	$config = new CRObject();
	$config->set('db_table', 'qa_log');
	CRLogger::configure($config);
}

function init_Session()
{
	$config = new CRObject();
	$config->set('time_out', SESSION_TIME_OUT);
	$config->set('bind_ip', BIND_SESSION_WITH_IP);
	$config->set('PK', 'username');
	Session::configure($config);
}

function init_accessMap()
{
	// $operation => arrayof roles
	$map = array(
		/* user */
		'user.get_self' => array('root', 'admin', 'developer', 'normal'),
		'user.get_others' => array('root', 'admin'),
		'user.update_admin' => array('root'),
		'user.update_developer' => array('root', 'admin'),
		'user.update_normal' => array('root', 'admin'),
		'user.update_blocked' => array('root', 'admin'),
		'user.update_removed' => array('root', 'admin'),
		'log.get_self' => array('root', 'admin', 'developer', 'normal'),
		'log.get_others' => array('root', 'admin'),

		/* site */
		'site.get_self' => array('root', 'admin', 'developer'),
		'site.get_others' => array('root', 'admin'),
		'site.add' => array('root', 'admin', 'developer', 'normal'),
		'site.update_others' => array('root', 'admin'),
		'site.remove' => array('root', 'admin', 'developer', 'normal'),
		'site.remove_others' => array('root', 'admin'),

		/* session */
		'session.get_online' => array('root', 'admin'),
		'session.tick_out_user' => array('root', 'admin'),
		'session.tick_out_removed' => array('root', 'admin'),
		'session.tick_out_blocked' => array('root', 'admin'),
		'session.tick_out_normal' => array('root', 'admin'),
		'session.tick_out_developer' => array('root', 'admin'),
		'session.tick_out_admin' => array('root'),

		/* rate limit */
		'rc.list' => array('root', 'admin'),
		'rc.block' => array('root', 'admin'),
		'rc.unblock' => array('root', 'admin'),

		/* ucenter entry show control */
		'ucenter.show_home' => array('root', 'admin', 'developer', 'normal'),
		'ucenter.show_profile' => array('admin', 'developer', 'normal'),
		'ucenter.show_changepwd' => array('root', 'admin', 'developer', 'normal'),
		'ucenter.show_logs' => array('root', 'admin', 'developer', 'normal'),
		'ucenter.show_auth_list' => array('root', 'admin', 'developer', 'normal'),
		'ucenter.show_user_sessions' => array('root', 'admin', 'developer', 'normal'),
		'ucenter.show_admin' => array('root', 'admin'),

		'ucenter.show_users' => array('root', 'admin'),
		'ucenter.show_sites' => array('admin', 'developer', 'normal'),
		'ucenter.show_sites_all' => array('root', 'admin'),
		'ucenter.show_users_online' => array('root', 'admin'),
		'ucenter.show_blocked_list' => array('root', 'admin'),
		'ucenter.show_logs_all' => array('root', 'admin'),
		'ucenter.show_visitors' => array('root', 'admin'),

	);
	AccessController::setMap($map);
}

function init_RateLimiter()
{
	$rules = array(
		array('interval' => 300, 'degree' => 200),
		array('interval' => 3600, 'degree' => 500),
		array('interval' => 86400, 'degree' => 1000)
	);
	$config = new CRObject();
	$config->set('key_prefix', RATE_LIMIT_KEY_PREFIX);
	$config->set('rules', $rules);
	RateLimiter::configure($config);
}

function init_OAuth()
{
	$config = new CRObject();
	$config->set('table_client', 'qa_oauth_client');
	$config->set('table_openid', 'qa_oauth_openid');
	$config->set('table_code', 'qa_oauth_code');
	$config->set('table_token', 'qa_oauth_token');
	$config->set('code_timeout', OAUTH_CODE_TIMEOUT);
	$config->set('token_timeout', OAUTH_TOKEN_TIMEOUT);
	OAuth2::configure($config);
	SiteManager::configure($config);
}
