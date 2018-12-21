<?php

require_once('util4p/CRObject.class.php');
require_once('util4p/MysqlPDO.class.php');
require_once('util4p/SQLBuilder.class.php');

class SiteManager
{

	private static $table_client = 'oauth_client';

	public static function configure(CRObject $config)
	{
		self::$table_client = $config->get('table_client', self::$table_client);
	}

	/**/
	public static function add(CRObject $site)
	{
		$domain = $site->get('domain');
		$owner = $site->get('owner');
		$client_id = $site->get('client_id');
		$client_secret = $site->get('client_secret');

		$key_values = array('domain' => '?', 'owner' => '?', 'client_id' => '?', 'client_secret' => '?');
		$builder = new SQLBuilder();
		$builder->insert(self::$table_client, $key_values);
		$sql = $builder->build();
		$params = array($domain, $owner, $client_id, $client_secret);
		return (new MysqlPDO())->execute($sql, $params);
	}

	/**/
	public static function gets(CRObject $rule)
	{
		$owner = $rule->get('owner');
		$offset = $rule->getInt('offset', 0);
		$limit = $rule->getInt('limit', -1);
		$selected_rows = array();
		$where_arr = array();
		$params = array();
		if ($owner !== null) {
			$where_arr['owner'] = '?';
			$params[] = $owner;
		}
		$builder = new SQLBuilder();
		$builder->select(self::$table_client, $selected_rows);
		$builder->where($where_arr);
		$builder->limit($offset, $limit);
		$sql = $builder->build();
		$sites = (new MysqlPDO())->executeQuery($sql, $params);
		return $sites;
	}

	/**/
	public static function get(CRObject $rule)
	{
		$client_id = $rule->get('client_id');
		$selected_rows = array();
		$where_arr = array('client_id' => '?');
		$params = array($client_id);
		$builder = new SQLBuilder();
		$builder->select(self::$table_client, $selected_rows);
		$builder->where($where_arr);
		$sql = $builder->build();
		$sites = (new MysqlPDO())->executeQuery($sql, $params);
		return $sites !== null && count($sites) > 0 ? $sites[0] : null;
	}

	/**/
	public static function getCount(CRObject $rule)
	{
		$owner = $rule->get('owner');
		$selected_rows = array('COUNT(1) AS `count`');
		$where_arr = array();
		$params = array();
		if ($owner !== null) {
			$where_arr['owner'] = '?';
			$params[] = $owner;
		}
		$builder = new SQLBuilder();
		$builder->select(self::$table_client, $selected_rows);
		$builder->where($where_arr);
		$sql = $builder->build();
		$res = (new MysqlPDO())->executeQuery($sql, $params);
		return $res !== null ? intval($res[0]['count']) : 0;
	}

	/**/
	public static function remove(CRObject $site)
	{
		$client_id = $site->get('client_id');
		$where = array('client_id' => '?');
		$builder = new SQLBuilder();
		$builder->delete(self::$table_client);
		$builder->where($where);
		$sql = $builder->build();
		$params = array($client_id);
		return (new MysqlPDO())->execute($sql, $params);
	}

}