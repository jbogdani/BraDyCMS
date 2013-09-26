<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Feb 16, 2013
 * @uses			utils
 * @used			cfg
 */
 
class Out
{
	private 
		$get,		// GET data
		$context,	// context: article, section, tags, search
		$data,	//
		$metadata
		;
	public 
		$session_lang, // different from $lang whitch if is the same of sys_lang is not set!
		$lang;		// current language ( = session['lang'] set in globals, same as get['lang'])
	
	/**
	 * Starts object, sets context and language data
	 * @param array $get	array of $_GET data
	 * @param string $lang	language
	 */
	public function __construct($get, $lang = false)
	{
		//Set get
		$this->get = $get;
		
		if ($this->get['tags'])
		{
			if(preg_match('/~/', $this->get['tags']))
			{
				$this->get['tag_all'] = true;
				$this->get['tags'] = utils::csv_explode($this->get['tags'], '~');
			}
			else
			{
				$this->get['tags'] = utils::csv_explode($this->get['tags'], '-');
			}
		}
		
		if ($lang AND ( $lang != cfg::get('sys_lang') ) )
		{
			$this->lang = $lang;
		}
		
		if ($lang)
		{
			$this->session_lang = $lang;
		}
		
		// Set context
		if ( $get['art_title'] )
		{
			$this->context = 'article';
			
			// load curent article's data in $data['article'] array
			$this->data['article'][$get['art_title']] = Article::getByTextid($get['art_title'], $this->lang, $this->get['draft']);
		}
		else if ( $get['search'] )
		{
			$this->context = 'search';
		}
		else if ( $get['tags'] )
		{
			$this->context = 'tags';
		}
		else
		{
			$this->context = 'home';
		}
	}
	
	
	/**
	 * Returns full link to provided content (article or section)
	 * 
	 * @param array|string $art	article or section name
	 * @param boolean $tags	arrbay of tags to use for filtering section articles
	 * @return string
	 */
	public function link2($art, $tags = false)
	{
		return $tags ? link::to_tags($art, $this->lang) : link::to_article($art == 'home' ? './' : $art, $this->lang);
	}
	
	
	/**
	 * Returns array of currently used tags. 
	 * @return array|string
	 */
	public function getFilterTags()
	{
		return $this->get['tags'];
	}
	
