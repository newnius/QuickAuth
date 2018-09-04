<?php

require_once('util4p/CRObject.class.php');
require_once('util4p/Random.class.php');
require_once('util4p/MysqlPDO.class.php');
require_once('util4p/SQLBuilder.class.php');

require_once('SiteManager.class.php');

class OAuth2
{
	private static $table_client = 'oauth_client';
	private static $table_openid = 'oauth_openid';
	private static $table_code = 'oauth_code';
	private static $table_token = 'oauth_token';
	private static $code_timeout = 300;
	private static $token_timeout = 86400;


	public static function configure(CRObject $config)
	{
		self::$table_client = $config->get('table_client', self::$table_client);
		self::$table_openid = $config->get('table_openid', self::$table_openid);
		self::$table_code = $config->get('table_code', self::$table_code);
		self::$table_token = $config->get('table_token', self::$table_token);
		self::$code_timeout = $config->getInt('code_timeout', self::$code_timeout);
		self::$token_timeout = $config->getInt('token_timeout', self::$token_timeout);
	}

	public static function getUID($open_id, $client_id)
	{
		$selected_rows = array('uid');
		$where = array('open_id' => '?', 'client_id' => '?');
		$values = array($open_id, $client_id);

		$builder = new SQLBuilder();
		$builder->select(self::$table_openid, $selected_rows);
		$builder->where($where);
		$sql = $builder->build();
		$UIDs = (new MysqlPDO())->executeQuery($sql, $values);
		var_dump($UIDs);
		if (count($UIDs) > 0) {
			return $UIDs[0]['uid'];
		}
		return null;
	}

	/* Get or Create OpenID of uid in client_id, false for non */
	public static function getOpenID($uid, $client_id)
	{
		$selected_rows = array('open_id');
		$where = array('uid' => '?', 'client_id' => '?');
		$values = array($uid, $client_id);

		$builder = new SQLBuilder();
		$builder->select(self::$table_openid, $selected_rows);
		$builder->where($where);
		$sql = $builder->build();

		$ids = (new MysqlPDO())->executeQuery($sql, $values);
		if (count($ids) > 0) {
			return $ids[0]['open_id'];
		}
		/* Create if not exist */
		for ($i = 0; $i < 10; $i++) { /* In case this OpenID exists */
			$open_id = Random::randomString(64);
			$key_values = array('uid' => '?', 'client_id' => '?', 'open_id' => '?');
			$builder = new SQLBuilder();
			$builder->insert(self::$table_openid, $key_values);
			$sql = $builder->build();
			$values = array($uid, $client_id, $open_id);
			$count = (new MysqlPDO())->execute($sql, $values);
			if ($count > 0) {
				return $open_id;
			}
		}
		return null;
	}

	/*
	 * array('client_id', 'open_id', 'redirect_uri', 'scope')
	 *
	 * */
	public static function getCode(CRObject $data)
	{
		$scope = json_encode(array_filter(explode(',', $data->get('scope', '')), 'strlen'));
		for ($i = 0; $i < 10; $i++) {
			$code = Random::randomString(64);
			$key_values = array('code' => '?', 'client_id' => '?', 'open_id' => '?', 'expires' => '?', 'scope' => '?', 'redirect_uri' => '?');
			$builder = new SQLBuilder();
			$builder->insert(self::$table_code, $key_values);
			$sql = $builder->build();
			$values = array($code, $data->get('client_id'), $data->get('open_id'), time() + self::$code_timeout, $scope, $data->get('redirect_uri'));
			$count = (new MysqlPDO())->execute($sql, $values);
			if ($count === 1) {
				return $code;
			}
		}
		return null;
	}

	/*
	 * array('client_id', 'client_secret', 'code', 'redirect_uri')
	 *
	 * */
	public static function getToken(CRObject $data)
	{
		/* Validate code */
		$selected_rows = array();
		$where = array('code' => '?');
		$values = array($data->get('code'));
		$builder = new SQLBuilder();
		$builder->select(self::$table_code, $selected_rows);
		$builder->where($where);
		$sql = $builder->build();
		$records = (new MysqlPDO())->executeQuery($sql, $values);
		if (count($records) === 0) {
			return null;
		}
		$record = $records[0];
		/* Validate */
		if ($record['client_id'] != $data->get('client_id') || $record['redirect_uri'] != $data->get('redirect_uri') || $record['expires'] <= time()) {
			return null;
		}
		$client = new CRObject();
		$client->set('client_id', $data->get('client_id'));
		$site = SiteManager::get($client);
		if ($site === null || $site['client_secret'] !== $data->get('client_secret')) {
			return null;
		}
		/* Expire Code */
		$builder = new SQLBuilder();
		$builder->delete(self::$table_code);
		$builder->where($where);
		$sql = $builder->build();
		(new MysqlPDO())->execute($sql, $values);
		/* Remove previous tokens */
		self::revoke($record['open_id'], $record['client_id']);
		/* Generate new token */
		for ($i = 0; $i < 10; $i++) {
			$token = Random::randomString(64);
			$key_values = array('token' => '?', 'client_id' => '?', 'open_id' => '?', 'expires' => '?', 'scope' => '?');
			$builder = new SQLBuilder();
			$builder->insert(self::$table_token, $key_values);
			$sql = $builder->build();
			$params = array($token, $record['client_id'], $record['open_id'], time() + AUTH_TOKEN_TIMEOUT, $record['scope']);
			$count = (new MysqlPDO())->execute($sql, $params);
			if ($count > 0) {
				return $token;
			}
		}
		return null;
	}

