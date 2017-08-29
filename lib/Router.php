<?php
/**
 * [Router description]
 * @uses Altorouter
 */
class Router
{

  public static function run()
  {
    $get = [];

    $router = new AltoRouter();

    $router->setBasePath(cfg::get('rewriteBase') === '/' ? false : cfg::get('rewriteBase'));

    $controller = new Controller($_GET, $_POST, $_REQUEST);

    $router->map( 'GET|POST', "/controller.php", function() use ($controller) {
      $controller->route();
      return false;
    }, 'controller');

    $router->map( 'GET', "/robots.txt", function() use ($controller) {
      $controller->route('seo_ctrl', 'robots');
      return false;
    }, 'robots');

    $router->map( 'GET', "/sitemap.xml", function() use ($controller) {
      $controller->route('seo_ctrl', 'sitemap');
      return false;
    }, 'sitemap');

    $router->map( 'GET', "/feed/rss", function() use ($controller) {
      $controller->route('feeds_ctrl', 'rss2');
      return false;
    }, 'rss');

    $router->map( 'GET', "/feed/atom", function() use ($controller) {
      $controller->route('feeds_ctrl', 'atom');
      return false;
    }, 'atom');

    $router->map( 'GET', "/oai", function() use ($controller) {
      $controller->route('OAI_ctrl', 'run');
      return false;
    }, 'oai');

    $router->map( 'GET', "/api/", function() use ($controller) {
      $controller->route('api_ctrl', 'run');
      return false;
    }, 'api');

    $router->map( 'GET', "/download/[*:querystring]", function($querystring) use ($controller) {
      $controller->route('download_ctrl', 'go', ['file' => $querystring]);
      return false;
    }, 'download');

    $router->map( 'GET', "/admin", function() {
      require __DIR__ . '/../admin.php';
    }, 'admin');

    $router->addMatchTypes(array('lng' => '[a-z]{2}'));
    $router->addMatchTypes(array('tag' => '[a-zA-Z0-9-_~]*'));
    $router->addMatchTypes(array('art' => '[a-zA-Z0-9-_\/]*'));
    $router->addMatchTypes(array('string' => '[a-zA-Z0-9-_+]*'));

    $router->map( 'GET', "/[lng:lng]?/?", function($lng = false) {
      return [
        'lang' => $lng
      ];
    }, 'home');

    $router->map( 'GET', "/[lng:lng]?/[art:art]", function($lng = false, $art) {
      return [
        'lang' => $lng,
        'art_title' => $art
      ];
    }, 'article');

    $router->map( 'GET', "/[lng:lng]?/[art:art].draft", function($lng = false, $art) {
      return [
        'lang' => $lng,
        'art_title' => $art,
        'draft' => true
      ];
    }, 'draft');

    $router->map( 'GET', "/[lng:lng]?/[tag:tags].all/[i:pg]?", function($lng = false, $tags, $pg = false) {
      return [
        'lang' => $lng,
        'tags' => $tags,
        'page' => $pg
      ];
    }, 'tags');

    $router->map( 'GET', "/[lng:lng]?/search:[:string]/[i:pg]?", function($lng = false, $string, $pg = false) {
      return [
        'lang' => $lng,
        'search' => $string,
        'page' => $pg
      ];
    }, 'search');

    $router->map( 'GET', "/[lng:lng]?/?[**:requestedUrl]", function($lng = false, $requestedUrl) {
      return [
        'lang' => $lng,
        'art_title' => $requestedUrl,
        'default' => true
      ];
    }, 'default');


    $match = $router->match();

    if( $match && is_callable( $match['target'] ) ) {
      $get = call_user_func_array( $match['target'], $match['params'] );

      if(!$get) {
        return;
      }
    } else {

      // $get = [ 'art_title' => 'not_found' ];
      header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    }

    echo self::frontend($get);
  }






  private static function frontend($get)
  {
    $outHtml = new OutHtml($get, $_SESSION['lang']);

    $settings = unserialize(CACHE);
    $settings['autoescape'] = false;

    $twig = new Twig_Environment(
      new Twig_Loader_Filesystem('./sites/default' . ($_SESSION['sandbox'] ? '/' . $_SESSION['sandbox'] : '')),
      $settings
    );

    if ($_SESSION['debug'])
    {
      $twig->addExtension(new Twig_Extension_Debug());
    }
    // TODO: document intersect
    $intersect = new Twig_SimpleFunction('intersect', function () {
      return array_values(call_user_func_array('array_intersect',func_get_args()));
    });
    $twig->addFunction($intersect);

    $fn_file_exists = new Twig_SimpleFunction('file_exists', function ($file) {
      return file_exists($file);
    });
    $twig->addFunction($fn_file_exists);

    $filter = new Twig_SimpleFilter('parseTags', function ($string) {
        return customTags::parseContent($string, $outHtml);
      });

    $twig->addFilter($filter);

    return $twig->render('index.twig', array(
        'html'=>$outHtml
    ));
  }
}
