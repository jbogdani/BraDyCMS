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
 * 		tag_siblings	array
 * 		page
 * 			title
 * 			description
 * 			keywords
 * 			lang
 * 			url	(page URL)
 * 			image* (article's body first image src)
 * 			author* (article's author)
 * 			publisher* (OAI data publisher)
 * 			date* (article's publish_on)
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
	$data,	//
	$metadata
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
		
		if ($this->get['tags'])
		{
			$this->get['tags'] = utils::csv_explode($this->get['tags'], '-');
		}
		
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
	/**
	 * If called sets Article object's preview firlds to *. All fields will be returned!
	 */
	public function getAllFlds()
	{
		$this->article->flds = ' * '; 
	}
	
	/**
	 * Returns full link to provided content (article or section)
	 * 
	 * @param string $art	article or section name
	 * @param boolean $isSection if true $art is a section name, if false (default) $art is an article text_id
	 * @param array|false $tags	array of tags to use for filtering section articles
	 * @return string
	 */
	public function link2($art, $isSection = false, $tags = false)
	{
		if ($isSection)
		{
			return link::to_section($art, $tags, $this->lang);	
		}
		else
		{
			return link::to_article($art == 'home' ? './' : $art, $this->lang);
		}
	}
	
	/**
	 * Returns true if current context is section and some tag filtering is active
	 * @return boolean
	 */
	public function sectionHasTags()
	{
		return ($this->get['tags'] && $this->context == 'section');
	}
	
	/**
	 * Returns curent context
	 * @return string
	 */
	public function getContext()
	{
		return $this->context;
	}
	
	/**
	 * Returns current secion name. Works  if section is article or section
	 */
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
	
	/**
	 * Returns array with atricle data. If $article is not provided current article's data will be returned
	 * @param string $article article text_id
	 * @return array
	 */
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
	
	/**
	 * Returns array of articles of current section or of provided section
	 * @param string $section Section name to get articles for
	 */
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
	
	public function getArticlesTagSiblings()
	{
		if (!$this->get['art_title'])
		{
			return false;
		}
		if (!$this->data['tag_siblings'])
		{
			if (!$this->data['article'][$this->get['art_title']])
			{
				$this->getArticle();
			}
			$art_tags = utils::csv_explode($this->data['article'][$this->get['art_title']]['tags']);
			
			if (is_array($art_tags))
			{
				$this->data['tag_siblings'] = $this->article->getTagSiblings($art_tags, $this->get['art_title'], $this->lang);
			}
			else
			{
				return false;
			}
		}
		return $this->data['tag_siblings'];
	}
	
	
	public function getArticlesBySectionAndTags()
	{
		$args = func_get_args();
		
		if (is_array($args) && !empty($args))
		{
			$section = $args[0];
		
			unset($args[0]);
		
			$tags = $args;
		}
		else
		{
			$section = $this->get['section'];
			$tags = $this->get['tags'];
		}
		if (!is_array($tags))
		{
			return false;
		}
		
		return $this->article->getArticlesBySectionAndTag($section, $tags, $this->lang);
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
			if ( $this->data['article'][$this->get['art_title']]['summary'] && trim(strip_tags($this->data['article'][$this->get['art_title']]['summary'])) != '' )
			{
				$this->data['page']['description'] = trim(strip_tags($this->data['article'][$this->get['art_title']]['summary']));
			}
			else
			{
				$this->data['page']['description'] = ($this->data['article'][$this->get['art_title']]['section'] ? $this->data['article'][$this->get['art_title']]['section'] . ' :: ' : '') . $this->cfg['description'];
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
			
			$art_img_paths = cfg::get('art_img');
			
			if (file_exists('./sites/default/images/articles/' . $art_img_paths[0] . '/' . $this->data['article'][$this->get['art_title']]['id'] . '.jpg'))
			{
				$this->data['page']['image'] = './sites/default/images/articles/' . $art_img_paths[0] . '/' . $this->data['article'][$this->get['art_title']]['id'] . '.jpg';
			}
			else if ($this->data['article'][$this->get['art_title']]['text'])
			{
				$xpath = new DOMXPath(@DOMDocument::loadHTML($this->data['article'][$this->get['art_title']]['text']));
				$src = $xpath->evaluate("string(//img/@src)");
				
				$this->data['page']['image'] = ( !preg_match('/http/', $src) ? 'http://' . $_SERVER['HTTP_HOST'] . '/' : '') . $src;
			}
			
			if ($this->data['article'][$this->get['art_title']]['author'] && $this->data['article'][$this->get['art_title']]['author'] != '')
			{
				$this->data['page']['author'] = $this->data['article'][$this->get['art_title']]['author'];
			}
			
			if ($this->data['article'][$this->get['art_title']]['publish_on'] && $this->data['article'][$this->get['art_title']]['publish_on'] != '0000-00-00')
			
			$this->data['page']['date'] = $this->data['article'][$this->get['art_title']]['publish_on'];
			
		}
	
		return $el ? $this->data['page'][$el] : $this->data['page'];
	}
	
	public function getMenu($menu_name, $hide_sub = false)
	{
		if (!$this->data['menu'][$menu])
		{
			$menu = new Menu();
			$tmp = $menu->get_structured_menu($menu_name, $this->lang);
			
			$this->data['menu'][$menu_name] = $this->recursiveRichMenu($tmp);
			
			return $this->data['menu'][$menu_name];
			
			$not_structured_menu = $menu->get_all_items_of_menu($menu_name, $this->lang);
				
			foreach($not_structured_menu as $item)
			{
				/**
				 * Current is true if:
				 * 	1. The menu points to the article I'm viewing
				 * 	2. The menu point to the section blog I'm actually viewing
				 * 	3. The menu point to a section blog and the current section name of the article I'm viewing is the same
				 * 	4. The menu point to the home page and I'm actually on the home page
				 */
				
				if ($this->get['art_title'] && preg_match('/art_title=' . $this->get['art_title'] . '/', $item['href']))
				{
					$item['current'] = true;
				}
				else if ($this->get['section'] && preg_match('/section=' . $this->get['section'] . '/', $item['href']))
				{
					$item['current'] = true;
				}
				else if ($this->context == 'article' && preg_match('/section=' . $this->getSectionName() . '/', $item['href']))
				{
					$item['current'] = true;
				}
				else if ($this->context == 'home' && $item['href'] == './')
				{
					$item['current'] = true;
				}
				/*
				else if (!$this->get['search'] && !$this->get['art_title'] && !$this->get['section'] && $item['href'] == './')
				{
					$item['current'] = true;
				}
				*/
	
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
	
	
	private function recursiveRichMenu($menu)
	{
		foreach ($menu as $item)
		{
			if ($this->get['art_title'] && preg_match('/art_title=' . $this->get['art_title'] . '/', $item['href']))
			{
				$item['current'] = true;
			}
			else if ($this->get['section'] && preg_match('/section=' . $this->get['section'] . '/', $item['href']))
			{
				$item['current'] = true;
			}
			else if ($this->context == 'article' && preg_match('/section=' . $this->getSectionName() . '/', $item['href']))
			{
				$item['current'] = true;
			}
			else if ($this->context == 'home' && $item['href'] == './')
			{
				$item['current'] = true;
			}
			
			if (is_array($item['sub']))
			{
				$item['sub'] = $this->recursiveRichMenu($item['sub']);
			}
		}
		return $menu;
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
	
	
	public function parseContent($html)
	{
		//return customTagParser::do_shortcode($html);
		preg_match_all('/\[\[(\w+)\s*([^\]]*)\]\](.*?)\[\[\/\1\]\]/s', $html, $customTags, PREG_SET_ORDER);
		
		foreach ($customTags as $customTag)
		{
			$attributes = array();
			$rawAttributes = array();
			$formatedAttributes = array();
			
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
			
			$formatedAttributes['lang'] = $this->lang;
			
			if (method_exists('customTags', $tag))
			{
				$replace = call_user_func(array('customTags', $tag), $formatedAttributes);
		
				$html = str_replace($originalTag, $replace, $html);
			}
			else if (file_exists('sites/default/modules/' . $tag . '/' . $tag . '.inc'))
			{
				require_once 'sites/default/modules/' . $tag . '/' . $tag . '.inc';
				
				$replace = call_user_func(array($tag, 'init'), $formatedAttributes);
				
				$html = str_replace($originalTag, $replace, $html);
				
				$pending[$tag] = true;
			}
		}
		
		if (is_array($pending) && !empty($pending))
		{
			foreach ($pending as $obj => $true)
			{
				if (method_exists($obj, 'end'))
				{
					$html .= call_user_func(array($obj, 'end'));
				}
			}
		}
		return $html;
	}
	
	
	public function gallery($data)
	{
		return customTags::gallery($data);
	}
	
	public function MD()
	{
		if (!$this->metadata)
		{
			if(!file_exists('./sites/default/modules/metadata/MD_repository.inc'))
			{
				return false;
			}
			else
			{
				require_once './sites/default/modules/metadata/MD_repository.inc';
				
				$this->metadata = new MD_repository();
			}
		}
		
		return $this->metadata;
	}
}