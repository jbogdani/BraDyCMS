<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013 
 * @license      MIT, See LICENSE file
 * @since        Sep 16, 2013
 */

class admin_ctrl extends Controller
{
  public function showLoginForm()
  {
    if (!$_SESSION['token'])
    {
      $_SESSION['token'] = md5(uniqid(rand(), true));
    }
    
    $this->render('admin', 'loginForm', array('version'=>  version::current(), 'token' => $_SESSION['token']));
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
    
    if (file_exists('./sites/default/welcome.md')){
      $welcome_text = Parsedown::instance()->text(file_get_contents('./sites/default/welcome.md'));
    }
    else if(file_exists('./sites/default/welcome.html'))
    {
      $welcome_text = file_get_contents('./sites/default/welcome.html');
    }
    
    $this->render('admin', 'body', array(
      'version' => version::current(),
      'custom_mods' => $custom_mods,
      'welcome' => $welcome_text,
      'user' => $_SESSION['user_confirmed'],
      'is_admin' => $_SESSION['user_admin'],
      'gravatar' => md5( strtolower( trim( $_SESSION['user_confirmed'] ) ) )
      )
    );
  }
}
?>
