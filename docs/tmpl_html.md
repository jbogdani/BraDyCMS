{% raw %}

# The `html` object

The `html` object is the core of the BraDyCMS template system. The `html` object
is organized in methods that can return everything relative to the CMS content:
strings, html pieces of code, php arrays to use in iterations and also complex php objects.
This object is used following the [Twig syntax](tmpl_twig).

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

#### asset('asset', 'type', 'version')
Returns a well-formatted html string to use for requiring and including an
asset, typically a javascript or css file, in a html document. The asset type
should be specified as the second argument ($type) as 'js' or 'css'.
If missing it will be guessed by the url extension (.js or .css). Some very common
presets (shortcuts) are available: query|bootstrap|fancybox|frontend.
If $version is present this version will be loaded
If the system is setted to use CDN, the the available CDN version will be loaded
instead of the local version.
- **asset**, string, required. The asset url or one of the following presets: query|bootstrap|fancybox|frontend
- **type**, string, optional, default: js. The asset type: js|css. If missing the system will try to guess
by looking at the extension of the url; for presets the default js value will be used
- **version**, string, optional. A specific version to load. By default the following
versions will be loaded for the presets: jquery: 1.11.3; boostrap: 3.3.5; fancybox: 2.1.5
- **asset** string, required. Asset to load; can be an url or one of the following:
jquery, bootstrap, fancybox, frontend

E.g.: `{{ html.asset('bootstrap') }}`

```html
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
```

E.g.: `{{ html.asset('bootstrap', 'css', '3.3.4') }}`
```html
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
```

---

#### articleBody('article_text_id')
Returns well-formatted html with article content. If article_text_id is present the matching article will be shown, otherwise the current article will appear
- **article_text_id** string, optional, required: false. Article's textual id

E.g.: `{{ html.articleBody('lorem_ipsum') }}`

```html
<div class="section">
  <div class="article">
    <h1>Web design</h1>
    <div class="content">
      Lorem ipsum dolor sit amet, consectetur adipiscing elit...
    </div>
  </div>
</div>
```
---

#### canView()
Returns true if content (article tag blog or single article) is not password protected and false if it's protected

---

#### ct('customtag', 'params')
Returns html produced by custom tag or user module
- **customtag** string, required. Custom tag to run
- **params** array|string, optional, default: false. Array or JSON encoded
key-value pairs array (string) to use as parameters

E.g.: `{{ html.ct('addThis', {"content": "share", "pubid": "ra-4d8b051f4951c18f"}) }}`

```html
<a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&pubid=ra-4d8b051f4951c18f">share</a>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4d8b051f4951c18f"></script>
```
---

#### downloadNode('node', 'class')
Returns well-formatted html showing table element with data for available files in download node
- **node** string, required. Name of download node to display
- **class** string, optional, css class or space separated css classes to attache to main table element

E.g.: `{{ html.downloadNode('curricula', 'table-stripped') }}`

```html
<table class="table-stripped">
  <tbody>
    <tr>
      <td><a href="full-path-to-file" class="downloadFile">file-title</a></td>
      <td><a href="full-path-to-file" class="downloadFile">file-description }</a></td>
      <td><a href="full-path-to-file" class="downloadFile">Download</a></td>
    </tr>
    <tr>...</tr>
  <tbody>
</table>
```

---

#### GA()
Returns javascript code for Google Analytics Tracking. Google Analytics id must be provided in the site configuration file. A domain is provided in the configuration file; the code will be shown only if current domain matches the provided domain. This is very useful in test installations.

E.g.: `{{ html.GA }}`

```javascript
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
```
---

#### gallery('name', 'thumb_dim', 'class')
Returns HTML code with gallery data.
- **name** string, required. Textual id of the gallery to show
- **thumb_dim** string, optional, default 200x200. Dimensions of the thumbnails, following the {integer}x{integer} format
- **class** string, optional, default false. Css class (or space separated classes) to add to main unordered list (ul)

E.g.: `{{ html.gallery('news', '200x200', 'latest') }}`

