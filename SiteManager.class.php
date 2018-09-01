<?php

require_once('util4p/CRObject.class.php');
require_once('util4p/MysqlPDO.class.php');
require_once('util4p/SQLBuilder.class.php');

class SiteManager
{

	/**/
	public static function add($site)
	{
		$domain = $site->get('domain');
		$owner = $site->get('owner');
		$key = $site->get('key');
		$revoke_url = $site->get('revoke_url');
		$level = $site->getInt('level');

		$key_values = array('domain' => '?', 'owner' => '?', 'key' => '?', 'revoke_url' => '?', 'level' => '?');
		$builder = new SQLBuilder();
		$builder->insert('qa_site', $key_values);
		$sql = $builder->build();
		$params = array($domain, $owner, $key, $revoke_url, $level);
		$count = (new MysqlPDO())->execute($sql, $params);
		return $count === 1;
	}

	/**/
	public static function gets($rule)
	{
		$owner = $rule->get('owner');
		$offset = $rule->getInt('offset', 0);
		$limit = $rule->getInt('limit', -1);
		$selected_rows = array();
		$where_arr = array('level' => '?');
		$opts_arr = array('level' => '!=');
		$params = array('0');
		if ($owner !== null) {
			$where_arr['owner'] = '?';
			$params[] = $owner;
		}
		$builder = new SQLBuilder();
		$builder->select('qa_site', $selected_rows);
		$builder->where($where_arr, $opts_arr);
		$builder->limit($offset, $limit);
		$sql = $builder->build();
		$sites = (new MysqlPDO())->executeQuery($sql, $params);
		return $sites;
	}

	/**/
	public static function get($rule)
	{
		$id = $rule->getInt('id');
		$selected_rows = array();
		$where_arr = array('id' => '?', 'level' => '?');
		$opts_arr = array('level' => '!=');
		$params = array($id, '0');
		$builder = new SQLBuilder();
		$builder->select('qa_site', $selected_rows);
		$builder->where($where_arr, $opts_arr);
		$sql = $builder->build();
		$sites = (new MysqlPDO())->executeQuery($sql, $params);
		return count($sites) > 0 ? $sites[0] : null;
	}

	/**/
	public static function getCount($rule)
	{
		$owner = $rule->get('owner');
		$selected_rows = array('COUNT(1) AS `count`');
		$where_arr = array('level' => '?');
		$opts_arr = array('level' => '!=');
		$params = array('0');
		if ($owner !== null) {
			$where_arr['owner'] = '?';
			$params[] = $owner;
		}
		$builder = new SQLBuilder();
		$builder->select('qa_site', $selected_rows);
		$builder->where($where_arr, $opts_arr);
		$sql = $builder->build();
		$res = (new MysqlPDO())->executeQuery($sql, $params);
		return intval($res[0]['count']);
	}

	/**/
	public function update($site)
	{
		$id = $site->getInt('id');
		$domain = $site->get('domain');
		$key = $site->get('key');
		$revoke_url = $site->get('revoke_url');
		$level = $site->get('level');

		$key_values = array('domain' => '?', 'key' => '?', 'revoke_url' => '?', 'level' => '?');
		$where_arr = array('id' => '?');

		$builder = new SQLBuilder();
		$builder->update('qa_site', $key_values);
		$builder->where($where_arr);
		$sql = $builder->build();
		$params = array($domain, $key, $revoke_url, $level, $id);
		$count = (new MysqlPDO())->execute($sql, $params);
		return $count === 1;
	}

}