<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since        Sep 16, 2013
 */

class admin_ctrl extends Controller
{

  private function showBody()
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

    return [
      'version' => version::current(),
      'custom_mods' => $custom_mods,
      'welcome' => $welcome_text,
      'user' => $_SESSION['user_confirmed'],
      'is_admin' => $_SESSION['user_admin'],
      'gravatar' => md5( strtolower( trim( $_SESSION['user_confirmed'] ) ) )
    ];
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

      $tmpl = 'createInstallForm';
      $addsite = new addsite_ctrl();
      $add_param = ['preInstallErrors' => $addsite->preInstallErrors()];

    } else if (!$_SESSION['user_confirmed']) {

      $tmpl = 'loginForm';
      $add_param = [
        'version'=> version::current(),
        'token' => $_SESSION['token'] ?: md5(uniqid(rand(), true)),
        'grc_sitekey' => cfg::get('grc_sitekey')
      ];

    } else {

      $tmpl = 'body';
      $add_param = $this->showBody();
    }

    $this->render('admin', $tmpl, array_merge([
      'css' => $css,
      'js' => $js,
      'user_confirmed' => $_SESSION['user_confirmed'],
      'content' => $content,
      'recaptcha' => defined('CREATE_SITE') ? false : cfg::get('grc_sitekey')
    ], $add_param));
  }
}
?>