```html
<div class="gallery-container">
  <ul class="gallery news latest">
    <li>
      <a href="./sites/default/images/galleries/news/01.jpg" class="fancybox" title="Figure 01 caption" rel="news" >
        <img src="./cache/galleries/200x200/fc/cf/fccf1c1db130ca6f420445c75c84f4d9.jpg" alt="Figure 01 caption" />
      </a>
      <div class="caption">Figure 01 caption</div>
    </li>

    <li>... </li>

    ...
  </ul>
</div>
```

---

#### getArticle('article')
Returns array with article data. If article is not provided, current article's data will be returned.
- **article** string, optional, default false. Article's textid

E.g.: `{{ html.getArticle('contacts') }}`

```php
<?php
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
```

---

#### getArticlesByFilter('filter', 'connector', 'dontparse')
Returns array of articles matching query parameters contained in $filter of false if nothing is matched
- ***filter*** array, required. (Multidimensional) array with query parameters (field, value, operator)
- ***connector*** boolean, optional, default AND. Logical connector to use for query parts (AND, OR, etc.).
- ***dontparse*** boolean, optional, default false. If true the articles' content will not be parsed.

E.g.: `{{ html.getArticlesByFilter(['author', 'BraDypUS']) }}`

or

`{{ html.getArticlesByFilter([['author', '%BraDypUS%', 'LIKE'], ['title', '%hello%', 'LIKE']], 'OR', true) }}`

```php
<?php
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
```

---

#### getArticlesByTag('tag1', 'tag2', 'ecc')
Returns array of articles arrays matching all the provided tags.
If no tag is provided as argument, URL tags will be used.
If any of the arguments is an array in the form of `['page', 'max']` these two
values will be used for pagination
- **tag1** string, optional, default false. First filtering tag
- **tag2** string, optional, default false. Second filtering tag
- **ecc** ...

E.g.: `{{ html.getArticlesByTag('news', 'web') }}` or

E.g.: `{{ html.getArticlesByTag([1, 10], 'news', 'web') }}`

```php
<?php
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
```
---

#### getArticlesByTagArray(tags, dontparse, page, max)
Returns array of articles arrays matching all tags provided in tags array.
If dontparse is not null, articles texts will not be parsed (customtags will not be replaced)
- **tags** array, requited. Array of tags
- **dontparse** boolean, default false. If true article texts will not be parsed
- **page** integer, default 1. Current page number
- **max** integer, default 20. Maximum of records to show in each page

E.g.: `{{ html.getArticlesByTag(['news', 'web']) }}`

```php
<?php
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
```

---

#### getArticleTags()
Returns array of tags for current article

E.g.: `{{ html.getArticleTags }}`

```php
<?php
array(
  'tag1',
  'tag2',
  'etc'
)
```

---

#### getContext()
Returns context of usage: article, tags, search or home

E.g.: `{{ html.getContext }}`

```html
home
```
---

#### getData('index')
Returns array of user defined static data. User data are set as JSON object using UserData plugin
- **index** string, default false. Array index to return (use dot notation for tree-like structure)

E.g.: `{{ html.getData }}`

```php
<?php
array(
  "test" => array(
    "one" => "1",
    "two" => "2",
    "etc" => "et caetera"
    ),
  "test1" => array(
    "one1" => "1.1",
    "two1" => "2.1",
    "etc1" => "et caetera 1"
    )
)
```

E.g. 2: `{{ html.getData('test.etc') }}`

```
et caetera
```

---


#### getDownloadNode('node')
Returns array with data for available files in download node
- **node** string, required. Name of download node to display

E.g.: `{{ html.getDownloadNode('curricula') }}`

```php
<?php
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
```

---

#### getFilterTags()
Returns array of URL tags used as filters

E.g.: `{{ html.getFilterTags }}`

```php
<?php
array(
  'news',
  'web'
)
```

---

#### getGallery('gallery', 'thumb_dim')
Returns array with data for gallery name
- **gallery** string, required. Name of gallery to get data for
- **thumb_dim** string, optional, default 200x200. Dimensions of the thumbnails, following the {integer}x{integer} format

