# The `html` object

The `html` object is the core of the BraDyCMS template system. The `html` object
is organized in methods that can return everything relative to the CMS content: 
strings, html pieces of code, php arrays to use in iterations and also complex php objects.
This object is used following the [Twig syntax](#docs/read/tmpl_twig).

Calling a method of the `html` object is as simple as writing the method name after 
the `html` string separating them by a dot (.), e.g.: `html.methodName`.

Methods can have zero, one or more arguments, e.g.:
* `html.methodName` (no arguments)
* `html.methodName()` (no arguments)
* `html.methodName('arg1')` (one argument)
* `html.methodName('arg1', 'arg2', 'argN')` (several arguments)

Some arguments may be *required*, i.e. if not provided an error occurs, or *optional*.

Arguments can be strings (also pieces of html), arrays or other objects.

In general, there are two types of methods/attributes of the `html` objects: 
the ones that return well-formatted and rich HTML 
and the ones that return strings, arrays or PHP objects. These methods can be 
identified by the prefix `get`, ex: `{{ html.getContext }}`.

---

## Docs

---

#### articleBody('article_text_id')
Returns well-formatted html with article content. If article_text_id is present the matching article will be shown, otherwise the current article will appear
- **article_text_id** string, optional, required: false. Article's textual id

E.g.: `{{ html.articleBody('lorem_ipsum') }}`
    <div class="section">
      <div class="article">
        <h1>Web design</h1>
        <div class="content">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit...
        </div>
      </div>
    </div>

---

#### ct('customtag', 'params')
Returns html produced by custom tag or user module
- **customtag** string, required. Custom tag to run
- **params** array|string, optional, default: false. Array or JSON encoded 
key-value pairs array (string) to use as parameters

E.g.: `{{ html.ct('addThis', {"content": "share", "pubid": "ra-4d8b051f4951c18f"}) }}`
    <a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&pubid=ra-4d8b051f4951c18f">share</a>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4d8b051f4951c18f"></script>

---

#### downloadNode('node', 'class')
Returns well-formatted html showing table element with data for available files in download node
- **node** string, required. Name of download node to display
- **class** string, optional, css class or space separated css classes to attache to main table element

E.g.: `{{ html.downloadNode('curricula', 'table-stripped') }}`
    <table class=\"table-stripped\">
      <tbody>
        <tr>
          <td><a href="{ full path to file }" class="downloadFile" target="_blank">{ file title }</a></td>
          <td><a href="{ full path to file }" class="downloadFile" target="_blank">{ file description }</a></td>
          <td><a href="{ full path to file }" class="downloadFile" target="_blank">Download</a></td>
        </tr>
        <tr>...</tr>
      <tbody>
    </table>

---

#### GA()
Returns javascript code for Google Analytics Tracking. Google Analytics id must be provided in the site configuration file. A domain is provided in the configuration file; the code will be shown only if current domain matches the provided domain. This is very useful in test installations.

E.g.: `{{ html.GA }}`
    <script>
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'GA-ID']);
      _gaq.push(['_trackPageview']);
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>

---

#### gallery('name', 'class')
Returns HTML code with gallery data.
- **name** string, required. Textual id of the gallery to show
- **class** string, optional, default false. Css class (or space separated classes) to add to main unordered list (ul)

E.g.: `{{ html.gallery('news', 'latest') }}`
    <div class="gallery-container">
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
    </div>

---

#### getArticle('article')
Returns array with article data. If article is not provided, current article's data will be returned.
- **article** string, optional, default false. Article's textid

E.g.: `{{ html.getArticle('contacts') }}`
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
   )

---

#### getArticlesByTag('tag1', 'tag2', 'ecc')
Returns array of articles arrays matching all the provided tags. If no tag is provided as argument, URL tags will be used
- **tag1** string, optional, default false. First filtering tag
- **tag2** string, optional, default false. Second filtering tag
- **ecc** ...

E.g.: `{{ html.getArticlesByTag('news', 'web') }}`
    array(
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
    )

---

#### getArticleTags()
Returns array of tags for current article

E.g.: `{{ html.getArticleTags }}`
    array(
      'tag1',
      'tag2',
      'etc'
    )

---

#### getContext()
Returns context of usage: article, tags, search or home

E.g.: `{{ html.getContext }}`
    home

---

#### getDevice()
Returns device type: computer, tablet, phone

E.g.: `{{ html.getDevice }}`
    computer

---

#### getDownloadNode('node')
Returns array with data for available files in download node
- **node** string, required. Name of download node to display

E.g.: `{{ html.getDownloadNode('curricula') }}`
    array (
      array (
       path => sites/default/images/downloads/curricola/jbogdani.pdf,
       basename => jbogdani,
       ext => pdf,
       title => CV of Julian Bogdani,
       description => Detailed CV of Julian Bogdani, PDF version. Last updated 2014,
      ),
     ...
    );

