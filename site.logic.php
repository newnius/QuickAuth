<?php
	require_once('util4p/CRObject.class.php');
	require_once('util4p/CRErrorCode.class.php');
	require_once('util4p/Validator.class.php');
	require_once('util4p/CRLogger.class.php');
	require_once('util4p/AccessController.class.php');
	require_once('util4p/Random.class.php');
	require_once('SiteManager.class.php');

	require_once('config.inc.php');
	require_once('init.inc.php');

	/**/
	function site_add($site)
	{
		if(!AccessController::hasAccess(Session::get('role'), 'site_add_'.$site->getInt('level', 1))){
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}
		$site->set('owner', Session::get('username'));
		$site->set('key', Random::randomString(64));
		$success = SiteManager::add($site);
		$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;

		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'site_add');
		$content = array('domain' => $site->get('domain'), 'revoke_url' => $site->get('revoke_url'), 'level' => $site->getInt('level'), 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}


	/**/
	function site_update($site)
	{
		$site_arr = SiteManager::get($site);
		if($site_arr === null){
			$res['errno'] = CRErrorCode::RECORD_NOT_EXIST;
			return $res; 
		}
		if($site_arr['owner']!==Session::get('username')) {
			if(!AccessController::hasAccess(Session::get('role'), 'site_update_others')){
				$res['errno'] = CRErrorCode::NO_PRIVILEGE;
				return $res;
			}
		}
		if(!AccessController::hasAccess(Session::get('role'), 'site_add_'.$site->getInt('level', 1))){// can update to level
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}
		$site_arr['level'] = $site->getInt('level', 1);
		$site_arr['domain'] = $site->get('domain');
		$site_arr['revoke_url'] = $site->get('revoke_url');

		$success = SiteManager::update(new CRObject($site_arr));
		$res['errno'] = $success?CRErrorCode::SUCCESS:CRErrorCode::UNKNOWN_ERROR;
		$log = new CRObject();
		$log->set('scope', Session::get('username'));
		$log->set('tag', 'update_site');
		$content = array(
			'id' => $site_arr['id'],
			'domain' => $site_arr['domain'],
			'revoke_url' => $site_arr['revoke_url'],
			'level' => $site_arr['level'],
			'response' => $res['errno']
		);
		$log->set('content', json_encode($content));
		CRLogger::log2db($log);
		return $res;
	}

	/**/
	function sites_get($rule)
	{
		if(!AccessController::hasAccess(Session::get('role', 'visitor'), 'sites_get_all')){
			$res['errno'] = CRErrorCode::NO_PRIVILEGE;
			return $res;
		}
		$res['errno'] = CRErrorCode::SUCCESS;
		$res['count'] = SiteManager::getCount($rule);
		$res['sites'] = SiteManager::gets($rule);
		return $res;
	}
