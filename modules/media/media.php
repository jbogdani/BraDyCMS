<?php

/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Dec 9, 2012
 */

class media_ctrl extends Controller
{
  public function makeArtImages($force = false)
  {
    if ($this->get['param'][0] === 'force') {
      $force = true;
    }
    try {
      $dir = IMG_DIR . 'articles/';
      if (!is_dir($dir) || !is_readable($dir)) {
        throw new \Exception("Problem with directory " . $dir);
      }
      $origs = utils::dirContent($dir . 'orig');

      if (!is_array($origs)) {
        throw new \Exception("No original file found!");
      }

      $sizes = cfg::get('art_img');
      if (!is_array($sizes)) {
        throw new \Exception("Sizes found in config!");
      }

      foreach ($origs as $of) {
        $offull = $dir . 'orig/' . $of;
        foreach ($sizes as $s) {
          $dd = $dir . $s . '/';
          if (!is_dir($dd)) {
            @mkdir($dd, 0777, true);
          }
          if (!is_dir($dd) || !is_writable($dd)) {
            throw new \Exception("Can not create directory $dd or $dd is not writeable");
          }

          $df = $dd . $of;

          list($w, $h) = explode('x', $s);
          if (file_exists($df) && !$force) {
            continue;
          }
          imgMng::thumb($offull, $df, $w, $h);
        }
      }

      $out['status'] = 'success';
    } catch (Exception $e) {
      error_log($e->getMessage);
      $out['status'] = 'error';
    }

    echo json_encode($out);
  }

  public function all()
  {
    $rel_path = implode('/', $this->get['param']);
    $path = IMG_DIR . $rel_path;

    if (!is_dir($path)) {
      if (!mkdir($path, 0777, 1)) {
        $error_create = tr::sget('create_dir_error', [$path]);
        $path = IMG_DIR;
      }
    }

    $files = utils::dirContent($path);

    if (is_array($files)) {
      sort($files);

      $file_obj = array();

      foreach ($files as $file) {
        $file_obj[$file]['href'] = ($rel_path ? $rel_path . '/' : '') . $file;
        $file_obj[$file]['name'] = $file;

        if (is_file($path . '/' . $file)) {
          $file_obj[$file]['type'] = 'file';

          $ftype = utils::checkMimeExt($path . '' . $file);

          if ($ftype[0] === 'image') {
            $file_obj[$file]['src'] = $path . '/' . $file;
            $file_obj[$file]['image'] = true;
          } else {
            $file_obj[$file]['src'] = './img/ftype_icons/' . $ftype[1];
          }
        } else {
          $file_obj[$file]['type'] = 'folder';
        }
      }
    }

    if (!empty($this->request['param'][0])) {
      $path_arr = $this->request['param'];
      array_unshift($path_arr, '.');
    } else {
      $path_arr = array('.');
    }

    $this->render('media', 'list', array(
      'error_create' => $error_create,
      'path_arr' => $path_arr,
      'path' => $path,
      'rel_path' => $rel_path,
      'files' => $file_obj
    ));
  }

  public function edit()
  {
    $file = IMG_DIR . implode('/', $this->get['param']);

    $this->render('media', 'edit_form', array(
      'file' => $file,
      'uid' => uniqid(),
      'finfo' => getimagesize($file),
      'pathinfo' => pathinfo($file),
      'tr' => new tr()
    ));
  }

  public function copy()
  {
    $this->request['param'][3] = true;
    $this->rename();
  }

  public function rename()
  {
    $dir = $this->request['param'][0];
    $ofile = $this->request['param'][1];
    $nfile = $this->request['param'][2];
    $copy = $this->request['param'][3];

    $dir .= '/';

    try {
      if (!file_exists($dir . $ofile)) {
        throw new Exception(tr::sget('original_file_not_found', [$dir . $ofile]));
      }

      if (file_exists($dir . $nfile)) {
        throw new Exception(tr::sget('file_exists', [$dir . $nfile]));
      }

      $copy ? @copy($dir . $ofile, $dir . $nfile) : @rename($dir . $ofile, $dir . $nfile);

      if (!file_exists($dir . $nfile)) {
        throw new Exception(tr::sget($copy ? 'copying_file_error' : 'moving_file_error', [$dir . $nfile]));
      }

      $out['status'] = 'success';
      $out['text'] = tr::get($copy ? 'copying_file_ok' : 'moving_file_ok');
    } catch (Exception $e) {
      $out['status'] = 'error';
      $out['text'] = $e->getMessage();
    }

    echo json_encode($out);
  }