---

#### getFilterTags()
Returns array of URL tags used as filters

E.g.: `{{ html.getFilterTag }}`
    array(
      'news',
      'web'
    )

---

#### getGallery('gallery')
Returns array with data for gallery name
- **gallery** string, required. Name of gallery to get data for

E.g.: `{{ html.getGallery('our_works') }}`
    array (
      array (
       img => sites/default/images/galleries/our_works/picture01.jpg,
       thumb => sites/default/images/galleries/our_works/thumbs/picture01.jpg,
       caption => Caption of picture01
      ),
     ...
    );

---

#### getLanguages()
Returns array of available languages

E.g.: `html.getLanguages`
    array(
      array(
        'code'=>'it',
        'string'=>'Italiano',
        'is_current'=>true,
        'href'=>'http://bradypus.net/it'
      ),
      array(...)
    )

---

#### getMD()
Tries to include Metadata object file, initializes object and returnes it.

E.g.: ` {{ html.getMD }}`
    

---

#### getPageData('el', 'escape')
Returns document metadata array (all metadata). Available document metadata are: title (custom titles for single articles, different from article's title field, can be set using a custom field named customtitle), description, keywords, robots (custom robots meta tag values for single articles, different from the configuration global setting, can be set using a custom field named robots), lang, url, image (may be false. It's the src of the first image of the article's content), author (may be false. It's the article's author), date (may be false. It's the article's date of publication), publisher (may be false. It's the OAI config publisher parameter)
- **el** string, optional. If present, only this element will be returned; otherwise, the entire array will be returned
- **escape** boolean, optional, default: false. If true the sigle quotes of the string will be escaped

E.g.: `{{ html.getPageData.title }}`
    Home Page

---

#### getTextId()
Returns article's text id required in URL

E.g.: `{{ html.getTextId() }}`
    contact_us

---

#### getSearchResults()
Returns array of article arrays matching the searched string

E.g.: `{{ html.getSearchResults }}`
    array(
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
    )

---

#### getSearchString()
Returns string used as filter in URL. Available only if the context is found

E.g.: ` {{html.getSearchString }}`
    web

---

#### getSimilar('textid')
Returns array of article arrays matching the most similar (having the same tags) results compared to the article with given textid or to the current article
- **textid** string, optional, default false. Text id to use for comparison

E.g.: `{{ html.getSimilar }}`
    array(
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
    )

---

#### getVersion
Returns installed and running version of BraDyCMS according to [semver syntax](http://semver.org/)

E.g.: `{{ html.getVersion }}`
      3.7.0

---

#### GUA()
Returns javascript code for Google Universal Analytics Tracking. Google Analytics id must be provided in the site configuration file. A domain is provided in the configuration file; the code will be shown only if current domain matches the provided domain. This is very useful in test installations.

E.g.: `{{ html.GUA }}`
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'GA-ID', 'auto');
      ga('send', 'pageview');
    </script>

---

#### jQuery('version')
Returns string for jQuery library inclusion, trying first to load from Google CDN then from local host
- **version** string, required. jQuery version (x.x.x)

