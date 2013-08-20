<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2013
 * @license			All rights reserved
 * @since			May 20, 2013
 */
 
class link
{
	private static function getPath()
	{
		$path = $_REQUEST['lang'] ? '..' : '.';
		$path .= ($_REQUEST['section'] && $_REQUEST['tags']) ? '/..' : '';
		
		return $path;
	}
	
	public static function to_article($text_id, $lang = false)
	{
		return self::getPath() . '/' . ( $lang ? $lang . '/' : '') . $text_id;
	}
	
	
	public static function to_section($section, $tags = false, $lang = false)
	{
		return self::getPath() . '/' . ( $lang ? $lang . '/' : '') . $section . '.all' . (!empty($tags) ? '/' . implode('-', $tags) : '');
	}
	
	
	public static function format($href, $lang = false)
	{
		$path = self::getPath();
	
		// absolute url is not processed
		if (preg_match('/^http:\/\/(.+)/', $href))
		{
			return $href;
		}
	
		// incomplete absolute url is completed with http:// prefix and not processed further
		if (preg_match('/^www\.(.+)/', $href))
		{
			return 'http://' . $href;
		}
	
		// links to nothing is not processed
		if ($href == '#')
		{
			return ($path != '.' ? $path : '') . $href;
		}
	
		// links to home page is not processed
		if ($href == '')
		{
			return $path;
		}
	
		// add language to href
		if ($lang)
		{
			$href = $lang . '/' . $href;
		}
		
		return $path . '/' . $href;
	}
}