<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since        Jan 11, 2016
 */

class protectedtags_ctrl extends Controller
{

  /**
   * Displays email compose form
   */
  public function sendemail_form()
  {
    $this->render('protectedtags', 'email_form', array(
      'imploded_tags' => '"' . implode('","', protectedTags::getData('tags')) . '"'
    ));
  }

  public function send_email()
  {
    set_time_limit(0);
    $from = $this->post['from'];
    $tags = utils::csv_explode($this->post['tags']);
    $subject = $this->post['subject'];
    $text = $this->post['text'];
    $sent_to = array();
    $not_sent_to = array();

    foreach ((array)protectedTags::getData('users') as $user)
    {
      $intersect = array_intersect($tags, utils::csv_explode($user['tags']));
      if(!empty($intersect))
      {
        $newtext = str_replace(
          array('%name%', '%email%'),
          array((string)$user['name'], (string)$user['email']),
          $text
        );
        $message = new PHPMailer();
        $message->setFrom($from);
        $message->addAddress($user['email']);
        $message->Subject = $subject;
        $message->Body = $newtext;

        if ($message->send())
        {
          array_push($sent_to, $user['email']);
        }
        else
        {
          array_push($not_sent_to, $user['email']);
        }
      }
    }

    echo json_encode(array('success' => $sent_to, 'error' => $not_sent_to));
  }

  /**
   * Downloads complete list of users
   */
  public function downloadUsers()
  {
    $users = protectedTags::getData('users');

    $tmp_file = tempnam(TMP_DIR, '') . '.csv';

    $fp = fopen($tmp_file, 'w');

    foreach ($users as $u)
    {
      fputcsv($fp, $u);
    }
    fclose($fp);

    header('Content-Description: File Transfer');
    header("Content-type: text/csv");
    header('Content-Length: ' . filesize($tmp_file));
    header('Content-Disposition: attachment; filename=' . basename($tmp_file));
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile($tmp_file);
  }


  /**
   * Check if it's allowed to read tags
   * @param  string|array $tags single tag or array of tags to check
   * @param  string|false $user_email User email to check, if missing $_SESSION['user_email'] will be used
   * @return boolean       true|false
   */
  public function canUserRead($tags, $user_email = false)
  {
    return protectedTags::canUserRead($tags, $user_email = false);
  }

  /**
   * Shows users list
   */
  public function users()
  {
    $data = protectedTags::getData();

    if (!is_array($data['autoregister']))
    {
      $data['autoregister'] = array();
    }

    $this->render('protectedtags', 'users', array(
        'users' => $data['users'],
        'protected' => $data['tags'],
        'autoregister' => $data['autoregister'],
        'disable_captcha' => $data['disable_captcha']
    ));
  }

  public function saveAutoregister()
  {
    if (protectedTags::saveAutoregister(
          $this->post['mode'],
          $this->post['tag'],
          $this->post['from'],
          $this->post['subject'],
          $this->post['text']
        )
    )
    {
      echo $this->responseJson('success', tr::get('ok_data_saved'));
    }
    else
    {
      echo $this->responseJson('error', tr::get('error_data_not_saved'));
    }
  }

/**
 * Shows single user add/edit form
 */
  public function show_user_form()
  {
    $id = $this->get['param'][0];

    if ($id)
    {
      $user_data = protectedTags::getData('users', $id);
    }
    $this->render('protectedtags', 'user_form', array(
      'user' => $user_data,
      'imploded_tags' => '"' . implode('","', Tag::getAllTitles()) . '"'
    ));
  }

  /**
   * Deletes user and saves data
   * @return string json (status, text) message
   */
  public function deleteUser()
  {
    $id = $this->get['param'][0];

    if (protectedTags::deleteUser($id))
    {
      echo $this->responseJson('success', tr::get('ok_user_deleted'));
    }
    else {
      echo $this->responseJson('error', tr::get('error_user_deleted'));
    }
  }

  /**
   * Saves new user data or updates single user
   * @return string json (status, text) message
   */
  public function save()
  {

    if (protectedTags::saveUser(
        $this->post['email'],
        $this->post['password'],
        $this->post['tags'],
        $this->post['name'],
        $this->post['id'],
        $this->post['confirmationcode'])
    )
    {
      echo $this->responseJson('success', tr::get('ok_user_saved'));
    }
    else {
      echo $this->responseJson('error', tr::get('error_user_saved'));
    }
  }

