<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @uses Altorouter
 */
class Router
{
    public static function run()
    {
        try {
            $get = [];

            $router = new AltoRouter();

            $router->setBasePath(defined('CREATE_SITE') || utils::getBase() === '/' ? false : rtrim(utils::getBase(), '/'));

            $controller = new Controller($_GET, $_POST, $_REQUEST);

            $router->map('GET|POST', "/controller.php", function () use ($controller) {
                $controller->route();
                return false;
            }, 'controller');

            $router->map('GET', "/robots.txt", function () use ($controller) {
                $controller->route('seo_ctrl', 'robots');
                return false;
            }, 'robots');

            $router->map('GET', "/sitemap.xml", function () use ($controller) {
                $controller->route('seo_ctrl', 'sitemap');
                return false;
            }, 'sitemap');

            $router->map('GET', "/feed/rss", function () use ($controller) {
                $controller->route('feeds_ctrl', 'rss2');
                return false;
            }, 'rss');

            $router->map('GET', "/feed/atom", function () use ($controller) {
                $controller->route('feeds_ctrl', 'atom');
                return false;
            }, 'atom');

            $router->map('GET', "/oai", function () use ($controller) {
                $controller->route('OAI_ctrl', 'run');
                return false;
            }, 'oai');

            $router->map('GET', "/api/", function () use ($controller) {
                $controller->route('api_ctrl', 'run');
                return false;
            }, 'api');

            $router->map('GET', "/download/[*:querystring]", function ($querystring) use ($controller) {
                $controller->route('download_ctrl', 'go', ['file' => $querystring]);
                return false;
            }, 'download');

            $router->map('GET', "/admin", function () use ($controller) {
                $controller->route('admin_ctrl', 'showMainAdmin');
            }, 'admin');

            $router->addMatchTypes(array('lng' => '[a-z]{2}'));
            $router->addMatchTypes(array('tag' => '[a-zA-Z0-9-_~]*'));
            $router->addMatchTypes(array('art' => '[a-zA-Z0-9-_\/]*'));
            $router->addMatchTypes(array('string' => '[a-zA-Z0-9-_+]*'));

            $router->map('GET', "/[lng:lng]?/?", function ($lng = false) use ($controller) {
                if (defined('CREATE_SITE')) {
                    $controller->route('admin_ctrl', 'showMainAdmin');
                    return false;
                } else {
                    return [
                      'lang' => $lng
                    ];
                }
            }, 'home');

            $router->map('GET', "/[lng:lng]?/[art:art]", function ($lng = false, $art) {
              $exploded = explode('/', $art);
              $p = end($exploded);
              if (preg_match('/^p[0-9]{1,3}$/', $p)){
                $page = str_replace('p', null, $p);
                array_pop($exploded);
                $art = implode('/', $exploded) . '/';
              }
                $arr = [
                  'lang' => $lng,
                  'art_title' => $art,
                  'page' => $page
                ];
        return $arr;
            }, 'article');

            $router->map('GET', "/[lng:lng]?/[art:art].draft", function ($lng = false, $art) {
                return [
                  'lang' => $lng,
                  'art_title' => $art,
                  'draft' => true
                ];
            }, 'draft');

            $router->map('GET', "/[lng:lng]?/[tag:tags].all/[i:pg]?", function ($lng = false, $tags, $pg = false) {
                return [
                  'lang' => $lng,
                  'tags' => $tags,
                  'page' => $pg
                ];
              }, 'tags');

            $router->map('GET', "/[lng:lng]?/search:[:string]/[i:pg]?", function ($lng = false, $string, $pg = false) {
                return [
                  'lang' => $lng,
                  'search' => $string,
                  'page' => $pg
                ];
            }, 'search');

            $router->map('GET', "/[lng:lng]?/[**:requestedUrl]", function ($lng = false, $requestedUrl) {
                return [
                  'lang' => $lng,
                  'art_title' => $requestedUrl,
                  'default' => true
                ];
            }, 'default');


            $match = $router->match();

            if ($match && is_callable($match['target'])) {
                $get = call_user_func_array($match['target'], $match['params']);

                if (!$get) {
                    return;
                }
            } else {

        // $get = [ 'art_title' => 'not_found' ];
                header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            }

            echo self::frontend($get);
        } catch (Exception $e) {
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            throw $e;
        }
    }


    private static function frontend($get)
    {
        $outHtml = new OutHtml($get, $_SESSION['lang']);

        $settings = unserialize(CACHE);
        $settings['autoescape'] = false;

        $twig = new \Twig\Environment(
          new \Twig\Loader\FilesystemLoader('./sites/default' . ($_SESSION['sandbox'] ? '/' . $_SESSION['sandbox'] : '')),
          $settings
        );

        if ($_SESSION['debug']) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }
        // TODO: document intersect
        $intersect = new \Twig\TwigFunction('intersect', function () {
            return array_values(call_user_func_array('array_intersect', func_get_args()));
        });
        $twig->addFunction($intersect);

        $fn_file_exists = new \Twig\TwigFunction('file_exists', function ($file) {
            return file_exists($file);
        });
        $twig->addFunction($fn_file_exists);

        $filter = new \Twig\TwigFilter('parseTags', function ($string) {
            return customTags::parseContent($string, $outHtml);
        });

        $twig->addFilter($filter);

        return $twig->render('index.twig', [
          'html' => $outHtml
        ]);
    }
}
