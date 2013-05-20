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
		$path .= $_REQUEST['tags'] ? '/..' : '';
		
		return $path;
	}
	
	public static function to_article($text_id, $lang = false)
	{
		return self::getPath() . '/' . ( $lang ? $lang . '/' : '') . $text_id;
	}
	
	
	public static function to_section($section, $tags = false, $lang = false)
	{
		return self::getPath() . '/' . ( $lang ? $lang . '/' : '') . $text_id . '.all' . (!empty($tags) ? '/' . implode('-', $tags) : '');
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
		if ($href == './')
		{
			return $href;
		}
	
		// if language variable is not present in href string, it will be added!
		if (!preg_match('/lang=/', $href) AND $lang)
		{
			// question mark is added to make href a wel formatted query string
			if (!preg_match('/\?/', $href))
			{
				$href = './?' . $href;
			}
			if ($href != '#')
			{
				$href .= '&lang=' . $lang;
			}
		}
	
		// if friendly url is turned off, the formatted string is returned
		if ( !cfg::get('friendly_url'))
		{
			return $path . $href;
		}
	
		$href = str_replace(array('index.php?', './?'), null, $href);
	
		$word_patt = '[a-zA-Z0-9_-]+';
	
		$pattern = array (
					
				//section + lang
				'/^section=(' . $word_patt . ')&lang=([a-z]{2})$/',
	
				//section + lang + tags
				'/^section=(' . $word_patt . ')&tags=(' . $word_patt. ')&lang=([a-z]{2})$/',
	
				//art (id) + lang
				'/^art=([0-9]+)&lang=([a-z]{2})$/',
	
				//art_title + lang
				'/^art_title=(' . $word_patt . ')&lang=([a-z]{2})$/',
	
				// secion, no lang
				'/^section=(' . $word_patt . ')$/',
	
				// secion, no lang
				'/^section=(' . $word_patt . ')&tags=(' . $word_patt . ')$/',
	
				// art (id), no lang
				'/^art=([0-9]+)$/',
	
				// art_title, no lang
				'/^art_title=(' . $word_patt . ')$/',
	
				// homepage
				'/^lang=([a-z]{2})$/'
		);
	
		$replacement = array (
	
				// lang support
				$path . '/$2/$1.all', // ex.: ./en/news.all
	
				$path . '/$3/$1.all/$2', // ex.: ./en/news.all/latest
					
				$path . '/$2/id-$1',	// ex.: ../en/id-5
	
				$path . '/$2/$1', // ex: ../en/about
	
				// no lang
				$path . '/$1.all', // ex.: ./news.all
					
				// no lang
				$path . '/$1.all/$2', // ex.: ./news.all/latest
	
				$path . '/id-$1', // ex.: ./id-5
	
				$path . '/$1', //ex: /about
					
				$path . '/$1/' // ex.: ../en
		);
	
		$href = preg_replace($pattern, $replacement, $href);
	
		return $href;
	
	}
}