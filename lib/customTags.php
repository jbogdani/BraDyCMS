<?php

/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Apr 7, 2013
 */

class customTags
{
  public static function parseContent($html, Out $out, $id = false)
  {
    // Replaces relative links in database to root-specific ones.
    $html = preg_replace([
      '/=\s*"\.\/sites\/default\//',
      '/=\s*"\/sites\/default\//',
      '/=\s*"sites\/default\//'
    ], $out->link2('home') . 'sites/default/', $html);

    // If no shorttag pattern is found, exit the method.
    if (!preg_match('/\[\[(\w+)\s*([^\]]*)\]\](.*?)\[\[\/\1\]\]/s', $html)) {
      return $html;
    }

    //return customTagParser::do_shortcode($html);
    preg_match_all('/\[\[(\w+)\s*([^\]]*)\]\](.*?)\[\[\/\1\]\]/s', $html, $customTagsFound, PREG_SET_ORDER);

    foreach ($customTagsFound as $customTag) {
      $attributes = [];
      $rawAttributes = [];
      $formatedAttributes = [];

      $originalTag = '/' . preg_quote($customTag[0], '/') . '/';

      $tag = $customTag[1];

      $rawAttributes = $customTag[2];

      $content = $customTag[3];

      preg_match_all('/([^=\s]+)="([^"]+)"/', $rawAttributes, $attributes, PREG_SET_ORDER);

      $formatedAttributes['content'] = $content;

      foreach ($attributes as $attribute) {
        $formatedAttributes[$attribute[1]] = $attribute[2];
      }

      $formatedAttributes['lang'] =
        (($_SESSION['lang'] && $_SESSION['lang'] !== cfg::get('sys_lang')) ?
          $_SESSION['lang'] : false);

      if (method_exists('customTags', $tag)) {
        $replace = call_user_func(array('customTags', $tag), $formatedAttributes, $out, $id);

        $html = preg_replace($originalTag, $replace, $html, 1);
      } else {
        if (method_exists($tag, 'init')) {
          $replace = call_user_func([new $tag, 'init'], $formatedAttributes, $out, $id);

          $html = preg_replace($originalTag, $replace, $html, 1);

          $pending[$tag] = true;
        }
      }
    }

    if (is_array($pending) && !empty($pending)) {
      foreach ($pending as $obj => $true) {
        if (method_exists($obj, 'end')) {
          $html .= call_user_func(array($obj, 'end'));
        }
      }
    }
    return $html;
  }

  /**
   * Displays a user defined form
   * @param array $data
   *      $data[content]  is the name of the map to display (i.e. the name of the json file with configuration)
   *      $data[width],   the width of the map container element (default value: 100%)
   *      $data[height],  the height of the map container element (default value: 400px)
   * @param object $out    Instance of Out
   * @return type
   */
  public static function map($data, $out = false)
  {
    $f = new usermap_ctrl();

    return $f->showMap($data, $out);
  }

  /**
   * Displays a user defined form
   * @param array $data
   *      $data[content]  is the name of the form to display (i.e. the name of the json file with configuration)
   *      $data[nojs],    if true, no JS code will be printed
   * @param object $out    Instance of Out
   * @return type
   */
  public static function userform($data, $out = false)
  {
    $f = new userform_ctrl();

    return $f->showForm($data, $out);
  }

