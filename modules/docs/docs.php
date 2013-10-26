<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Dec 21, 2012
 */
 
class docs_ctrl extends Controller
{
	public function read()
	{
		$file = $this->get['param'][0];
		
		if (file_exists(MOD_DIR . 'docs/tmpl/' . $file . '.twig'))
		{
			$this->render('docs', $file, array(
				'art_arr'=>$art_array,
				'docs' => $file == 'template' ? $this->stuctured_docs() : ''
				
			));
		}
	}
	
	
	private function stuctured_docs()
	{
		$docs[] = array(
			'method' => 'articleBody',
			'params' => array(
				'article_text_id' => 'string, optional, required: false. Article\'s textual id',
			),
			'description' => 'Returns well-formatted html with article content. If article_text_id is present the matching article will be shown otherwise the current article will be shown',
			'example_usage' => "{{ html.articleBody('lorem_ipsum') }}",
			'example_output' => '<div class="section">
  <div class="article">
    <h1>Web design</h1>
    <div class="content">
      Lorem ipsum dolor sit amet, consectetur adipiscing elit...
    </div>
  </div>
</div>'
		);
		
		
		$docs[] = array(
			'method' => 'ct',
			'params' => array(
				'customtag' => 'string, required. Custom tag to run',
				'params' => 'string, optional, default: false. Json encoded key-value pairs to use as parameters',
			),
			'description' => 'Returns html produced by custom tag or user module',
			'example_usage' => "{{ html.ct('addThis', '{\"content\": \"share\"}') }}",
			'example_output' => '<a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&pubid=ra-4d8b051f4951c18f">share</a>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4d8b051f4951c18f"></script>'
			);
		
		
		$docs[] = array(
			'method' => 'GA',
			'description' => 'Returns javascript code for Google Analytics Tracking. Google Analytics id must be provided in site configuration file. A domain is provided in configuration file, the code will be shown only if current domain matches the provided domain. This is very usefull in test installations.',
			'example_usage' => "{{ html.GA }}",
			'example_output' => "<script>
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'GA-ID']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>"
			);
		
