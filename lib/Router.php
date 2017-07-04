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

    $router->map( 'GET', "/robots.txt", function() {
      $controller = new Controller();
      $controller->route('seo_ctrl', 'robots');
      return false;
    });

    $router->map( 'GET', "/sitemap.xml", function() {
      $controller = new Controller();
      $controller->route('seo_ctrl', 'sitemap');
      return false;
    });

    $router->map( 'GET', "/feed/rss", function() {
      $controller = new Controller();
      $controller->route('feeds_ctrl', 'rss2');
      return false;
    });

    $router->map( 'GET', "/feed/atom", function() {
      $controller = new Controller();
      $controller->route('feeds_ctrl', 'atom');
      return false;
    });

    $router->map( 'GET', "/oai", function() {
      $controller = new Controller();
      $controller->route('OAI_ctrl', 'run');
      return false;
    });

    $router->map( 'GET', "/api", function() {
      $controller = new Controller();
      $controller->route('api_ctrl', 'run');
      return false;
    });

    $router->map( 'GET', "/download/[*:querystring]", function($querystring) {
      $controller = new Controller();
      $controller->route('download_ctrl', 'go', ['file' => $querystring]);
      return false;
    });

    $router->map( 'GET', "/admin", function() {
      echo "Do something";
    });

    $router->addMatchTypes(array('lng' => '[a-z]{2}'));
    $router->addMatchTypes(array('art' => '[a-zA-Z0-9-_]*'));
    $router->addMatchTypes(array('pg' => '[0-9]{1,3}'));

    $router->map( 'GET', "/[lng:lng]/?", function($lng) {
      return [
        'lang' => $lng
      ];
    });

    $router->map( 'GET', "/[lng:lng]?/[art:art]", function($lng, $art) {
      return [
        'lang' => $lng,
        'art_title' => $art
      ];
    });

    $router->map( 'GET', "/[lng:lng]?/[art:art].draft", function($lng, $art) {
      return [
        'lang' => $lng,
        'art_title' => $art,
        'draft' => true
      ];
    });

    $router->map( 'GET', "/[lng:lng]?/[art:tags].all", function($lng, $tags) {
      return [
        'lang' => $lng,
        'tags' => $tags
      ];
    });

    $router->map( 'GET', "/[lng:lng]?/search:[:string]/[pg:pg]?", function($lng, $string, $pg = false) {
      return [
        'lang' => $lng,
        'search' => $string,
        'page' => $pg
      ];
    });





    $match = $router->match();

    if( $match && is_callable( $match['target'] ) ) {
      return call_user_func_array( $match['target'], $match['params'] );
    } else {
      return [
        'art_title' => 'not_found'
      ];
      var_dump($match);
      // header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    }
  }
}
