<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 27, 2011
 */

require_once 'includes/XLS/reader.php';

class myReader extends Spreadsheet_Excel_Reader
{
	
	/**
	 * 
	 * Converts $mix (string or array) from ISO-8859-1 to UTF-8 usin iconv
	 * @param unknown_type $mix
	 */
	public function recursive_iconv ( $mix )
	{
		if ( is_string ( $mix ) )
		{
			return iconv ( "ISO-8859-1", "UTF-8", $mix );
		}
		else if ( is_array ($mix) )
		{
			foreach ( $mix as &$v )
			{
				$v = $this->recursive_iconv($v);
			}
			return $mix;
		}
	}
}