	/**
	 * Returns array of tags used by current article
	 * @return array|false
	 */
	public function getArticleTags()
	{
		if ($this->context !== 'article')
		{
			return false;
		}
		
		if (!$this->data['article'][$article]['tags'])
		{
			$art = $this->getArticle();

			if (!$art->id)
			{
				return false;
			}


			$this->data['article'][$article]['tags'] =  R::tag($art);
		}
		
		return $this->data['article'][$article]['tags'];
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
	 * Returns array of similar articles
	 * @param string $article articles textid
	 * @return array of beans|false
	 */
	public function getSimilar($textid = false)
	{
		$article = $this->getArticle($textid);
		
		return Article::getSimilar($article['tags'], $article['textid'], $this->lang);
	}

		/**
	 * Returns array with article data. If $article is not provided current article's data will be returned
	 * @param string $article article's textid
	 * @return array
	 */
	public function getArticle($article = false, $draft = false)
	{
		$draft = $draft ? $draft : $this->get['draft'];
		
		if (!$article)
		{
			$article = $this->get['art_title'];
		}
		
		if ($article && $article != $this->get['art_title'])
		{
			$this->data['article'][$article] = Article::getByTextid($article, $this->lang, $draft);
		}
		
		if (empty($this->data['article'][$article]))
		{
			return false;
		}
		
		if ($this->data['article'][$article]['text'])
		{
			$this->data['article'][$article]['text'] = $this->parseContent($this->data['article'][$article]['text']);
		}
		
		return $this->data['article'][$article];
	}
	

	/**
	 * Returns list of articles filtered by one or more tags. The tags can be specified as method arguments, if not current GET tags will be used
	 * @param $tag, $tag, if present, the first and other parameters are assumed to be the tags.
	 * @return array|false
	 */
	public function getArticlesByTag()
	{
		$tags = func_get_args() ? func_get_args() : $this->getFilterTags();
		
		return Article::getByTag($tags, $this->lang, $this->get['tag_all']);
	}
	
	/**
	 * Return list of articles filtered by one or more tags
	 * @param array $tags array of tags
	 * @return type
	 */
	protected function getArticlesByTagArray($tags)
	{
		return Article::getByTag($tags, $this->lang);
	}
	
	/**
	 * Returns metadata about the current page, filtered by $el
	 * @param string $el page element to return
	 * @return string|array
	 */
	public function getPageData($el = false)
	{
		if (!$this->data['page'])
		{
			$this->setPageData();
		}
	
		return $el ? $this->data['page'][$el] : $this->data['page'];
	}
	
	
	/**
	 * Sets $data['page'] info
	 */
	private function setPageData()
	{
		// Title
		$this->data['page']['title'] = $this->data['article'][$this->get['art_title']]['title'] ? $this->data['article'][$this->get['art_title']]['title'] : cfg::get('title');
		
		// Description
		if ( $this->data['article'][$this->get['art_title']]['summary'] && trim(strip_tags($this->data['article'][$this->get['art_title']]['summary'])) != '' )
		{
			$this->data['page']['description'] = trim(strip_tags($this->data['article'][$this->get['art_title']]['summary']));
		}
		else
		{
			$this->data['page']['description'] = ($this->data['article'][$this->get['art_title']]['section'] ? $this->data['article'][$this->get['art_title']]['section'] . ' :: ' : '') . cfg::get('description');
		}
			
		if (strlen($this->data['page']['description']) > 500)
		{
			$this->data['page']['description'] = substr($this->data['page']['description'], 0, 497) . '...';
		}
		
		// Keywords
		$this->data['page']['keywords'] = $this->data['article'][$this->get['art_title']]['keywords'] ? $this->data['article'][$this->get['art_title']]['keywords'] : cfg::get('keywords');
		
		// Lang
		$this->data['page']['lang'] = $this->lang ? $this->lang: cfg::get('sys_lang');
			
		// Url
		$this->data['page']['url']  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			
		// Mission
		$this->data['page']['mission'] = cfg::get('mission');
			
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
			
		if ($this->data['article'][$this->get['art_title']]['publish'] && $this->data['article'][$this->get['art_title']]['publish'] != '0000-00-00')
		{		
			$this->data['page']['date'] = $this->data['article'][$this->get['art_title']]['publish'];
		}
	}
	
	/**
	 * Returns structured array of menu data for $menu_name.
	 * @param string $menu_name menu name to retrieve from the database
	 * @return array|false
	 */
	public function getMenu($menu_name)
	{
		if (!$this->data['menu'][$menu_name])
		{
			$tmp = Menu::get_structured_menu($menu_name, $this->lang);
			
			$this->data['menu'][$menu_name] = $this->recursiveRichMenu($tmp);
			
		}
		return $this->data['menu'][$menu_name];
	}
	
	/**
	 * Private function, used by getMenu to structure sub menu items in main menu list
	 * @param string $menu
	 * @return array
	 */
	private function recursiveRichMenu($menu)
	{
		if (!is_array($menu))
		{
			return false;
		}
		
		foreach ($menu as &$item)
		{
			if ($this->get['art_title'] && $this->get['art_title'] == $item['href'])
			{
				$item['current'] = true;
			}
			else if ($this->context == 'home' && ( $item['href'] == './' || $item['href'] == ''))
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
	
	/**
	 * Returns structured array of available system languages
	 * @return array
	 */
	public function getLanguages()
	{
		if (!$this->data['languages'])
		{
			$this->data['languages'] = utils::getLanguages($this->session_lang);
		}
		return $this->data['languages'];
	}
	
	/**
	 * Returns list of articles containing the searched string 
	 * @return array|false
	 */
	public function getSearchResults()
	{
		return Article::search($this->get['search'], $this->lang);
	}
	
	/**
	 * Returns, if available, the searched string
	 * @return string|false
	 */
	public function getSearchString()
	{
		return $this->get['search'];
	}
	
	
	/**
	 * 
	 * @param string $html parses html string and checks for custom tags, both system and user's. Runs functions and replaces string content.
	 * @return string
	 */
	private function parseContent($html)
	{
    //return customTagParser::do_shortcode($html);
		preg_match_all('/\[\[(\w+)\s*([^\]]*)\]\](.*?)\[\[\/\1\]\]/s', $html, $customTags, PREG_SET_ORDER);
		
    foreach ($customTags as $customTag)
		{
      $attributes = array();
			$rawAttributes = array();
			$formatedAttributes = array();
			
			$originalTag = '/' . preg_quote($customTag[0], '/') . '/';
		
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
		
				$html = preg_replace($originalTag, $replace, $html, 1);
			}
			else
			{
        if (method_exists($tag, 'init'))
        {
          $replace = call_user_func(array($tag, 'init'), $formatedAttributes);
          
          $html = preg_replace($originalTag, $replace, $html, 1);
          
          $pending[$tag] = true;
        }
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
	
	/**
	 * Returns, if available, a new instance of object M(eta)D(ata)_repository
	 * @return boolean|MD_repository
	 */
	public function getMD()
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