		$docs[] = array(
			'method' => 'gallery',
			'params' => array(
				'name' => 'string, required. Textual id of the gallery to show',
				'class' => 'string, optional, default false. Css class (or space separated classes) to add to main unordered list (ul)',
			),
			'description' => 'Returns HTML code with gallery data.',
			'example_usage' => "{{ html.gallery('news', 'latest') }}",
			'example_output' => '<div class="gallery-container">
  <ul class="gallery news latest">
    <li>
      <a href="./sites/default/images/galleries/news/01.jpg" class="fancybox" title="Figure 01 caption" rel="news" >
        <img src="./sites/default/images/galleries/news/thumbs/01.jpg" alt="Figure 01 caption" />
      </a>
      <div class="caption">Figure 01 caption</div>
    </li>

    <li>... </li>
  
    ...
  </ul>
</div>'
			);
    
		
    $docs[] = array(
      'method' => 'jQuery',
      'params' => array(
        'version' => 'string, required. jQuery version (x.x.x)'
      ), 
      'description' => 'Returns string for jQuery library inclusion, trying first to load from Google CDN then from local host',
      'example_usage' => "{{ html.jQuery('1.10.2') }}",
			'example_output' => "<script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"></script>
<script>window.jQuery || document.write(\'<script src=\"./js/jquery-1.10.2.min.js\"><\/script>\')</script>"
    );
    
		
		$docs[] = array(
			'method' => 'langMenu',
			'params' => array(
				'flags' => 'boolean, optional, default false. If true image flags will be shown',
				'no_text' => 'boolean, optional, default false. If true no language text will be shown'
			),
			'description' => 'Returns well-formatted html of language menu. Current language\'s list item (li) has class current',
			'example_usage' => "{{ html.langMenu(true) }}",
			'example_output' => '<ul class="menu lang">
  <li class="current">
  	<a href="/it" data-ajax="false">$360;img src="./img/flags/it.png"  alt="italiano" /> Italiano</a>
  </li>
  <li>
  	<a href="/en" data-ajax="false">$360;img src="./img/flags/en.png"  alt="english" /> English</a>
  </li>
  <li>
  	<a href="/fr" data-ajax="false">$360;img src="./img/flags/fr.png"  alt="français" /> Français</a>
  </li>'
			);
		
		
		$docs[] = array(
			'method' => 'menu',
			'params' => array(
				'menu' => 'string, required textual. Id of the menu to show',
				'class' => 'string, optional, default false. Css class (or space separated classes) of the menu\'s unordered list (ul)'
			),
			'description' => 'Returns well-formatted html of menu items',
			'example_usage' => "{{ html.menu('main', 'nav') }}",
			'example_output' => '<ul class="menu nav">
  <li class="menu-item"><a href="#">Home</a></li>
  <li class="menu-item"><a href="#">Who we are</a></li>
  <li class="menu-item dropdown">
     <a href="#" class="dropdown-toggle" data-toggle="dropdown">Portfolio</a> <b class="caret"></b>
   <ul class="submenu dropdown-menu">
    <li class="menu-item"><a href="#">Web</a></li>
    <li class="menu-item"><a href="#">Print</a></li>
  </ul>
  </li>
  <li class="menu-item"><a href="#" target="_blank">@Twitter</a></li>
 </ul>'
			);
		
		
		$docs[] = array(
			'method' => 'metadata',
			'params' => array(
				'no_og' => 'boolean, optional, default: false. If true the open graph metadata will not be shown'
			),
			'description' => 'Returns well-formatted html code of page html and open graph metadata and links to feeds',
			'example_usage' => "{{ html.metadata() }}",
			'example_output' => '<title>Home Page</title>
<meta name="description" content="Web sites main description" />
<meta name="keywords" content="home, page, keyword1, keyword2" />
<meta lang="en" />
<!-- Open Graph metadata -->
<meta property="og:title" content="Home Page" />
<meta property="og:description" content="Web sites main description" />
<meta property="og:url" content="http://thishost/thispage" />
<meta property="og:image" content="http://thishost/path_to_the_first_image_of_article_body_if_exist.extension" />

<!-- Feed links -->
<link rel="alternate" type="application/rss+xml" title="RSS" href="/feed/rss" />
<link rel="alternate" type="application/atom" title="RSS" href="/feed/atom" />'
			);
		
		$docs[] = array(
			'method' => 'medatada_dc',
			'description' => 'Returns well-formatted html code with Dublin Core html metadata. Title, description, languale, identifier (url), subject (keywords) are the same as html & og metadata. Date is the publish date of the article publisher is the publsher name as defined in OAI module configuration',
			'example_usage' => "{{ html.metadata_dc }}",
			'example_output' => '<!-- Dublin Core metadata -->
<meta name="DC.type" content="Text" />
<meta name="DC.format" content="text/html" />
<meta name="DC.identifier" scheme="URI" content="http://thishost/thispage_url" />
<meta name="DC.title" content="Home Page" />
<meta name="DC.description" content="Web sites main description" /<
<meta name="DC.language" scheme="RFC3066" content="en" />
<meta name="DC.creator" content="John Doe" />
<meta name="DC.publisher" content="John Doe publishing" />
<meta name="DC.subject" scheme="RFC3066" content="home, page, keyword1, keyword2" />
<meta name="DC.date" scheme="W3CDTF" content="2013-04-17" />'
			);
		
		$docs[] = array(
			'method' => 'metadata_hp',
			'description' => 'Returns html with Highwire Press metadata, for Google Scholar usage',
			'example_usage' => "{{ html.metadata_hp }}",
			'example_output' => '<meta name="citation_journal_title" content=""/>
<meta name="citation_issn" content="Lorem ipsum"/>
<meta name="citation_author" content="Lorem ipsum" />
<meta name="citation_title" content="Lorem ipsum" />
<meta name="citation_date" content="0000-00-00" />
<meta name="citation_doi" content="10/123456.23456"/>
<meta name="citation_abstract_html_url" content="http://lorem.ipsum"/>
<meta name="citation_language" content="en" />
<meta name="citation_pdf_url" content="http://lorem.ipsum.pdf"/>'
			);
		
		$docs[] = array(
			'method' => 'searchForm',
			'description' => 'Returns well-formatted html of search form input. Form submission should be done with javascript. The data attribute contains the path to the site root!',
			'example_usage' => "{{ html.searchForm }}",
			'example_output' => '<form action="javascript:void(0);" id="searchForm" data-path="./">
  <input class="search" type="search" placeholder="Search in web site" name="search" id="search" />
</form>'
			);
		
		$docs[] = array(
			'method' => 'searchResults',
			'description' => 'Returns well-formatted html with list of articles found by search. This function will return false if context is not search!',
			'example_usage' => "{{ html.searchResults }}",
			'example_output' => '<h1>10 articles found searching <code>web</code></h1>
<div class="section blog search">
  <div class="article">
    <h3><a href="/en/web-design">Web design</a></h3>
    <div class="content">
      Some of our latest work on web design...
    </div>
    <div class="read_more">
      <a href="/en/web-design">Read more</a>
    </div>
  </div>
  
  <div class="article">
    ...
  </div>
  
  ...
</div>'
			);
		
		$docs[] = array(
			'method' => 'similarBlog',
			'description' => 'Returns well-formatted html with list of articles similar (having the same tags) as current article.',
			'example_usage' => "{{ html.similarBlog}}",
			'example_output' => '<div class="section blog tags">
  <div class="article">
    <h3><a href="/en/web-design">Web design</a></h3>
    <div class="content">
      Some of our latest work on web design...
    </div>
    <div class="read_more">
      <a href="/en/web-design">Read more</a>
    </div>
  </div>
  
  <div class="article">
    ...
  </div>
  
  ...
</div>'
			);
		
		$docs[] = array(
			'method' => 'tagBlog',
			'params' => array(
				'tag1' => 'string, optional, default false. First tag to use as filter',
				'tag2' => 'string, optional, default false. Second tag to use as filter',
				'ecc' => '...'
			),
			'description' => 'Returns well-formatted html with list of articles tagged with tags provided as arguments or present in URL',
			'example_usage' => "{{ html.tagBlog('news', 'web') }}",
			'example_output' => '<div class="section blog tags">
  <div class="article">
    <h3><a href="/en/web-design">Web design</a></h3>
    <div class="content">
      Some of our latest work on web design...
    </div>
    <div class="read_more">
      <a href="/en/web-design">Read more</a>
    </div>
  </div>
  
  <div class="article">
    ...
  </div>
  
  ...
</div>'
			);
		
		
		
		
		$docs[] = array(
			'method' => 'getArticle',
			'params' => array(
				'article' => 'string, optional, default false. Article\'s textid'
			),
			'description' => 'Returns array with article data. If article is not provided current article\'s data will be returned.',
			'example_usage' => "{{ html.getArticle('contacts') }}",
			'example_output' => "array(
   'id' => integer,
   'title' => string,
   'textid' => string,
   'sort' => integer,
   'summary' => string,
   'text' => string,
   'keywords' => string,
   'author' => string,
   'status' => string,
   'section' => string,
   'tags' => array('string', 'string', 'etc..'),
   'created' => datestamp,
   'publish' => datestamp,
   'expires' => datestamp,
   'updated' => boolean,
   'url' => string,
   'full_url' => string
)
"
			);
		