  /**
   * Deletes user session data
   * @return string JSON string with success status
   */
  public function logout()
  {
    protectedTags::logUser();
    echo $this->responseJson('success');
  }

  /**
   * Tries to authenticate user using POST data.
   * @return string JSON encoded message containing status (error|success) and text (in case of error) data.
   */
  public function login()
  {
    $email    = $this->post['email'];
    $password = $this->post['password'];
    $token    = $this->post['token'];
    $name     = $this->post['name'];
    $confirmationcode = $this->post['confirmationcode'];
    $repeatpassword = $this->post['repeatpassword'];
    $tag = $this->post['tag'];

    try
    {
      // Check for required  POST values: token, password, email
      if (!$token || !$email || !$password)
      {
        throw new Exception('access_denied');
      }

      // Post token must be the same of SESSION token (same sassion login attempt)
      if (!$_SESSION['token'] || $token !== $_SESSION['token'])
      {
        throw new Exception('invalid_token');
      }

      // Prevent serial attempts
      if (!utils::checkAttemptTime(MAIN_DIR . 'logs/protectedTagsAttempts.log', 2000))
      {
        throw new Exception('too_much_attempts');
      }

      // Google reCAPTCHA check
      if (protectedTags::isCaptchaEnabled() && reCAPTCHA::isProtected())
      {
        try
        {
          reCAPTCHA::validate($this->post['g-recaptcha-response']);
        }
        catch (Exception $e)
        {
          error_log($e);
          throw new Exception('captcha_error');
        }
      }

      if (!empty($confirmationcode))
      {
        if (!protectedTags::userConfirm($email, $password, $confirmationcode))
        {
          throw new Exception('authentication_failed');
        }
        protectedTags::logUser($email);
      }
      elseif(!empty($repeatpassword))
      {
        try
        {
          protectedTags::registerUser($email, $password, $tag, $name);
        }
        catch(Exception $e)
        {
          error_log($e->getMessage());
          throw new Exception('registration_failed');
        }
      }
      else
      {
        if (!protectedTags::isValidUser($email, $password))
        {
          throw new Exception('authentication_failed');
        }
        protectedTags::logUser($email);
      }

      $resp = array(
        'status' => 'success'
      );

    }
    catch (Exception $e)
    {
      $resp = array(
        'status' => 'error',
        'text' => tr::get($e->getMessage())
      );
    }

    echo json_encode($resp);
  }

  /**
   * Displays login form
   * @param  array $css array with css class names for each form element. The following indexes are allawed: form, email_cont, email_input, password_cont, password_input, sumbit_cont, submit_input
   * @return text  well-formatted html and javascript for login form
   */
  public function loginForm($css)
  {
    if (!$_SESSION['token'])
    {
      $_SESSION['token'] = md5(uniqid(rand(), true));
    }

    $this->render('protectedtags', 'login_form', array(
      'token' => $_SESSION['token'],
      'grc_sitekey' => protectedTags::isCaptchaEnabled() ? cfg::get('grc_sitekey') : false,
      'css' => $css
    ));
  }

  public function registerForm($tag, $css)
  {
    if (!$_SESSION['token'])
    {
      $_SESSION['token'] = md5(uniqid(rand(), true));
    }

    $ar = protectedTags::getData('autoregister');

    $this->render('protectedtags', 'register', array(
      'token' => $_SESSION['token'],
      'grc_sitekey' => protectedTags::isCaptchaEnabled() ? cfg::get('grc_sitekey') : false,
      'tag' => $tag,
      'css' => $css,
      'mode' => is_array($ar) ? $ar[$tag]['mode'] : false
    ));

  }

  /**
   * Displays logout button
   * @param  array $css array with CSS classes. The following can be added:
   *                    logout_cont: container class
   *                    logout_input: input class
   * @return string      Valid html with logout button
   */
  public function logoutButton($css)
  {
    if ($_SESSION['user_email'])
    {
      $this->render('protectedtags', 'logout_button', array(
        'css' => $css
      ));
    }
  }

  public function toggleCaptcha()
  {
    $captcha = ($this->get['param'][0] === '1');

    if (protectedTags::captchaStatus($captcha))
    {
      echo $this->responseJson('success', tr::get('ok_setting_updated'));
    }
    else
    {
      echo $this->responseJson('error', tr::get('error_setting_not_updated'));
    }
  }
}
