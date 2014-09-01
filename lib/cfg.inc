<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Dec 1, 2012
 */
 
class cfg
{
	private static $data = array();
	
	public static function get($el = false)
	{
		if (!self::$data)
		{
      if (!is_dir(MAIN_DIR . 'sites/default'))
      {
        throw new Exception('missing_site_dir');
      }
      
			if (!file_exists(MAIN_DIR . 'sites/default/cfg/config.json'))
			{
        throw new Exception('Missing configuration file for site');
			}
      
			self::$data = json_decode(file_get_contents(MAIN_DIR . 'sites/default/cfg/config.json'), true);
			
			if (!self::$data)
			{
        throw new Exception('Error in reading config file! Maybe your JSON is is using invalid syntax');
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
		return utils::write_in_file(MAIN_DIR . 'sites/default/cfg/config.json', self::$data, 'json');
	}
}