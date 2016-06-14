<?php

/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2014
 * @license      All rights reserved
 * @since        Feb 28, 2014
 */

class seo_ctrl extends Controller
{

  /**
   * shows and edits json file or sqlite table with metadata foreach custom URL
   * @return [type] [description]
   */
  public function all()
  {
    $this->render('seo', 'list', ['cfg_langs' => cfg::get('languages')] );
  }

  /**
   * Shows edit form
   * @return [type] [description]
   */
  public function showForm()
  {
    $data = [];

    if ($this->get['param'][0])
    {
      $data = Seo::getById($this->get['param'][0]);
    }
    $this->render('seo', 'form', ['data' => $data]);
  }

  public function sql2json()
  {
    $aColumns = array( 'id', 'url', 'title', 'description', 'keywords');

    $sIndexColumn = 'id';


    // Paging
    $sLimit = '';
    if ( isset( $this->request['iDisplayStart'] ) && $this->request['iDisplayLength'] != '-1' )
    {
      $sLimit = "LIMIT " . $this->request['iDisplayStart'] .", "
        . $this->request['iDisplayLength'];
    }

    // Ordering
    if ( isset( $this->request['iSortCol_0'] ) )
    {
      $sOrder = "ORDER BY  ";

      for ( $i=0 ; $i<intval( $this->request['iSortingCols'] ) ; $i++ )
      {
        if ( $this->request[ 'bSortable_' . intval($this->request['iSortCol_' . $i]) ] == "true" )
        {
          $sOrder .= $aColumns[ intval( $this->request['iSortCol_'.$i] ) ] . "
            " . $this->request['sSortDir_'.$i] . ", ";
        }
      }

      $sOrder = substr_replace( $sOrder, "", -2 );

      if ( $sOrder == "ORDER BY" )
      {
        $sOrder = "";
      }
    }

    $sWhere = "";


    // Filtering
    if ( $this->request['sSearch'] != "" )
    {
      $sWhere .= ($sWhere == "" ? "WHERE (" : "AND (");
      for ( $i=0 ; $i<count($aColumns) ; $i++ )
      {
        $sWhere .= $aColumns[$i]." LIKE '%". $this->request['sSearch'] ."%' OR ";
      }
      $sWhere = substr_replace( $sWhere, "", -3 );
      $sWhere .= ')';
    }

    $totalRows = $this->request['iTotalRecords'] ? $this->request['iTotalRecords'] : R::getCell(" SELECT count(*) FROM `seo` " . $sWhere);

    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
      if ( $this->request['bSearchable_'.$i] == "true" && $this->request['sSearch_'.$i] != '' )
      {
        if ( $sWhere === "" )
        {
          $sWhere = "WHERE ";
        }
        else
        {
          $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i]." LIKE '%" . $this->request['sSearch_'.$i] ."%' ";
      }
    }

    $sQuery = "
      SELECT `" . implode('`, `', $aColumns ). "` FROM `seo`
        $sWhere
        $sOrder
        $sLimit
      ";

    $result = R::getAll($sQuery);

    $output = array(
      "sEcho" => intval($this->request['sEcho']),
      "iTotalRecords" => count($result),
      "iTotalDisplayRecords" => $totalRows,
      "aaData" => $result
      );

    header('Content-type: application/json');
    echo json_encode($output);
  }



  public function action()
  {
    $action = $this->get['action'];
    $id = $this->get['id'];

    switch($action)
    {
      case 'get':
        $ret = Seo::get($id);
        $error = tr::get('error_getting_seo');
        break;

      case 'delete':
        $ret = Seo::delete($id);
        $success = tr::get('ok_deleting_seo');
        $error = tr::get('error_deleting_seo');
        break;

      case 'edit':
      case 'insert':
        $ret = Seo::edit($this->post['id'], $this->post);
        $success = tr::get('ok_saving_seo');
        $error = tr::get('error_saving_seo');
        break;
    }


    if ($ret)
    {
      if($success)
      {
        echo $this->responseJson('success', $success);
      }
      return $ret;
    }
    else
    {
      echo $this->responseJson('error', $error);
      return false;
    }
  }



  /**
   * Echoes default robots text
   * @return string Robots default text with text/plain content type
   */
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


  /**
   * Echoes sitemap XML
   * @return string Sitemam XML default text with text/xml content type
   */
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
