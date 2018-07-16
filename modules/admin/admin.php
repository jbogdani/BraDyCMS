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

        if (file_exists('./sites/default/modules/' . $mod . '/' . $mod . '.php')) {

          require_once './sites/default/modules/' . $mod . '/' . $mod . '.php';

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
    $b = './frontLibs/';
    foreach ([
      "{$b}font-awesome/css/font-awesome.min.css",
      "{$b}select2/dist/css/select2.min.css",
      "{$b}select2-bootstrap-theme/dist/select2-bootstrap.min.css",
      "{$b}bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css",
      "{$b}google-code-prettify/bin/prettify.min.css",
      "{$b}fine-uploader/fine-uploader/fine-uploader.min.css",
      "./css/admin.css"
    ] as $f) {
      if (!file_exists($f)) {
        throw new Exception("File not found:" . $f);
      }
      $css[$f] = sha1_file($f);
    }

    foreach ([
      "{$b}jquery/dist/jquery.min.js",
      "{$b}bootstrap3/dist/js/bootstrap.min.js",
      "{$b}datatables.net/js/jquery.dataTables.min.js",
      "{$b}pnotify/dist/iife/PNotify.js",
      "{$b}bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js",
      "{$b}select2/dist/js/select2.min.js",
      "{$b}google-code-prettify/bin/prettify.min.js",
      "{$b}datatables.net/js/jquery.dataTables.min.js",
      "{$b}fine-uploader/fine-uploader/fine-uploader.min.js",
      "{$b}tinymce/tinymce.min.js",
      "{$b}jquery-nestable/jquery.nestable.js",
      "./js/admin.min.js"
      ] as $f) {
        if (!file_exists($f)) {
          throw new Exception("File not found:" . $f);
        }
      $js[$f] = sha1_file($f);
    }


    if (defined('CREATE_SITE')) {

      $tmpl = 'createInstallForm';
      $addsite = new addsite_ctrl();
      $add_param = ['preInstallErrors' => $addsite->preInstallErrors()];

    } else if (!$_SESSION['user_confirmed']) {

      $tmpl = 'loginForm';
      if (!$_SESSION['token']) {
        $_SESSION['token'] = md5(uniqid(rand(), true));
      }
      $add_param = [
        'version'=> version::current(),
        'token' => $_SESSION['token'],
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
