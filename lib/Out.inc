<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Feb 16, 2013
 * 
 * 	data				array
 * 		article			array
 * 			text_id		array
 * 				...
 * 		section			array
 * 			section-name
 * 				...
 * 		siblings		array
 * 		page
 * 			title
 * 			description
 * 			keywords
 * 			lang
 * 			url
 * 			image
 * 		menu
 * 		languages
 * 		sectionName
 */
 
class Out
{
	private $db, // database object
	$article,	// article object
	$cfg,		// cfg object
	$get,		// GET data
	$context,	// context: article, section, search
	$data	//
	;
	public 
	$session_lang, // different from $lang that if is the same of sys_lang is not set!
	$lang;		// current language ( = session['lang'] set in globals, same as get['lang'])
	
	
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
		
		if ($lang AND ( $lang != $this->cfg['sys_lang'] ) )
		{
			$this->lang = $lang;
		}
		
		if ($lang)
		{
			$this->session_lang = $lang;
		}
		
		// Set context
		if ( $get['art'] OR $get['art_title'] )
		{
			$this->context = 'article';
			$this->getArticle($ge['art_title']);
		}
		else if ( $get['section'] )
		{
			$this->context = 'section';
			$this->getArticlesBySection($get['section']);
		}
		else if ($get['search'])
		{
			$this->context = 'search';
		}
		else
		{
			$this->context = 'home';
		}
	}
	
	public function getContext()
	{
		return $this->context;
	}
	
	public function getSectionName()
	{
		if ($this->context == 'section')
		{
			return $this->get['section'];
		}
		else if ($this->context == 'article')
		{
			if (!$this->data['article'][$this->get['art_title']])
			{
				$this->getArticle();
			}
			return $this->data['article'][$this->get['art_title']]['section'];
		}
	}
	
	public function getArticle($article = false)
	{
		$article = $article ? $article : $this->get['art_title'];
		
		if (!$article)
		{
			return false;
		}
		
		if (!$this->data['article'][$article])
		{
			$this->data['article'][$article] = $this->article->getArticle(false, $article, $this->lang);
		}
		
		$this->data['article'][$article]['text'] = $this->parseContent($this->data['article'][$article]['text']);
		return $this->data['article'][$article];
	}
	
	
	public function getArticlesBySection($section = false)
	{
		$section = $section ? $section : $this->get['section'];
		
		if (!$this->data['section'][$section])
		{
			$this->data['section'][$section] = $this->article->getArticlesBySection($section, $this->lang);
		}
		
		return $this->data['section'][$section];
	}
	
	public function getArticleSiblings()
	{
		if (!$this->get['art_title'])
		{
			return false;
		}
		if (!$this->data['siblings'])
		{
			$this->data['siblings'] = $this->article->getSiblings($this->get['art_title'], $this->lang);
		}
		return $this->data['siblings'];
	}
	
	public function getArticlesByTag()
	{
		return $this->article->getArticlesByTag(func_get_args(), $this->lang);
	}
	
	public function getPageData($el = false)
	{
		if (!$this->data['page'])
		{
			// Title
			$this->data['page']['title'] = $this->data['article'][$this->get['art_title']]['title'] ? $this->data['article'][$this->get['art_title']]['title'] : $this->cfg['title'];
	
			// Description
			if ( $this->data['article'][$this->get['art_title']]['summary'] )
			{
				$this->data['page']['description'] = trim(strip_tags($this->data['article'][$this->get['art_title']]['summary']));
			}
			else if ( $this->data['article'][$this->get['art_title']]['section'])
			{
				$this->data['page']['description'] = $this->data['article'][$this->get['art_title']]['section'];
			}
			else
			{
				$this->data['page']['description'] = $this->cfg['description'];
			}
			
			if (strlen($this->data['page']['description']) > 500)
			{
				$this->data['page']['description'] = substr($this->data['page']['description'], 0, 497) . '...';
			}
	
			// Keywords
			$this->data['page']['keywords'] = $this->data['article'][$this->get['art_title']]['keywords'] ? $this->data['article'][$this->get['art_title']]['keywords'] : $this->cfg['keywords'];
	
			// Lang
			$this->data['page']['lang'] = $this->lang ? $this->lang: $this->cfg['sys_lang'];
			
			$this->data['page']['url']  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			
			if ($this->data['article'][$this->get['art_title']]['text'])
			{
				$xpath = new DOMXPath(@DOMDocument::loadHTML($this->data['article'][$this->get['art_title']]['text']));
				$src = $xpath->evaluate("string(//img/@src)");
				
				$this->data['page']['image'] = ( !preg_match('/http/', $src) ? 'http://' . $_SERVER['HTTP_HOST'] . '/' : '') . $src;
				
			}
		}
	
		return $el ? $this->data['page'][$el] : $this->data['page'];
	}
	
	public function getMenu($menu_name, $hide_sub = false)
	{
		if (!$this->data['menu'][$menu])
		{
			$menu = new Menu();
			
			$not_structured_menu = $menu->get_all_items_of_menu($menu_name, $this->lang);
				
			foreach($not_structured_menu as $item)
			{
				if ($this->get['art_title'] AND preg_match('/art_title=' . $this->get['art_title'] . '/', $item['href']))
				{
					$item['current'] = true;
				}
				else if ($this->get['section'] AND preg_match('/section=' . $this->get['section'] . '/', $item['href']))
				{
					$item['current'] = true;
				}
				else if (!$this->get['search'] && !$this->get['art_title'] && !$this->get['section'] && $item['href'] == './')
				{
					$item['current'] = true;
				}
	
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
	
	public function getLanguages()
	{
		if (!$this->data['languages'])
		{
			$this->data['languages'] = utils::getLanguages($this->session_lang);
		}
		return $this->data['languages'];
	}
	
	public function getSearchResults()
	{
		return $this->article->search($this->get['search'], $this->lang);
	}
	
	public function getSearchString()
	{
		return $this->get['search'];
	}
	
	
	private function parseContent($html)
	{
		preg_match_all('/\[\[(\w+)\s*([^\]]*)\]\](.*?)\[\[\/\1\]\]/s', $html, $customTags, PREG_SET_ORDER);
		
		foreach ($customTags as $customTag)
		{
			$originalTag = $customTag[0];
		
			$tag = $customTag[1];
		
			$rawAttributes = $customTag[2];
		
			$content = $customTag[3];
		
			preg_match_all('/([^=\s]+)="([^"]+)"/', $rawAttributes, $attributes, PREG_SET_ORDER);
		
			$formatedAttributes['content'] = $content;
		
			foreach ($attributes as $attribute)
			{
				$formatedAttributes[$attribute[1]] = $attribute[2];
			}
			
			if (method_exists('customTags', $tag))
			{
				$replace = call_user_func(array('customTags', $tag), $formatedAttributes);
		
				$html = str_replace($originalTag, $replace, $html);
			}
		}
			
		return $html;
	}
	
	
	public function gallery($data)
	{
		return customTags::gallery($data);
	}
}