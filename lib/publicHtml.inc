<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			01/mar/2011
 * @uses			globals.inc
 * @uses			class.db.inc
 * @uses			class.ArticleRead.inc
 */

class publicHtml extends ArticleRead implements frontend 
{
	/**
	 *
	 * Array of default configuration settings
	 * @var array
	 */
	private $cfg;

	/**
	 *
	 * Language to be uses; will be set ONLY if user language is different from system language
	 * @var string
	 */
	private $lang;

	/**
	 *
	 * Object with output data
	 * 	out->context: [home | search]
	 * 	out->
	 * @var object
	 */
	private $out;

	/**
	 * (non-PHPdoc)
	 * @see frontend::mainSetter()
	 */
	public function mainSetter( $get, $lang = false )
	{
		global $cfg;
		
		try
		{
			// set language
			if ($lang AND ( $lang != $cfg['sys_lang'] ) )
			{
				$this->lang = $lang;
			}
	
			//set default configiguration
			$this->cfg = $cfg;
			
			/**
			 * SET CONTEXT ETC
			 */
	
			// context = article
			if ( $get['art'] OR $get['art_title'] )
			{
				$this->out['context'] = 'article';
				
				$this->out['get_id'] = $get['art']  ? $get['art'] : $get['art_title'];
					
				$this->out['article'] = $this->get_article_by_id ( $get['art'], $get['art_title'], $this->lang );
				
				$this->out['section_name'] = $this->out['article']['section'];
			}
	
			// context = section
			else if ( $get['section'] )
			{
				$this->out['context'] = 'section';
					
				$this->out['get_id'] = $get['section'];
				
				$this->out['section'] = $this->get_articles_by_section($get['section']);
				
				$this->out['section_name'] = $get['section'];
			}
	
			// search
			else if ($get['search'])
			{
				$this->out['context'] = 'search';

				$this->out['get_id'] = $get['search'];
				
				$this->out['search'] = $this->search($get['search']);
					
				$this->out['searchTerm'] = $get['search'];
			}
				
			// out->context = HOME
			else if ( !$get['art'] AND !$get['art_title']  AND !$get['section'] AND !$get['search'] )
			{
				$this->out['context'] = 'home';
			}
			
			/**
			 * flag to check if language is present in url
			 */
			$this->cfg['url_lang'] = $get['lang'] ? true : false;
			
		}
		catch (MyExc $e)
		{
			echo utils::tr('Qualcosa è andato storto. Controllare contattare l\'amministrattore per maggiori informazioni');
			$e->log();
		}


	}
	
