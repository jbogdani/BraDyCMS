<?php

/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2014 
 * @license			All rights reserved
 * @since				Feb 28, 2014
 */

class seo_ctrl extends Controller
{
  public function robots()
  {
    $sitemapPath = utils::getBaseUrl() . 'sitemap.xml';
    $robots = <<<EOD
User-agent: *
Allow: /
Allow: /sites/default/sitemap.xml
Disallow: /cache/
Disallow: /css/
Disallow: /img/
Disallow: /js/
Disallow: /lib/
Disallow: /logs/
Disallow: /locale/
Disallow: /modules/
Disallow: /tiny_mce/
Disallow: /tmp/
Disallow: /xsl/
Disallow: /controller.php
Disallow: /admin.php
Disallow: /admin
Disallow: /sites/default/cfg/
Disallow: /sites/default/css/
Sitemap: {$sitemapPath}
EOD;
    header("Content-Type: text/plain");
    echo $robots;
  }
}
?>
