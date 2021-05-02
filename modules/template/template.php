<?php

/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Feb 1, 2013
 */

class template_ctrl extends Controller
{

  public $adminRequired = array(
    'confirm_delete_file', 'create_file', 'new_file',
    'delete_file', 'compile', 'edit', 'dashboard', 'save'
  );

  public function confirm_delete_file()
  {
    $file = $this->get['param'][0];
    $ext = pathinfo($file, PATHINFO_EXTENSION);

    $path = 'sites/default/';
    try {
      switch ($ext) {
        case 'twig':
          continue;
        case 'css':
        case 'less':
          $path .= 'css/';
          break;
        case 'js':
          $path .= 'js/';
          break;
        default:
          throw new Exception(tr::get('error_unknown_extension'));
          break;
      }

      $path .= $file;

      if (!file_exists($path)) {
        throw new Exception(tr::get('error_file_doesnt_exists'));
      }

      @unlink($path);

      if (file_exists($path)) {
        throw new Exception(tr::get('error_file_not_deleted'));
      }

      $resp['status'] = 'success';
      $resp['text'] = tr::get('ok_file_deleted');
    } catch (Exception $e) {
      $resp['status'] = 'error';
      $resp['text'] = $e->getMessage();
    }

    echo json_encode($resp);
  }


  public function create_file()
  {
    $file = $this->get['param'][0];

    try {
      if (file_exists($file)) {
        throw new Exception(tr::get('error_file_exists'));
      }

      @touch($file);

      if (!file_exists($file)) {
        throw new Exception(tr::get('error_file_not_created'));
      }

      $resp['status'] = 'success';
      $resp['text'] = tr::get('ok_file_created');
    } catch (Exception $e) {
      $resp['status'] = 'error';
      $resp['text'] = $e->getMessage();
    }

    echo json_encode($resp);
  }


  public function new_file()
  {
    $this->render('template', 'new_file_type');
  }

  public function delete_file()
  {
    $this->render('template', 'delete_file', [
      "files" => $this->getList()
    ]);
  }



  public function compile()
  {
    try {
      $opts = array(
        'compress' => true
      );
      $parser = new Less_Parser($opts);
      $parser->parseFile(SITE_DIR .  "css/styles.less");
      $css = $parser->getCss();
      file_put_contents(SITE_DIR . "css/styles.css", $css);

      $ret['status'] = 'success';
      $ret['text'] = tr::get('ok_compiling_less');
    } catch (Exception $e) {
      error_log($e->getTraceAsString());
      $ret['status'] = 'error';
      $ret['text'] = tr::get('error_compiling_less');
    }

    echo json_encode($ret);
  }

  public function edit()
  {
    $file = $this->get['param'][0];
    $type = $this->get['param'][1];

    switch ($type) {
      case 'twig':
        $file = SITE_DIR . $file;
        break;

      case 'css':
      case 'less':
        $file = SITE_DIR . 'css/' . $file;
        break;

      case 'js':
        $file = SITE_DIR . 'js/' . $file;
        break;

      case 'md':
        $file = SITE_DIR . $file;
        break;

      default:
        return false;
        break;
    }

    $content = file_exists($file) ? file_get_contents($file) : '';

    $this->render('template', 'editor', array(
      'filename' => $file,
      'content' => $content,
      'lang' => $file
    ));
  }

  public function dashboard()
  {
    $file = $this->get['param'][0];
    $type = pathinfo($file, PATHINFO_EXTENSION);

    $this->render('template', 'list', array(
      'files' => $this->getList(),
      'file' => $file,
      'type' => $type
    ));
  }


  private function getList()
  {
    foreach (utils::dirContent(SITE_DIR) as $f) {
      if (preg_match('/\.twig/', $f)) {
        $twig[] = $f;
      }
    }


    foreach (utils::dirContent(SITE_DIR . 'css') as $f) {
      if (preg_match('/\.css/', $f)) {
        $css[] = $f;
      }

      if (preg_match('/\.less/', $f)) {
        $less[] = $f;
      }
    }

    $md[] = 'welcome.md';

    $js = array('frontend.js');


    return array(
      'twig' => $twig,
      'css' => $css,
      'less' => $less,
      'js' => $js,
      'md' => $md
    );
  }

  public function save()
  {
    $file = $this->get['param'][0];
    $text = $this->post['text'];

    error_log(var_export($this->post, true));

    if ($file) {
      try {
        if (!utils::write_in_file($file, $text)) {
          throw new Exception('Can not write in file ' . $file);
        }
        $ret = array('status' => 'success', 'text' => 'File updated');
      } catch (Exception $e) {
        $ret = array('status' => 'error', 'text' => $e->getMessage());
      }

      echo json_encode($ret);
    }
  }
}
