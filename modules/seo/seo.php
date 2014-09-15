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
  
  
  public function sitemap()
  {
    $home = htmlspecialchars(utils::getBaseUrl());
    
    $xml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<url>
  <loc>{$home}</loc>
  <changefreq>monthly</changefreq>
</url>
EOD;
    
    $all_arts = Article::getAllValid(false, true);
    
    $artimgs = cfg::get('art_img');
    
    if (is_array($all_arts))
    {
      foreach ($all_arts as $art)
      {
        if ($art['robots'] && (strpos('noindex', $art['robots']) !== false || strpos('nofollow', $art['robots']) !== false ))
        {
          continue;
        }
        $xml .= "\n" . '<url>'
          . "\n\t" . '<loc>' 
            . htmlspecialchars(utils::getBaseUrl() . str_replace('./', null, link::to_article($art['textid'])))
          . '</loc>'
          . (
              file_exists('./sites/default/images/articles/' . $artimgs[0] . '/' . $art['id'] . '.jpg') ? 
              "\n\t<image:image>\n\t\t<image:loc>"
              . htmlspecialchars(utils::getBaseUrl() . 'sites/default/images/articles/' . $artimgs[0] . '/' . $art['id'] . '.jpg')
              . "</image:loc>\n\t</image:image>"
              : ''
          )
          . ($art['updated'] ? "\n\t" . '<lastmod>' . date('Y-m-d', strtotime($art['updated'])) . '</lastmod>' : '')
          . "\n\t" . '<changefreq>monthly</changefreq>'
        . "\n". '</url>';
      }
    }
    
    $xml .= "\n</urlset>";
    header ("Content-Type:text/xml");
    echo $xml;
    
    }
}
?>