E.g.: `{{ html.getGallery('our_works', '200x200') }}`

```php
<?php
array (
  array (
   img => "sites/default/images/galleries/our_works/picture01.jpg",
   thumb => "./cache/galleries/200x200/fc/cf/fccf1c1db130ca6f420445c75c84f4d9.jpg",
   caption => "Caption of picture01",
   href => "http://some.url"
  ),
 ...
);
```

---

#### getLanguages()
Returns array of available languages

E.g.: `html.getLanguages`

```php
<?php
array(
  array(
    'code'=>'it',
    'string'=>'Italiano',
    'is_current'=>true,
    'href'=>'http://bradypus.net/it'
  ),
  array(...)
)
```
---

#### getMD()
Include Metadata object file, if exists, initializes object and returns it.

E.g.: ` {{ html.getMD }}`


---

#### getPageData('el', 'escape')
Returns document metadata array (all metadata). Available document metadata are: title (custom titles for single articles, different from article's title field, can be set using a custom field named customtitle), description, keywords, robots (custom robots meta tag values for single articles, different from the configuration global setting, can be set using a custom field named robots), lang, url, image (may be false. It's the src of the first image of the article's content), author (may be false. It's the article's author), date (may be false. It's the article's date of publication), publisher (may be false. It's the OAI config publisher parameter)
- **el** string, optional. If present, only this element will be returned; otherwise, the entire array will be returned
- **escape** boolean, optional, default: false. If true the sigle quotes of the string will be escaped

E.g.: `{{ html.getPageData.title }}`

```
Home Page
```

---

#### getMenu('menuName')
Returns structured array of menu data for $menu_name.
- **menuname** string, required. Name of menu to show

---

#### getPagination()
Array with pagination data for current page: start, end, current

E.g.: `{{ html.getPagination }}`
```php
array(
  'start' => '1',
  'end' => 10,
  'current' => 7
)
```
---

```php
<?php
array(
  'start' => '1',
  'end' => 10,
  'current' => 7
)
```

---

#### getParts('index')
Returns array of URL parts
- **index** int, optional, default: false. Index of the array to return

E.g.: `{{ html.getParts }}` for URL: http://bradypus.net/start/contact_us
```php
array(
  0 => 'start',
  1 => 'contact_us'
)
```

---

E.g.: `{{ html.getParts(0) }}`
```
start
```


---

#### getTextId()
Returns article's text id required in URL

E.g.: `{{ html.getTextId() }}`
```
contact_us
```

---

#### getTotal()
Returns total number of articles available for current filter. Useful for pagination

---

#### getSearchResults('string', 'page', 'max')
Returns array of article arrays matching the searched string
- **string** string, optional, default false. If present this string will be used to filter articles, otherwise the URL search parameter will be used.
- **page**, integer, optional, default 1. Current page number
- **max**, integer, optional, default 20. Maximum of records to show in each page

E.g.: `{{ html.getSearchResults }}` or `{{ html.getSearchResults('something') }}`

```php
<?php
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
```
---

#### getSearchString('escape')
Returns string used as filter in URL. Available only if the context is found
- **escape** boolean, optional, default false. If true all applicable characters of the string will be converted to HTML entities

E.g.: ` {{html.getSearchString }}`

```
web
```

---

#### getSimilar('textid', 'max')
Returns array of article arrays matching the most similar (having the same tags) results compared to the article with given textid or to the current article
- **textid** string, optional, default false. Text id to use for comparison
- **max** integer, optional default false. Number of articles to return

E.g.: `{{ html.getSimilar }}`

```php
<?php
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
```

---

