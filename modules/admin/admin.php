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

    return $this->render('admin', 'loginForm', [
      'version'=> version::current(),
      'token' => $_SESSION['token'],
      'grc_sitekey' => cfg::get('grc_sitekey')
    ], true
    );
  }

  public function showCreateInstallForm()
  {
    $addsite = new addsite_ctrl();

    return $this->render('admin', 'createInstallForm', ['preInstallErrors' => $addsite->preInstallErrors()], true);
  }

  public function showBody()
  {
    $usr_mods = utils::dirContent('./sites/default/modules');

    if (is_array($usr_mods) && !empty($usr_mods)) {

      foreach ($usr_mods as $mod) {

        if (file_exists('./sites/default/modules/' . $mod . '/' . $mod . '.inc')) {

          require_once './sites/default/modules/' . $mod . '/' . $mod . '.inc';

          if (method_exists($mod, 'admin')) {
            $custom_mods[] = $mod;
          }
        }
      }
    }

    if (file_exists('./sites/default/welcome.md')) {
      $welcome_text = Parsedown::instance()->text(file_get_contents('./sites/default/welcome.md'));
    } else if(file_exists('./sites/default/welcome.html')) {
      $welcome_text = file_get_contents('./sites/default/welcome.html');
    }

    return $this->render('admin', 'body', [
      'version' => version::current(),
      'custom_mods' => $custom_mods,
      'welcome' => $welcome_text,
      'user' => $_SESSION['user_confirmed'],
      'is_admin' => $_SESSION['user_admin'],
      'gravatar' => md5( strtolower( trim( $_SESSION['user_confirmed'] ) ) )
    ], true
    );
  }

  public function showMainAdmin()
  {
    foreach ([
      './bower_components/font-awesome/css/font-awesome.min.css',
      './bower_components/Ionicons/css/ionicons.min.css',
      './bower_components/pnotify/dist/pnotify.css',
      './bower_components/select2/select2.css',
      './bower_components/select2/select2-bootstrap.css',
      './bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
      './bower_components/google-code-prettify/bin/prettify.min.css',
      './bower_components/datatables/media/css/dataTables.bootstrap.min.css',
      './bower_components/fine-uploader/dist/fine-uploader.min.css',
      './css/admin.css'
    ] as $f) {
      $css[$f] = sha1_file($f);
    }

    foreach ([
      './bower_components/tinymce/tinymce.js',
      './js/admin.min.js'
      ] as $f) {
      $js[$f] = sha1_file($f);
    }


    if (defined('CREATE_SITE')) {
      $content = $this->showCreateInstallForm();
    } else if (!$_SESSION['user_confirmed']) {
      $content = $this->showLoginForm();
    } else {
      $content = $this->showBody();
    }

    $this->render('admin', 'admin', [
      'css' => $css,
      'js' => $js,
      'user_confirmed' => $_SESSION['user_confirmed'],
      'content' => $content,
      'create_site' => defined('CREATE_SITE'),
      'recaptcha' => defined('CREATE_SITE') ? false : cfg::get('grc_sitekey')
    ]);
  }
}
?>