		$docs[] = array(
			'method' => 'getArticleTags',
			'description' => 'Return array of tags for current article',
			'example_usage' => "{{ html.getArticleTags }}",
			'example_output' => "array(
  'tag1',
  'tag2',
  'etc'
)"
			);
		
		$docs[] = array(
			'method' => 'getArticlesByTag',
			'params' => array(
				'tag1' => 'string, optional, default false. First filtering tag',
				'tag2' => 'string, optional, default false. Second filtering tag',
				'ecc' => '...',
			),
			'description' => 'Return array of articles arrays matching all the provided tags. If no tag is provided as argument URL tags will be used',
			'example_usage' => "{{ html.getArticlesByTag('news', 'web') }}",
			'example_output' => "array(
  array(
    'id' => integer,
    'title' => string,
    'textid' => string,
    'sort' => integer,
    'summary' => string,
    'text' => string,
    'keywords' => string,
    'author' => string,
    'status' => string,
    'section' => string,
    'tags' => array('string', 'string', 'etc..'),
    'created' => datestamp,
    'publish' => datestamp,
    'expires' => datestamp,
    'updated' => boolean,
    'url' => string,
    'full_url' => string
  ), 
  array(
    'id' => integer,
    'title' => string,
    'textid' => string,
    'sort' => integer,
    'summary' => string,
    'text' => string,
    ...
  ),
  ...
)"
			);
		
		$docs[] = array(
			'method' => 'getContext',
			'description' => 'Returns context of usage: article, tags, search or home',
			'example_usage' => "{{ out.getContext }}",
			'example_output' => 'home'
			);
		
		$docs[] = array(
			'method' => 'getFilterTags',
			'description' => 'Returns array of URL tags used as filters',
			'example_usage' => "{{ html.getFilterTag }}",
			'example_output' => "array(
  'news',
  'web'
)"
			);
		 
		$docs[] = array(
			'method' => 'getLanguages',
			'description' => 'Returns array of available languages',
			'example_usage' => "out.getLanguages",
			'example_output' => "array(
   array(
     'code'=>'it',
     'string'=>'Italiano',
     'is_current'=>true,
     'href'=>'http://bradypus.net/it'
   ),
   array(...)
)"
		);
		
		$docs[] = array(
			'method' => 'getMD',
			'description' => 'Tryies to include Metadata object file, initializes object and returnes it.',
			'example_usage' => " {{ html.getMD }}"
		);
		
		$docs[] = array(
			'method' => 'getPageData',
			'params' => array(
				'' => '',
			),
			'description' => 'Returns document metadata array (all metadata). Available documenta metadata are: title, description, keywords, lang, url, image (may be false. It\'s the src of the first image of article\'s content), author (may be false. It\'s article\'s author), date (may be false. It\'s article\'s publish date), publisher (may be false. It\'s OAI config publisher parameter)',	
			'example_usage' => "{{ out.getPageData.title }}",
			'example_output' => 'Home Page'
		);
		
		$docs[] = array(
			'method' => 'getSearchResults',
			'description' => 'Return array of article arrays matching searched string',
			'example_usage' => "{{ html.getSearchResults }}",
			'example_output' => "array(
  array(
    'id' => integer,
    'title' => string,
    'textid' => string,
    'sort' => integer,
    'summary' => string,
    'text' => string,
    'keywords' => string,
    'author' => string,
    'status' => string,
    'section' => string,
    'tags' => array('string', 'string', 'etc..'),
    'created' => datestamp,
    'publish' => datestamp,
    'expires' => datestamp,
    'updated' => boolean,
    'url' => string,
    'full_url' => string
  ), 
  array(
    'id' => integer,
    'title' => string,
    'textid' => string,
    'sort' => integer,
    'summary' => string,
    'text' => string,
    ...
  ),
  ...
)"
		);
		
		$docs[] = array(
			'method' => 'getSearchString',
			'description' => 'Rerurns string used as filter in URL. Available only if context si search',
			'example_usage' => " {{html.getSearchString }}",
			'example_output' => 'web'
		);
		
		$docs[] = array(
			'method' => 'getSimilar',
			'params' => array(
				'textid' => 'string, optional, default false. Text id to use for comparison',
			),
			'description' => 'Return array of article arrays matching the most similar (having the same tags) to article with given textid or to the current article',
			'example_usage' => "{{ html.getSimilar }}",
			'example_output' => "array(
  array(
    'id' => integer,
    'title' => string,
    'textid' => string,
    'sort' => integer,
    'summary' => string,
    'text' => string,
    'keywords' => string,
    'author' => string,
    'status' => string,
    'section' => string,
    'tags' => array('string', 'string', 'etc..'),
    'created' => datestamp,
    'publish' => datestamp,
    'expires' => datestamp,
    'updated' => boolean,
    'url' => string,
    'full_url' => string
  ), 
  array(
    'id' => integer,
    'title' => string,
    'textid' => string,
    'sort' => integer,
    'summary' => string,
    'text' => string,
    ...
  ),
  ...
)"
		);
		
		$docs[] = array(
			'method' => 'link2(resource, tags)',
			'params' => array(
				'resource' => 'string|array, required. If is a string it\'s textid of an article. If it\'s home then link to homepage will be created. If it\'s an array and tags is true a link to a list of tags will be created',
			),
			'description' => 'Returns relative link string to resource or to site homepage (if resource is home), depending on current url',
			'example_usage' => "{{ html.link2('news', true) }}",
			'example_output' => './news.all'
		);
		
		

		
		return $docs;
		
	}
}

		$docs[] = array(
			'method' => '',
			'params' => array(
				'' => '',
			),
			'description' => '',
			'example_usage' => "",
			'example_output' => ''
		);