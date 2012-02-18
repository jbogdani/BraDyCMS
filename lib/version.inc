<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 22, 2011
 */

class version
{
	private function parse()
	{
		$va = @parse_ini_file('version', 1);
		if (!$va)
		{
			throw new MyExc('Non è stato possibile processare il file version in ' . __FILE__ . ', ' . __LINE__);
		}
		else
		{
			return $va;
		}
	}
	
	public static function changelog()
	{
		return self::parse();
	}
	
	public static function current()
	{
		$va = self::parse();
		return max(array_keys($va));
	}
	 
}