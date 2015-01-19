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
    $parts = array('.');
    
    if ($_REQUEST['lang'])
    {
      array_push($parts, '..');
    }
    
    if ($_REQUEST['page'])
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
    else
    {
      $string .= '.search';
    }
    return self::format($string, $lang, $page);
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
		return self::format($textid, $lang, $page);
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
    
    return self::format(implode('-', $tags) . '.all', $lang, $page);
	}
	
	/**
   * Returns formatted href
   * @param string $href unformatted href
   * @param string $lang two-digits language code
   * @param int $page Page number
   * @return string
   */
	public static function format($href, $lang = false, $page = false)
	{
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
    
    // skype  & callto links
    if (preg_match('/^skype(.+)/', $href) || preg_match('/^callto(.+)/', $href) )
		{
			return $href;
		}
	
    
    $href = self::formatHref($href);
		
		$path = self::getPath();
    
    // links to nothing is not processed
		if ($href == '#')
		{
			return ($path != '.' ? $path : '') . $href;
		}
    
    // links to home page is not processed
		if ($href == '' || $href == './' || $href == 'home')
		{
      return $path;
		}
	
		// add language to href
		if ($lang)
		{
			$href = $lang . '/' . $href;
		}
    
    // add page to href
    if ($page)
    {
      $href .= '/' . $page; 
    }
    
    return $path . '/' . $href;
	}
}