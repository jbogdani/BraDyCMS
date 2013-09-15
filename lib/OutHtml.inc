<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 18, 2012
 */
 
class OutHtml extends Out
{
	
	/**
	 * Returns html with general and Open graph metadata (if $no_og is false).
	 * @param boolean $no_og
	 * @return string
	 */
	public function metadata($no_og = false)
	{
		$html = "\n\t <!-- HTML metadata -->" .
				"\n\t" . '<meta http-equiv="content-type" content="text/html; charset=utf-8" />' .
				"\n\t" . '<meta name="robots" content="index, follow" />' .
				"\n\t" . '<title>' . $this->getPageData('title') . '</title>' .
				"\n\t" . '<meta name="description" content="' . $this->getPageData('description') . '" />' .
				"\n\t" . '<meta name="keywords" content="' . $this->getPageData('keywords') . '" />' .
				"\n\t" . '<meta lang="' . $this->getPageData('lang') . '" />' .
				"\n\t" . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
			;

		if (!$no_og)
		{
			$html .= "\n\n\t<!-- Open Graph metadata -->" .
					"\n\t" . '<meta property="og:title" content="' . $this->getPageData('title') . '" />' .
					"\n\t" . '<meta property="og:description" content="' . $this->getPageData('description') . '" />' .
					"\n\t" . '<meta property="og:url" content="' . $this->getPageData('url') . '" />' .
					( $this->getPageData('image') ? "\n\t" . '<meta property="og:image" content="' . $this->getPageData('image') . '" />' : '' )
			;
		}
				
		$html .= "\n\n\t<!-- Feed links -->" .
				"\n\t" . '<link rel="alternate" type="application/rss+xml" title="RSS" href="/feed/rss" />' .
				"\n\t" . '<link rel="alternate" type="application/atom" title="RSS" href="/feed/atom" />';
		
		return $html;
	}
	
