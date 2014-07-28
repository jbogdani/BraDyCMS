<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013 
 * @license			MIT, See LICENSE file
 * @since				Sep 16, 2013
 */

class admin_ctrl extends Controller
{
  public function showLoginForm()
  {
    $this->render('admin', 'loginForm', array('version'=>  version::current()));
  }
  
  public function showCreateInstallForm()
  {
    $addsite = new addsite_ctrl();
    
    $this->render('admin', 'createInstallForm', array('preInstallErrors' => $addsite->preInstallErrors()));
  }
  
  public function showError($error)
  {
    echo '<div class="container">' .
    '<div class="alert alert-danger text-center">Something went wrong! '. $stop_error . '</div>' .
    '</div>';
  }
  
  public function showBody()
  {
    $usr_mods = utils::dirContent('./sites/default/modules');
    if (is_array($usr_mods) && !empty($usr_mods))
    {
      foreach ($usr_mods as $mod)
      {
        if (file_exists('./sites/default/modules/' . $mod . '/' . $mod . '.inc'))
        {
          require_once './sites/default/modules/' . $mod . '/' . $mod . '.inc';

          if (method_exists($mod, 'admin'))
          {
            $custom_mods[] = $mod;
          }
        }
      }
    }
    
    $this->render('admin', 'body', array(
      'version' => version::current(),
      'custom_mods' => $custom_mods,
      'welcome' => file_exists('./sites/default/welcome.html') ? Parsedown::instance()->text(file_get_contents('./sites/default/welcome.html')) : '',
      'user' => $_SESSION['user_confirmed'],
      'gravatar' => md5( strtolower( trim( $_SESSION['user_confirmed'] ) ) )
      )
    );
  }
}
?>