	/*
	 * array('client_id', 'client_secret', 'token')
	 *
	 * */
	public static function refreshToken(CRObject $data)
	{
		if (!self::validateToken($data)) {
			return null;
		}
		$selected_rows = array();
		$where = array('token' => '?');
		$values = array($data->get('token'));
		$builder = new SQLBuilder();
		$builder->select(self::$table_token, $selected_rows);
		$builder->where($where);
		$sql = $builder->build();
		$records = (new MysqlPDO())->executeQuery($sql, $values);
		if (count($records) === 0) {
			return null;
		}
		$record = $records[0];
		/* Remove previous tokens */
		self::revoke($record['open_id'], $record['client_id']);
		/* Generate new token */
		for ($i = 0; $i < 10; $i++) {
			$token = Random::randomString(64);
			$key_values = array('token' => '?', 'client_id' => '?', 'open_id' => '?', 'expires' => '?', 'scope' => '?');
			$builder = new SQLBuilder();
			$builder->insert(self::$table_token, $key_values);
			$sql = $builder->build();
			$params = array($token, $record['client_id'], $record['open_id'], time() + AUTH_TOKEN_TIMEOUT, $record['scope']);
			$count = (new MysqlPDO())->execute($sql, $params);
			if ($count > 0) {
				return $token;
			}
		}
		return null;
	}

	/*
	 * array('client_id', 'client_secret', 'token')
	 *
	 */
	public static function validateToken(CRObject $data)
	{
		/* Validate token */
		$selected_rows = array();
		$where = array('token' => '?');
		$values = array($data->get('token'));
		$builder = new SQLBuilder();
		$builder->select(self::$table_token, $selected_rows);
		$builder->where($where);
		$sql = $builder->build();
		$records = (new MysqlPDO())->executeQuery($sql, $values);
		if (count($records) === 0) {
			return false;
		}
		$record = $records[0];
		/* Validate */
		if ($record['expires'] <= time()) {
			return false;
		}
		$client = new CRObject();
		$client->set('client_id', $data->get('client_id'));
		$site = SiteManager::get($client);
		if ($site !== null && $site['client_secret'] === $data->get('client_secret')) {
			return true;
		}
		return false;
	}

	/* Remove tokens of (open_id, client_id) */
	public static function revoke($open_id, $client_id)
	{
		$where = array('client_id' => '?', 'open_id' => '?');
		$values = array($client_id, $open_id);
		$builder = new SQLBuilder();
		$builder->delete(self::$table_token);
		$builder->where($where);
		$sql = $builder->build();
		$count = (new MysqlPDO())->execute($sql, $values);
		return $count > 0;
	}

	public static function getAuth(CRObject $data)
	{
		$selected_rows = array();
		$where = array('token' => '?');
		$values = array($data->get('token'));
		$builder = new SQLBuilder();
		$builder->select(self::$table_token, $selected_rows);
		$builder->where($where);
		$sql = $builder->build();
		$records = (new MysqlPDO())->executeQuery($sql, $values);
		return count($records) > 0 ? $records[0] : null;
	}

	/* */
	public static function getAuthListByUID($uid)
	{
		$selected_rows = array('client_id', 'open_id');
		$where = array('uid' => '?');
		$values = array($uid);
		$builder = new SQLBuilder();
		$builder->select(self::$table_openid, $selected_rows);
		$builder->where($where);
		$sql = $builder->build();

		$sql = "SELECT token.`client_id`, `scope`, `expires`, `domain`
				FROM " . self::$table_token . " token
				LEFT JOIN " . self::$table_client . " client
				ON token.client_id = client.client_id
				WHERE (token.`client_id`, `open_id`)
				IN ($sql)";
		$records = (new MysqlPDO())->executeQuery($sql, $values);
		return $records;
	}
}