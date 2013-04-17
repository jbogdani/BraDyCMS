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
	
	private static $oaiData;
	
	/**
	 * Parses and returns OAI data
	 * @param string $el	OAI element to return
	 * @return mixed|boolean
	 */
	public static function getOAI($el = false)
	{
		if (!self::$oaiData && file_exists(MAIN_DIR . 'sites/default/OAI/config.json'))
		{
			self::$oaiData = json_decode(file_get_contents(MAIN_DIR . 'sites/default/OAI/config.json'), true);
		}
		
		if (self::$oaiData)
		{
			return $el ? self::$oaiData[$el] : self::$oaiData; 
		}
		return false;
	}
	
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
			
			if (!self::$data)
			{
				error_log("Error in reading config file! Maybe your JSON is invalid");
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