<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Mar 25, 2013
 */

class galleries_ctrl extends Controller
{
  private $path = GALLERY_DIR;

  public function all()
  {
    $this->render('galleries', 'listAllGalleries', array(
      'galleries' => Gallery::getAll()
      ));
  }

  /**
   * Displays GUI for gallery editing
   * available parameters:
   *   gallery_name (string)
   *   lang (string|false)
   */
  public function edit()
  {

    $this->render('galleries', 'editGal', array(
      'gallery'=> $this->get['param'][0],
      'files'  => Gallery::getAllContent($this->get['param'][0], $this->get['param'][1]),
      'upload_dir'=> $this->path . $this->get['param'][0],
      'sys_lang' => array(cfg::get('sys_lang'), cfg::get('sys_lang_string')),
      'langs' => cfg::get('languages'),
      'thumb_dimensions' => '200x200',
      'max_img_size' => cfg::get('max_img_size')
      )
    );
  }

  /**
   * Writes $post data in data.json file in $gallery folder
   * available parameters:
   *   gallery_name (string)
   *   lang (string|false)
   */
  public function saveData()
  {
    try
    {
      Gallery::save($this->get['param'][0], $this->post);
      echo $this->responseJson('success', tr::get('gallery_updated'));
    }
    catch (Exception $e)
    {
      echo $this->responseJson('error', tr::get('gallery_not_updated'));
    }
  }

  /**
   * Creates thumbnail image for $image in $gallery
   */
  public function makeThumbs()
  {
    $path = $this->get['param'][0];
    $file = $this->get['param'][1];
    $thumbs_dimensions = $this->get['param'][2];

    if (!$thumbs_dimensions || !preg_match('/^([0-9]{1,4})x([0-9]{1,4})$/', $thumbs_dimensions))
    {
      $thumbs_dimensions = '200x200';
    }

    if (!is_dir($path . '/thumbs'))
    {
      mkdir($path . '/thumbs', 0777, true);
    }

    $thumb_path = $path . '/thumbs/' . $file;

    try{
      $dims_arr = explode('x', $thumbs_dimensions);
      imgMng::thumb($path . '/' . $file, $thumb_path, $dims_arr[0], $dims_arr[1]);

      $msg['text'] = tr::get('thumbnail_created');
      $msg['status'] = 'success';
      $msg['thumb'] = $thumb_path;
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());
      $msg['text'] = tr::get('thumbnail_not_created');
      $msg['status'] = 'error';
    }

    echo json_encode($msg);
  }

  /**
   * Creates $gallery folder
   * Creates thumbs folder in $gallery folder
   */
  public function addGallery()
  {
    try{

      $gal_name = $this->path . strtolower(str_replace(array(' ', "'", '"'), '_', $this->get['param'][0]));

      Gallery::addNew($this->get['param'][0]);
      echo $this->responseJson('success', tr::get('gallery_created'));
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());

      switch($e->getCode())
      {
        case '1':
          $msg = tr::get('gallery_exists');
          break;
        case '2':
          $msg = tr::get('gallery_not_created');
          break;
        case '3':
          $msg = tr::get('gallery_partially_created');
          break;
      }
      echo $this->responseJson('error', $msg);
    }
  }

  /**
   * Deletes $image in $gallery folder
   * Removes data from data.json file
   */
  public function deleteImg()
  {
    try
    {
      Gallery::deleteItem($this->get['param'][0], $this->get['param'][1]);
      echo $this->responseJson('success', tr::get('img_thumb_data_deleted'));
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());
      switch ($e->getCode())
      {
        case '1':
          echo $this->responseJson('error', tr::get('img_not_deleted'));
          break;
        case '2':
          $msg = tr::get('img_deleted_thumb_json_not_deleted');
          break;
        case '3':
          $msg = tr::get('img_deleted_thumb_not_deleted');
          break;
        case '4':
          $msg = tr::get('img_deleted_json_not_deleted');
          break;
      }
      if ($msg)
      {
        echo $this->responseJson('warning', $msg);
      }
    }
  }

  /**
   * Deletes all image files inside $gallery folder
   * Deletes all files inside thumbs folder in $gallery folder
   * Deletes thumbs folder in $gallery folder
   * Deletes data.json file in $gallery folder
   * Deletes $gallery folder
   */
  public function deleteGallery()
  {
    try
    {
      Gallery::deleteGallery($this->get['param'][0]);
      echo $this->responseJson('success', tr::get('gallery_deleted'));
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());
      echo $this->responseJson('error', tr::get('gallery_not_deleted'));
    }
  }

  public function renameGallery(){

    $old_name = $this->get['param'][0];
    $new_name = $this->get['param'][1];

    try
    {
      Gallery::rename($old_name, $new_name);
      echo $this->responseJson('success', tr::get('ok_gallery_renamed'));
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());
      switch($e->getCode())
      {
        case '1':
          $msg = tr::get('error_gallery_does_not_exist');
          break;
        case '2':
          $msg = tr::get('error_new_gallery_name_exist');
          break;
        case '3':
          $msg = tr::get('error_new_gallery_not_renamed');
          break;
      }
      echo $this->responseJson('error', $msg);
    }
  }

}
