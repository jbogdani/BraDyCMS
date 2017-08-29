<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since        Aug 29, 2013
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

        if (!utils::write_in_file($this->data_file, $this->post['data'], 'json')) {
          throw new Exception();
        }
      }

      echo json_encode( ['status' => 'success', 'text' => tr::get('ok_setting_updated') ] );

    } catch (Exception $e) {
      echo json_encode( ['status' => 'error', 'text' => tr::get('error_setting_not_updated') ] );
    }


  }

  public function view()
  {
    $this->render('tmpldata', 'edit', array(
        'data' => (file_exists($this->data_file) ? file_get_contents($this->data_file) : '{}')
    ));
  }


}

?>
