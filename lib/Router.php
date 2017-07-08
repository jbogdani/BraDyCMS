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

    $controller = new Controller($_GET, $_POST, $_REQUEST);


    $router->map( 'GET', "/controller.php", function() use ($controller) {
      $controller->route();
      return false;
    });

    $router->map( 'GET', "/robots.txt", function() use ($controller) {
      $controller->route('seo_ctrl', 'robots');
      return false;
    });

    $router->map( 'GET', "/robots.txt", function() use ($controller) {
      $controller->route('seo_ctrl', 'robots');
      return false;
    });

    $router->map( 'GET', "/sitemap.xml", function() use ($controller) {
      $controller->route('seo_ctrl', 'sitemap');
      return false;
    });

    $router->map( 'GET', "/feed/rss", function() use ($controller) {
      $controller->route('feeds_ctrl', 'rss2');
      return false;
    });

    $router->map( 'GET', "/feed/atom", function() use ($controller) {
      $controller->route('feeds_ctrl', 'atom');
      return false;
    });

    $router->map( 'GET', "/oai", function() use ($controller) {
      $controller->route('OAI_ctrl', 'run');
      return false;
    });

    $router->map( 'GET', "/api", function() use ($controller) {
      $controller->route('api_ctrl', 'run');
      return false;
    });

    $router->map( 'GET', "/download/[*:querystring]", function($querystring) use ($controller) {
      $controller->route('download_ctrl', 'go', ['file' => $querystring]);
      return false;
    });

    $router->map( 'GET', "/admin", function() {
      require __DIR__ . '/../admin.php';
    });

    $router->addMatchTypes(array('lng' => '[a-z]{2}'));
    $router->addMatchTypes(array('art' => '[a-zA-Z0-9-_]*'));
    $router->addMatchTypes(array('string' => '[a-zA-Z0-9-_]*'));
    $router->addMatchTypes(array('pg' => '[0-9]{1,3}'));

    $router->map( 'GET', "/[lng:lng]?/?", function($lng = false) {
      return [
        'lang' => $lng
      ];
    });

    $router->map( 'GET', "/[lng:lng]?/[art:art]", function($lng = false, $art) {
      return [
        'lang' => $lng,
        'art_title' => $art
      ];
    });

    $router->map( 'GET', "/[lng:lng]?/[art:art].draft", function($lng = false, $art) {
      return [
        'lang' => $lng,
        'art_title' => $art,
        'draft' => true
      ];
    });

    $router->map( 'GET', "/[lng:lng]?/[art:tags].all", function($lng = false, $tags) {
      return [
        'lang' => $lng,
        'tags' => $tags
      ];
    });

    $router->map( 'GET', "/[lng:lng]?/?search:[:string]/?[pg:pg]?", function($lng = false, $string, $pg = false) {
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
/*
#sitemap
# RewriteRule ^sitemap.xml$ controller.php?obj=seo_ctrl&method=sitemap

# www to non-www url syntax
# RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
# RewriteRule ^(.*)$ http://%1/$1 [R=301,L]

#safe site, js and css, tiny_mce
# RewriteCond %{REQUEST_FILENAME} !-f
# RewriteRule (css|sites|js|img|xsl|fonts)/(.+) $1/$2

#admin
# RewriteRule ^admin/?$ admin.php [L]

#safe home page
# RewriteRule ^([a-z]{2})/?$ index.php?lang=$1  [L]

#tags: (ln/)some-tag.all !paginate
# RewriteRule ^([a-z]{2})/([a-zA-Z0-9-_+~]+)\.all(/[0-9]{1,2})?$ index.php?lang=$1&tags=$2&page=$3 [QSA,L]
# RewriteRule ^([a-zA-Z0-9-_+~]+)\.all(/[0-9]{1,2})?$ index.php?tags=$1&page=$2 [QSA,L]

#article title: (ln/)sometitle
# RewriteRule ^([a-z]{2})/([a-zA-Z0-9-_]+)/?$ index.php?lang=$1&art_title=$2 [QSA,L]
# RewriteRule ^([a-zA-Z0-9-_]+)/?$ index.php?art_title=$1 [QSA,L]

#Drafts: article title: (ln/)sometitle.draft
# RewriteRule ^([a-z]{2})/([a-zA-Z0-9-_]+).draft$ index.php?lang=$1&art_title=$2&draft=1 [L]
# RewriteRule ^([a-zA-Z0-9-_]+).draft$ index.php?art_title=$1&draft=true [L]

#search string: (ln/)search:some string !paginate
# RewriteRule ^([a-z]{2})/search:([^\/]+)(/[0-9]{1,2})?$ index.php?lang=$1&search=$2&page=$3 [QSA,L]
# RewriteRule ^search:([^\/]+)(/[0-9]{1,2})?$ index.php?search=$1&page=$2 [QSA,L]

#search tags: (ln/)some-tags.search !paginate
# RewriteRule ^([a-z]{2})/([a-zA-Z0-9-_+~]+).search(/[0-9]{1,2})?$ index.php?lang=$1&tags=$2&is_search=1&page=$3 [QSA,L]
# RewriteRule ^([a-zA-Z0-9-_+~]+).search(/[0-9]{1,2})?$ index.php?tags=$1&is_search=1&page=$2 [QSA,L]

#all non existing, non image content to index.php http://jrgns.net/redirect_request_to_index/
# RewriteCond %{REQUEST_URI} !\.(gif|jpg|png)$
# RewriteCond %{REQUEST_FILENAME} !-d
# RewriteCond %{REQUEST_FILENAME} !-f
# RewriteRule . index.php?art_title=not_found [L]
#ErrorDocument 404 /index.php?art_title=not_found

<FilesMatch "\.(htaccess|htpasswd|ini|log|inc|bak|sqlite|json)$">
	order allow,deny
	deny from all
</FilesMatch>

<FilesMatch "\.(png|jpg|jpeg|gif)$">
	Header set Access-Control-Allow-Origin "*"
</FilesMatch>

<IfModule mod_deflate.c>

# Force compression for mangled headers.
# http://developer.yahoo.com/blogs/ydn/posts/2010/12/pushing-beyond-gzipping
<IfModule mod_setenvif.c>
<IfModule mod_headers.c>
SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding
RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding
</IfModule>
</IfModule>


# Enable compression as copied from Html5Boilerplate
# https://github.com/h5bp/html5-boilerplate
# ----------------------------------------------------------------------
# | Compression                                                        |
# ----------------------------------------------------------------------

<IfModule mod_deflate.c>

    # Force compression for mangled `Accept-Encoding` request headers
    # https://developer.yahoo.com/blogs/ydn/pushing-beyond-gzipping-25601.html

    <IfModule mod_setenvif.c>
        <IfModule mod_headers.c>
            SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding
            RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding
        </IfModule>
    </IfModule>

    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    # Compress all output labeled with one of the following media types.
    #
    # (!) For Apache versions below version 2.3.7 you don't need to
    # enable `mod_filter` and can remove the `<IfModule mod_filter.c>`
    # and `</IfModule>` lines as `AddOutputFilterByType` is still in
    # the core directives.
    #
    # https://httpd.apache.org/docs/current/mod/mod_filter.html#addoutputfilterbytype

    <IfModule mod_filter.c>
        AddOutputFilterByType DEFLATE "application/atom+xml" \
                                      "application/javascript" \
                                      "application/json" \
                                      "application/ld+json" \
                                      "application/manifest+json" \
                                      "application/rdf+xml" \
                                      "application/rss+xml" \
                                      "application/schema+json" \
                                      "application/vnd.geo+json" \
                                      "application/vnd.ms-fontobject" \
                                      "application/x-font-ttf" \
                                      "application/x-javascript" \
                                      "application/x-web-app-manifest+json" \
                                      "application/xhtml+xml" \
                                      "application/xml" \
                                      "font/eot" \
                                      "font/opentype" \
                                      "image/bmp" \
                                      "image/svg+xml" \
                                      "image/vnd.microsoft.icon" \
                                      "image/x-icon" \
                                      "text/cache-manifest" \
                                      "text/css" \
                                      "text/html" \
                                      "text/javascript" \
                                      "text/plain" \
                                      "text/vcard" \
                                      "text/vnd.rim.location.xloc" \
                                      "text/vtt" \
                                      "text/x-component" \
                                      "text/x-cross-domain-policy" \
                                      "text/xml"

    </IfModule>

    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    # Map the following filename extensions to the specified
    # encoding type in order to make Apache serve the file types
    # with the appropriate `Content-Encoding` response header
    # (do note that this will NOT make Apache compress them!).
    #
    # If these files types would be served without an appropriate
    # `Content-Enable` response header, client applications (e.g.:
    # browsers) wouldn't know that they first need to uncompress
    # the response, and thus, wouldn't be able to understand the
    # content.
    #
    # https://httpd.apache.org/docs/current/mod/mod_mime.html#addencoding

    <IfModule mod_mime.c>
        AddEncoding gzip              svgz
    </IfModule>

</IfModule>

*/
