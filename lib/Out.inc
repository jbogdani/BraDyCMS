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
    $cfg,
    $data = [],  // cached data
    $metadata,
    $mobiledetect,
    $totalArticles,
    $queue;

  /**
   * Starts object, sets context and language data
   * @param array $get  array of $_GET data
   */
  public function __construct($get)
  {
    $this->loadSettings($get);
  }

  /**
   * Loads settings in $this->cfg variable from $get array
   * @param  array $get  input data
   *                 page       string  current page, for paginates content
   *                 art_title  string  current article's textid
   *                 draft      boolean if true draft view is required
   *                 search     string  searched string
   *                 tags       string  list of tags separated by - or ~
   * @param  string|false $lang Input (url) language
   * @return array       $this->cfg array with data to be used in this object and its extensions
   *                            page    int  current page
   *                            lang    string
   *                            context
   *                            textId  string  Article's textid passed in URL
   *                            parts   array   URL parts
   *                            isDraft boolean if true draft version of article is required
   *                            searchString
   *                            tags    array   List of filtering tags
   *                            tagAll boolean if true article must be tagged witl all listed tags to be retrieved
   */
  private function loadSettings($get, $lang = false)
  {

    // Set page
    if ($get['page']) {
      $this->cfg['page'] = (int)str_replace('/', null, $get['page']);
    }

    // Languages: there used to be 3 public language related variables:
    //  lang:         $lang, if $lang != sys_lang
    //  There is only one now, index of cfg variable: input_lang
    $this->cfg['input_lang'] = $lang;

    // User input language
    $this->cfg['lang'] = $get['lang'];

    // Set context and context related data
    if ( $get['art_title'] ) {

      // 1. article
      $this->cfg['context'] = 'article';

      $exploded = explode('/', $get['art_title']);
      $this->cfg['textId'] = array_values(array_slice($exploded, -1))[0];
      $this->cfg['parts'] = $exploded;
      $this->cfg['isDraft'] = ($get['draft'] !== false);

    } else if ( $get['search'] ) {

      // 2. search
      $this->cfg['context'] = 'search';
      $this->cfg['searchString'] = $get['search'];

    } else if ( $get['tags'] ) {

      // 3. tags
      $this->cfg['context'] = 'tags';

      if(preg_match('/~/', $get['tags'])) {
        // All tags
        $this->cfg['tagAll'] = true;
        $this->cfg['tags'] = utils::csv_explode($get['tags'], '~');

      } else {
        // Any tag
        $this->cfg['tags'] = utils::csv_explode($get['tags'], '-');
      }
    }
    else
    {
      $this->cfg['context'] = 'home';
    }
    return $this->cfg;
  }

  /**
   * Return language or false. Type of language depends on $what and $return_always
   * @param  string|false $what     if 'input' or false, the input (in Out initialization) language will be returned, no matter if false
   *                                  if 'current' and $return_always is false the input language will be returned if it is not the same of the system language, otherwise false is returned
   *                                  if 'current' and $return_always is true: the input language OR the system language will be returned
   * @param  boolean $return_always if true a language will be returned, the sys language if no other information is available
   * @return string|false           Content language
   */
  public function getLang($what = false, $return_always = false)
  {
    switch ($what) {
      case 'current':
        if ($return_always) {
          return $this->cfg['lang'] ? $this->cfg['lang'] : cfg::get('sys_lang');
        } else {
          return ( $this->cfg['lang'] && $this->cfg['lang'] !== cfg::get('sys_lang') ) ? $this->cfg['lang'] : false;
        }
        break;

      case 'input':
      default:
        return $this->cfg['lang'];
        break;
    }
  }

  /**
   * Returns array with url parts (non language, non page, slash separated)
   * @return array url parts
   */
  public function getParts()
  {
    return $this->cfg['parts'];
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
    return $this->cfg['textId'];
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
   * @param mixed   $parts can be string (/ separated parts), true (or "true"): out::getParts() will be used, array or false
   * @return string
   */
  public function link2($art = false, $tags = false, $page = false, $absl_link = false, $parts = false)
  {
    $page = $page ? $page : $this->cfg['page'];

    if ($parts && ($parts === "true" || is_bool($parts)) ) {

      $parts = $this->getParts();
      array_pop($parts);

    } elseif ($parts && is_string($parts)) {

      $parts = explode('/', $parts);
    }

    $url = '';

    switch($tags)
    {
      case 'search':
        $url = link::to_search($art, $this->getLang('input'), $page);
        break;
      case true:
        $url = link::to_tags($art, $this->getLang('input'), $page);
        break;
      default:
        $url = link::to_article( !$art ? 'home' : $art, $this->getLang('input'), $page, $parts);
        break;
    }

    if ($absl_link)
    {
      $url = utils::getBaseUrl() . $url;
    }

    return $url;
  }


  /**
   * Returns array of currently used tags.
   * @return array|string
   */
  public function getFilterTags()
  {
    return $this->cfg['tags'];
  }

  /**
   * Returns array of tags used by current article
   * @return array|false
   */
  public function getArticleTags($article = false)
  {
    if ($this->getContext() !== 'article')
    {
      return false;
    }

    $article = $article ? $article :  $this->getTextId();


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
    return $this->cfg['context'];
  }


  /**
   * Returns array of similar articles
   * @param string $textid articles' textid. Default false (current art_title will be used)
   * @param int $max maximum number of array elements to return
   * @return array of beans|false
   */
  public function getSimilar($textid = false, $max = false)
  {
    $textid = $textid ? $textid : $this->getTextId();

    return $textid ? Article::getSimilar(false, $textid, $this->getLang('input'), $max) : false;
  }

    /**
   * Returns array with article data. If $article is not provided current article's data will be returned
   * @param string $article article's textid
   * @return array
   */
  public function getArticle($article = false, $draft = false)
  {
    $article = $article ? $article : $this->getTextId();

    $draft = $draft ? $draft : $this->cfg['isDraft'];

    // Check in cache first
    if ( $this->data['article'][$article] ) {
      return $this->data['article'][$article];
    }

    $this->data['article'][$article] = Article::getByTextid($article, $this->getLang('input'), $draft);

    // Article does not exist!
    if (empty($this->data['article'][$article])) {
      return false;
    }

    $other_flds = cfg::get('custom_fields');
    if ($other_flds) {
      foreach ($other_flds as $f) {
        if ($this->data['article'][$article][$f['name']] && $f['type'] == 'longtext'){
          $this->data['article'][$article][$f['name']] = customTags::parseContent($this->data['article'][$article][$f['name']], $this, $this->data['article'][$article]['id']);
        }
      }
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

    if (!is_array($tags)) {
      return false;
    }

    foreach ($tags as $x=>&$t) {

      if(is_array($t)) {
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

    $this->totalArticles = Article::getByTag($tags, $this->getLang('input'), $this->cfg['tagAll'], false, false, false, true);

    $arts = Article::getByTag($tags, $this->getLang('input'), $this->cfg['tagAll'], false, $start, $max);

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

    $seo = Seo::get($path, $this->getLang('input'));

    // SITE NAME
    $this->data['page']['site_name'] = cfg::get('name');


    // ROBOTS
    if ($this->data['article'][$this->getTextId()]['robots'])
    {
      // 1. get value from the custom field named robots
      $this->data['page']['robots'] = $this->data['article'][$this->getTextId()]['robots'];
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
    if ($this->data['article'][$this->getTextId()]['customtitle'])
    {
      // 1. get value from the custom field named customtitle
      $this->data['page']['custom_title'] = str_replace('"', '&quot;', $this->data['article'][$this->getTextId()]['customtitle']);
    }
    if ($seo && $seo['title'])
    {
      // 2. SEO table value
      $this->data['page']['title'] = $seo['title'];
    }
    else if ($this->data['article'][$this->getTextId()]['title'])
    {
      // 3. use the value of the field named "title"
      $this->data['page']['title'] = $this->data['article'][$this->getTextId()]['title'];
    }
    else if ($this->cfg['context'] === 'tags')
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
    else if ($this->data['article'][$this->getTextId()]['customdescription'])
    {
      // 2. get value from the custom field named customdescription
      $this->data['page']['description'] = str_replace('"', '&quot;', $this->data['article'][$this->getTextId()]['customdescription']);
    }
    else if ( $this->data['article'][$this->getTextId()]['summary'] && trim(strip_tags($this->data['article'][$this->getTextId()]['summary'])) != '' )
    {
      // 3. use the value of the field named "summary"
      $this->data['page']['description'] = str_replace('"', '&quot;', trim(strip_tags($this->data['article'][$this->getTextId()]['summary'])));
    }
    else if ($this->cfg['context'] === 'tags')
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
    else if ($this->data['article'][$this->getTextId()]['keywords'])
    {
      // 1. use the value of the field named "keywords"
      $this->data['page']['keywords'] = $this->data['article'][$this->getTextId()]['keywords'];
    }
    else
    {
      // 2. default value is main site configuration value (keywords)
      $this->data['page']['keywords'] = cfg::get('keywords');
    }
    // Replace double quotes with html character in Keywords
    $this->data['page']['keywords'] = str_replace('"', '&quot;', $this->data['page']['keywords']);

    // LANG
    $this->data['page']['lang'] = $this->getLang('current', true);

    // URL
    $this->data['page']['url']  = utils::getBaseUrl() . str_replace(cfg::get('rewriteBase'), null, $_SERVER['REQUEST_URI']);

    // MISSION
    $this->data['page']['mission'] = cfg::get('mission');

    // IMAGE
    $path2img = str_replace(utils::getBaseUrl(), null, $this->data['article'][$this->getTextId()]['art_img']['orig']);

    if (file_exists($path2img))
    {
      // 1. check first for article image
      $this->data['page']['image'] = utils::getBaseUrl() . $path2img;
    }
    else if ($this->data['article'][$this->getTextId()]['text'])
    {
      // 2. get first image available in the article body
      $xpath = new DOMXPath(@DOMDocument::loadHTML($this->data['article'][$this->getTextId()]['text']));
      $src = $xpath->evaluate("string(//img/@src)");

      $this->data['page']['image'] = ( !preg_match('/http/', $src) ? utils::getBaseUrl() . '/' : '') . $src;
    }

    // AUTHOR
    if (
      $this->data['article'][$this->getTextId()]['author']
      &&
      trim($this->data['article'][$this->getTextId()]['author']) != ''
    )
    {
      $this->data['page']['author'] = $this->data['article'][$this->getTextId()]['author'];
    }

    // DATE
    if ($this->data['article'][$this->getTextId()]['publish'] && $this->data['article'][$this->getTextId()]['publish'] != '0000-00-00')
    {
      $this->data['page']['date'] = $this->data['article'][$this->getTextId()]['publish'];
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
      $tmp = Menu::get_structured_menu($menu_name, $this->getLang('input'));

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
      if ($this->getTextId() && $this->getTextId() == $item['href'])
      {
        $item['current'] = true;
      }
      else if ($this->cfg['context'] == 'home' && ( $item['href'] == './' || $item['href'] == ''))
      {
        $item['current'] = true;
      }
      else if ($this->cfg['context'] === 'tags' && (
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
      $this->data['languages'] = utils::getLanguages($this->getLang('current', true));
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
    $string = $string ? $string : $this->getSearchString();
    $string = preg_replace('/^"(.+)"$/', '$1', $string, -1, $tot);

    $this->totalArticles = Article::search($string, false, false, false, false, true);

    list($start, $max) = $this->page2limit($page, $max);

    return Article::search($string, ($tot > 0), $this->getLang('input'), $start, $max);
  }

  /**
   * Returns, if available, the searched string
   * @param boolean $escape
   * @return string|false
   */
  public function getSearchString($escape = false)
  {
    return $escape ? htmlentities($this->cfg['searchString']) : $this->cfg['searchString'];
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

    $arts = Article::getByFilter($filter, $this->getLang('input'), $connector, $start, $max);

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

    $page = $page ? $page : ($this->cfg['page'] ? $this->cfg['page'] : 1);

    $max = $max ? $max : ($max_cfg ? $max_cfg : 20);

    $start = ( ($page -1 ) * $max);

    return [$start, $max];
  }

  /**
   *
   * @return array: Array with pagination data for current page: start, end, current
   */
  public function getPagination()
  {
    $return = false;
    $max = cfg::get('pagination') ? cfg::get('pagination') : 20;
    $page = $this->cfg['page'] ? $this->cfg['page'] : 1;
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