  /**
   *
   * @param type $data
   *  content: node name
   *  getObject: if false a well formatted html table will be returned, otherwize an array
   *  class: css class or space-separated classes to attache to main html table (getObject should be false)
   *  limit: maximum number of records to show
   * @return string|boolean
   */
  public static function download($data)
  {
    $path2node = './sites/default/images/downloads/' . $data['content'];

    $json_file = $path2node . '/data'
      . ($_SESSION['lang'] && $_SESSION['lang'] !== cfg::get('sys_lang') ? '_' . $_SESSION['lang'] : '')
      . '.json';

    if (!file_exists($path2node) || !file_exists($json_file)) {
      return false;
    }

    $fileData = json_decode(file_get_contents($json_file), true);

    if (!$fileData || !is_array($fileData)) {
      return false;
    }

    $node_content = utils::dirContent($path2node);

    $files = array();


    foreach ($node_content as $file) {
      if (preg_match('/data_?([a-z]{2})?\.json/', $file) || $file == 'covers') {
        continue;
      }

      $formattedName = str_replace('.', '__x__', $file);

      $cover_name = $path2node . DIRECTORY_SEPARATOR
        . 'covers' . DIRECTORY_SEPARATOR
        . pathinfo($file, PATHINFO_FILENAME) . '.jpg';

      if (file_exists($cover_name)) {
        $cover = $cover_name;
      } else {
        $cover = false;
      }

      $files[] = array(
        'path' => $path2node . '/' . $file,
        'basename' => pathinfo($file, PATHINFO_FILENAME),
        'ext' => pathinfo($file, PATHINFO_EXTENSION),
        'title' => $fileData[$formattedName]['title'] ? $fileData[$formattedName]['title'] : $file,
        'description' => $fileData[$formattedName]['description'],
        'sort' => $fileData[$formattedName]['sort'],
        'cover' => $cover
      );
    }

    usort($files, function ($a, $b) {
      if ($a['sort'] === $b['sort']) {
        return 0;
      }
      return ($a['sort'] > $b['sort']) ? -1 : 1;
    });

    if ($data['limit']) {
      $files = array_slice($files, 0, (int) $data['limit']);
    }


    if ($data['getObject']) {
      return $files;
    }


    if ($data['getList']) {
      $html = '<ul ' . ($data['class'] ? 'class="' . $data['class'] . '"' : '') . '>';

      foreach ($files as $d) {
        $html .= '<li>'
          . '<a href="' . $d['path'] . '" class="downloadFile" target="_blank">'
          . ($d['cover'] ? '<div class="cover"><img src="' . $d['cover'] . '" class="img-responsive"></div>' : '')
          . '<div class="title">' . $d['title'] . '</div>'
          . '<div class="description">' . $d['description'] . '</div>'
          . '</a>'
          . '</li>';
      }

      $html .= '</ul>';
    } else {
      $html = '<table ' . ($data['class'] ? 'class="' . $data['class'] . '"' : '') . '>'
        . '<tbody>';

      foreach ($files as $d) {
        $html .= '<tr>'
          . '<td class="cover"> ' . ($d['cover'] ? '<img src="' . $d['cover'] . '" class="img-responsive">' : '') . ' </td>'
          . '<th class="title">' . $d['title'] . '</th>'
          . '<td class="description">' . $d['description'] . '</td>'
          . '<td class="link"><a href="' . $d['path'] . '" class="downloadFile" target="_blank">' . tr::get('open_download') . '</a></td>'
          . '</tr>';
      }

      $html .= '</tbody>'
        . '</table>';
    }

    return $html;
  }

  public static function cl_gallery($data)
  {
    return self::format_gallery($data, true);
  }

  public static function gallery($data)
  {
    return self::format_gallery($data);
  }