	/**
	 * Returns html with Dublin Core metadata
	 * @return string
	 */
	public function metadata_dc()
	{
		/**
		 * <meta name="DC.Contributor.Sponsor" xml:lang="en" content=""/>
		 * <meta name="DC.Contributor.Sponsor" xml:lang="it" content=""/>
		 * <meta name="DC.Date.dateSubmitted" scheme="ISO8601" content="2012-12-20"/>
		 * <meta name="DC.Date.issued" scheme="ISO8601" content="2012-12-21"/>
		 * <meta name="DC.Description" xml:lang="en" content="English Description"/>
		 * <meta name="DC.Identifier.pageNumber" content="1-46"/>
		 * <meta name="DC.Rights" content="I copyright degli articoli pubblicati su questa rivista appartengono agli autori, e i diritti di prima pubblicazione sono concessi alla rivista. Nel momento in cui presentano il proprio lavoro, gli autori accettano che possa essere copiato da chiunque per fini non commerciali, ma solamente nel caso in cui venga appropriatamente citato. In virtù della loro apparizione su questa rivista gratuita, gli articoli sono dichiarati usufruibili gratuitamente, con una corretta attribuzione, in contesti non commerciali. Ogni autore è responsabile per il contenuto del proprio lavoro, incluse citazioni, attribuzione e permessi d&#039;uso."/>
		 * <meta name="DC.Source.Issue" content="1"/>
		 * <meta name="DC.Source.Volume" content="5"/>
		 * <meta name="DC.Title.Alternative" xml:lang="en" content="The Bronze Age settlement of S. Giovanni in Triario (Bologna)"/>
		 * <meta name="DC.Type" content="Text.Serial.Journal"/>
		 */
		
		$art = $this->getArticle();
	
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
				"\n\t" . '<meta name="DC.Identifier.URI" scheme="URI" content="' . $this->getPageData('url') . '" />' .
				
				// Identifier.DOI
				"\n\t" . '<meta name="DC.Identifier.DOI" content="' . $this->getMD()->getDoiPrefix() . '.' . $art[$this->getMD()->getTable('id')] . '" />' .
				
				// Titile
				"\n\t" . '<meta name="DC.Title" content="' . $this->getPageData('title') . '" />' .
				
				// Description
				"\n\t" . '<meta name="DC.Description" content="' . $this->getPageData('description') . '" />' .
				
				// Language
				"\n\t" . '<meta name="DC.Language" scheme="ISO639-1" content="' . $this->getPageData('lang') . '" />' .
				
				// Creator
				( $this->getPageData('author') ? "\n\t" . '<meta name="DC.Creator" content="' . $this->getPageData('author') . '" />' : '') .
				
				// Creator.PersonalName
				( $this->getPageData('author') ? "\n\t" . '<meta name="DC.Creator.PersonalName" content="' . $this->getPageData('author') . '" />' : '') .
				
				// Publisher
				( $this->getMD()->getPublisher() ? "\n\t" . '<meta name="DC.Publisher" content="' . $this->getMD()->getPublisher() . '" />' : '') .
				
				// Subject
				"\n\t" . '<meta name="DC.subject" scheme="RFC3066" content="' . $this->getPageData('keywords') . '" />' .

				// Date
				( $this->getPageData('date') ? "\n\t" . '<meta name="DC.Date" scheme="W3CDTF" content="' . $this->getPageData('date') . '" />' : '') .
				
				// Date.created
				( $this->getPageData('date') ? "\n\t" . '<meta name="DC.Date.created" scheme="ISO8601" content="' . $this->getPageData('date') . '" />' : '') .
				
				// Date.modified
				( $art['updated'] ? "\n\t" . '<meta name="DC.Date.modified" scheme="ISO8601" content="' . $art['updated'] . '" />' : '') .
				
				// Source
				($this->getMD()->getRepositoryName() ? "\n\t" . '<meta name="DC.Source" content="' . $this->getMD()->getRepositoryName() . '"/>' : '') .
				
				// Source.ISSN
				($this->getMD()->getISSN() ? "\n\t" . '<meta name="DC.Source.ISSN" content="' . $this->getMD()->getISSN() . '"/>' : '') .
				
				// Source.URI
				($this->getMD()->getURL() ? "\n\t" . '<meta name="DC.Source.URI" content="' . $this->getMD()->getURL() . '"/>' : '')
				;
		return $html;
	}
	
	
	/**
	 * Returns html with Highwire Press metadata, for Google Scholar usage
	 * @return string
	 */
	public function metadata_hp()
	{
		/**
		 * http://www.google.com/intl/en/scholar/inclusion.html#indexing
		 * <meta name="citation_volume" content="5"/>
		 * <meta name="citation_issue" content="1"/>
		 * <meta name="citation_firstpage" content="1-46"/>
		 */
		$art = $this->getArticle();
		
		$html =  "\n\t<!-- Highwire Press metadata tags -->" .
				
				// journal title
				($this->getMD()->getRepositoryName() ? "\n\t" . '<meta name="citation_journal_title" content="' . $this->getMD()->getRepositoryName() . '"/>' : '') .
				
				// journal issn
				($this->getMD()->getISSN() ? "\n\t" . '<meta name="citation_issn" content="' . $this->getMD()->getISSN() . '"/>' : '') .
				
				// article author
				( $this->getPageData('author') ? "\n\t" . '<meta name="citation_author" content="' . $this->getPageData('author') . '" />' : '') .
				
				// article title
				"\n\t" . '<meta name="citation_title" content="' . $this->getPageData('title') . '" />' .
				
				// article date
				( $this->getPageData('date') ? "\n\t" . '<meta name="citation_date" content="' . $this->getPageData('date') . '" />' : '') .
				
				// article doi
				"\n\t" . '<meta name="citation_doi" content="' . $this->getMD()->getDoiPrefix() . '.' . $art[$this->getMD()->getTable('id')] . '"/>' .
		
				// article abstract url
				"\n\t" . '<meta name="citation_abstract_html_url" content="' . $this->getPageData('url') . '"/>' .
				
				// article language
				"\n\t" . '<meta name="citation_language" content="' . $this->getPageData('lang') . '" />' .
		
				// article pdf url
				(file_exists('./sites/default/images/pdf/' . $art['textid'] . '.pdf') ?  
					"\n\t" .  '<meta name="citation_pdf_url" content="' . $this->getMD()->getURL() . '/sites/default/images/pdf/' . $art['textid'] . '.pdf"/>'
					: '')
	
		;
		return $html;
	}
	
