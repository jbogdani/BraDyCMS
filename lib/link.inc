<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2013
 * @license			MIT, See LICENSE file
 * @since			May 20, 2013
 */
 
class link
{
	/**
   * Returns path to main domain root
   * @return string
   */
  private static function getPath()
	{
    $parts = array();
    
    if ($_REQUEST['lang'])
    {
      array_push($parts, '..');
    }
    
    if ($_REQUEST['page'])
    {
      array_push($parts, '..');
    }
    
    if ($_REQUEST['section'] && $_REQUEST['tags'])
    {
      array_push($parts, '..');
    }
    
    return implode('/', $parts);
	}
  
  /**
   * Returns URL safe string
   * @param string $href url to link 2
   * @return string
   */
	private static function formatHref($href)
	{
		return str_replace(' ', '+', $href);
	}
  
  /**
   * Returns well-formatted URL string
   * @param string $string Searched string or stringified list of tags
   * @param string $lang  Two-difigts language code
   * @param int $page Page number
   * @param boolean $isTag If true I'm searching for tags
   * @return string
   */
  public static function to_search($string, $lang = false, $page = false, $isTag = false)
  {
    if (!$isTag)
    {
      $string = 'search:' . $string;
    }
    return self::getPath()
      . '/' .  ($lang ? $lang . '/' : '')
      . self::format($string)
      . ($isTag ? '.search' : '')
      . ($page && $isTag ? '/' . $page : '');
  }
	
  /**
   * Return well formatted link string
   * @param string $textid Article's textid to link to
   * @param string $lang two-digits languagae code, default false
   * @param int|false $page Page number, default false.
   * @return string
   */
	public static function to_article($textid, $lang = false, $page = false)
	{
		return self::getPath() 
      . '/' . ( $lang ? $lang . '/' : '') 
      . self::formatHref($textid)
      . ($page ? '/' . $page : '');
	}
	
	/**
   * Return well formatted link string
   * @param string|array $tags string of single tag or array of tags to link to
   * @param string $lang two-digits languagae code, default false
   * @param int|false $page Page number, default false.
   * @return string
   */
	public static function to_tags($tags, $lang = false, $page = false)
	{
		if (!is_array($tags))
		{
			$tags = array($tags);
		}
		return self::getPath() 
      . '/' . ( $lang ? $lang . '/' : '') 
      . self::format(implode('-', $tags)) . '.all'
      . ($page ? '/' . $page : '');
	}
	
	/**
   * Returns formatted href
   * @param string $href unformatted href
   * @param string $lang two-digits language code
   * @return string
   */
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
		if ($href == '' || $href == './')
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