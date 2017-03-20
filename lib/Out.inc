<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Feb 16, 2013
 * @uses      utils
 * @used      cfg
 */

class Out
{
  private
    $context,  // context: article, tags, search, home
    $data = array(),  //
    $metadata,
    $mobiledetect,
    $totalArticles,
    $queue
    ;
  protected $get = array();

  public
    // $_SESSION['lang'], if set
    $session_lang,
    // $_SESSION['lang'] only if different from main configuration language
    $lang,
    // Current user language, always set: it will be the $_SESSION['lang'] or main configuration language
    $user_lang;

  /**
   * Starts object, sets context and language data
   * @param array $get  array of $_GET data
   * @param string $lang  language, set in index using $_SESSION['lang'] variable
   */
  public function __construct($get, $lang = false)
  {
    /**
     * Set get
     */
    $this->get = $get;

    /**
     * Explode tags using ~ or - (AND or OR)
     */
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

    /**
     * Set page
     */
    if ($this->get['page'])
    {
      $this->get['page'] = str_replace('/', null, $this->get['page']);
    }

    /**
     * Set language
     */
    if ($lang AND ( $lang != cfg::get('sys_lang') ) )
    {
      $this->lang = $lang;
    }

    if ($lang)
    {
      $this->session_lang = $lang;
    }

    $this->user_lang = $lang ? $lang : cfg::get('sys_lang');

    /**
     * Set context
     */
    if ( $get['art_title'] )
    {
      $this->context = 'article';

      // load curent article's data in $data['article'] array
      $this->data['article'][$get['art_title']] = Article::getByTextid($get['art_title'], $this->lang, $this->get['draft']);
    }
    else if ( $get['search'] || $get['is_search'])
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
   *
   * @param string $gal
   * @return boolean|false Array with gallery data or fale if gallery does not exist or is not well-formatted
   */
  public function getGallery($gal)
  {
    try
    {
      return Gallery::get($gal);
    }
    catch (Exception $e)
    {
      return false;
    }
  }

  /**
   * Returns textid specified in URL
   * @return string
   */
  public function getTextId()
  {
    return $this->get['art_title'];
  }

  /**
   * Returns number of all articles found by a query, search ot tag filtering
   * @return int
   */
  public function getTotal()
  {
    return $this->totalArticles;
  }

  /**
   * Returns translated string
   * @param string $string  string to translate
   * @param mixed: string|array|false $args if true tr::sget will be used
   * @param boolean $escape  if true the single quotes will be escaped (usefull for usage in javascript scripts)
   * @return string
   */
  public function tr($string, $args = false, $escape = false)
  {
    return $args ? tr::sget($string, $args, $escape) : tr::get($string, $escape);
  }

  /**
   * Returns full link to provided content (article or section)
   *
   * @param array|string $art  article or section name
   * @param boolean $tags  array of tags to use for filtering section articles
   * @param integer $page Page number, default false
   * @param boolean $full_link  If true the absolute link will be returned.
   * @return string
   */
  public function link2($art, $tags = false, $page = false, $absl_link = false)
  {
    !$page ? $page = $this->get['page'] : '';

    $url = '';

    switch($tags)
    {
      case 'search':
        $url = link::to_search($art, $this->session_lang, $page, $this->get['is_search'], $page);
        break;
      case true:
        $url = link::to_tags($art, $this->session_lang, $page);
        break;
      default:
        $url = link::to_article($art == 'home' ? './' : $art, $this->session_lang, $page);
        break;
    }

    if ($absl_link)
    {
      $url = utils::getBaseUrl() . str_replace('./', null, $url);
    }

    return $url;
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
  public function getArticleTags($article = false)
  {
    if (!$article)
    {
      $article = $this->get['art_title'];
    }

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
   * Returns curent context. Can be one of the following: article, tags, search, home
   * @return string
   */
  public function getContext()
  {
    return $this->context;
  }


  /**
   * Returns array of similar articles
   * @param string $textid articles' textid. Default false (current art_title will be used)
   * @param int $max maximum number of array elements to return
   * @return array of beans|false
   */
  public function getSimilar($textid = false, $max = false)
  {
    $textid = $textid ? $textid : $this->get['art_title'];

    return $textid ? Article::getSimilar(false, $textid, $this->lang, $max) : false;
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
      $this->data['article'][$article]['text'] = customTags::parseContent($this->data['article'][$article]['text'], $this, $this->data['article'][$article]['id']);
    }

    return $this->data['article'][$article];
  }


  /**
   * Returns list of articles filtered by one or more tags. The tags can be specified as method arguments, if not current GET tags will be used
   * @param $tag, if present, the list of arguments to be the filtering tags. If one of the parameters is an array, this is assumed to be the pagination information: [$page, $max]
   * @return array|false
   */
  public function getArticlesByTag()
  {
    $tags = func_get_args() ? func_get_args() : $this->getFilterTags();

    if (!is_array($tags))
    {
      return false;
    }

    foreach ($tags as $x=>&$t)
    {
      if(is_array($t))
      {
        list($page, $max) = $t;
        unset($tags[$x]);
      }
    }

    return $this->getArticlesByTagArray($tags, false, $page, $max);
  }


  /**
   * Return list of articles filtered by one or more tags
   * @param array $tags array of tags
   * @param boolean $dontparse if true article content will not be parsed. Default false
   * @param int $page Current page number, default 1
   * @param int $max Maximum of records to show in each page, default 20
   * @return type
   */
  public function getArticlesByTagArray($tags, $dontparse = false, $page = false, $max = false)
  {
    list($start, $max) = $this->page2limit($page, $max);

    $this->totalArticles = Article::getByTag($tags, $this->lang, $this->get['tag_all'], false, false, false, true);

    $arts = Article::getByTag($tags, $this->lang, $this->get['tag_all'], false, $start, $max);

    if (is_array($arts) && !$dontparse)
    {
      foreach($arts as &$art)
      {
        $art['text'] = customTags::parseContent($art['text'], $this, $art['id']);
      }
    }
    return $arts;
  }

  /**
   * Returns metadata about the current page, filtered by $el
   * @param string $el page element to return
   * @param boolean $escape if true double apices will be escaped
   * @return string|array
   */
  public function getPageData($el = false, $escape = false)
  {
    if (!$this->data['page'])
    {
      $this->setPageData();
    }

    return $el ? ( $escape ? str_replace('"', '\"', $this->data['page'][$el]) : $this->data['page'][$el] ) : $this->data['page'];
  }


  /**
   * Return device type; one of: computer | tablet | phone
   * @return string one of: computer|tablet|phone
   */
  public function getDevice()
  {
    if (!$this->mobiledetect)
    {
      $this->mobiledetect = new Mobile_Detect();
    }

    return ($this->mobiledetect->isMobile() ? ($this->mobiledetect->isTablet() ? 'tablet' : 'phone') : 'computer');
  }

  /**
   * Sets $data['page'] info
   *   site_name: site name as set in main config option
   *   robots:    as set in artcle's optional robots field, or as set in main config option, or "index, follow"
   *   title:
   *   description
   *   keywords
   *   lang
   *   url
   *   mission
   *   image
   *   author
   *   date
   */
  private function setPageData()
  {
    $path_arr = explode('/', $_SERVER['REQUEST_URI']);
    $path = $path_arr[count($path_arr)-1];

    $seo = Seo::get($path, $this->lang);

    // SITE NAME
    $this->data['page']['site_name'] = cfg::get('name');


    // ROBOTS
    if ($this->data['article'][$this->get['art_title']]['robots'])
    {
      // 1. get value from the custom field named robots
      $this->data['page']['robots'] = $this->data['article'][$this->get['art_title']]['robots'];
    }
    else if(cfg::get('robots'))
    {
      // 2. get value from main site configuration (robots)
      $this->data['page']['robots'] = cfg::get('robots');
    }
    else
    {
      // 3. default value: "index, follow"
      $this->data['page']['robots'] = 'index, follow';
    }

    // TITLE
    if ($this->data['article'][$this->get['art_title']]['customtitle'])
    {
      // 1. get value from the custom field named customtitle
      $this->data['page']['custom_title'] = str_replace('"', '&quot;', $this->data['article'][$this->get['art_title']]['customtitle']);
    }
    if ($seo && $seo['title'])
    {
      // 2. SEO table value
      $this->data['page']['title'] = $seo['title'];
    }
    else if ($this->data['article'][$this->get['art_title']]['title'])
    {
      // 3. use the value of the field named "title"
      $this->data['page']['title'] = $this->data['article'][$this->get['art_title']]['title'];
    }
    else if ($this->context === 'tags')
    {
      // 4. if context is tags the main site configuration (title) will be used followed by comma-separated-list of filtering tgs
      $this->data['page']['title'] = cfg::get('title') . ' / ' .  implode(', ', $this->getFilterTags());
    }
    else
    {
      // 5. default value is main site configuration value (title)
      $this->data['page']['title'] = cfg::get('title');
    }
    // Replace double quotes with html character in Title
    $this->data['page']['title'] = str_replace('"', '&quot;', $this->data['page']['title']);

    // DESCRIPTION
    if ($seo && $seo['description'])
    {
      // 1. Use Seo table value
      $this->data['page']['description'] = $seo['description'];
    }
    else if ($this->data['article'][$this->get['art_title']]['customdescription'])
    {
      // 2. get value from the custom field named customdescription
      $this->data['page']['description'] = str_replace('"', '&quot;', $this->data['article'][$this->get['art_title']]['customdescription']);
    }
    else if ( $this->data['article'][$this->get['art_title']]['summary'] && trim(strip_tags($this->data['article'][$this->get['art_title']]['summary'])) != '' )
    {
      // 3. use the value of the field named "summary"
      $this->data['page']['description'] = str_replace('"', '&quot;', trim(strip_tags($this->data['article'][$this->get['art_title']]['summary'])));
    }
    else if ($this->context === 'tags')
    {
      // 4. if context is tags the main site configuration (description) will be used followed by comma-separated-list of filtering tgs
      $this->data['page']['description'] = str_replace('"', '&quot;', cfg::get('description') . ' / ' .  implode(', ', $this->getFilterTags()));
    }
    else
    {
      // 5. default value is main site configuration value (description)
      $this->data['page']['description'] = str_replace('"', '&quot;', cfg::get('description'));
    }
    // truncate description up to 500 digits
    if (strlen($this->data['page']['description']) > 500)
    {
      $this->data['page']['description'] = str_replace('"', '&quot;', substr($this->data['page']['description'], 0, 497) . '...');
    }

    // Replace double quotes with html character in Description
    $this->data['page']['description'] = str_replace('"', '&quot;', $this->data['page']['description']);

    // KEYWORDS
    if ($seo && $seo['keywords'])
    {
      $this->data['page']['keywords'] = $seo['keywords'];
    }
    else if ($this->data['article'][$this->get['art_title']]['keywords'])
    {
      // 1. use the value of the field named "keywords"
      $this->data['page']['keywords'] = $this->data['article'][$this->get['art_title']]['keywords'];
    }
    else
    {
      // 2. default value is main site configuration value (keywords)
      $this->data['page']['keywords'] = cfg::get('keywords');
    }
    // Replace double quotes with html character in Keywords
    $this->data['page']['keywords'] = str_replace('"', '&quot;', $this->data['page']['keywords']);

    // LANG
    $this->data['page']['lang'] = $this->lang ? $this->lang: cfg::get('sys_lang');

    // URL
    $this->data['page']['url']  = utils::getBaseUrl() . str_replace(cfg::get('rewriteBase'), null, $_SERVER['REQUEST_URI']);

    // MISSION
    $this->data['page']['mission'] = cfg::get('mission');

    // IMAGE
    $path2img = str_replace(utils::getBaseUrl(), null, $this->data['article'][$this->get['art_title']]['art_img']['orig']);

    if (file_exists($path2img))
    {
      // 1. check first for article image
      $this->data['page']['image'] = utils::getBaseUrl() . $path2img;
    }
    else if ($this->data['article'][$this->get['art_title']]['text'])
    {
      // 2. get first image available in the article body
      $xpath = new DOMXPath(@DOMDocument::loadHTML($this->data['article'][$this->get['art_title']]['text']));
      $src = $xpath->evaluate("string(//img/@src)");

      $this->data['page']['image'] = ( !preg_match('/http/', $src) ? utils::getBaseUrl() . '/' : '') . $src;
    }

    // AUTHOR
    if (
      $this->data['article'][$this->get['art_title']]['author']
      &&
      trim($this->data['article'][$this->get['art_title']]['author']) != ''
    )
    {
      $this->data['page']['author'] = $this->data['article'][$this->get['art_title']]['author'];
    }

    // DATE
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
      else if ($this->context === 'tags' && (
        $item['href'] === implode('-', $this->getFilterTags()) . '.all' ||
        $item['href'] === implode('~', $this->getFilterTags()) . '.all' ||
        $item['href'] === './' . implode('-', $this->getFilterTags()) . '.all' ||
        $item['href'] === './' . implode('~', $this->getFilterTags()) . '.all'
        ))
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
   * @param string $string Custom string to search in the database
   * @param int $page Current page number, default 1
   * @param int $max Maximum of records to show in each page, default 20
   * @return array|false
   */
  public function getSearchResults($string = false, $page = false, $max = false)
  {
    $string = $string ? $string : $this->get['search'];
    $string = preg_replace('/^"(.+)"$/', '$1', $string, -1, $tot);

    $this->totalArticles = Article::search($string, false, false, false, false, true);

    list($start, $max) = $this->page2limit($page, $max);

    return Article::search($string, ($tot > 0), $this->lang, $start, $max);
  }

  /**
   * Returns, if available, the searched string
   * @param boolean $escape
   * @return string|false
   */
  public function getSearchString($escape = false)
  {
    return $escape ? htmlentities($this->get['search']) : $this->get['search'];
  }


  /**
   * Returns, if available, a new instance of object M(eta)D(ata)_repository
   * @return boolean|Metadata
   */
  public function getMD()
  {
    if (!$this->metadata)
    {
      $this->metadata = new Metadata('./sites/default/modules/metadataRepo/metadata.json');
    }

    return $this->metadata;
  }

  /**
   * Returns array of data for download node. Foreach file the following information is returned:
   *  path
   *  ext
   *  title
   *  description
   * @param string $node Download node name
   * @return false|array
   */
  public function getDownloadNode($node)
  {
    return customTags::download(
      array(
        'content' => $node,
        'getObject' => true
        )
      );

  }

  /**
   * Returns installed and running version of BraDyCMS
   * @return string
   */
  public function getVersion()
  {
    return version::current();
  }
  /**
   * Returns array of articles matching query parameters contained in $filter of false if nothing is matched
   * @param array $filter (Multidimensional) array with query parameters:
   *      [field, value, operator] or
   *      [[field, value, operator], [field, value, operator], ...]
   * @param string|false $connector Logical connector to use for query parts; Default AND
   * @param boolean $dontparse If true article content will not be parsed. Default false
   * @return array|false array of articles of false
   */
  public function getArticlesByFilter($filter, $connector = false, $dontparse = false, $page = false, $max = false)
  {
    list($start, $max) = $this->page2limit($page, $max);

    $this->totalArticles = Article::getByFilter($filter, false, $connector, false, false, true);

    $arts = Article::getByFilter($filter, $this->lang, $connector, $start, $max);

    if (is_array($arts) && !$dontparse)
    {
      foreach($arts as &$art)
      {
        $art['text'] = customTags::parseContent($art['text'], $this, $art['id']);
      }
    }
    return $arts;
  }

  /**
   * Returns array with start and max
   * @param int $page Current page name, default: 1
   * @param int $max Maximum of records to show on page. A per-site default value can be set in the config file, otherwize 20
   * @return array Array with calculated start and $max values
   */
  private function page2limit($page = false, $max = false)
  {
    $max_cfg = cfg::get('pagination');

    if ($page || $this->get['page'] || $max_cfg)
    {
      if (!$page)
      {
        $page = $this->get['page'] ? $this->get['page'] : 1;
      }

      if (!$max)
      {
        $max = $max_cfg ? $max_cfg : 20;
      }

      $start = ( ($page -1 ) * $max);
    }

    return array($start, $max);
  }

  /**
   *
   * @return array: Array with pagination data for current page: start, end, current
   */
  public function getPagination()
  {
    $return = false;
    $max = cfg::get('pagination') ? cfg::get('pagination') : 20;
    $page = $this->get['page'] ? $this->get['page'] : 1;
    $tot = $this->totalArticles;
    $return['current'] = $page;
    if ($tot && $max && $tot > $max)
    {
      $return['start'] = '1';
      $return['end'] = ceil($tot/$max);
    }
    return $return;
  }

  /**
   * Adds a new element to the main queue
   * @param string $key   queue element id
   * @param mixed $value Queue element value, a string or a callable
   * @param boolean $concatenate if true the value will be concatenated to existent value
   */
  public function setQueue($key, $value, $concatenate = false)
  {
    if ($concatenate)
    {
      $current = $this->getQueue($key);
      $value = $current . $value;
    }
    $this->queue[$key] = $value;
  }

  /**
   * Gets and returns a queue element. If the element is a callable, it will be called
   * @param  string $key queue element id
   * @return mixed      string or callable result
   */
  public function getQueue($key = 'modules')
  {
    if (isset($this->queue[$key]))
    {
      if (is_string($this->queue[$key]))
      {
        return $this->queue[$key];
      }
      else if (is_callable($this->queue[$key]))
      {
        return call_user_func($this->queue[$key]);
      }
    }
  }

  /**
   * Gets and returns array (or array segment, using dot notation) of template static data
   * Stored in sites/default/modules/tmpldata/tmpldata.json
   * Returns false if no JSON object is available or if has any syntax error
   * @param  string|false $key index or index series (dor notation) of array to return. If false the whole array will be returned
   * @return mixed       false, current vale or complete array of data
   */
  public function getData($key = false)
  {
    $datafile = SITE_DIR . 'modules/tmpldata/tmpldata.json';
    // Return false if data file is not available
    if(!file_exists($datafile)){
      return false;
    }
    // Convert JSON to indexes array
    $data = json_decode(file_get_contents( $datafile ), true);

    // If conversione fails (syntax error) return false and write log file
    if (!$data || ! is_array($data)){
      error_log("Syntax error in file: " . $datafile);
      return false;
    }

    // Return the whole object if no $key is defined
    if (!$key)
    {
      return $data;
    }
    // Loop into key to return requested index
    // https://github.com/dflydev/dflydev-dot-access-data/blob/master/src/Dflydev/DotAccessData/Data.php
    $currentValue = $data;
    $keyPath = explode('.', $key);
    for ( $i = 0; $i < count($keyPath); $i++ )
    {
      $currentKey = $keyPath[$i];
      if (!isset($currentValue[$currentKey]) )
      {
          return false;
      }
      if (!is_array($currentValue))
      {
          return false;
      }
      $currentValue = $currentValue[$currentKey];
    }
    return (!$currentValue || $currentValue === null) ? false : $currentValue;
  }


}
