<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 18, 2012
 */
 
class htmlOut
{
	private $out;
	
	
	public function __construct(Out $out)
	{
		$this->out = $out;
	}
	
	public function metadata($no_og = false)
	{
		$html = "\n\t" . '<title>' . $this->out->getPageData('title') . '</title>' .
				"\n\t" . '<meta name="description" content="' . $this->out->getPageData('description') . '" />' .
				"\n\t" . '<meta name="keywords" content="' . $this->out->getPageData('keywords') . '" />' .
				"\n\t" . '<meta lang="' . $this->out->getPageData('lang') . '" />' .
				"\n\t" . '<meta name="viewport" content="width=device-width, initial-scale=1.0">';

		if (!$no_og)
		{
			$html .= "\n\n\t<!-- Open Graph metadata -->" .
					"\n\t" . '<meta property="og:title" content="' . $this->out->getPageData('title') . '" />' .
					"\n\t" . '<meta property="og:description" content="' . $this->out->getPageData('description') . '" />' .
					"\n\t" . '<meta property="og:url" content="' . $this->out->getPageData('url') . '" />' .
					( $this->out->getPageData('image') ? "\n\t" . '<meta property="og:image" content="' . $this->out->getPageData('image') . '" />' : '' );
		}
				
		$html .= "\n\n\t<!-- Feed links -->" .
				"\n\t" . '<link rel="alternate" type="application/rss+xml" title="RSS" href="/feed/rss" />' .
				"\n\t" . '<link rel="alternate" type="application/atom" title="RSS" href="/feed/atom" />';
		
		return $html;
	}
	
	public function metadata_dc()
	{
		$html =  "\n\t<!-- Dublin Core metadata -->" .
				"\n\t" . '<meta name="DC.type" content="Text" />' .
				"\n\t" . '<meta name="DC.format" content="text/html" />' .
				"\n\t" . '<meta name="DC.identifier" scheme="URI" content="' . $this->out->getPageData('url') . '" />' .
				"\n\t" . '<meta name="DC.title" content="' . $this->out->getPageData('title') . '" />' .
				"\n\t" . '<meta name="DC.description" content="' . $this->out->getPageData('description') . '" />' .
				"\n\t" . '<meta name="DC.language" scheme="RFC3066" content="' . $this->out->getPageData('lang') . '" />' .
				( $this->out->getPageData('author') ? "\n\n" . '<meta name="DC.creator" content="' . $this->out->getPageData('author') . '" />' : '') .
				( $this->out->getPageData('publisher') ? "\n\n" . '<meta name="DC.publisher" content="' . $this->out->getPageData('publisher') . '" />' : '') .
				"\n\t" . '<meta name="DC.subject" scheme="RFC3066" content="' . $this->out->getPageData('keywords') . '" />' . 
				( $this->out->getPageData('date') ? "\n\n" . '<meta name="DC.date" scheme="W3CDTF" content="' . $this->out->getPageData('date') . '" />' : '')
				;
		return $html;
	}
	
	public function menu($menu = false, $class = false, $data = false)
	{
		if ($menu)
		{
			$menu_arr = $this->out->getMenu($menu);
		}
		else if ($data)
		{
			$menu_arr = $data;
		}
		else 
		{
			error_log('No menu or data to display');
			return false;
		}
		
		if (!is_array($menu_arr))
		{
			error_log('Menu ' . $menu . ' not found');
			return false;
		}
		
		
		$html = '<ul class="' . ( $menu ? 'menu ' . $class . ' ' . $menu : 'submenu dropdown-menu' ) . '">';
		
		foreach ($menu_arr as $menu)
		{
			if ($menu['item'] == '.' && $menu['href'] == '.')
			{
				$html .= '<li class="divider"></li>';
			}
			else
			{
				$html .= '<li class="menu-item ' . ($menu['sub'] ? ' dropdown ' : '') . ($menu['current'] ? 'active' : '') . '">'
						. '<a href="' . link::format($menu['href'], $this->out->session_lang) . '" ' 
								. ($menu['title'] ? ' title="' . $menu['title'] . '" ' : '')
								. ($menu['target'] ? ' target="' . $menu['target'] . '" ' : '')
								. ($menu['sub'] ? ' class="dropdown-toggle" data-toggle="dropdown"' : '')
								. '>' 
								. $menu['item']
								. ($menu['sub'] ? ' <b class="caret"></b>' : '')
						. '</a>';
			}
				
			if ($menu['sub'])
			{
				$html .= $this->menu(false, false, $menu['sub']);
			}
			$html .= '</li>';
		}
		$html .= '</ul>';
		
		return $html;
	}
	
	public function langMenu($flags = false, $no_text = false)
	{
		$lang_arr = $this->out->getLanguages();
		
		$html = '<ul class="menu lang">';
		
		foreach ($lang_arr as $lang)
		{
			$flag_html = '<img src="./img/flags/' . $lang['code'] . '.png"  alt="' . $lang['string'] . '"  /> ';
		
			$html .= '<li ' . ($lang['is_current'] ? 'class="current" ' : '') . '><a '
					. ' data-ajax="false" '
					. ' href="' . $lang['href'] . '" '
					. ' title="' . $lang['string'] . '">'
					. ($flags ? $flag_html : '')
					. ($no_text ? '' : $lang['string'] )
					. '</a></li>';
		}
		$html .= '</ul>';
		
		return $html;
	}
	
	public function searchForm()
	{
		$html = '<form action="javascript:void(0);" id="searchForm">'
				. '<input class="search-query" type="search" placeholder="' . tr::get('search_site') . '" name="search" id="search" />'
			. '</form>';
		return $html;
	}
	