  private static function format_gallery($data, $responsive = false)
  {
    $gal = $data['content'];
    $class = $data['class'];
    $thumb_dim = $data['thumb_dim'] ? $data['thumb_dim'] : false;

    $rel = $data['rel'] ? $data['rel'] : $gal;

    $html = '';

    try {
      $gal_data = Gallery::get($gal, $thumb_dim);

      if ($responsive) {

        /**
         * <ul data-rel="sth" class="a b c cl_gallery">
         *   <li data-img="img_src" data-thumb="thumb_img_src">caption text</li>
         *   ...
         * </ul>
         */
        $html .= '<div class="gallery-container">'
          . '<ul class="cl_gallery ' . $gal . ($class ? ' ' . $class : '') . '" ' . 'data-rel="' . $rel . '"' . '>';

        foreach ($gal_data as $item) {
          $html .= '<li data-id="' . basename($item['img']) . '" data-img="' . $item['img'] . '" data-thumb="' . $item['thumb'] . '">'
            . $item['caption']
            . '</li>';
        }

        $html .= '</ul>'
          . '</div>';
      } else {
        $html .= '<div class="gallery-container">'
          . '<ul class="gallery ' . $gal
          . ($class ? ' ' . $class : '') . '" data-rel="' . $rel . '">';

        foreach ($gal_data as $item) {
          $title = str_replace('"', '&quot;', strip_tags($item['caption']));
          $html .= '<li data-id="' . basename($item['img']) . '">'
            // Link will be set to external reference, if href is set or to image (fancybox) if not
            . '<a href="' . ($item['href'] ? $item['href'] : $item['img']) . '" '
            . (!$item['href'] ? 'class="fancybox" data-caption="' . $title . '"' : '')
            . 'title="' . $title . '"  '
            . 'rel="' . $rel . '"'
            . (!$item['href'] ? 'data-fancybox="' . $rel . '"' : '')
            . '>'
            . '<img src="' . $item['thumb'] . '" alt="' . $title . '" />'
            . '</a>'
            . '<div class="caption">'
            . ($item['href'] ? '<a href="' . $item['href'] . '" title="' . $title . '">' . $item['caption'] . '</a>' : $item['caption'])
            . '</div>'
            . '</li>';
        }

        $html .= '</ul>'
          . '</div>';
      }

      return $html;
    } catch (Exception $e) {
      error_log($e->getMessage());
      return false;
    }
  }


  /**
   *
   * @param array $data
   *  content: YouTube video ID
   *  ratio: Video ratio: 4by3 or 16by9. If defined the Twitter Bootstrap responsive layout will be used (http://getbootstrap.com/components/#responsive-embed)
   *  width: if ratio is not defined the width will be used, default value is 560
   *  height: if ratio is not defined the height will be used, default value is 315
   *  align: left, right, center, default false
   *  class: custom css class
   * @return string
   */
  public static function youTube($data)
  {
    $code = $data['content'];

    if (!$code) {
      return;
    }

    $width = $data['width'] ? $data['width'] : 560;
    $height = $data['height'] ? $data['height'] : 315;
    $ratio = ($data['ratio'] && ($data['ratio'] === '4by3' || $data['ratio'] === '16by9')) ? $data['ratio'] : '';
    $align = $data['align'];
    $class = $data['class'];
    $start = $data['start'];
    $protocol = utils::is_ssl() ? "https" : "http";

    return '<div class="youtube'
      . ($class ? ' ' . $class : '')
      . ($align ? ' text-' . $align : '')
      . ($ratio ? ' embed-responsive embed-responsive-' . $ratio . '' : '')
      . '"'
      . '>'
      . '<iframe '
      . ($ratio ? ' class="embed-responsive-item" ' : ' width="' . $width . '" height="' . $height . '" ')
      . 'src="' . $protocol . '://www.youtube.com/embed/' . $code . '?rel=0' . ($start ? '&start=' . $start : '') . '" frameborder="0" allowfullscreen></iframe>'
      . '</div>';
  }

  public static function vimeo($data)
  {
    $code = $data['content'];
    $width = $data['width'] ? $data['width'] : 560;
    $height = $data['height'] ? $data['height'] : 315;
    $protocol = utils::is_ssl() ? "https" : "http";

    return '<iframe src="' . $protocol . '://player.vimeo.com/video/' . $code . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
  }


  public static function twitter($data)
  {
    $user = $data['user'];
    $widget_id = $data['id'];
    $content = $data['content'] ? $data['content'] : 'Tweets by @' . $data['user'];

    return '<a class="twitter-timeline"  href="https://twitter.com/' . $user . '"  data-widget-id="' . $widget_id . '">' . $content . '</a>' .
      '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
  }

  /**
   * Requires and initializates Facebook's SDK
   * @param string $lang Two digits language definition
   * @return sting
   */
  private static function fb_sdk($lang = false)
  {
    if (!isset($lang) || !preg_match('/([a-z]{2})_([A-Z]{2})/', $lang)) {
      $lang = $_SESSION['lang'] ? $_SESSION['lang'] : cfg::get('sys_lang');
      $lang = strtolower($lang) . '_' . strtoupper($lang);
    }

    $html = <<<EOD
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/{$lang}/sdk.js#xfbml=1&version=v2.3";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
EOD;

    return $html;
  }

