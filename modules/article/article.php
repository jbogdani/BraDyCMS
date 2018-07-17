<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Dec 1, 2012
 */


class article_ctrl extends Controller
{

  public function sql2json()
  {
    $aColumns = array( 'id', 'title', 'textid', 'sort', 'author', 'status', 'publish');

    $sIndexColumn = 'id';


    // Paging
    $sLimit = '';
    if ( isset( $this->request['iDisplayStart'] ) && $this->request['iDisplayLength'] != '-1' ) {
      $sLimit = "LIMIT " . $this->request['iDisplayStart'] .", " .
        $this->request['iDisplayLength'];
    }

    // Ordering
    if ( isset( $this->request['iSortCol_0'] ) ) {
      $sOrder = "ORDER BY  ";

      for ( $i=0 ; $i<intval( $this->request['iSortingCols'] ) ; $i++ ) {

        if ( $this->request[ 'bSortable_' . intval($this->request['iSortCol_' . $i]) ] == "true" ) {

          $sOrder .= $aColumns[ intval( $this->request['iSortCol_'.$i] ) ] . "
            " . $this->request['sSortDir_'.$i] . ", ";
        }
      }

      $sOrder = substr_replace( $sOrder, "", -2 );

      if ( $sOrder == "ORDER BY" ) {
        $sOrder = "";
      }
    }


    if (is_array($this->request['param']) && !empty($this->request['param'][0])) {

      $tag_ids = R::getCol("SELECT `id` FROM `tag` WHERE `title` IN ('" . implode("','", $this->request['param']) . "')");

      // If tag does not exist > return 0 records!!!
      if(empty($tag_ids)){
        $output = array(
          "sEcho" => intval($this->request['sEcho']),
          "iTotalRecords" => 0,
          "iTotalDisplayRecords" => 0,
          "aaData" => []
          );

        header('Content-type: application/json');
        echo json_encode($output);
        return;
      }

      $tmp = [];
      foreach($tag_ids as $tagid) {
        array_push($tmp, "`tag_id` = " . $tagid);
      }

      $sWhere = "WHERE  `id` IN ( "
          . "SELECT `articles_id` FROM `articles_tag` WHERE " . implode(' OR ', $tmp) . " GROUP BY `articles_id` HAVING count(*) = ". count($tmp)
        . " ) ";
    } else {
      $sWhere = "";
    }


    // Filtering
    if ( $this->request['sSearch'] !== '' ) {

      $sWhere .= ($sWhere === '' ? 'WHERE (' : ' AND (');

      for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
        $sWhere .= $aColumns[$i] . " LIKE '%" . $this->request['sSearch'] . "%' OR ";
      }
      $sWhere = substr_replace( $sWhere, '', -3 );
      $sWhere .= ')';
    }

