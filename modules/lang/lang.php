<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since        Sep 14, 2013
 */

class lang_ctrl extends Controller
{
  public function change()
  {
    $_SESSION['adm_lang'] = $this->get['param'][0];
    
    tr::load_file($this->get['param'][0]);
    
    echo '<script>window.location = "./admin"</script>';
    
  }
}
