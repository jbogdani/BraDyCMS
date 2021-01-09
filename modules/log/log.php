<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Apr 3, 2013
 */

class log_ctrl extends Controller
{

  private function updateUsersLog($user, $action = ' in')
  {
    $users_log = MAIN_DIR . 'logs/users.log';

    // Create users log if it does not exist
    if (!file_exists($users_log)) {
      $fh = @fopen($users_log, 'w');
      @fclose($fh);
    }

    if (!file_exists($users_log)) {
      return false;
    }

    $log_str = $user
      . ' logged ' . $action
      . ' on: ' . date('r') . ' (unix microtime: ' . microtime(true) . ')'
      . ' from IP: ' . $_SERVER['REMOTE_ADDR']
      . "\n";

      error_log($log_str, 3, $users_log);
      return true;
  }

  public function out()
  {
    $this->updateUsersLog($_SESSION['user_confirmed'], 'out');

    $_SESSION['user_confirmed'] = false;
    $_SESSION['user_admin'] = false;

    utils::emptyTmp();

    session_destroy();

    echo '<script>window.location = "./admin"</script>';
  }

  public function encodePwd($password = false, $echo = false)
  {
    $password = $password ? $password : $this->request['password'];

    $echo = $echo ? $echo : $this->request['echo'];

    $password = sha1($password);

    if($echo) {
      echo $password;
    } else {
      return $password;
    }
  }

  public function in()
  {
    try {

      if (!filter_var($this->post['username'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception(tr::sget('invalid_email', tr::get('email_address')));
      }

      $username = $this->post['username'];

      $password = $this->post['password'];

      $token = $this->post['token'];

      if (!$token || !$username || !$password) {
        throw new Exception(tr::get('access_denied'));
      }

      if (!$_SESSION['token'] || $token !== $_SESSION['token']) {
        throw new Exception(tr::get('invalid_token'));
      }

      if (!utils::checkAttemptTime()) {
        throw new Exception(tr::get('too_much_attempts'));
      }

      // Google reCAPTCHA check
      if (reCAPTCHA::isProtected()) {

        try {
          reCAPTCHA::validate($this->post['g-recaptcha-response']);
        } catch (Exception $e) {
          error_log($e);
          throw new Exception(tr::get('captcha_error'));
        }
      }

      $cfg_users = cfg::get('users');

      foreach ($cfg_users as $user) {

        if($username === $user['name'] && $this->encodePwd($password) === $user['pwd']) {

          session_regenerate_id(true);
          $_SESSION['user_confirmed'] = $username;
          if ($user['admin'] === 'admin') {
            $_SESSION['user_admin'] = true;
          }

          $this->updateUsersLog($username);

          echo json_encode(array('status' => 'success'));
          return;

        } else {
          continue;
        }
      }

      throw new Exception(tr::get('access_denied'));

    } catch(Exception $e) {
      echo json_encode(array('status' => 'error', 'text' => $e->getMessage()));
      return;
    }
  }
}
