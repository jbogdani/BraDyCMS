<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2012
 * @license			All rights reserved
 * @since			Jan 10, 2012
 */
 
class ClassAutoloader
{
	public function __construct()
	{
		spl_autoload_register(array($this, 'loader'));
	}
	
	private function loader($className)
	{
		if (file_exists('lib/class.' . $className . '.inc'))
		{
			require_once 'lib/class.' . $className . '.inc';
		}
		else if (file_exists('lib/' . $className . '.inc'))
		{
			require_once 'lib/' . $className . '.inc';
		}
		else
		{
			throw new MyExc('Impossibile caricare la classe `' . $className . '`');
		}
		
	}
}