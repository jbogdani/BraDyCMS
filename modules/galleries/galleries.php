<?php

/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Mar 25, 2013
 */

class galleries_ctrl extends Controller
{
  private $path = GALLERY_DIR;

  public function all()
  {
    $this->render('galleries', 'listAllGalleries', [
      'galleries' => Gallery::getAll()
    ]);
  }

  /**
   * Displays GUI for gallery editing
   * available parameters:
   *   gallery_name (string)
   *   lang (string|false)
   */
  public function edit()
  {
    $data = [
      'gallery' => $this->get['param'][0],
      'files'  => Gallery::getAllContent($this->get['param'][0], $this->get['param'][1]),
      'upload_dir' => $this->path . $this->get['param'][0],
      'sys_lang' => array(cfg::get('sys_lang'), cfg::get('sys_lang_string')),
      'langs' => cfg::get('languages'),
      'max_img_size' => cfg::get('max_img_size')
    ];
    $this->render('galleries', 'editGal', $data);
  }

  /**
   * Writes $post data in data.json file in $gallery folder
   * available parameters:
   *   gallery_name (string)
   *   lang (string|false)
   */
  public function saveData()
  {
    try {
      Gallery::save($this->get['param'][0], $this->post);
      echo $this->responseJson('success', tr::get('gallery_updated'));
    } catch (Exception $e) {
      echo $this->responseJson('error', tr::get('gallery_not_updated'));
    }
  }
  /**
   * Creates $gallery folder
   */
  public function addGallery()
  {
    try {

      $gal_name = $this->path . strtolower(str_replace(array(' ', "'", '"'), '_', $this->get['param'][0]));

      Gallery::addNew($this->get['param'][0]);
      echo $this->responseJson('success', tr::get('gallery_created'));
    } catch (Exception $e) {
      error_log($e->getMessage());

      switch ($e->getCode()) {
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
    try {

      Gallery::deleteItem($this->get['param'][0], $this->get['param'][1]);
      echo $this->responseJson('success', tr::get('img_data_deleted'));
    } catch (Exception $e) {

      error_log($e->getMessage());

      switch ($e->getCode()) {
        case '1':
          echo $this->responseJson('error', tr::get('img_not_deleted'));
          break;
        case '2':
          $msg = tr::get('img_deleted_json_not_deleted');
          break;
      }

      if ($msg) {
        echo $this->responseJson('warning', $msg);
      }
    }
  }

  /**
   * Deletes gallery folder and everything inside
   */
  public function deleteGallery()
  {
    try {
      Gallery::deleteGallery($this->get['param'][0]);
      echo $this->responseJson('success', tr::get('gallery_deleted'));
    } catch (Exception $e) {
      error_log($e->getMessage());
      echo $this->responseJson('error', tr::get('gallery_not_deleted'));
    }
  }

  public function renameGallery()
  {

    $old_name = $this->get['param'][0];
    $new_name = $this->get['param'][1];

    try {
      Gallery::rename($old_name, $new_name);
      echo $this->responseJson('success', tr::get('ok_gallery_renamed'));
    } catch (Exception $e) {
      error_log($e->getMessage());
      switch ($e->getCode()) {
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
