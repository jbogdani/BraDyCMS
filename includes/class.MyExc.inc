<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 30, 2011
 */

class MyExc extends Exception{

	public function log($file = NULL)
	{
		if (!$file)
		{
			$file = ERR_LOG;
		}
		
		error_log($this->__toString(). "\n\n", 3, $file);
	}
}