    $totalRows = $this->request['iTotalRecords'] ? $this->request['iTotalRecords'] : R::getCell(" SELECT count(*) FROM `articles` " . $sWhere);

    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ ) {

      if ( $this->request['bSearchable_'.$i] == "true" && $this->request['sSearch_'.$i] != '' ) {

        if ( $sWhere === "" ) {
          $sWhere = "WHERE ";
        } else {
          $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i]." LIKE '%" . $this->request['sSearch_'.$i] ."%' ";
      }
    }

    $sQuery = "
      SELECT `" . implode('`, `', $aColumns ). "` FROM `articles`
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


  public function all()
  {
    $tags = Article::getTags();

    if (!is_array($tags))
    {
      $tags = array();
    }

    $this->render('article', 'list', array(
      'tags' => $tags,
      'all_tags' => $tags,
      'active_tags' => $this->request['param'],
      'imploded_active_tags' => (!empty($this->request['param'][0]) ? '&param[]=' . implode('&param[]=', $this->request['param']) : ''),
      'cfg_langs' => cfg::get('languages')
      ));
  }

  public function deleteTag()
  {
    try
    {
      Article::deleteTag($this->get['tag']);
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());

      echo tr::get('error_delete_tag');
    }
  }

  public function deleteAllTags()
  {
    try
    {
      Article::deleteUnusedTags();
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());

      echo tr::get('error_delete_tag');
    }
  }

  public function check_duplicates()
  {
    $val = $this->get['param'][0];
    $id = $this->get['param'][1];

    $res = Article::getByTextid($val, false, true);

    if ($res && $res['id'] !== $id)
    {
      echo tr::sget('duplicate_textid', $val);
    }
  }


  public function edit()
  {
    $id = $this->get['param'][0];

    // check if art_img files exist
    $art_img = cfg::get('art_img');
    $art_img[] = 'orig';

    // get list of (different dimensions of) article's images, if present
    if (is_array($art_img))
    {
      foreach($art_img as $dimension)
      {
        if (file_exists(IMG_DIR . 'articles/' . $dimension . '/' . $id . '.jpg'))
        {
          $article_images[$dimension] = IMG_DIR . 'articles/' . $dimension . '/' . $id . '.jpg';
        }
      }
    }

    // get list of all available image files
    if (is_dir(IMG_DIR . 'articles/media/' . $id))
    {
      $art_media = utils::dirContent(IMG_DIR . 'articles/media/' . $id);

      if(($key = array_search('thumbs', $art_media)) !== false)
      {
        unset($art_media[$key]);
      }
    }

    $art = Article::getById($id);

    $available_tags = Article::getTags();

    if (!is_array($available_tags))
    {
      $available_tags = array();
    }
    $customflds = cfg::get('custom_fields');

    if (is_array($customflds))
    {
      foreach ($customflds as &$a)
      {
        if($a['values'])
        {
          $a['values'] = utils::csv_explode($a['values']);
        }
      }
    }

    $this->render('article', 'form', array(
      'art'=>$art,
      'custom_fields' => $customflds,
      'date' => date('Y-m-d'),
      'all_tags' => $available_tags,
      'art_imgs' => $article_images,
      'tmp_path' => TMP_DIR,
      'cfg_langs' => cfg::get('languages'),
      'art_media' => $art_media,
      'art_gallery' => ( $art['textid'] && file_exists(GALLERY_DIR . $art['textid']) && is_dir(GALLERY_DIR . $art['textid']))
    ));
  }

  public function saveTransl()
  {
    $data = $this->post;
    $art_id = $this->get['param'][0];

    try
    {
      Article::translate($art_id, $data);

      $out['text'] = tr::get('ok_translation_saved');
      $out['status'] = 'success';
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());
      $out['text'] = tr::get('error_translation_not_saved');
      $out['status'] = 'error';
    }

    echo json_encode($out);
  }

  /**
   * Displays articles's translation form
   * param[0] is translation language
   * param[1] is article's id
   */
  public function translate()
  {
    $id = $this->get['param'][1];
    $lang = $this->get['param'][0];

    $article = Article::getById($id, false, true);
    $translations = $article->ownArttrans;

    if (is_array($translations)) {

      foreach ($translations as $trans) {

        if ($trans->lang == $lang) {

          $art_translation = $trans->export();
        }

      }
    }

    if (!$art_translation) {
      $art_translation = [ 'lang' => 'en', 'status' => 0, 'art_id' => $id ];
    }

    $article_array = $article->export();

    foreach (cfg::get('languages') as $langs) {

      if ($lang === $langs['id']) {

        $lang_arr = $langs;
      }
    }

    $this->render('article', 'transl_form', [
      'art'=>$article_array,
      'custom_fields' => cfg::get('custom_fields'),
      'transl' => $art_translation,
      'lang_arr' => $lang_arr
    ]);

  }


  /**
   * Alias for article_ctrl::edit
   */
  public function addNew()
  {
    $this->edit();
  }


  public function delete()
  {
    $id = $this->get['param'][0];
    try
    {
      Article::delete($id);
      $this->delete_art_img($id, true);

      $out['type'] = 'success';
      $out['text'] = tr::get('delete_article_ok');
    }
    catch (Exception $e)
    {
      error_log(tr::sget('delete_article_error'));
      $out['type'] = 'error';
      $out['text'] = $e->getMessage();
    }

    echo json_encode($out);
  }

  public function save()
  {
    $id = $this->get['param'][0];
    $data = $this->post;

    try
    {
      $new_id = Article::save($id, $data);

      if (!$new_id)
      {
        throw new Exception(tr::get('save_article_error'));
      }
      $out['type'] = 'success';
      $out['text'] = tr::get('save_article_ok');
      $out['id'] = $new_id;
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());
      $out['type'] = 'error';
      $out['text'] = tr::get('save_article_error');
    }
    echo json_encode($out);
  }

  public function delete_art_img($return = false)
  {
    $id = $this->get['param'][0];
    try
    {
      $dimensions = cfg::get('art_img');
      $dimensions[] = 'orig';

      // check if cfg::art_img is available
      if (!is_array($dimensions))
      {
        throw new Exception(tr::get('cfg_art_img_missing'));
      }
      foreach($dimensions as $dim)
      {
        $file = IMG_DIR . 'articles/' . $dim . '/' . $id . '.jpg';

        if (file_exists($file))
        {
          @unlink($file);

          if (file_exists($file))
          {
            $error[] = $file;
          }
          else
          {
            $ok = $file;
          }
        }
      }
    }
    catch(Exception $e)
    {
      if ($return)
      {
        throw new Exception($e);
      }
      else
      {
        $msg['status'] = 'error';
        $msg['text'] = $e->getMessage();
      }
    }

    if ($return)
    {
      return array('ok'=> $ok, 'error' => $error);
    }
    else
    {
      if (count($error) > 0 && count($ok) > 0)
      {
        $msg['status'] = 'error';
        $msg['text'] = tr::sget('art_img_partially_deleted', implode(', ', $error));
      }
      else if (count($error) > 0 && count($ok) == 0)
      {
        $msg['status'] = 'error';
        $msg['text'] = tr::get('art_img_not_deleted');
      }
      else if (count($error) == 0 && count($ok) > 0)
      {
        $msg['status'] = 'success';
        $msg['text'] = tr::get('art_img_deleted');
      }
    }

    echo json_encode($msg);
  }

  /**
   * Creates image files using configuration dimensions
   * and places this images in appropriate directories (./sites/images/articles/dim_widthxdim_height/art_id.jpg)
   *
   * @param int $id article ID
   * @param string $file full path to uploaded file in temporary dire
   * @throws Exception on erros
   */
  public function attachImage()
  {
    if (func_num_args() > 0)
    {
      list($id, $file) = func_get_args();
    }
    else
    {
      $id = $this->get['param'][0];
      $file = $this->get['param'][1];
    }

    try{

      $dimensions = cfg::get('art_img');
      $dimensions[] = 'orig';

      // check if cfg::art_img is available
      if (!is_array($dimensions))
      {
        throw new Exception(tr::get('cfg_art_img_missing'));
      }

      // loop in dimensions
      foreach($dimensions as $dim)
      {
        // define image directory
        $dir = IMG_DIR . 'articles/' . $dim;

        // if image dir does not exist, try to create it
        if(!is_dir($dir))
        {
          @mkdir($dir, 0777, true);
        }

        // if image dir does not exist, throw exception
        if(!is_dir($dir))
        {
          throw new Exception(tr::sget('create_dir_error', $dir));
        }

        // if image dir is not writeable, throw exception
        if(!is_writable($dir))
        {
          throw new Exception(tr::sget('dir_is_not_writable', $dir));
        }


        // make thumbnails in jpg format, using original file
        $output = $dir . '/' . $id . '.jpg';

        if ($dim === 'orig')
        {
          @copy(TMP_DIR . $file, $output);
          if (!file_exists($output))
          {
            throw new Exception(tr::get('error_copying_file'));
          }
        }
        else
        {
          try
          {
            $dim_arr = explode('x', $dim);
            imgMng::thumb(TMP_DIR . $file, $output, $dim_arr[0], $dim_arr[1]);
          }
          catch (Exception $e)
          {
            error_log($e->getMessage());
            throw new Exception(tr::get('thumbnail_not_created'));
          }
        }
      }

      $msg['status'] = 'success';
      $msg['text'] = tr::get('art_images_created');
    }
    catch (Exception $e)
    {
      $msg['status'] = 'error';
      $msg['text'] = $e->getMessage();
    }
    echo json_encode($msg);
  }

  /**
   * Attaches media file (in TMP directory) to article
   * @param int $id article ID
   * @param string $file full path to uploaded file in temporary dire
   * @return string json encoded response
   */
  public function attachMedia()
  {
    if (func_num_args() > 0)
    {
      list($id, $file) = func_get_args();
    }
    else
    {
      $id = $this->get['param'][0];
      $file = $this->get['param'][1];
    }


    // define image directory
    $dir = IMG_DIR . 'articles/media/' . $id;

    try
    {
      // if image dir does not exist, try to create it
      if(!is_dir($dir))
      {
        @mkdir($dir, 0777, true);
      }

      // if image dir does not exist, throw exception
      if(!is_dir($dir))
      {
        throw new Exception(tr::sget('create_dir_error', $dir));
      }

      // if image dir is not writeable, throw exception
      if(!is_writable($dir))
      {
        throw new Exception(tr::sget('dir_is_not_writable', $dir));
      }

      // set output file full path
      $output = $dir . '/' . $file;

      // copy file to destination
      @copy(TMP_DIR . $file, $output);
      if (!file_exists($output))
      {
        throw new Exception(tr::get('error_copying_file'));
      }

      $msg['status'] = 'success';
      $msg['text'] = tr::get('ok_file_uploaded');
      $msg['all_media'] = utils::dirContent($dir);
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());
      $msg['status'] = 'error';
      $msg['text'] = tr::get('error_file_not_uploaded');
    }

    echo json_encode($msg);
  }


}
