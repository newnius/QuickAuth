<?php
	require_once('util4p/util.php');
	require_once('util4p/CRObject.class.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/MysqlPDO.class.php');
	require_once('util4p/SQLBuilder.class.php');
	require_once('util4p/Random.class.php');

	require_once('UserManager.class.php');

	require_once('config.inc.php');
	require_once('init.inc.php');


	create_tables_user();
	create_tables_site();
	create_tables_log();

	add_root_user();
  
	function execute_sqls($sqls){
		foreach($sqls as $description=>$sql){
			echo "Executing $description: ";
			var_dump((new MysqlPDO)->execute($sql, array()));
		}
	}

	function add_root_user(){
		$password = Random::randomString(12);
		echo "Adding user: (root, $password) \n";
		
		$password = md5($password.'account');
		$password = md5($password.'newnius');
		$password = md5($password.'com');

		$password = password_hash($password, PASSWORD_DEFAULT);

		$user = new CRObject();
		$user->set('username', 'root');
		$user->set('email', 'root@domain.com');
		$user->set('password', $password);
		$user->set('role', 'root');
		var_dump(UserManager::addUser($user));
	}


	function create_tables_user(){
		$sqls = array(
			'DROP `qa_user`' => 'DROP TABLE IF EXISTS `qa_user`',
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

	function create_tables_site(){
		$sqls = array(
			'DROP `qa_site`' =>
			'DROP TABLE IF EXISTS `qa_site`',
			'CREATE `qa_site`' =>
			'CREATE TABLE `qa_site`(
				`id` INT AUTO_INCREMENT PRIMARY KEY,
				`domain` VARCHAR(64) NOT NULL,
				`key` CHAR(64) NOT NULL,
				`owner` VARCHAR(12) NOT NULL,
				 INDEX(`owner`),
				`level` INT NOT NULL DEFAULT 0 /* 0-blocked, 1-normal, 99-self*/
			)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci'
		);
		execute_sqls($sqls);
	}
  
	function create_tables_log(){
		$sqls = array(
			'DROP `qa_log`' => 'DROP TABLE IF EXISTS `qa_log`',
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