	public function searchResults()
	{
		$art_list = $this->out->getSearchResults();
		
		$tot_found = count($art_list);
		
		if ( $tot_found == 0 )
		{
			$html = '<h4>' . sprintf(tr::get('no_result_for_query'), $this->out->getSearchString()). '</h4>';
		}
		else
		{
			$html = '<h4>' . sprintf(tr::get('x_results_for_query'), $this->out->getSearchString(), $tot_found) . '</h4>';
			
			$html .= '<section class="blog search">';
				
			foreach ( $art_list as $art )
			{
				$html .= '<article>'
						. ($art['title'] ? ' <h1><a href="' . $art['link']  . '">' . $art['title'] . '</a></h1>' : '')
						. '<div class="content">' . $art['summary'] . '</div>'
						. '<div class="read_more">'
							. '<a href="' . $art['link']  . '">' . tr::get('read_more') . '</a>'
						. '</div>'
					. '</article>';
			}
			
			$html .= '</section>';
		}
		return $html;
	}
	
	public function articleBody($get = false)
	{
		$art = $this->out->getArticle();
		
		if ($art)
		{
			$html = '<section>'
						. '<article>'
							. '<h1>' . $art['title'] . '</h1>'
							. '<div class="content">' . $art['text'] . '</div>'
						. '</article>'
					. '</section>';
		}
		else
		{
			$html = '<section class="error">' . tr::get('article_does_not_exist') . '</section>';
		}
		return $html;
	}
	
	public function tagBlog()
	{
		$art_list = $this->out->getArticlesByTag(func_get_args());
		
		if (is_array($art_list))
		{
			$html = '<section class="blog tags">';
				
			foreach ( $art_list as $art )
			{
				$html .= '<article>'
						. ($art['title'] ? ' <h3><a href="' . $art['link']  . '">' . $art['title'] . '</a></h3>' : '')
						. '<div class="content">' . $art['summary'] . '</div>'
						. '<div class="read_more">'
							. '<a href="' . $art['link']  . '">' . tr::get('read_more') . '</a>'
						. '</div>'
						. '</article>';
			}
		
			$html .= '</section>';
		}
		return $html;
	}
	
	public function sectionBlog($section = false, $tags = false)
	{
		
		if (is_array($this->out->get['tags']))
		{
			$this->art_list = $this->out->getArticlesBySectionAndTags($section, $tags);
		}
		else 
		{
			$art_list = $this->out->getArticlesBySection($section);
		}
		
		$html = '';
		
		if (($art_list['head']['title'] && $art_list['head']['text']) || is_array($art_list['list']))
		{
			$html .= '<section class="section blog ' . ( $section ? $section : $this->out->getSectionName() ) . '">';
			$close_section = true;
		}
		
		if (is_array($art_list['head']))
		{
			if ($art_list['head']['title'] && $art_list['head']['text'])
			{
				$html .= '<article class="head">' .
					( $art_list['head']['title'] ? '<h1>'  . $art_list['head']['title'] . '</h1>' : '') .
					
					$this->out->parseContent($art_list['head']['text']) . 
					'</article>';
			}
			
		}
		
		if (is_array($art_list['list']))
		{
			foreach ( $art_list['list'] as $art )
			{
				$html .= '<article class="list">'
						. ($art['title'] ? ' <h3><a href="' . $art['link']  . '">' . $art['title'] . '</a></h3>' : '')
						. '<div class="content">' . $art['summary'] . '</div>'
						. '<div class="read_more">'
							. '<a href="' . $art['link']  . '">' . tr::get('read_more') . '</a>'
						. '</div>'
					. '</article>';
			}
		}
		else
		{
			$html = '<div class="error">' . tr::get('no_articles_in_section'). '</div>';
		}
		
		if ($close_section)
		{
			$html .= '</section>';
		}
		
		return $html;
	}
	
	public function siblingsBlog()
	{
		$art_list = $this->out->getArticleSiblings();
	
		if (!is_array($art_list))
		{
			return false;
		}
		
		$html = '<section class="blog siblings">'
				. '<h3>' . tr::get('from_same_section') . '</h3>';
			
		foreach ( $art_list as $art )
		{
			$html .= '<article>'
					. ($art['title'] ? ' <h3><a href="' . $art['link']  . '">' . $art['title'] . '</a></h3>' : '')
					. '<div class="content">' . $art['summary'] . '</div>'
					. '<div class="read_more">'
						. '<a href="' . $art['link']  . '">' . tr::get('read_more') . '</a>'
					. '</div>'
				. '</article>';
		}
		$html .= '</section>';
		
		return $html;
	}
	
	public function GA($id = false, $restrict_domain = false)
	{
		$id = $id ? $id : cfg::get('ga_id');
		
		$restrict_domain = $restrict_domain ? $restrict_domain : cfg::get('ga_domain');
		
		
		if (
				id && (
				!$restrict_domain || (preg_match('/' . $restrict_domain . '/', $_SERVER['HTTP_HOST']) )
				)
			)
		{
			$html = '<script type="text/javascript">'
					. 'var _gaq = _gaq || [];'
					. "_gaq.push(['_setAccount', '" . $id . "']);"
					. " _gaq.push(['_trackPageview']);"
					. "(function() {"
					. "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;"
					. "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';"
					. "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);"
					. '})();'
				. '</script>';
		}
		
		return $html;
	}
	
	public function gallery($gal, $rel = false, $class = false)
	{
		if (!file_exists(GALLERY_DIR . $gal . '/data.json'))
		{
			return;
		}
		
		echo $this->out->gallery(array('content' => $gal, 'rel'=>$rel, 'class' => $class));
	}
		
}