  /**
   * Facebook like box widget (https://developers.facebook.com/docs/plugins/comments
   * @param array $data array of parameters. All parameters are optional
   * @return string
   */
  public static function fb_comments($data)
  {
    $protocol = utils::is_ssl() ? "https" : "http";

    $url = $data['content'] ? $data['content'] : $protocol . '://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

    $html = self::fb_sdk($data['lang']) . '<div class="fb-comments" data-href="' . $url . '" ';

    foreach (array('colorscheme', 'mobile', 'num_posts', 'order_by', 'width') as $el) {
      if ($data[$el]) {
        $html .= ' data-' . str_replace('_', '-', $el) . '="' . $data[$el] . '"';
      }
    }

    $html .= '></div>';

    return $html;
  }

  /**
   * Facebook follow button widget (https://developers.facebook.com/docs/plugins/follow-button
   * @param array $data array of parameters. All parameters, except content (or href) are optional
   * @return string
   */
  public static function fb_follow($data)
  {
    if (!$data['href'] && !$data['content']) {
      return false;
    }

    $html = self::fb_sdk($data['lang'])
      . '<div class="fb-follow" data-href="' . ($data['href'] ? $data['href'] : $data['content']) . '" ';

    foreach (array('colorscheme', 'kid_directed_site', 'layout', 'show_faces', 'width') as $el) {
      if ($data[$el]) {
        $html .= ' data-' . str_replace('_', '-', $el) . '="' . $data[$el] . '"';
      }
    }
    $html .= '></div>';

    return $html;
  }

  /**
   * Facebook like box widget (https://developers.facebook.com/docs/plugins/like-box-for-pages)
   * @param array $data array of parameters. All parameters except href are optional
   * @return string
   */
  public static function fb_like_box($data)
  {
    if (!$data['href'] && !$data['content']) {
      return;
    }

    $html = self::fb_sdk($data['lang'])
      . '<div class="fb-like-box" ' .
      ' data-href="' . ($data['content'] ? $data['content'] : $data['href']) . '" ';
    foreach (array('colorscheme', 'force_wall', 'header', 'height', 'show_border', 'show_faces', 'stream', 'width') as $el) {
      if ($data[$el]) {
        $html .= ' data-' . str_replace('_', '-', $el) . '="' . $data[$el] . '"';
      }
    }

    $html .= '></div>';
    return $html;
  }

  /**
   * Facebook like button widget https://developers.facebook.com/docs/plugins/like-button
   * @param array $data array of parameters. All parameters, except content (or href) are optional
   * @return string
   */
  public static function fb_like($data)
  {
    $protocol = utils::is_ssl() ? "https" : "http";

    $url = $data['content'] ? $data['content'] : $protocol . '://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

    $html = self::fb_sdk($data['lang'])
      . '<div class="fb-like" data-href="' . $url . '" ';

    foreach (array('action', 'colorscheme', 'kid_directed_site', 'layout', 'ref', 'share', 'show_faces', 'width') as $el) {
      if ($data[$el]) {
        $html .= ' data-' . str_replace('_', '-', $el) . '="' . $data[$el] . '"';
      }
    }

    $html .= '></div>';

    return $html;
  }

  public static function fb_send($data)
  {
    $protocol = utils::is_ssl() ? "https" : "http";

    return self::fb_sdk($data['lang']) . '<div class="fb-like" ' .
      ' data-href="' . $protocol . '://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . '" ' .
      ($data['font'] ? ' data-font="' . $data['font'] . '" ' : '') .
      ($data['colorscheme'] ? ' data-colorscheme="' . $data['colorscheme'] . '" ' : '') .
      ($data['ref'] ? ' data-ref="' . $data['ref'] . '" ' : '') .
      ' ></div>';
  }

