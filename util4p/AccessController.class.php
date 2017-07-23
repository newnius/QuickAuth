<?php

	class AccessController
	{
		// $operation => arrayof roles
		private static $rules_array = array();

		/*
		 */
		public static function setMap($map)
		{
			self::$rules_array = $map;
		}

		public static function hasAccess($role, $operation)
		{
			//echo "Calling hasAccess($role, $operation)\n";
			if(array_key_exists($operation, self::$rules_array))
			{
				return in_array($role, self::$rules_array[$operation]);
			}
			return false;
		}

  }
