<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013 
 * @license      MIT, See LICENSE file
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
?>
