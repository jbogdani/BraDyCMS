<?php

/**
 * 
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since        Sep 16, 2013
 */

class addsite_ctrl extends Controller
{

  public function preInstallErrors()
  {
    if (!function_exists('apache_get_modules') || !in_array('mod_rewrite', apache_get_modules())) {
      $error[] = 'Apache mod_rewrite is not enabled!';
    }

    if (!extension_loaded('PDO')) {
      $error[] = 'PDO extension not loaded!';
    }

    if (!extension_loaded('pdo_sqlite')) {
      $error[] = 'PDO SQLITE extension not loaded!';
    }

    if (!extension_loaded('imagick') && !extension_loaded('gd')) {
      $error[] = 'Neither Imagick or GD extesions are loaded';
    }

    if (!is_writable('./')) {
      $error[] = 'Main installation directory is not writeable';
    }

    if (version_compare(PHP_VERSION, '5.3') < 0) {
      $error[] = 'At least php 5.3 is required';
    }

    if (!ini_get('allow_url_fopen')) {
      $error[] = 'PHP allow_url_fopen setting should be on for auto-update function to work';
    }

    return $error;
  }


  public function build()
  {
    try {

      if (!preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/', $this->post['users'][0]['name'])) {
        throw new Exception('Please enter a valid email address to continue');
      }

      if (!$this->post['users'][0]['pwd']) {
        throw new Exception('Password field can not be empty');
      }

      $this->buildTree();

      $this->buildCfg($this->post);

      $this->buildDB();

      $this->buildTemplate();

      $this->fixMod();

      $ret = array('text' => 'Site built!!', 'status' => 'success');
    } catch (Exception $e) {
      $ret = array('text' => 'Error building site: ' . $e->getMessage(), 'status' => 'error');
    }

    echo json_encode($ret);
  }

  private function fixMod()
  {
    $list = array(
      './sites/default/cfg/config.json',
      './sites/default/cfg/database.sqlite',
      './sites/default/css/styles.css',
      './sites/default/less/styles.less',
      './sites/default/images',
      './sites/default/index.twig',
      './sites/default/js/frontend.js',
      './sites/default/users.log'
    );

    foreach ($list as $l) {
      @chmod($l, 0777);
    }
  }

  private function buildTemplate()
  {
    $this->write_file('./sites/default/css/styles.css');
    $this->write_file('./sites/default/css/styles.less');

    $this->write_file('./sites/default/js/frontend.js', "$(document).ready(function(){\n\n  $('#searchForm').submit(function(){\n    if($('#search').val() !== '' ){\n      window.location = $(this).data('path') + './search:' + encodeURIComponent($('#search').val());\n    }\n  });\n});");

    $this->write_file('./sites/default/index.twig', file_get_contents('./modules/addsite/indexModel.twig'));
    $this->write_file('./sites/default/welcome.md', file_get_contents('./modules/addsite/welcome.md'));
  }

  private function buildDB()
  {
    $sql = file_get_contents('./modules/addsite/dbSchema.sql');

    $sql_arr = utils::csv_explode($sql, '--end');

    foreach ($sql_arr as $q) {
      R::exec($q);
    }
    return true;
  }


  private function buildCfg($post_data)
  {
    $log = new log_ctrl;

    $post_data['users'][0]['pwd'] = $log->encodePwd($post_data['users'][0]['pwd']);
    $this->write_file('./sites/default/cfg/config.json', $post_data, 'json');

    $this->write_file('./sites/default/cfg/database.sqlite');
  }

  private function buildTree()
  {
    $dirs = array(
      './sites/default/cfg',
      './sites/default/css',
      './sites/default/images',
      './sites/default/images',
      './sites/default/js'
    );

    foreach ($dirs as $d) {
      if (!$this->createDir($d)) {
        throw new Exception('Can not create directory: ' . $d);
      }
    }

    return true;
  }


  private function createDir($dir)
  {
    if (is_dir($dir)) {
      return true;
    }

    @mkdir($dir, 0777, true);

    return is_dir($dir);
  }

  private function write_file($file, $text = false, $type = false)
  {
    if (!$text) {
      if (!touch($file)) {
        throw new Exception('Can not create file: ' . $file);
      }
    } else {
      if (!utils::write_in_file($file, $text, $type)) {
        throw new Exception('Can not create/write file: ' . $file);
      }
    }
  }
}