	/**
	 * (non-PHPdoc)
	 * @see frontend::getSection()
	 */
	public function getSection()
	{
		if ($this->out['section_name'])
		{
			return $this->out['section_name'];
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see frontend::getContext()
	 */
	public function getContext()
	{
		return $this->out['context'];
	}
	
	/**
	 * 
	 * Returned requested string (article, section, or searched string)
	 */
	public function getId()
	{
		return $this->out['get_id'];
	}

	/**
	 * (non-PHPdoc)
	 * @see frontend::getMeta()
	 */
	public function getMeta( $el, $html = false )
	{
		switch ( $el )
		{
			case 'title':
				if ( $this->out['article']['title'] )
				{
					$title = $this->out['article']['title'];
				}
				else
				{
					$title = $this->cfg['title'];
				}
				
				return $html ? ' <title>' . $title . ' </title>' : $title;
				break;

			case 'description':
			case 'summary':
				if ( $this->out['article']['summary'] )
				{
					$description = strip_tags($this->out['article']['summary']);
				}
				else if ( $this->out['section']['title'] )
				{
					$description = $this->out['section']['title'];
				}
				else
				{
					$description = $this->cfg['description'];
				}
				
				return $html ? '<meta name="description" content="' . $description . '" />' : $description;
				break;

			case 'keywords':
				if ( $this->out['article']['keywords'] )
				{
					$keywords = $this->out['article']['keywords'];
				}
				else
				{
					$keywords = $this->cfg['keywords'];
				}
				
				return $html ? '<meta name="keywords" content="' . $keywords . '" />' : $keywords;
				break;
				
			case 'lang':
				$l = utils::getLang();
				if($html AND $l)
				{
					return '<meta lang="' . $l . '" />';
				}
				else if ($l)
				{
					return $l;
				}
				break;
		}
	}

	/**
	 *
	 * if fiendly_url is active formats links strings to match system requirements for friendly urls
	 * @param string $href link string to format
	 */
	private function format_link( $href )
	{
		if ($href == './' || preg_match('/http:\/\/(.+)/', $href))
		{
			return $href;
		}
		if (!preg_match('/lang=/', $href) AND $_SESSION['lang'])
		{
			if ($href != '#' AND !preg_match('/\?/', $href))
			{
				$href .= '?';
			}
			if ($href != '#')
			{
				$href .= '&lang=' . $_SESSION['lang'];
			}
		}
		if ( !$this->cfg['friendly_url'])
		{
			return $href;
		}
		else
		{
			
			$path = $this->cfg['url_lang'] ? '..' : '.';
			$pattern = array (
			
			//lang support
				'/index\.php\?section=([0-9a-zA-Z_-]+)&lang=([a-zA-Z]{2})/',
				'/\.?\/?\?section=([0-9a-zA-Z_-]+)&lang=([a-zA-Z]{2})/',
				
				
				'/index\.php\?art=([0-9]+)&lang=([a-zA-Z]{2})/',
				'/\.?\/?\?art=([0-9]+)&lang=([a-zA-Z]{2})/',
				
				'/index.php\?art_title=([0-9a-zA-Z_-]+)&lang=([a-zA-Z]{2})/',
				'/\.?\/?\?art_title=([0-9a-zA-Z_-]+)&lang=([a-zA-Z]{2})/',
				
			// no lang
				'/index\.php\?section=([0-9a-zA-Z_-]+)/',
				'/\.?\/?\?section=([0-9a-zA-Z_-]+)/',
				
				
				'/index\.php\?art=([0-9]+)/',
				'/\.?\/?\?art=([0-9]+)/',
				
				'/index\.php\?art_title=([0-9a-zA-Z_-]+)/',
				'/\.?\/?\?art_title=([0-9a-zA-Z_-]+)/',
				
			// homepage
				'/\.\/\?lang=([a-zA-Z]{2})/',
				'/index\.php\?lang=([a-zA-Z]{2})/'
				);

				
			$replacement = array (
				
				// lang support
				$path . '/$2/$1.all', // ex.: ../en/news.all
				$path . '/$2/$1.all', // ex.: ./en/news.all
					
				$path . '/$2/id-$1', // ex.: ../en/id-5
				$path . '/$2/id-$1',	// ex.: ../en/id-5

				$path . '/$2/$1', // ex: ../en/about
				$path . '/$2/$1', // ex: ../en/about
				
				// no lang
				'$1.all', // ex.: ../news.all
				'$1.all', // ex.: ./news.all
					
				'id-$1', // ex.: ../id-5
				'id-$1', // ex.: ./id-5

				'$1', //ex: /about
				'$1', //ex: /about
					
				$path . '/$1/', // ex.: ../en
				$path . '/$1/' // ex.: ../en
				);
					
				return preg_replace($pattern, $replacement, $href);
		}
	}
	
	/**
	 * 
	 * Returns formatted HTML for menu items with or without submenus
	 * HTML structure: ul[.menu menu_name] > li[(.selected)] > a[href, title, target] > string (ul[.submenu itemname] >li[(.selected)] > a[href, title, target] > string)
	 * @param string $menu_to_show	menu name to show up
	 * @param string $item			item name to show up submenu for
	 * @param boolean $recursive	flag to controle submenu displaying
	 * @param array $tags			tags to add to ul element
	 */
	private function recursive_print_menu_items($menu_to_show, $item = false, $recursive = false, $tags = false)
	{
		$menu = new Menu();
		
		$res = $menu->get_menu_items($menu_to_show, $item, $this->lang);
		
		if (!$res OR count($res) == 0)
		{
			return false;
		}
		
		$html = '<ul ';
		if (is_array($tags))
		{
			foreach ($tags as $tag=>$value)
			{
				if ($tag != 'class')
				{
					$html .= ' ' . $tag . '="' . $value . '"';
				}
			}
		} 
		$html .= 'class="' . ($item ? 'submenu ' . $item : ' menu ' . $menu_to_show) . ' ' . $tag['class'] . '">';
		
		foreach ( $res as $item_arr )
		{
			$flag_selected = false;
			
			if ($_REQUEST['art_title'] AND preg_match('/art_title=' . $_REQUEST['art_title'] . '/', $item_arr['href']))
			{
				$flag_selected = 1;
			}
			else if ($_REQUEST['section'] AND preg_match('/section=' . $_REQUEST['section'] . '/', $item_arr['href']))
			{
				$flag_selected = 1;
			}
			
			else if ($_REQUEST['art'] AND preg_match('/section=' . $_REQUEST['art'] . '/', $item_arr['href']))
			{
				$flag_selected = 1;
			}
			$html .= '<li'. ( $flag_selected ? ' class="selected" ' : '' ) . '>'
			. '<a href="' . $this->format_link ( $item_arr['href'] ) . '" '
			. ( $item_arr['title'] ? ' title="' . $item_arr['title'] . '" ' : '')
			. ( $item_arr['target'] ? ' target="' . $item_arr['target'] . '" ' : '' )
			. '>'
			. $item_arr['item']
			. '</a>'
			. ($recursive ? $this->recursive_print_menu_items($menu_to_show, $item_arr['id'], true) : '')
			. '</li>';
		}

		$html .= '</ul>';
		
		return $html;
	}

	/**
	 * (non-PHPdoc)
	 * @see frontend::printMenu()
	 */
	public function printMenu( $menu_to_show, $show_sub = false, $tags = false )
	{
		try
		{
			echo $this->recursive_print_menu_items($menu_to_show, false, $show_sub, $tags);
		}
		catch (MyExc $e)
		{
			$e->log();
			printf(utils::tr('menu_error'), $menu_to_show);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see frontend::printsubMenu()
	 */
	public function printsubMenu($item, $show_sub = false)
	{
	try
		{
			echo $this->recursive_print_menu_items(false, $item, $show_sub);
		}
		catch (MyExc $e)
		{
			$e->log();
			printf(utils::tr('sub_menu_error'), $menu_to_show);
		}
		
	}


	/**
	 * (non-PHPdoc)
	 * @see frontend::printSection()
	 */
	public function printSection( $section )
	{
		try
		{
			$art_array = $this->get_articles_by_section( $section );
	
			if (is_array($art_array['article']))
			{
				echo $this->returnSection($art_array['article'], true);
			}
		}
		catch (MyExc $e)
		{
			$e->log();
			printf(utils::tr('section_list_error'), $menu_to_show);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see frontend::printArticleBody()
	 */
	public function printArticleBody( $art_id = false, $art_title = false )
	{
		try
		{
			if ( !$art_id AND !$art_title )
			{
				return false;
			}
			else
			{
				$article = $this->get_article_by_id ( $art_id, $art_title, $this->lang );
					
				echo $article['text'];
			}
		}
		catch (MyExc $e)
		{
			$e->log();
			echo utils::tr('article_error');
		}

	}

	/**
	 *
	 * Returns HTML with all artices in art_array
	 * HTML structure
	 * 		section[.blog]	> article > h1 (title)
	 * 						> div[.content] > text (summary)
	 * 						> div[.read_more] > a > text (leggi tutto)
	 * @param array $art_array	array of articles to print out
	 */
	private function returnSection ( $art_array, $no_blog = false )
	{
		$html = '<section ' . ($no_blog ? '' : ' class="blog" ') . '>';
			
		foreach ( $art_array as $art )
		{
			$html .= '<article>'
			. '		<h1>' . $art['title'] . '</h1>'
			. '		<div class="content">' . $art['summary'] . '</div>'
			. '		<div class="read_more"><a href="' . $this->format_link( '?art_title=' . $art['text_id']  ) . '">' . utils::tr('read_more') . '</a></div>'
			. '</article>';
		}

		$html .= '</section>';

		return $html;
	}

	/**
	 * (non-PHPdoc)
	 * @see frontend::printBody()
	 */
	public function printBody ( )
	{
		try
		{
			$html = '';
	
			if ( $this->out['context'] == 'section')
			{
				if ( !$this->out['section']['article'] )
				{
					$html = '<section class="error">' . utils::tr('no_articles_in_section'). '</section>';
				}
				else
				{
					$html = $this->returnSection( $this->out['section']['article'] );
				}
			}
			else if ( $this->out['context'] == 'article' )
			{
				if ( $this->out['article'] )
				{
					$html = '<section>'
					
					. '<article>'
					
					. '<h1>' . $this->out['article']['title'] . '</h1>'
						
					. '<div class="content">' . $this->out['article']['text'] . '</div>'
						
					. '</article>'
					
					. '</section>';
	
				}
				else
				{
					$html = '<section class="error">' . utils::tr('article_does_not_exist') . '</section>';
				}
			}
	
			echo $html;
		}
		catch (MyExc $e)
		{
			$e->log();
			echo utils::tr('article_error');
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see frontend::printSiblings()
	 */
	public function printSiblings ()
	{
		try
		{
			if ($this->out['article']['id'])
			{
				$list = $this->getSiblings( $this->out['article']['id'], $this->lang );
	
				if ( $list )
				{
					echo '<hr /><div class="siblings"><h3>' . utils::tr('from_same_section'). ':</h3>' . $this->returnSection($list) . '</div>';
				}
			}
		}
		catch (MyExc $e)
		{
			$e->log();
			echo utils::tr('siblings_error');
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see frontend::printSearch()
	 */
	public function printSearch()
	{
		$html = '<form action="javascript:void(0);" id="searchForm">'
		. '<input class="search" type="text" placeholder="' . utils::tr('search_site') . '" name="search" id="search" />'
		. '</form>'
		. '<script type="text/javascript">'
		. "$('#searchForm').submit(function(){"
		. "if($('#search').val() != '' ){"
		. "window.location = './search:' + $('#search').val();"
		. "}"
		. "});"
		. '</script>';

		echo $html;
	}

	/**
	 * (non-PHPdoc)
	 * @see frontend::printSearchResult()
	 */
	public function printSearchResult()
	{
		try
		{
			$tot_found = count($this->out['search']);
	
			if ( $tot_found == 0 )
			{
				echo '<h2>' . sprintf(utils::tr('no_result_for_query'), $this->out['searchTerm']). '</h2>';
			}
			else
			{
				echo '<h2>' . sprintf(utils::tr('x_results_for_query'), $this->out['searchTerm'], $tot_found) . '</h2>';
					
				echo $this->returnSection($this->out['search']);
			}
		}
		catch (MyExc $e)
		{
			$e->log();
			echo utils::tr('search_error');
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see frontend::printLangSelect()
	 */
	public function printLangSelect($text = false)
	{
		$html = '';

		foreach ($_GET as $k=>$v)
		{
			if ($k != 'lang')
			{
				$str[] = $k . '=' .$v;
			}
		}
		if (is_array($str))
		{
			$href = './?' . implode('&', $str) . '&lang=';
		}
		else
		{
			$href = './?lang=';
		}

		$lang_arr = array($this->cfg['sys_lang']=>$this->cfg['sys_lang_string']);


		foreach ($this->cfg['languages'] as $k=>$v)
		{
			$lang_arr[$k] = $v;
		}

		$html = '<div class="lang">';

		foreach ($lang_arr as $code=>$string)
		{
			$html .= '<a '
				. ' data-ajax="false" '
				. ' href="' . $this->format_link($href . $code) . '" '
				. ' title="' . $string. '"><img src="./css/flags/' . $code . '.png" '
				. ' alt="' . $string . '"'
				. '  /> ' 
				. ( $text ? $string : '') 
				. '</a> ';
		}
		$html .= '</div>';

		echo $html;
	}

}