#### getVersion
Returns installed and running version of BraDyCMS according to [semver syntax](http://semver.org/)

E.g.: `{{ html.getVersion }}`

```
3.7.0
```
---

#### gtag()
Returns javascript code for Google Global Tag (gtag.js). Google Analytics id must be provided in the site configuration file. A domain is provided in the configuration file; the code will be shown only if current domain matches the provided domain. This is very useful in test installations.

E.g.: `{{ html.gtag }}`

```javascript
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA-ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA-ID');
</script>
```
---
#### GUA()
Returns javascript code for Google Universal Analytics Tracking. Google Analytics id must be provided in the site configuration file. A domain is provided in the configuration file; the code will be shown only if current domain matches the provided domain. This is very useful in test installations.

E.g.: `{{ html.GUA }}`

```javascript
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'GA-ID', 'auto');
  ga('send', 'pageview');
</script>
```
---

#### isDraft
Returns true if currently viewed article is a draft and false if not
E.g.: `{{ html.isDraft }}`

---

#### langMenu('flags', 'no_text')
Returns well-formatted html of language menu. Current language's list item (li) has class current
- **flags** boolean, optional, default false. If true image flags will be shown
- **no_text** boolean, optional, default false. If true no language text will be shown

E.g.: `{{ html.langMenu(true) }}`

```html
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
```

---

#### link2('resource', 'is_tag', 'page')
Returns relative link string to resource or to site homepage (if resource is home), depending on current url
- **resource** string|array, required. If string, it's textid of an article. If home, then a link to the homepage will be created. If an array and tags is true, a link to a list of tags will be created
- **is_tag**, boolean, default false. If true, resources will be treated as tag or tag list, is set to 'search' a search link will be returned
- **page**, integer, optional, default false. If provided the link will have a fixed
- **absl_link**, boolean, optional, default false. If true the absolute link will be returned, instead of the default relative path
reference to a page

E.g.: `{{ html.link2 }}`
    ./

E.g.: `{{ html.link2('news') }}`
```
./news
```

E.g.: `{{ html.link2('news', 'true') }}`
```
./news.all
```

E.g.: `{{ html.link2('news', 'true', 2) }}`
```
./news.all/2
```

E.g.: `{{ html.link2('news', 'search') }}`
```
./search:news
```

E.g.: `{{ html.link2('news', '', '', 'true') }}`
```
http://your.base.domain/news
```

---

#### loginForm(css)
Returns well formatted html and javascript (jquery is required and should be already loaded) code to securely perform a login action
- **css** object, optional. An object containing several css classes to use for the form output. The following options are supported
  - css.form: class to apply to main form element
  - css.error: class to apply to error text container
  - css.email_cont: class to apply to email input container
  - css.email_input: class to apply to email input element
  - css.password_cont: class to apply to password input container
  - css.password_input: class to apply to password input element
  - css.submit_cont: class to apply to submit input container
  - css.submit_input: class to apply to submit input element

E.g.: `{{ html.loginForm }}` (simple) or `{{ html.loginForm({'form':'form-inline', 'error':'text-error', 'email_cont': 'form-group', 'email_input': 'form-control', 'password_cont': 'form-group', 'password_input': 'form-control', 'submit_cont': 'form-group', 'submit_input': 'btn btn-success' }) }}` (full)

```html
<form action="javascript:void(0);" id="loginform" class="form-inline">

  <div id="error" class="text-error"></div>

  <div class="email form-group">
    <input type="email" name="email"
      placeholder="Email address"
      class="form-control">
  </div>

  <div class="password form-group">
    <input type="password" name="password"
      placeholder="Password"
      class="form-control">
  </div>

  <input type="hidden" name="token" value="token-value">

  <div class="submit form-group">
    <input type="submit" class="submit form-control">
  </div>
</form>


<script>
  form submission script...
</script>
```

---

#### logoutButton(css)
Returns well formatted html and javascript (jquery is required and should be already loaded) code to securely perform a logout action
- **css** object, optional. An object containing several css classes to use for the form output. The following options are supported
  - css.logout_cont: class to apply to button container
  - css.logout_input: class to apply to button element

E.g.: `{{ html.logoutButton }}` (simple) or `{{ html.logoutButton({'logout_cont': 'form-group', 'logout_input': 'form-control' }) }}` (full)

```html
<div class="logout_cont form-group">
  <button id="logoutbutton" class="form-control">
    Logout
  </button>
</div>

<script>
  Logout script here...
</script>
```
---

#### menu('menu', 'class', 'strip')
Returns well-formatted html of menu items
- **menu** string, required textual. Id of the menu to show
- **class** string, optional, default false. Css class (or space separated classes) of the menu's unordered list (ul)
- **strip** boolean, optional, default false. If true bootstrap style markup will not be shown

E.g.: `{{ html.menu('main', 'nav') }}`
```html
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
 ```

---

#### metadata('no_og')
Returns well-formatted html code of page html and open graph metadata and links to feeds
- **no_og** boolean, optional, default: false. If true the open graph metadata will not be shown

E.g.: `{{ html.metadata() }}`

```html
<title>Home Page</title>
<meta name="description" content="Web sites main description" />
<meta name="keywords" content="home, page, keyword1, keyword2" />
<meta lang="en" />
<link rel="alternate" href="http://bradypus.net" hreflang="it" />
<link rel="alternate" href="http:/bradypus.net/en" hreflang="en" />
<!-- Open Graph metadata -->
<meta property="og:title" content="Home Page" />
<meta property="og:description" content="Web sites main description" />
<meta property="og:url" content="http://thishost/thispage" />
<meta property="og:image" content="http://thishost/path_to_the_first_image_of_article_body_if_exist.extension" />

<!-- Feed links -->
<link rel="alternate" type="application/rss+xml" title="RSS" href="/feed/rss" />
<link rel="alternate" type="application/atom" title="RSS" href="/feed/atom" />
```

---

#### medatada_dc()
Returns well-formatted html code with Dublin Core html metadata. Title, description, language, identifier (url), subject (keywords) are the same as html & og metadata. Date is the date of publication of the article publisher is the publsher name as defined in OAI module configuration

E.g.: `{{ html.metadata_dc }}`

```html
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
```

---

#### metadata_hp()
Returns html with Highwire Press metadata, for Google Scholar usage

E.g.: `{{ html.metadata_hp }}`

```html
<meta name="citation_journal_title" content=""/>
<meta name="citation_issn" content="Lorem ipsum"/>
<meta name="citation_author" content="Lorem ipsum" />
<meta name="citation_title" content="Lorem ipsum" />
<meta name="citation_date" content="0000-00-00" />
<meta name="citation_doi" content="10/123456.23456"/>
<meta name="citation_abstract_html_url" content="http://lorem.ipsum"/>
<meta name="citation_language" content="en" />
<meta name="citation_pdf_url" content="http://lorem.ipsum.pdf"/>
```

---

#### pagination('cssClass')
Returns well-formatted html with unordered list (ul) of pagination data when available
depending on context. Current page will have css class `active`
- **cssClass** string, optional, default false. Css class to apply to main `ul`

E.g.: `{{ html.pagination('pagination') }}`

```html
<ul class="pagination">
  <li class="disabled"><a href="page1">1</a>
  <li class="disabled"><a href="page2">2</a>
  <li class="disabled"><a class="disabled" href="#">...</a>
  <li class="disabled"><a class="active" href="page7">7</a>
  <li class="disabled"><a class="disabled" href="#">...</a>
  <li class="disabled"><a href="page15">15</a>
  <li class="disabled"><a href="page16">16</a>
</ul>
```

---

#### registerForm(tag, css)
Returns well formatted html and javascript (jquery is required and should be already loaded) code to securely perform user registration and regisrtation confirmation
-**tag** string, required. Tag for which to enable user registration
- **css** object, optional. An object containing several css classes to use for the form output. The following options are supported
  - css.form: class to apply to main form element
  - css.error: class to apply to error text container
  - css.email_cont: class to apply to email input container
  - css.email_input: class to apply to email input element
  - css.select_cont: class to apply to drop-down-menu select mode (registration/confirmation) container
  - css.select_input:class to apply to drop-down-menu select mode (registration/confirmation) input
  - css.confirmationcode_cont: class to apply to code confirmation  container
  - css.confirmationcode_input:class to apply to code confirmation input
  - css.password_cont: class to apply to password input container
  - css.password_input: class to apply to password input element
  - css.submit_cont: class to apply to submit input container
  - css.submit_input: class to apply to submit input element

E.g.: `{{ html.loginForm('protected') }}` (simple) or `{{ html.loginForm('protected', {'form':'form-inline', 'error':'text-error', 'email_cont': 'form-group', 'email_input': 'form-control', 'select_cont': 'form-group', 'select_input': 'form-control', 'password_cont': 'form-group', 'password_input': 'form-control', 'confirmationcode_cont': 'form-group', 'confirmationcode_input': 'form-control', 'submit_cont': 'form-group', 'submit_input': 'btn btn-success' }) }}` (full)

```html
<form action="javascript:void(0);" id="registerform" class="form-inline">

  <input type="hidden" name="token" value="token-value">
  <input type="hidden" name="tag"value="protected">

  <div id="error" class="text-error"></div>

  <div class="email form-group">
    <input type="email" name="email"
      placeholder="Email address"
      class="form-control">
  </div>

  <div class="password form-group">
    <input type="password" name="password"
      placeholder="Password"
      class="form-control">
  </div>

  <div class="select form-group">
    <select
      class="select form-control">
      <option value="new">New user</option>
      <option value="confirm">Confirm user</option>
    </select>
  </div>

  <div class="confirmationcode form-group">
    <input type="text" name="confirmationcode"
      class="form-control"
      placeholder="Confirmation code">
  </div>

  <div class="repeatpassword form-group">
    <input type="text" name="repeatpassword"
      placeholder="Repeat password"
      class="form-control">
  </div>

  <div class="submit form-group">
    <input type="submit" class="submit form-control">
  </div>
</form>


<script>
  form submission script...
</script>
```

---

#### queue('key')
Returns queued string or runs queued function defined by key. A typical key,
used for all system core modules javascrit is 'modules'
- **key** string, optional, default 'modules'. Name of queue to load

E.g.: `{{ html.queue }} same as {{ html.queue{'modules'} }}`

```javascrit
<script>
  $('#searchForm').submit(function(){
    if($('#search').val() !== '' ){
      window.location = $(this).data('path') + '/search:' + encodeURIComponent($('#search').val());
    }
  });
</script>
```

---

#### tagBlog('tag1', 'tag2', 'ecc')
Returns well-formatted html with list of articles tagged with tags provided as arguments or present in URL
- **tag1** string, optional, default false. First tag to use as filter
- **tag2** string, optional, default false. Second tag to use as filter
- **ecc** ...

E.g.: `{{ html.tagBlog('news', 'web') }}`

```html
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
```

---

#### TBSjs('version')
Dropped support in favor of html.asset('bootstrap', 'js')

---

#### tr('string', 'args', 'escape')
Returns the translated value for the given string in the current language
- **string** string, required. String to translate (translation ID)
- **args** string|array, optional, default false. String, or array of strings, to use for replacment of placeholders (%s)
- **escape** boolean, optional, default false. If true the single quotes will be replaced in the resulting string

E.g.: `tr('hello_world')`
```
Ciao mondo!
```

---

#### searchForm()
Returns well-formatted html of search form input. Form submission should be done with javascript. The data attribute contains the path to the site root!

E.g.: `{{ html.searchForm }}`

```html
<form action="javascript:void(0);" id="searchForm" data-path="./">
  <input class="search" type="search" placeholder="Search in web site" name="search" id="search" />
</form>
```

---

#### searchResults()
Returns well-formatted html with list of articles found by search. This function will return false if context is not found!

E.g.: `{{ html.searchResults }}`

```html
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
```

---

#### similarBlog('max')
Returns well-formatted html with list of similar articles (having the same tags) as current article.
- **max** integer, optional default false. Number of articles to return

E.g.: `{{ html.similarBlog}}`

```html
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
```

{% endraw %}
