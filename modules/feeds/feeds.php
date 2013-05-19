<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
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
	
	private function feed($type = false, $lang = false)
	{
		$feedWriter = new FeedWriter($type == 'atom' ? ATOM : RSS2);
		
		$feedWriter->setTitle(cfg::get('title'));
		
		$feedWriter->setLink('http://' . $_SERVER['HTTP_HOST']);
		
		$feedWriter->setChannelElement('language', $lang ? $lang : cfg::get('sys_lang'));
		
		$feedWriter->setChannelElement('pubDate', date(DATE_RSS, time()));
		
		$article = new Article(new DB());
		
		$art_array = $article->getAllValid($lang);
		
		if (is_array($art_array))
		{
			foreach ($art_array as $art)
			{
				$newItem = $feedWriter->createNewItem();
				
				$newItem->setTitle($art['title']);
				
				$newItem->setLink($art['full_url']);
				
				$newItem->setDate($art['created']);
				
				$newItem->setDescription($art['summary'] ? $art['summary'] : substr($art['text'], 0, 500) . '...');
				
				$feedWriter->addItem($newItem);
			}
		}
		
		
		$feedWriter->genarateFeed();
		
	}
}