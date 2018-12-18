<?php
require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');
require_once('Code.class.php');
require_once('util4p/MysqlPDO.class.php');
require_once('util4p/SQLBuilder.class.php');
require_once('util4p/Random.class.php');

require_once('UserManager.class.php');

require_once('config.inc.php');
require_once('init.inc.php');

/* show error for debug purpose */
$config = new CRObject();
$config->set('show_error', true);
MysqlPDO::configure($config);

create_tables_user();
create_tables_oauth();
create_tables_log();

add_root_user();

function execute_sqls($sqls)
{
	foreach ($sqls as $description => $sql) {
		echo "Executing $description: ";
		$success = (new MysqlPDO)->execute($sql, array());
		echo $success ? '<em>Success</em>' : '<em>Failed</em>';
		echo "<hr/>";
	}
}

function add_root_user()
{
	$password = Random::randomString(12);
	echo "Adding root user with password $password \n";

	/* pseudo encryption in client side */
	$password = md5($password . 'QuickAuth');
	$password = md5($password . 'newnius');
	$password = md5($password . 'com');

	$password = password_hash($password, PASSWORD_DEFAULT);

	$user = new CRObject();
	$user->set('username', 'root');
	$user->set('email', 'root@domain.com');
	$user->set('password', $password);
	$user->set('role', 'root');
	$success = UserManager::add($user);
	echo $success ? '<em>Success</em>' : '<em>Failed</em>';
	echo "<hr/>";
}


function create_tables_user()
{
	$sqls = array(
//		'DROP `qa_user`' => 'DROP TABLE IF EXISTS `qa_user`',
		'CREATE `qa_user`' =>
			'CREATE TABLE `qa_user`( 
				`username` VARCHAR(12) PRIMARY KEY,
				`password` CHAR(255) NOT NULL,
				`email` VARCHAR(45) NOT NULL UNIQUE,
				 INDEX(`email`),
				`email_verified` TINYINT NOT NULL DEFAULT 0,
				`role` VARCHAR(12) NOT NULL,
				`reg_time` BIGINT,
				`reg_ip` BIGINT
			)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci'
	);
	execute_sqls($sqls);
}

function create_tables_oauth()
{
	$sqls = array(
//		'DROP `qa_oauth_client`' => 'DROP TABLE IF EXISTS `qa_oauth_client`',
		'CREATE `qa_oauth_client`' =>
			'CREATE TABLE `qa_oauth_client`(
				`client_id` VARCHAR(16) PRIMARY KEY,
				`client_secret` CHAR(64) NOT NULL,
				`domain` VARCHAR(64) NOT NULL,
				`owner` VARCHAR(12) NOT NULL,
				 INDEX(`owner`)
			)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci',

//		'DROP `qa_oauth_openid`' => 'DROP TABLE IF EXISTS `qa_oauth_openid`',
		'CREATE `qa_oauth_openid`' =>
			'CREATE TABLE `qa_oauth_openid`(
				`open_id` VARCHAR(64) PRIMARY KEY,
				`uid` VARCHAR(12) NOT NULL,
				`client_id` CHAR(16) NOT NULL,
				 UNIQUE(`uid`, `client_id`)
			)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci',

//		'DROP `qa_oauth_code`' => 'DROP TABLE IF EXISTS `qa_oauth_code`',
		'CREATE `qa_oauth_code`' =>
			'CREATE TABLE `qa_oauth_code`(
				`code` VARCHAR(64) PRIMARY KEY,
				`client_id` CHAR(16) NOT NULL,
				`open_id` VARCHAR(64) NOT NULL,
				`expires` BIGINT NOT NULL DEFAULT 0,
				`redirect_uri` VARCHAR(256) NOT NULL,
				`scope` VARCHAR(256) NOT NULL DEFAULT ""
			)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci',

//		'DROP `qa_oauth_token`' => 'DROP TABLE IF EXISTS `qa_oauth_token`',
		'CREATE `qa_oauth_token`' =>
			'CREATE TABLE `qa_oauth_token`(
				`token` VARCHAR(64) PRIMARY KEY,
				`client_id` CHAR(16) NOT NULL,
				`open_id` VARCHAR(64) NOT NULL,
				 INDEX(`client_id`, `open_id`),
				`expires` BIGINT NOT NULL DEFAULT 0,
				`scope` VARCHAR(256) NOT NULL DEFAULT ""
			)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci',
	);
	execute_sqls($sqls);
}

function create_tables_log()
{
	$sqls = array(
//		'DROP `qa_log`' => 'DROP TABLE IF EXISTS `qa_log`',
		'CREATE `qa_log`' =>
			'CREATE TABLE `qa_log`(
				`id` BIGINT AUTO_INCREMENT,
				 PRIMARY KEY(`id`),
				`scope` VARCHAR(128) NOT NULL,
				 INDEX(`scope`),
				`tag` VARCHAR(128) NOT NULL,
				 INDEX(`tag`),
				`level` INT NOT NULL, /* too small value sets, no need to index */
				`time` BIGINT NOT NULL,
				 INDEX(`time`),
				`ip` BIGINT NOT NULL,
				 INDEX(`ip`),
				`content` json
			)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci'
	);
	execute_sqls($sqls);
}