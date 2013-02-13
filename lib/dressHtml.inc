<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 18, 2012
 */
 
class dressHtml
{
	private $html;
	
	
	public function __construct(publicHtml $publicHtml)
	{
		$this->html = $publicHtml;
	}
	
	public function metadata()
	{
		$html = "\n\t" . '<title>' . $this->html->getPageData('title') . ' </title>'
			. "\n\t" . '<meta name="description" content="' . $this->html->getPageData('description') . '" />'
			. "\n\t" . '<meta name="keywords" content="' . $this->html->getPageData('keywords') . '" />'
			. "\n\t" . '<meta lang="' . $this->html->getPageData('lang') . '" />';
		
		return $html;
	}
	
	public function menu($menu = false, $class = false, $data = false)
	{
		if ($menu)
		{
			$menu_arr = $this->html->getMenu($menu);
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
				$html .= '<li class="menu-item ' . ($menu['sub'] ? ' dropdown ' : '') . '">'
						. '<a href="' . utils::format_link($menu['href']) . '" ' 
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
		$lang_arr = $this->html->getLanguages();
		
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
				. '<input class="search" type="search" placeholder="' . tr::get('search_site') . '" name="search" id="search" />'
			. '</form>';

		return $html;
	}
	
	public function searchResults()
	{
		if ($this->html->getContext() != 'search')
		{
			return false;
		}
		$art_list = $this->html->getArticleList();
		
		$tot_found = count($art_list);
		
		if ( $tot_found == 0 )
		{
			$html = '<h1>' . sprintf(tr::get('no_result_for_query'), $this->html->getSearchString()). '</h1>';
		}
		else
		{
			$html = '<h1>' . sprintf(tr::get('x_results_for_query'), $this->html->getSearchString(), $tot_found) . '</h1>';
			
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
			
			return $html;
		}
	}
	
	public function articleBody($get = false)
	{
		$art = $this->html->getArticleBody($get = false);
		
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
		$art_list = $this->html->getArticlesByTag(func_get_args());
		
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
	
	public function sectionBlog($section = false)
	{
		if ($this->html->getContext() != 'section')
		{
			return false;
		}
		
		$art_list = $this->html->getArticleList($section);
		
		if (is_array($art_list))
		{
			$html = '<section class="blog section ' . ( $section ? $section : $this->html->getSectionName() ) . '">';
			
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
		else
		{
			$html = '<section class="error">' . tr::get('no_articles_in_section'). '</section>';
		}
		
		return $html;
	}
	
	public function siblingsBlog()
	{
		if ($this->html->getContext() != 'article')
		{
			return false;
		}
	
		$art_list = $this->html->getArticleList();
	
		if (is_array($art_list))
		{
			$html = '<section class="blog siblings">'
					. '<h1>' . tr::get('from_same_section') . '</h3>';
				
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
		else
		{
			return false;
		}
	}
		
}