  public function delete()
  {
    $ofile = IMG_DIR . $this->request['param'][0];

    if (preg_match('/\//', $this->request['param'][0])) {
      $path_arr = explode('/', $this->request['param'][0]);

      array_pop($path_arr);

      $out['new_path'] = implode('/', $path_arr);
    }

    if (!$out['new_path']) {
      $out['new_path'] = '';
    }

    try {
      if (is_dir($ofile)) {
        @unlink($ofile . '/.DS_Store');
        @unlink($ofile . '/.Thumb.db');

        if (!@rmdir($ofile)) {
          throw new Exception(tr::get('delete_dir_error'));
        }
      } else if (is_file($ofile)) {
        if (!@unlink($ofile)) {
          throw new Exception(tr::get('delete_file_error'));
        }
      }

      $out['status'] = 'success';
      $out['text'] = tr::get('deletion_ok');

      $out['file'] = $ofile;
    } catch (Exception $e) {
      $out['status'] = 'error';
      $out['text'] = $e->getMessage();
    }
    echo json_encode($out);
  }

  public function crop()
  {
    $file = $this->request['param'][0];
    $crop = $this->request['param'][1];

    try {
      imgMng::crop(
        $this->request['param']['file'],
        $this->request['param']['width'],
        $this->request['param']['height'],
        $this->request['param']['offset_x'],
        $this->request['param']['offset_y']
      );
      echo $this->responseJson('success', tr::get('ok_cropping_file'));
    } catch (Exception $e) {
      error_log($e->getMessage());
      echo $this->responseJson('error', tr::get('error_cropping_file'));
    }
  }


  public function resize()
  {
    try {
      imgMng::resize(
        $this->request['param']['file'],
        $this->request['param']['width']
      );
      echo $this->responseJson('success', tr::get('ok_resizing_file'));
    } catch (Exception $e) {
      error_log($e->getMessage());
      echo $this->responseJson('error', tr::get('error_resizing_file'));
    }
  }


  public function convert()
  {

    try {
      imgMng::convert(
        $this->request['param']['oFile'],
        $this->request['param']['nFile']
      );
      echo $this->responseJson('success', tr::get('ok_converting_file'));
    } catch (Exception $e) {
      error_log($e->getMessage());
      echo $this->responseJson('error', tr::get('error_converting_file'));
    }
  }

  public function makeThumbs($dir = false, $max = false, $fixed = false, $overwrite = false, $recursive = false)
  {
    set_time_limit(0);
    !$dir ? $dir = $this->request['dir'] : '';
    !$max ? $max = $this->request['max'] : '';
    !$fixed ? $fixed = $this->request['fixed'] : '';
    !$overwrite ? $overwrite = $this->request['overwrite'] : '';
    !$recursive ? $recursive = $this->request['recursive'] : '';

    if (!$fixed && !$max) {
      echo tr::get('max_or_fixed_dim_required');
      return;
    }

    if ($fixed) {
      $details = $fixed;
    }

    $validExts = array('bmp', 'gif', 'jpg', 'png', 'tif');

    $files = utils::dirContent($dir);

    if (!is_array($files)) {
      return;
    }

    foreach ($files as $file) {
      if (
        is_dir($dir . '/' . $file)
        &&
        $file !== 'thumbs'
        &&
        $file !== 'downloads'
        &&
        $recursive
      ) {
        $this->makeThumbs($dir . '/' . $file, $max, $fixed, $overwrite, $recursive);
      } else if (is_file($dir . '/' . $file)) {

        $ext = pathinfo($file, PATHINFO_EXTENSION);

        $nfile = $dir . '/thumbs/' . str_replace($ext, 'jpg', $file);

        if (file_exists($nfile) && !$overwrite) {
          continue;
        }

        if (!$details) {
          $dim = getimagesize($dir . '/' . $file);
          $w = $dim[0];
          $h = $dim[1];

          if ($w > $h) {
            $width = $max;
            $height = ($h / ($w / $max));
          } else {
            $width = ($w / ($h / $max));
            $height = $max;
          }
        } else {
          $expl = explode('x', $details);
          $width = $expl[0];
          $height = $expl[1];
        }

        if (!in_array($ext, $validExts)) {
          continue;
        }

        if (!is_dir($dir . '/thumbs')) {
          @mkdir($dir . '/thumbs', 0777, true);
        }

        try {
          imgMng::thumb($dir . '/' . $file, $nfile, intval($width), intval($height));
        } catch (Exception $e) {
          error_log('Error can not create thumbnail: ' . $nfile);
          $error[] = $nfile;
        }
      }
    }

    if ($error) {
      echo 'error_in_creating_some_thumbails';
    }
  }
}