  private static function twitt_sdk()
  {
    return <<<EOD
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
EOD;
  }

  /**
   * Twitter share button
   * @param array $data: available option:
   *      via: Twitter username without @
   *      text: Tweet's text
   *      hashtag: Tweet's hashtag
   *      count: if 'none' no count will be shown
   *      lang: two-digits language id
   *      content: URL to share
   * @return string
   */
  public static function twitt_share($data)
  {
    if ($data['content']) {
      $data['url'] = $data['content'];
    }

    $html = '<a href="https://twitter.com/share" class="twitter-share-button"';

    foreach (array('via', 'text', 'hashtags', 'count', 'url', 'lang') as $el) {
      if ($data[$el]) {
        $html .= ' data-' . $el . '="' . $data[$el] . '"';
      }
    }

    $html .= '>Tweet</a>' . self::twitt_sdk();

    return $html;
  }

  private static function gplus_sdk()
  {
    return <<<EOD
<script src="https://apis.google.com/js/platform.js" async defer></script>
EOD;
  }

  public static function gplus_plusone($data)
  {
    if ($data['content']) {
      $data['href'] = $data['content'];
    }

    $html = '<div class="g-plusone"';

    foreach (array('href', 'size', 'annotation', 'width', 'align', 'expandTo', 'callback', 'onstartinteraction', 'onendinteraction', 'recommendations', 'count') as $el) {
      if ($data[$el]) {
        $html .= ' data-' . $el . '="' . $data[$el] . '"';
      }
    }
    $html .= '></div>' . self::gplus_sdk();

    return $html;
  }

  /**
   *
   * @param array $data
   *      pubid: public id string provided by addthis.
   *      content: html to show. Default: addthis small image
   *      only_js: if true no html will be showed
   * @return string
   */
  public static function addThis($data)
  {
    $pubid = $data['pubid'];
    if (!$pubid) {
      return false;
    }
    $protocol = utils::is_ssl() ? "https" : "http";

    return ($data['only_js'] ? '' : ($data['content'] ?
      '<a class="addthis_button" href="' . $protocol . '://www.addthis.com/bookmark.php?v=300&amp;pubid=' . $pubid . '">' . $data['content'] . '</a>'
      :
      '<div class="addthis_sharing_toolbox"></div>'
    )
    )
      . '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=' . $pubid . '" async="async"></script>';
  }

  /**
   *
   * @param array $data
   *     path*: image file relative url
   *     width:  main div (container) width
   *     content: image caption
   *     fancybox: boolean, if true the thumbnail will be displayed and the original file will be shown on click
   *     align: image alignent (left|right)
   *     href: href of image link
   *     href_class: css class of href link
   *     gal: string or false. If true the string will be used as rel attribute to create fancybox galleries
   * @param object $out    Instance of Out
   * @param int $id        Article id
   * @return string
   */
  public static function fig($data, $out = false, $id = false)
  {
    $new_data = [];
    if (!$data['path']) {
      return false;
    }
    // Set attributes
    foreach ([
      'path',
      'width',
      'caption' => 'content',
      'fancybox',
      'align',
      'href',
      'href_class',
      'gal'
    ] as $new => $where) {
      $tmp = $data[$where] && $data[$where] !== '' ? $data[$where] : null;
      if (is_string($new)) {
        $$new = $tmp;
      } else {
        $$where = $tmp;
      }
    }

    // Set $filename
    foreach ([
      './sites/default/images/articles/media/' . $id . '/',
      './sites/default/images/articles/',
      './sites/default/images/',
      ''
    ] as $path_to_file) {
      if (file_exists($path_to_file . $path)) {
        $filename = $path_to_file . $path;
        break;
      }
    }

    if (!$filename) {
      return false;
    }

    // Set $alt, ie. cleaned caption
    $alt_text = str_replace('"', '', strip_tags($caption));

    // Set $href for galleries
    if (!$href && $fancybox) {
      $href = $out->link2() . $filename;
    }

    // $filename is set to thumbnail, if thumbnail exists
    if ($fancybox) {
      $name = basename($filename);
      $thumb = str_replace($name, 'thumbs/' . $name, $filename);

      if (file_exists($thumb)) {
        $filename = $thumb;
      }
    }

    // Set width if align is set and no width is provided
    if ($align && in_array($align, ['left', 'right', 'center']) && !$width) {
      $file_dim = getimagesize($filename);
      $width = $file_dim[0] . 'px';
    }

    // Write html
    $html = '<figure class="figure ' . $align . '"';
    if ($align || $width) {
      $html .= ' style="'
        . ($align ? 'float: ' . $align . ';' : '')
        . ($width ? 'max-width: ' . $width . ';' : '')
        . '" ';
    }
    $html .= '>'
      . '<div class="image">';

    if ($href) {
      $html .= '<a '
        . ($gal ? ' rel="' . $gal . '" data-fancybox="' . $gal . '" ' : '')
        . 'href="' . $href . '" '
        . 'class="fancybox' . ($href_class ? ' ' . $href_class : '') . '" '
        . ($alt_text ? ' title="' . $alt_text . '" data-caption="' . $alt_text . '" ' : '')
        . '>';
    }

    $html .= '<img src="' . $out->link2() . $filename . '" alt="' . $alt_text . '">';

    if ($href) {
      $html .= '</a>';
    }

    $html .= '</div>'
      . ($caption ? '<figcaption class="caption">' . $caption . '</figcaption>' : '')
      . '</figure>';

    return $html;
  }


