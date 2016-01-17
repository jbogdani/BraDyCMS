<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since        Jan 11, 2016
 */

class protectedtags_ctrl extends Controller
{
  private $file = './sites/default/modules/protectedtags/users.json';
  private $data;

  /**
   * Check if it's allowed to read tags
   * @param  string|array $tags single tag or array of tags to check
   * @param  string|false $user_email User email to check, if missing $_SESSION['user_email'] will be used
   * @return boolean       true|false
   */
  public function canUserRead($tags, $user_email = false)
  {
    // No password proteciont cfg file
    if (!file_exists($this->file))
    {
      return true;
    }

    $protected_tags = $this->getData('tags');

    // No users defined or no protected tags saved
    if (empty($this->data['users']) || empty($protected_tags))
    {
      return true;
    }

    if (!$user_email)
    {
      $user_email = $_SESSION['user_email'];
    }

    if (!$user_email)
    {
      return false;
    }

    if(is_string($tags))
    {
      $tags = array($tags);
    }

    $restricted = array_intersect([$tags], $protected_tags);

    if (empty($restricted))
    {
      return true;
    }

    foreach ($this->data['users'] as $user)
    {
      if ($user['email'] === $user_email)
      {
        $current_user = $user;
      }
    }

    if (!$current_user)
    {
      return false;
    }

    return (
      count($restricted) == count(
        array_intersect(
          $restricted,
          explode(',', $current_user['tags'])
        )
      )
    );
  }

  /**
   * Loads data in $this->data and returns it
   * @param  string|false $el element on mail array data to return. If false all data will be retuned
   * @return array     array of data
   */
  private function getData($el = false)
  {
    $this->data = json_decode(file_get_contents($this->file), true);

    $ret = $el ? $this->data[$el] : $this->data;

    return $ret;
  }

  /**
   * Saves $this->data in json file and automatically updates the resticted tags array
   * @return boolean true|false
   */
  private function saveData()
  {
    if (!$this->data)
    {
      $this->getData();
    }
    $tags_part = array();

    foreach ($this->data['users'] as $user)
    {
      array_push($tags_part, $user['tags']);
    }

    $this->data['tags'] = array_unique(explode(',', implode(',', $tags_part)));

    return utils::write_in_file($this->file, $this->data, 'json');
  }

  /**
   * Shows users list
   */
  public function users()
  {
    if (!is_dir('./sites/default/modules/protectedtags'))
    {
      @mkdir('./sites/default/modules/protectedtags', 0777, true);
    }

    if (file_exists($this->file))
    {
      $this->getData();
    }
    $this->render('protectedtags', 'users', array(
        'users' => $this->data['users'],
        'protected' => $this->data['tags']
    ));
  }

/**
 * Shows single user add/edit form
 */
  public function show_user_form()
  {
    $id = $this->get['param'][0];

    if ($id)
    {
      foreach($this->getData('users') as $user)
      {
        if ($user['id'] == $id)
        {
          $user_data = $user;
        }
      }
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

    $users = $this->getData('users');

    foreach($users as $x=>&$user)
    {
      if ($user['id'] == $id)
      {
        unset($users[$x]);
      }
    }

    $this->data['users'] = $users;

    $resp =
      $this->saveData() ?
      array('status' => 'success', 'text' => tr::get('ok_user_deleted') ) :
      array('status' => 'error', 'text' => tr::get('error_user_deleted'));

    echo json_encode($resp);
  }

  /**
   * Saves new user data or updates single user
   * @return string json (status, text) message
   */
  public function save()
  {
    if ($this->post['id'])
    {
      $user_id = $this->post['id'];
    }

    $users = file_exists($this->file) ? $this->getData('users') : array();

    if (!$users || !is_array($users))
    {
      $users = array();
    }

    if ($user_id)
    {
      foreach ($users as &$user) {
        if ($user['id'] == $user_id)
        {
          $user['email'] = $this->post['email'];
          $user['password'] = $this->post['password'];
          $user['tags'] = $this->post['tags'];
        }
      }
    }
    else
    {
      array_push($users, array('id' => base_convert(microtime(false), 10, 36)) + $this->post);
    }

    $this->data['users'] = $users;

    $resp = $this->saveData() ?
      array('status' => 'success', 'text' => tr::get('ok_user_saved') ) :
      array('status' => 'error', 'text' => tr::get('error_user_saved'));

    echo json_encode($resp);
  }

  /**
   * Deletes user session data
   * @return string JSON string with success status
   */
  public function logout()
  {
    session_regenerate_id(true);
    unset($_SESSION['user_email']);
    echo json_encode(array('status' => 'success'));
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

    try
    {
      // Check for required  POST values: token, password, email
      if (!$token || !$email || !$password)
      {
        throw new Exception('access_denied');
      }

      // Post token mus be the same of SESSION token (same sassion login attempt)
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
      if (cfg::get(grc_sitekey))
      {
        $response = $this->post['g-recaptcha-response'];
        $secret = cfg::get('grc_secretkey');

        $url = 'https://www.google.com/recaptcha/api/siteverify';

    		$curl = new \Curl($url);

    		$curl->POST = true;

    		$post = array(
    				'secret' => $secret,
    				'response'	=> $response,
            'remoteip' => $_SERVER['HTTP_CLIENT_IP']

    		);
    		$curl->POSTFIELDS = $post;

        $data = json_decode($curl->fetch(), true);

        if (!$data['success'] || $data['success'] != 'true')
        {
          error_log(var_export($data, true));
          throw new Exception(tr::get('captcha_error'));
        }
      }

      // Finally check email/password
      $users = $this->getData('users');

      if (!$users || !is_array($users))
      {
        throw new Exception("no_protected_tags_users");
      }

      foreach ($users as $user)
      {
        if($user['email'] === $email && $user['password'] === $password)
        {
          session_regenerate_id(true);
          $_SESSION['user_email'] = $email;
          $resp = array(
            'status' => 'success'
          );
          break;
        }
      }

      if (!$resp)
      {
        $resp = array(
          'status' => 'error',
          'text'=> tr::get('authentication_failed')
        );
      }
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
    $uid = 'f' . uniqid();

    $this->render('protectedtags', 'login_form', array(
      'token' => $_SESSION['token'],
      'grc_sitekey' => cfg::get('grc_sitekey'),
      'css' => $css
    ));
  }

  public function logoutButton($css)
  {
    if ($_SESSION['user_email'])
    {
      $this->render('protectedtags', 'logout_button', array(
        'css' => $css
      ));
    }
  }

}
