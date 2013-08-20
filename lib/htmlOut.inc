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
		$html = "\n\t <!-- HTML metadata -->" .
				"\n\t" . '<meta http-equiv="content-type" content="text/html; charset=utf-8" />' .
				"\n\t" . '<meta name="robots" content="index, follow" />' .
				"\n\t" . '<title>' . $this->out->getPageData('title') . '</title>' .
				"\n\t" . '<meta name="description" content="' . $this->out->getPageData('description') . '" />' .
				"\n\t" . '<meta name="keywords" content="' . $this->out->getPageData('keywords') . '" />' .
				"\n\t" . '<meta lang="' . $this->out->getPageData('lang') . '" />' .
				"\n\t" . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
			;

		if (!$no_og)
		{
			$html .= "\n\n\t<!-- Open Graph metadata -->" .
					"\n\t" . '<meta property="og:title" content="' . $this->out->getPageData('title') . '" />' .
					"\n\t" . '<meta property="og:description" content="' . $this->out->getPageData('description') . '" />' .
					"\n\t" . '<meta property="og:url" content="' . $this->out->getPageData('url') . '" />' .
					( $this->out->getPageData('image') ? "\n\t" . '<meta property="og:image" content="' . $this->out->getPageData('image') . '" />' : '' )
			;
		}
				
		$html .= "\n\n\t<!-- Feed links -->" .
				"\n\t" . '<link rel="alternate" type="application/rss+xml" title="RSS" href="/feed/rss" />' .
				"\n\t" . '<link rel="alternate" type="application/atom" title="RSS" href="/feed/atom" />';
		
		return $html;
	}
	
	public function metadata_dc()
	{
		/*
		<meta name="DC.Contributor.Sponsor" xml:lang="en" content=""/>
		<meta name="DC.Contributor.Sponsor" xml:lang="it" content=""/>
		<meta name="DC.Date.dateSubmitted" scheme="ISO8601" content="2012-12-20"/>
		<meta name="DC.Date.issued" scheme="ISO8601" content="2012-12-21"/>
		<meta name="DC.Description" xml:lang="en" content="English Description"/>
		<meta name="DC.Identifier.pageNumber" content="1-46"/>
		<meta name="DC.Rights" content="I copyright degli articoli pubblicati su questa rivista appartengono agli autori, e i diritti di prima pubblicazione sono concessi alla rivista. Nel momento in cui presentano il proprio lavoro, gli autori accettano che possa essere copiato da chiunque per fini non commerciali, ma solamente nel caso in cui venga appropriatamente citato. In virtù della loro apparizione su questa rivista gratuita, gli articoli sono dichiarati usufruibili gratuitamente, con una corretta attribuzione, in contesti non commerciali. Ogni autore è responsabile per il contenuto del proprio lavoro, incluse citazioni, attribuzione e permessi d&#039;uso."/>
		<meta name="DC.Source.Issue" content="1"/>
		<meta name="DC.Source.Volume" content="5"/>
		<meta name="DC.Title.Alternative" xml:lang="en" content="The Bronze Age settlement of S. Giovanni in Triario (Bologna)"/>
		<meta name="DC.Type" content="Text.Serial.Journal"/>
		 */
		
		$art = $this->out->getArticle();
	
		if (!$art || !is_array($art) || empty($art))
		{
			return;
		}
	
		$html =  "\n\t<!-- Dublin Core metadata tags -->" .

				// Type
				"\n\t" . '<meta name="DC.Type" content="Text" />' .
				
				// Format
				"\n\t" . '<meta name="DC.Format" content="text/html" />' .
				
				// Identifier
				"\n\t" . '<meta name="DC.Identifier" content="' . $art['id'] . '"/>' .
				
				// Identifier.URI
				"\n\t" . '<meta name="DC.Identifier.URI" scheme="URI" content="' . $this->out->getPageData('url') . '" />' .
				
				// Identifier.DOI
				"\n\t" . '<meta name="DC.Identifier.DOI" content="' . $this->out->MD()->getDoiPrefix() . '.' . $art[$this->out->MD()->getTable('id')] . '" />' .
				
				// Titile
				"\n\t" . '<meta name="DC.Title" content="' . $this->out->getPageData('title') . '" />' .
				
				// Description
				"\n\t" . '<meta name="DC.Description" content="' . $this->out->getPageData('description') . '" />' .
				
				// Language
				"\n\t" . '<meta name="DC.Language" scheme="ISO639-1" content="' . $this->out->getPageData('lang') . '" />' .
				
				// Creator
				( $this->out->getPageData('author') ? "\n\t" . '<meta name="DC.Creator" content="' . $this->out->getPageData('author') . '" />' : '') .
				
				// Creator.PersonalName
				( $this->out->getPageData('author') ? "\n\t" . '<meta name="DC.Creator.PersonalName" content="' . $this->out->getPageData('author') . '" />' : '') .
				
				// Publisher
				( $this->out->MD()->getPublisher() ? "\n\t" . '<meta name="DC.Publisher" content="' . $this->out->MD()->getPublisher() . '" />' : '') .
				
				// Subject
				"\n\t" . '<meta name="DC.subject" scheme="RFC3066" content="' . $this->out->getPageData('keywords') . '" />' .

				// Date
				( $this->out->getPageData('date') ? "\n\t" . '<meta name="DC.Date" scheme="W3CDTF" content="' . $this->out->getPageData('date') . '" />' : '') .
				
				// Date.created
				( $this->out->getPageData('date') ? "\n\t" . '<meta name="DC.Date.created" scheme="ISO8601" content="' . $this->out->getPageData('date') . '" />' : '') .
				
				// Date.modified
				( $art['updated'] ? "\n\t" . '<meta name="DC.Date.modified" scheme="ISO8601" content="' . $art['updated'] . '" />' : '') .
				
				// Source
				($this->out->MD()->getRepositoryName() ? "\n\t" . '<meta name="DC.Source" content="' . $this->out->MD()->getRepositoryName() . '"/>' : '') .
				
				// Source.ISSN
				($this->out->MD()->getISSN() ? "\n\t" . '<meta name="DC.Source.ISSN" content="' . $this->out->MD()->getISSN() . '"/>' : '') .
				
				// Source.URI
				($this->out->MD()->getURL() ? "\n\t" . '<meta name="DC.Source.URI" content="' . $this->out->MD()->getURL() . '"/>' : '')
				;
		return $html;
	}
	
	public function metadata_hp()
	{
		/*
		 http://www.google.com/intl/en/scholar/inclusion.html#indexing
		 
		 <meta name="citation_volume" content="5"/>
		 <meta name="citation_issue" content="1"/>
		 <meta name="citation_firstpage" content="1-46"/>
		 */
		$art = $this->out->getArticle();
		
		$html =  "\n\t<!-- Highwire Press metadata tags -->" .
				
				// journal title
				($this->out->MD()->getRepositoryName() ? "\n\t" . '<meta name="citation_journal_title" content="' . $this->out->MD()->getRepositoryName() . '"/>' : '') .
				
				// journal issn
				($this->out->MD()->getISSN() ? "\n\t" . '<meta name="citation_issn" content="' . $this->out->MD()->getISSN() . '"/>' : '') .
				
				// article author
				( $this->out->getPageData('author') ? "\n\t" . '<meta name="citation_author" content="' . $this->out->getPageData('author') . '" />' : '') .
				
				// article title
				"\n\t" . '<meta name="citation_title" content="' . $this->out->getPageData('title') . '" />' .
				
				// article date
				( $this->out->getPageData('date') ? "\n\t" . '<meta name="citation_date" content="' . $this->out->getPageData('date') . '" />' : '') .
				
				// article doi
				"\n\t" . '<meta name="citation_doi" content="' . $this->out->MD()->getDoiPrefix() . '.' . $art[$this->out->MD()->getTable('id')] . '"/>' .
		
				// article abstract url
				"\n\t" . '<meta name="citation_abstract_html_url" content="' . $this->out->getPageData('url') . '"/>' .
				
				// article language
				"\n\t" . '<meta name="citation_language" content="' . $this->out->getPageData('lang') . '" />' .
		
				// article pdf url
				(file_exists('./sites/default/images/pdf/' . $art['text_id'] . '.pdf') ?  
					"\n\t" .  '<meta name="citation_pdf_url" content="' . $this->out->MD()->getURL() . '/sites/default/images/pdf/' . $art['text_id'] . '.pdf"/>'
					: '')
	
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
		
		if (!is_array($menu_arr) || empty($menu_arr))
		{
			error_log('Menu <' . $menu . '> not found');
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
		$html = '<form action="javascript:void(0);" id="searchForm" data-path="' . $this->out->link2('home') . '">'
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
						. ($art['title'] ? ' <h1><a href="' . $art['url']  . '">' . $art['title'] . '</a></h1>' : '')
						. '<div class="content">' . $art['summary'] . '</div>'
						. '<div class="read_more">'
							. '<a href="' . $art['url']  . '">' . tr::get('read_more') . '</a>'
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
			$html = '<section>' .
					'<article>' .
						'<h1>' . $art['title'] . '</h1>' .
						'<div class="content">' . $art['text'] . '</div>' .
					'</article>' .
				'</section>';
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
				$html .= '<article>' .
							($art['title'] ? ' <h3><a href="' . $art['url']  . '">' . $art['title'] . '</a></h3>' : '') .
							'<div class="content">' . $art['summary'] . '</div>' .
							'<div class="read_more">' .
								'<a href="' . $art['url']  . '">' . tr::get('read_more') . '</a>' .
							'</div>' .
						'</article>';
			}
		
			$html .= '</section>';
		}
		return $html;
	}
	
	public function sectionBlog($section = false, $tags = false)
	{
		
		if (is_array($this->out->sectionHasTags))
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
						. ($art['title'] ? ' <h3><a href="' . $art['url']  . '">' . $art['title'] . '</a></h3>' : '')
						. '<div class="content">' . $art['summary'] . '</div>'
						. '<div class="read_more">'
							. '<a href="' . $art['url']  . '">' . tr::get('read_more') . '</a>'
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
					. ($art['title'] ? ' <h3><a href="' . $art['url']  . '">' . $art['title'] . '</a></h3>' : '')
					. '<div class="content">' . $art['summary'] . '</div>'
					. '<div class="read_more">'
						. '<a href="' . $art['url']  . '">' . tr::get('read_more') . '</a>'
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
		
		echo customTags::gallery(array('content' => $gal, 'rel'=>$rel, 'class' => $class));
	}
	
	public function ct($method, $param = false)
	{
		if ($param)
		{
			$param = json_decode($param, true);
		}
		if (method_exists('customTags', $method))
		{
			$html = call_user_func(array('customTags', $method), $param);
		}
		else if (file_exists('sites/default/modules/' . $method . '/' . $method. '.inc'))
		{
			require_once 'sites/default/modules/' . $method . '/' . $method . '.inc';
	
			$html = call_user_func(array($method, 'init'), $param);
	
		}
	
		echo $html;
			
	}
		
}