<?php

class protectedTags
{
  /**
   * Path to main configuration file
   * @var string
   */
  private static $file = './sites/default/modules/protectedtags/users.json';

  /**
   * Array with user data
   * @var array
   */
  private static $data = array();

  /**
   * Checks if a certain user can access one or more tags
   * @param  mixed(string|array) $tags       tag sring or array of tags to check
   * @param  string|false $user_email user email to check, if false $_SESSION['user_email'] will be used
   * @return boolean             true if user can view, false if not
   */
  public static function canUserRead($tags, $user_email = false)
  {
    // No password proteciont cfg file
    if (!file_exists(self::$file))
    {
      return true;
    }

    $protected_tags = self::getData('tags');

    // No users defined or no protected tags saved
    if (empty(self::$data['users']) || empty($protected_tags))
    {
      return true;
    }

    if(is_string($tags))
    {
      $tags = array($tags);
    }

    if (!is_array($tags))
    {
      return true;
    }

    $restricted = array_intersect($tags, $protected_tags);

    if (empty($restricted))
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

    foreach (self::$data['users'] as $user)
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
   * Loads user data in $data variable and return all or part of the variable
   * @param  string|false $el          Part of the $data array to return; if false all array will be returned
   * @param  string|false $userid   id of user to return. $el should be users
   * @param  boolean $forcereload   If true the $data array will be repopulated
   * @return mixed(string|array)    String or array with user(s) data
   */
  public static function getData($el = false, $userid = false, $forcereload = false)
  {
    if (!is_dir(SITE_DIR . 'modules/protectedtags'))
    {
      @mkdir(SITE_DIR . 'modules/protectedtags', 0777, true);
    }

    if (!file_exists(self::$file))
    {
      return false;
    }

    if (empty(self::$data) || $forcereload)
    {
      self::$data = json_decode(file_get_contents(self::$file), true);
    }

    if ($el == 'users' && $userid)
    {
      foreach(self::$data['users'] as $user)
      {
        if ($user['id'] == $userid)
        {
          return $user;
        }
      }
      return false;
    }
    else
    {
      $ret = $el ? self::$data[$el] : self::$data;
      return $ret;
    }

  }

  /**
   * Saves data to json file
   * @param  array|false $users array with users data
   * @return boolean        true in success, false on error
   */
  private static function saveData($users = false)
  {
    $data = self::getData();

    if ($users)
    {
      $data["users"] = $users;
    }

    if (!is_array($data))
    {
      return false;
    }

    // rebuild tags
    $tags_part = array();
    foreach ($data['users'] as $user)
    {
      array_push($tags_part, $user['tags']);
    }
    $data['tags'] = array_unique(explode(',', implode(',', $tags_part)));

    // Update self::$data
    self::$data = $data;

    return utils::write_in_file(self::$file, $data, 'json');
  }

  /**
   * Checks if there is any valid user having $email and $password
   * @param  string  $email    email address, username
   * @param  string  $password password
   * @return boolean           true if ser is found, false if not
   */
  public static function isValidUser($email, $password)
  {
    // Finally check email/password
    $users = self::getData('users');

    if (!$users || !is_array($users))
    {
      error_log("no_protected_tags_users");
      return false;
    }

    $found = false;

    foreach ($users as $user)
    {
      if(
        $user['email'] === $email
        &&
        $user['password'] === $password
        &&
        (!$user['confirmationcode'] || empty($user['confirmationcode']))
      )
      {
        $found = true;
        break;
      }
    }

    return $found;
  }


  /**
   * Deletes user with $id and saves data
   * @param  string $id user id
   * @return boolean     true on success, false on error
   */
  public static function deleteUser($id)
  {
    $users = self::getData('users');

    foreach($users as $x=>&$user)
    {
      if ($user['id'] == $id)
      {
        unset($users[$x]);
      }
    }
    return self::saveData($users);
  }

  /**
   * Adds/updates user data in users' database
   * @param  string $email    email / username
   * @param  string $password password
   * @param  string $tags     list of tags
   * @param  string|false $id       user id, if false a new user will be added
   * @param string|false $confirmationcode if true a new element will be added to user row: confirmarion code
   * @return boolean           returns true in success, false on error
   */
  public static function saveUser($email, $password, $tags, $id = false, $confirmationcode = false)
  {
    $users = self::getData('users');

    if (!$users || !is_array($users))
    {
      $users = array();
    }

    if ($id)
    {
      foreach ($users as &$user) {
        if ($user['id'] == $id)
        {
          $user['email'] = $email;
          $user['password'] = $password;
          $user['tags'] = $tags;
          $user['confirmationcode'] = $confirmationcode;
        }
      }
    }
    else
    {
      array_push(
        $users,
        array(
          'id' => base_convert(microtime(false), 10, 36),
          "email" => $email,
          "password" => $password,
          "tags" => $tags,
          "confirmationcode" => $confirmationcode
        )
      );
    }
    return self::saveData($users);
  }

  /**
   * Saves autoregister information to database
   * @param  string $tag     reference tag
   * @param  string $from    from email address
   * @param  string $subject email message's subject
   * @param  string $text    email message's text
   * @return boolean          true on success, false on error
   */
  public static function saveAutoregister($tag, $from, $subject, $text)
  {
    $data = self::getData();

    if (empty($from) || empty($subject) || empty($text))
    {
      unset($data['autoregister'][$tag]);
    }
    else
    {
      $data['autoregister'][$tag] = array(
        "from" => $from,
        "subject" => $subject,
        "text" => $text
      );
    }

    self::$data = $data;
    return self::saveData();
  }

  /**
   * Checks if username & password & confirmationcode are valid.
   * If so, the confirmationcode will be removed from user data
   * @param  string $email            user's email
   * @param  string $password         user's password
   * @param  string $confirmationcode user's confirmation code, sent by email
   * @return boolean                   returns true on success, false on error
   */
  public static function userConfirm($email, $password, $confirmationcode)
  {
    $users = self::getData('users');

    if (!is_array($users) || empty($users))
    {
      return false;
    }
    $found = false;

    foreach ($users as &$u)
    {
      if (
        $u['email'] == $email
        &&
        $u['password'] == $password
        &&
        $u['confirmationcode'] == $confirmationcode
      )
      {
        $found = true;
        $u['confirmationcode'] = false;
      }
    }

    if ($found)
    {
      self::saveData($users);
    }

    return $found;
  }

  /**
   * Regenerates session and logs user IN and OUT
   * @param  string $email email to log in, if missing SESSION's ser_email will be logged out
   */
  public static function logUser($email = false)
  {
    session_regenerate_id(true);
    if ($email)
    {
      // LOGIN
      $_SESSION['user_email'] = $email;
    }
    else
    {
      // LOGOUT
      unset($_SESSION['user_email']);
    }
  }

  /**
   * Automatically registers a new user, setting the confirmationcode
   * sends an email to the new user and to the main FROM address.
   * Data (from email address, message's subject and text) are set in main config file
   * @param  [type] $email    [description]
   * @param  [type] $password [description]
   * @param  [type] $tag      [description]
   * @return [type]           [description]
   */
  public function registerUser($email, $password, $tag)
  {
    $users = self::getData('users');

    if (!is_array($users))
    {
      $users = array();
    }

    // Check if email exists
    foreach($users as $u)
    {
      if($u['email'] == $email)
      {
        $usertags = explode(',', $u['tags']);
        if (in_array($tag, $usertags))
        {
          throw new Exception('email_exists');
        }
        else
        {
          $tag = $u['tags'] . ',' . $tag;
          $id = $u['id'];
        }
      }
    }

    // Create confirmation code
    $confirmationcode = base_convert((microtime(false)+10), 10, 36);

    $autoregister = self::getData('autoregister');
    $d = $autoregister[$tag];

    if (!is_array($d))
    {
      throw new Exception('no_autoregister_data');
    }

    // Save new user's data
    if (!self::saveUser($email, $password, $tag, $id, $confirmationcode))
    {
      throw new Exception("error_saving_user");
    }

    // Send email
    try
    {
      $message = new PHPMailer();
      $message->setFrom($d['from']);
      $message->addAddress($email);
      $message->addBCC($d['from']);
      $message->Subject = $d['subject'];
      $message->Body = sprintf($d['text'], $email, $confirmationcode);
      $message->send();
      return true;
    }
    catch (phpmailerException $e)
    {
      error_log($e->getTraceAsString());
      throw new Exception('error_sending_email');
    }
  }

}
?>
