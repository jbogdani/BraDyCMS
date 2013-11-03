<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2013
 * @license			MIT, See LICENSE file
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
	
	private static function formatHref($href)
	{
		return str_replace(' ', '+', $href);
	}
	
	public static function to_article($textid, $lang = false)
	{
		return self::getPath() . '/' . ( $lang ? $lang . '/' : '') . self::formatHref($textid);
	}
	
	
	public static function to_tags($tags, $lang = false)
	{
		if (!is_array($tags))
		{
			$tags = array($tags);
		}
		return self::getPath() . '/' . ( $lang ? $lang . '/' : '') . self::format(implode('-', $tags)) . '.all';
	}
	
	
	public static function format($href, $lang = false)
	{
		$href = self::formatHref($href);
		
		$path = self::getPath();
	
		// absolute url is not processed
		if (preg_match('/^https?:\/\/(.+)/', $href))
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
    
    // skype  & callto links
    if (preg_match('/^skype(.+)/', $href) || preg_match('/^callto(.+)/', $href) )
		{
			return $href;
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