  /**
   * @param type $data
   *    content: (required) path to swf file.
   *    width: object's width
   *    height: object's height
   *    other artguments will be used as paramaeters
   * @example [[flash width="200" height="100" quality="height"]]./sites/default/images/flash/my.swf[[/flash]]
   */
  public static function flash($data)
  {
    if (!$data['content']) {
      return false;
    }

    //http://stackoverflow.com/questions/1333202/embed-flash-in-html
    $html = '<object ' .
      'type="application/x-shockwave-flash" ' .
      'data="' . $data['content'] . '" ' .
      ($data['width'] ? ' width="' . $data['width'] . '"' : '') .
      ($data['height'] ? ' height="' . $data['height'] . '"' : '') .
      '> ' .
      '<param name="movie" value="' . $data['content'] . '" />';

    foreach ($data as $k => $v) {
      if ($k !== 'content' && $k !== 'width' && $k !== 'height') {
        $html .= '<param name="' . $k . '" value="' . $v . '" />';
      }
    }

    $html .= '<embed src="' . $data['content'] . '" quality="high" />' .
      '</object>';
    return $html;
  }

  /**
   * Returns well formatted html with link to internal article
   * @param array $data
   *    content: link's text
   *    art:   destination article's textid, or 'home' for link to home page
   *    title: title attribute, optional
   *    rel: rel attribute, optional
   *    class: class attribute, optional
   *    id: id attribute, optional
   *
   * @return string
   */
  public static function link($data)
  {
    $lang =
      (($_SESSION['lang'] && $_SESSION['lang'] !== cfg::get('sys_lang')) ?
        $_SESSION['lang'] : false);

    $html = '<a '
      . ($data['title'] ? ' title="' . $data['title'] . '"' : '')
      . ($data['rel'] ? ' rel="' . $data['rel'] . '"' : '')
      . ($data['class'] ? ' class="' . $data['class'] . '"' : '')
      . ($data['id'] ? ' id="' . $data['id'] . '"' : '')
      . 'href="' . (link::to_article($data['art'] === 'home' ? './' : $data['art'], $lang)
      ) .
      '">' . $data['content'] . '</a>';

    return $html;
  }