	/**
	 * Returns html with menu data
	 * @param string $menu menu name to display
	 * @param string $class CSS class or space-separated classes to attach to main UL
	 * @param array $data if aray if data is provided, these data will be shown
	 * @return boolean|string
	 */
	public function menu($menu = false, $class = false, $data = false)
	{
		if ($menu)
		{
			$menu_arr = $this->getMenu($menu);
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
						. '<a href="' . link::format($menu['href'], $this->session_lang) . '" ' 
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
	
	
	/**
	 * Returns html for language selection
	 * @param boolean $flags if true flags images will be shown
	 * @param boolean $no_text if true no test will be shown
	 * @return string
	 */
	public function langMenu($flags = false, $no_text = false)
	{
		$lang_arr = $this->getLanguages();
    
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
	
	
	/**
	 * Returns html with search form
	 * @return string
	 */
	public function searchForm()
	{
		$html = '<form action="javascript:void(0);" id="searchForm" data-path="' . $this->link2('home') . '">'
				. '<input class="search-query" type="search" placeholder="' . tr::get('search_site') . '" name="search" id="search" />'
			. '</form>';
		return $html;
	}
	
	
	/**
	 * Returns html with formatted search result
	 * @return string
	 */
	public function searchResults()
	{
		$art_list = $this->getSearchResults();
		
		$tot_found = count($art_list);
		
		if ( $tot_found == 0 )
		{
			$html = '<h4>' . sprintf(tr::get('no_result_for_query'), $this->getSearchString()). '</h4>';
		}
		else
		{
			$html = '<h4>' . sprintf(tr::get('x_results_for_query'), $this->getSearchString(), $tot_found) . '</h4>';
			
			
			$html .= $this->array2html($art_list, 'search');
			
		}
		return $html;
	}
	
	
	/**
	 * Returns html with article's body
	 * @param string $article article to show, if false current article will be shown
	 * @return string
	 */
	public function articleBody($article = false)
	{
		$art = $this->getArticle($article);
		
		if ($art)
		{
			$html = '<div class="section">' .
					'<div class="article">' .
						'<h1>' . $art['title'] . '</h1>' .
						'<div class="content">' . $art['text'] . '</div>' .
					'</div>' .
				'</div>';
		}
		else
		{
			$html = '<div class="section error">' . tr::get('article_does_not_exist') . '</div>';
		}
		return $html;
	}
	
	
	/**
	 * Returns html with list of articles matching tags
	 * @return string
	 */
	public function tagBlog()
	{
		if (func_get_args())
		{
			$art_list = $this->getArticlesByTagArray(func_get_args());
      $tags = implode(' ', func_get_args());
		}
		else
		{
			$art_list = $this->getArticlesByTag(); 
      $tags = implode(' ', $this->getFilterTags());
		}
    
		return $this->array2html($art_list, 'tags ' . $tags);
		
	}
	
	
	/**
	 * Returns html data from array of articles
	 * @param array $art_list	array of articles (arrays)
	 * @param string $class	CSS class
	 * @return boolean|string
	 */
	private function array2html($art_list, $class = false)
	{
		if (!is_array($art_list))
		{
			return false;
		}
		
		$html = '<div class="blog ' . ($class ? $class : '') . '">';
		foreach ($art_list as $art)
		{
			$html .= '<div class="article">' .
					  ($art['title'] ? ' <h3><a href="' . $art['url']  . '">' . $art['title'] . '</a></h3>' : '') .
					  '<div class="content">' . $art['summary'] . '</div>' .
							'<div class="read_more">' .
								'<a href="' . $art['url']  . '">' . tr::get('read_more') . '</a>' .
							'</div>' .
					  '</div>';
		}
		$html .= '</div>';
		
		return $html;
	}
	
	
	/**
	 * Returns html with list of articles similar to present article
	 * @return boolean|string
	 */
	public function similarBlog()
	{
		$art_list = $this->getSimilar();
	
		if (!is_array($art_list))
		{
			return false;
		}
		
		$html = '<h3>' . tr::get('from_same_section') . '</h3>';
		
		$html = $this->array2html($art_list, 'similar');
		
		return $html;
	}
	
	
	/**
	 * Returns html with Google Analytics javascript code
	 * @return string
	 */
	public function GA()
	{
		$id = cfg::get('ga_id');
		
		$restrict_domain = cfg::get('ga_domain');
		
		
		if ( id && ( !$restrict_domain || (preg_match('/' . $restrict_domain . '/', $_SERVER['HTTP_HOST']) ) ) )
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
	
	
	/**
	 * Returns html with image gallery. Alias for customTags:gallery()
	 * @param string $gal	Gallery name
	 * @param string $class CSS class or space-separated CSS classes to apply to main UL
	 * @return type
	 */
	public function gallery($gal, $class = false)
	{
		if (!file_exists(GALLERY_DIR . $gal . '/data.json'))
		{
			return;
		}
		return customTags::gallery(array('content' => $gal, 'class' => $class));
	}
	
	
	/**
	 * Runs method of customTags object and returns html
	 * @param string $method method of customTags object to call
	 * @param string $param json with method parameters
	 */
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
	
		return $html;
			
	}
		
}