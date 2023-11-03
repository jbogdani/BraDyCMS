<?php

/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Aug 29, 2013
 */


class tmpldata_ctrl extends Controller
{
  private $data_file = SITE_DIR . 'modules/tmpldata/tmpldata.json';

  public function save()
  {
    try {

      if (empty($this->post['data'])) {

        @unlink($this->data_file);

        if (file_exists($this->data_file)) {
          throw new Exception();
        }
      } else {

        if (!is_dir(dirname($this->data_file))) {
          mkdir(dirname($this->data_file), 0755, true);
        }

        if (!utils::write_in_file( $this->data_file, json_encode($this->post['data'], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_FORCE_OBJECT) )) {
          throw new Exception();
        }
      }

      echo json_encode(['status' => 'success', 'text' => tr::get('ok_setting_updated')]);
    } catch (Exception $e) {
      echo json_encode(['status' => 'error', 'text' => tr::get('error_setting_not_updated')]);
    }
  }

  public function view()
  {
    $this->render('tmpldata', 'edit', array(
      'data' => (file_exists($this->data_file) ? file_get_contents($this->data_file) : '{}')
    ));
  }
}