  /**
   * Outputs well formatted html string that embeds a google calendar
   * @param  array $data array of input data:
   *                     content: (string) (required) google calendar id eg: something@group.calendar.google.com
   *                     showPrint: (boolean) (optional, default false): if true the print option will be visible
   *                     showTabs: (boolean) (optional, default false): if true the tabs will be visible
   *                     showCalendars: (boolean) (optional, default false): if true the list of calendars be visible
   *                     height: (int|false) (optional, default 600): the height in pixels of the calendar
   *                     width: (int|false) (optional, default 800): the width in pixels of the calendar
   *                     wkst: (int|false) (optional, default 2): start day of the week 1: sunday, 2 monday, etc..
   *                     bgcolor: (string|false) (optional, default FFFFFF): background color code
   *                     color: (string|false) (optional, default 8C500B): Text color
   *                     ctz: (string|false) (optional, default Europe/Rome): Time zone
   *                     mode: (string|false) (optional, default false): Calndar mode, one of: WEEK or AGENDA
   *                     hl: (string|false) (optional, default system language): Language of the calendar
   * @return string       valid html
   */
  public static function gcalendar($data)
  {
    if (!$data['content']) {
      return false;
    }

    $opts = array(
      'showTabs=' . ($data['showTabs'] ? '1' : '0'),
      'showCalendars=' . ($data['showCalendars'] ? '1' : '0'),
      'showTz=' . ($data['showTz'] ? '1' : '0'),
      'showPrint=' . ($data['showPrint'] ? '1' : '0'),
      'height='  . ($data['height'] ? $data['height'] : '600'),
      'wkst=' . ($data['wkst'] ? $data['wkst'] : '2'), // start day of the week 1: sunday, 2 monday
      'width='  . ($data['width'] ? $data['width'] : '800'),
      'bgcolor=' . ($data['bgcolor'] ? '%23' . $data['bgcolor'] : '%23FFFFFF'),
      'src=' . urlencode($data['content']),
      'color=' . ($data['showTz'] ? '#' . $data['color'] : '#8C500B'),
      'ctz=' . ($data['ctz'] ? urlencode($data['ctz']) : 'Europe%2FRome'),
      'mode=' . (($data['mode'] && in_array(strtoupper($data['mode']), array('WEEK', 'AGENDA'))) ? strtoupper($data['mode']) : false),
      'hl=' . ($data['hl'] ? $data['hl'] : $_SESSION['lang'])
    );

    $html = '<div class="google-calendar-container">'
      . '<iframe src="https://calendar.google.com/calendar/embed?'
      . implode('&amp;', $opts) . '"'
      . 'style="' . ($data['border'] ? $data['border'] : 'border-width:0') . '" '
      . 'width="' . ($data['width'] ? $data['width'] : 800) . '" '
      . 'height="' . ($data['height'] ? $data['height'] : 600) . '" '
      . 'frameborder="0" '
      . 'scrolling="no"></iframe>'
      . '</div>';

    return $html;
  }

  /**
   * Returns valid html link string to download file
   * @param  array $data array of input data
   *                     content: (string) (optional, default: false): Link text. If not available the file parameter will be used. The variable {tot}, if used, will be replaced with the total number of downloads for the file
   *                     file: (string) (required) absolute or relative path (some default system paths will be tested) to file to be downloaded.
   *                     * any other element will be automatically added as attribute to main <a> element
   * @return string       valid HTML or false
   */
  public static function dwnl($data, Out $out)
  {
    $file = $data['file'];
    $text = $data['content'] ? $data['content'] : $file;

    foreach ([
      './sites/default/images/articles/media/' . $out->getArticle()['id'] . '/',
      './sites/default/images/articles/',
      './sites/default/images/',
      ''
    ] as $path_to_file) {
      if (file_exists($path_to_file . $file)) {
        $file = $path_to_file . $file;
        break;
      }
    }

    if (!$file || !file_exists($file)) {
      return false;
    }

    $tot    = DownloadAndCount::getCount($file);
    $text   = str_replace('{tot}', $tot, $text);

    $html = '<a ';

    foreach ($data as $k => $v) {
      if (!in_array(strtolower($k), ['content', 'file'])) {
        $html .= " "  . $k . '="' . str_replace('"', '\"', $v) . '"';
      }
    }
    $html .= 'href="' . link::to_article('download') . '/' . $file . '" target="_blank">'
      . $text;
    $html .=  '</a>';

    return $html;
  }
}
