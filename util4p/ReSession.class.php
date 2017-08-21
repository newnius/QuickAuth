<?php
	session_start();
	require_once('util.php');
	require_once('RedisDAO.class.php');

	class Session
	{
		private static $time_out = 0; // 0-never expire
		private static $bind_ip = false; // bind session with ip, when client ip changes, previous session will be unavailable
		private static $PK = 'key';//primary key

		/*
		 */
		public static function configure($config)
		{
			self::$time_out = $config->get('time_out', self::$time_out);
			self::$bind_ip = $config->getBool('bind_ip', self::$bind_ip);
			self::$PK = $config->get('PK', self::$PK);
		}

		/*
		 */
		public static function put($key, $value, $namespace='default')
		{
			if($key === self::$PK){
				$_SESSION[$namespace][$key] = $value;
			}
			$redis = RedisDAO::instance();
			if($redis===null){
				return false;
			}
			if(!isset($_SESSION[$namespace][self::$PK])){
				return false;
			}
			$tmp = $_SESSION[$namespace][self::$PK];
			$redis_key = "session:{$namespace}.{$tmp}";
			$list = $redis->hgetall($redis_key);
			if(isset($list['sid']) && $list['sid']!==session_id()){
				$redis->del($redis_key);
			}
			if(self::$bind_ip){
				if(isset($list['IP']) && $list['IP']!==cr_get_client_ip()){
					$redis->del($redis_key);
				}
				$redis->hset($redis_key, 'IP', cr_get_client_ip());
			}
			$redis->hset($redis_key, 'sid', session_id());
			$redis->expire($redis_key, self::$time_out);
			$redis->hset($redis_key, $key, $value);
			$redis->disconnect();
			return true;
		}


		/*
		 */
		public static function get($key, $default=null, $namespace='default')
		{
			$redis = RedisDAO::instance();
			if($redis===null){
				return $default;
			}
			if(!isset($_SESSION[$namespace][self::$PK])){
				return $default;
			}
			$tmp = $_SESSION[$namespace][self::$PK];
			$redis_key = "session:{$namespace}.{$tmp}";
			$list = $redis->hgetall($redis_key);
			if(isset($list['sid']) && $list['sid']!==session_id()){
				return $default;
			}
			if(self::$bind_ip){
				if(isset($list['IP']) && $list['IP']!==cr_get_client_ip()){
					return $default;
				}
			}
			$redis->expire($redis_key, self::$time_out);
			$redis->disconnect();
			if(isset($list[$key])){
				return $list[$key];
			}
			return $default;
		}


		/*
		 */
		public static function remove($key, $namespace='default')
		{
			$redis = RedisDAO::instance();
			if($redis===null){
				return false;
			}
			if(!isset($_SESSION[$namespace][self::$PK])){
				return false;
			}
			$tmp = $_SESSION[$namespace][self::$PK];
			$redis_key = "session:{$namespace}.{$tmp}";
			$list = $redis->hgetall($redis_key);
			if(isset($list['sid']) && $list['sid']!==session_id()){
				return false;
			}
			if(self::$bind_ip){
				if(isset($list['IP']) && $list['IP']!==cr_get_client_ip()){
					return false;
				}
			}
			$redis->hdel($redis_key, $key);
			$redis->disconnect();
			return true;
		}


		/*
		 */
		public static function clear($namespace='default')
		{
			$redis = RedisDAO::instance();
			if($redis===null){
				return false;
			}
			if(!isset($_SESSION[$namespace][self::$PK])){
				return false;
			}
			$tmp = $_SESSION[$namespace][self::$PK];
			$redis_key = "session:{$namespace}.{$tmp}";
			$list = $redis->hgetall($redis_key);
			if(isset($list['sid']) && $list['sid']!==session_id()){
				return false;
			}
			if(self::$bind_ip){
				if(isset($list['IP']) && $list['IP']!==cr_get_client_ip()){
					return false;
				}
			}
			$redis->del($redis_key);
			$redis->disconnect();
			$_SESSION[$namespace] = array();
			return true;
		}


		/*
		 */
		public static function clearAll()
		{
			return true;
		}

		public static function tickOut($id, $namespace='default'){
			$redis = RedisDAO::instance();
			if($redis===null){
				return false;
			}
			$redis_key = "session:{$namespace}.{$id}";
			$redis->del($redis_key);
			$redis->disconnect();
			return true;
		}

		public static function listOnline($namespace='default'){
			$redis = RedisDAO::instance();
			if($redis===null){
				return false;
			}
			$redis_key = "session:{$namespace}.*";
			$list = $redis->keys($redis_key);
			$redis->disconnect();
			$len = strlen("session:{$namespace}.");
			$users = array();
			foreach($list as $item){
				$users[][self::$PK] = mb_substr($item, $len);
			}
			return $users;
		}

	}