E.g.: `{{ html.jQuery('1.10.2') }}`
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>window.jQuery || document.write(\'<script src="./js/jquery-1.10.2.min.js"><\/script>\')</script>

---

#### langMenu('flags', 'no_text')
Returns well-formatted html of language menu. Current language's list item (li) has class current
- **flags** boolean, optional, default false. If true image flags will be shown
- **no_text** boolean, optional, default false. If true no language text will be shown

E.g.: `{{ html.langMenu(true) }}`
    <ul class="menu lang">
      <li class="current">
        <a href="/it" data-ajax="false">$360;img src="./img/flags/it.png"  alt="italiano" /> Italiano</a>
      </li>
      <li>
        <a href="/en" data-ajax="false">$360;img src="./img/flags/en.png"  alt="english" /> English</a>
      </li>
      <li>
        <a href="/fr" data-ajax="false">$360;img src="./img/flags/fr.png"  alt="français" /> Français</a>
      </li>
    </ul>

---

#### link2('resource', 'is_tag')
Returns relative link string to resource or to site homepage (if resource is home), depending on current url
- **resource** string|array, required. If string, it's textid of an article. If home, then a link to the homepage will be created. If an array and tags is true, a link to a list of tags will be created
-- **is_tag**, boolean, default false. If true, resources will be treated as tag or tag list

E.g.: `{{ html.link2('news', true) }}`
    ./news.all

---

#### menu('menu', 'class')
Returns well-formatted html of menu items
- **menu** string, required textual. Id of the menu to show
- **class** string, optional, default false. Css class (or space separated classes) of the menu's unordered list (ul)

E.g.: `{{ html.menu('main', 'nav') }}`
    <ul class="menu nav">
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
     </ul>

---

#### metadata('no_og')
Returns well-formatted html code of page html and open graph metadata and links to feeds
- **no_og** boolean, optional, default: false. If true the open graph metadata will not be shown

E.g.: `{{ html.metadata() }}`
    <title>Home Page</title>
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
    <link rel="alternate" type="application/atom" title="RSS" href="/feed/atom" />

---

#### medatada_dc()
Returns well-formatted html code with Dublin Core html metadata. Title, description, language, identifier (url), subject (keywords) are the same as html & og metadata. Date is the date of publication of the article publisher is the publsher name as defined in OAI module configuration

E.g.: `{{ html.metadata_dc }}`
    <!-- Dublin Core metadata -->
    <meta name="DC.type" content="Text" />
    <meta name="DC.format" content="text/html" />
    <meta name="DC.identifier" scheme="URI" content="http://thishost/thispage_url" />
    <meta name="DC.title" content="Home Page" />
    <meta name="DC.description" content="Web sites main description" /<
    <meta name="DC.language" scheme="RFC3066" content="en" />
    <meta name="DC.creator" content="John Doe" />
    <meta name="DC.publisher" content="John Doe publishing" />
    <meta name="DC.subject" scheme="RFC3066" content="home, page, keyword1, keyword2" />
    <meta name="DC.date" scheme="W3CDTF" content="2013-04-17" />

---

#### metadata_hp()
Returns html with Highwire Press metadata, for Google Scholar usage

E.g.: `{{ html.metadata_hp }}`
    <meta name="citation_journal_title" content=""/>
    <meta name="citation_issn" content="Lorem ipsum"/>
    <meta name="citation_author" content="Lorem ipsum" />
    <meta name="citation_title" content="Lorem ipsum" />
    <meta name="citation_date" content="0000-00-00" />
    <meta name="citation_doi" content="10/123456.23456"/>
    <meta name="citation_abstract_html_url" content="http://lorem.ipsum"/>
    <meta name="citation_language" content="en" />
    <meta name="citation_pdf_url" content="http://lorem.ipsum.pdf"/>

---

#### tagBlog('tag1', 'tag2', 'ecc')
Returns well-formatted html with list of articles tagged with tags provided as arguments or present in URL
- **tag1** string, optional, default false. First tag to use as filter
- **tag2** string, optional, default false. Second tag to use as filter
- **ecc** ...

E.g.: `{{ html.tagBlog('news', 'web') }}`
    <div class="section blog tags">
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
    </div>

---

#### TBSjs('version')
Returns string for Twitter Bootstrap javascript library inclusion, trying first to load from CDN then from local host
- **version** string, required. Twiter Bootstrap version (x.x.x)

E.g.: `{{ html.TBSjs('3.0.1') }}`
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js"></script><script>if(typeof($.fn.modal) === \'undefined\') { document.write('<script src="./js/bootstrap-3.0.1.min.js"><\/script>')}</script>';

---

#### tr('string', 'args', 'escape')
Returns the translated value for the given string in the current language
- **string** string, required. String to translate (translation ID)
- **args** string|array, optional, default false. String, or array of strings, to use for replacment of placeholders (%s)
- **escape** boolean, optional, default false. If true the single quotes will be replaced in the resulting string

E.g.: `tr('hello_world')`
    Ciao mondo!
---

#### searchForm()
Returns well-formatted html of search form input. Form submission should be done with javascript. The data attribute contains the path to the site root!

E.g.: `{{ html.searchForm }}`
    <form action="javascript:void(0);" id="searchForm" data-path="./">
      <input class="search" type="search" placeholder="Search in web site" name="search" id="search" />
    </form>

---

#### searchResults()
Returns well-formatted html with list of articles found by search. This function will return false if context is not found!

E.g.: `{{ html.searchResults }}`
    <h1>10 articles found searching <code>web</code></h1>
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
    </div>

---

#### similarBlog()
Returns well-formatted html with list of similar articles (having the same tags) as current article.

E.g.: `{{ html.similarBlog}}`
    <div class="section blog tags">
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
    </div>


<script>
$('.active h2').after($('<input>').attr('placeholder', 'Search method').on('keyup', function(){
  var val = $(this).val().toLowerCase();

  $('.active h4').each(function(i, el){
    var txt = $(el).text().toLowerCase();
    if (txt.indexOf(val) < 0){
      $(el).hide().nextUntil('h4').hide();
    } else {
      $(el).show().nextUntil('h4').show();
    }
  });
}));
</script>