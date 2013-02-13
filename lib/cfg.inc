<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 1, 2012
 */
 
class cfg
{
	public static function get($el = false)
	{
		$cfg = parse_ini_file(MAIN_DIR . 'sites/default/cfg/config.ini', 1);
		
		if ($el)
		{
			return $cfg[$el];
		}
		else
		{
			return $cfg;
		}
	}
}