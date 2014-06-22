<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Mar 18, 2013
 */

class feeds_ctrl
{
	public static function rss2($lang = false)
	{
		self::feed();
	}
	
	public static function atom($lang = false)
	{
		self::feed('atom');
	}
	
	private static function feed($type = false, $lang = false)
	{
    $feedWriter = ($type == 'atom' ? new \FeedWriter\Atom() : new \FeedWriter\RSS2);
		
		$feedWriter->setTitle(cfg::get('title'));
		
		$feedWriter->setLink(utils::getBaseUrl());
		
		$feedWriter->setChannelElement('language', $lang ? $lang : cfg::get('sys_lang'));
		
		$feedWriter->setChannelElement('pubDate', date(DATE_RSS, time()));
		
		$art_array = Article::getAllValid($lang);
		
    if (is_array($art_array))
		{
			foreach ($art_array as $art)
			{
				$newItem = $feedWriter->createNewItem();
        
				$newItem->setTitle($art['title'] ? self::cleanStr($art['title']) : '');
				
				$newItem->setLink(self::cleanStr($art['full_url']));
				
        $date = $art['publish'] ? $art['publish'] : ( $art['created'] ? $art['created'] : false );
        
				$date ? $newItem->setDate(self::cleanStr($date)) : '';
				
        $description = $art['summary'] ? $art['summary'] : substr($art['text'], 0, 500) . '...';
				$newItem->setDescription(self::cleanStr($description));
				
				$feedWriter->addItem($newItem);
			}
		}
		$feedWriter->printFeed('application/rss+xml; charset=utf-8');
		
	}
  
  private static function cleanStr($str)
  {
    return trim ( htmlentities ( strip_tags ( $str) ) );
  }
}