<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			01/mar/2011
 * @uses			globals.inc
 * @uses			class.db.inc
 * 
 * $data
 * 		title		(string)
 * 		description
 * 		keywords
 * 		lang
 * 
 * 		context			(string) article, section, search, home
 * 		
 * 
 * {case article}
 * 		art_body		(array) (translated) article body array
 * 		id				(string) article id or article text
 * 		section_name	(string) articles' section(s)
 * 
 * {case section}
 * 		art_list		(false|array) array of articles
 * 		id				(string) section name
 * 		section_name	(string) section name 
 * {case search}
 * 		art_list		(false|array) array of articles
 * 		id				(string) searched string
 * 
 * 		section
 * 			[section_name]
 * 				head	(false|array with article data for article having text_id = section_name)
 * 				list	(false|array of articles)
 * 				
 * 
 */

class publicHtml 
{
	private $db, 		// database object
			$article,	// article object
			$cfg,		// configuration array
			$data,		// main data structured array
			$get;		// default get data
	
	
	public function __construct($get, $lang = false)
	{
		// initialize database object
		$this->db = new DB();
		
		// initialize article object		
		$this->article = new Article($this->db);
		
		// Get all settings
		$this->cfg = cfg::get();
		
		//Set get
		$this->get = $get;
		
		// Set language
		$this->data['lang'] = false;
		
		if ($lang AND ( $lang != $this->cfg['sys_lang'] ) )
		{
			$this->data['lang'] = $lang;
		}
		
		// Set url_lang
		$this->data['url_lang'] = $get['lang'] ? $get['lang'] : false;

		// Set context
		if ( $get['art'] OR $get['art_title'] )
		{
			$this->data['context'] = 'article';
			$this->data['article'] = $this->article->getArticle($get['art'], $get['art_title'], $this->data['lang']);
		}
		else if ( $get['section'] )
		{
			$this->data['context'] = 'section';
			$this->data['section_articles'] = $this->article->getArticlesBySection($this->get['section'], $this->data['lang']);
		}
		else if ($get['search'])
		{
			$this->data['context'] = 'search';
		}
		else
		{
			$this->data['context'] = 'home';
		}
	}
	
	public function get($el)
	{
		return $this->data[$el];
	}
	
	public function getArticle($el)
	{
		return $this->data['article'][$el];
	}
	
	public function getLanguages()
	{
		if (!$this->data['languages'])
		{
			$this->data['languages'] = utils::getLanguages($this->data['url_lang']);
		}
		return $this->data['languages'];
	}
	
	public function getContext()
	{
		return $this->data['context'];
	}
	
	public function getSearchString()
	{
		return $this->data['searchString'] = $this->get['search'];
	}
	
	public function getSectionName()
	{
		if (!$this->data['sectionName'])
		{
			switch ($this->data['contex'] )
			{
				case 'article':
					$this->data['sectionName'] = $this->data['article']['section'];
					break;
					
				case 'section':
					$this->data['sectionName'] = $this->get['section'];
					break;
					
				case 'search':
				default:
					break;
			}
		}
		return $this->data['sectionName'];
	}
	
	/**
	 * 
	 * @param string $get
	 * @return Ambigous array|boolean
	 */
	public function getArticleBody($get = false)
	{
		if ($get)
		{
			return $this->article->getArticle($get['art'], $get['art_title'], $this->data['lang']);
			
		}
		return $this->data['article'];
	}
	
	
	public function getArticleSiblings()
	{
		if (!$this->data['siblings'] && $this->data['context'] == 'article')
		{
			$this->data['siblings'] = $this->article->getSiblings($this->data['article']['id'], $this->data['article']['id'], $this->data['lang']); 
		}
		return $this->data['siblings'];
	}
	
	
	public function getArticlesByTag()
	{
		return $this->article->getArticlesByTag(func_get_args());
	}
	
	public function getArticlesBySection($section, $el = false)
	{
		if (!$this->data['section'][$section])
		{
			$this->data['section'][$section] = $this->article->getArticlesBySection($section);
		}
		
		
		switch($el)
		{
			case 'list':
				return $this->data['section'][$section]['list'];
				break;
				
			case 'head':
				return $this->data['section'][$section]['head'];
				break;
				
			default:
				return $this->data['section'][$section];
				break;
		}
	}
	
	
	public function getArticleList($section = false)
	{
		if(!$this->data['context'] == 'section' && !$this->data['context'] == 'search' && !$section)
		{
			return false;
		}
		
		if ($section)
		{
			return $this->article->getArticlesBySection($section);
		}
		else if ($this->data['context'] == 'section')
		{
			return $this->data['section_articles'];
		}
		else if ($this->data['context'] == 'search')
		{
			return $this->article->search($this->get['search'], $this->data['lang']);
		}
		else if ($this->data['context'] == 'article')
		{
			return $this->article->getSiblings($this->data['article']['id'], $this->data['article']['section'], $this->data['lang']);
		}
	}
	
	/**
	 * Return string pf page metadata
	 * @param string $el 	Element to return [title|description|keywords|lang]
	 */
	public function getPageData($el = false)
	{
		if (!$this->data['page'])
		{
			// Title
			$this->data['page']['title'] = $this->data['article']['title'] ? $this->data['article']['title'] : $this->cfg['title'];
				
			// Description
			if ( $this->data['article']['summary'] )
			{
				$this->data['page']['description'] = strip_tags($this->data['article']['summary']);
			}
			else if ( $this->data['article']['section'])
			{
				$this->data['page']['description'] = $this->data['article']['section'];
			}
			else
			{
				$this->data['page']['description'] = $this->cfg['description'];
			}
				
			// Keywords
			$this->data['page']['keywords'] = $this->data['article']['keywords'] ? $this->data['article']['keywords'] : $this->cfg['keywords'];
				
			// Lang
			$this->data['page']['lang'] = $this->data['lang'] ? $this->data['lang'] : $this->cfg['sys_lang'];
		}

		return $el ? $this->data['page'][$el] : $this->data['page'];
	}
	
	public function getMenu($menu_name, $hide_sub = false)
	{
		if (!$this->data['menu'][$menu])
		{
			$menu = new Menu();
			
			$not_structured_menu = $menu->get_all_items_of_menu($menu_name, $this->data['lang']);
			
			foreach($not_structured_menu as $item)
			{
				if (empty($item['subof']))
				{
					if (is_array($this->data['menu'][$menu_name][$item['id']]))
					{
						$this->data['menu'][$menu_name][$item['id']] = array_merge($this->data['menu'][$menu_name][$item['id']], $item);
					}
					else
					{
						$this->data['menu'][$menu_name][$item['id']] = $item;
					}
				}
				else
				{
					$this->data['menu'][$menu_name][$item['subof']]['sub'][] = $item;
				}
			}
		}
		return $this->data['menu'][$menu_name];
	}

}