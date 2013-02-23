<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 1, 2012
 */
 
class cfg
{
	private static $data;
	
	public static function get($el = false)
	{
		if (!self::$data)
		{
			if (file_exists(MAIN_DIR . 'sites/default/cfg/config.json'))
			{
				self::$data = json_decode(file_get_contents(MAIN_DIR . 'sites/default/cfg/config.json'), true);
			}
			else if (file_exists(MAIN_DIR . 'sites/default/cfg/config.ini'))
			{
				$data = parse_ini_file(MAIN_DIR . 'sites/default/cfg/config.ini', 1);
				self::save($data);
				unlink(MAIN_DIR . 'sites/default/cfg/config.ini');
			}
			else
			{
				error_log("Missing configuration file for site");
				return false;
			}
		}
		
		if ($el)
		{
			return self::$data[$el];
		}
		else
		{
			return self::$data;
		}
	}
	
	public static function save($data)
	{
		self::$data = $data;
		utils::write_in_file(MAIN_DIR . 'sites/default/cfg/config.json', self::$data, 'json');
	}
}