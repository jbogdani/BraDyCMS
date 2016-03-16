<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Dec 1, 2012
 */

class utils
{

  /**
   * Returns base URL of current site wit correct protocol (http|https), host and rewrite base
   * @return string
   */
  public static function getBaseUrl()
  {
    // http://stackoverflow.com/questions/4503135/php-get-site-url-protocol-http-vs-https
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = $protocol . $_SERVER['HTTP_HOST'] . cfg::get('rewriteBase');
    return (substr($url, -1) ===  '/' ? $url : $url . '/');
  }

  /**
   * Recursively empties/deletes directory
   * @param string $dir directory to empty/delete
   * @param boolean $dont_delete_self if true the directory will be emptied but not deleted
   * @return string Error text
   */
  public static function recursive_delete($dir, $dont_delete_self = false)
  {
    $files = self::dirContent($dir, true);

    if (is_array($files))
    {
      foreach($files as $file)
      {
        if (is_dir($dir . '/' . $file))
        {
          self::recursive_delete($dir . '/' . $file);

          @rmdir($dir . '/' . $file);
        }
        else
        {
          @unlink($dir . '/' . $file);

          if (file_exists($dir . '/' . $file))
          {
            $error[] = 'Can not delete file: ' . $dir . '/' . $file;
            error_log('1. Can not delete file: ' . $dir . '/' . $file);
          }
        }
      }
    }

    if ($dont_delete_self)
    {
      $check = self::dirContent($dir);
    }
    else
    {
      @rmdir($dir);
      $check = array($dir);
    }

    foreach ($check as $f)
    {
      if (file_exists($f))
      {
        $error[] = 'Can not delete directory: ' . $f;
        error_log('2. Can not delete directory: ' . $f);
      }
    }

    return $error;
  }

  /**
   * Deletes from temporary directory files older then 3 days (259200 seconds)
   */
  public static function emptyTmp()
  {
    $tmp_content = self::dirContent(TMP_DIR);

    if ($tmp_content)
    {
      foreach ($tmp_content as $f)
      {
        $f = TMP_DIR . $f;
        if ( ( filemtime($f) + 259200 ) < time() )
        {
          if ( !unlink($f) )
          {
            error_log('Error. Can not delete ' . $f . "\n", 3, ERR_LOG);
          }
        }
      }
    }
  }

  /**
   * Returns array with list of files in directory
   * @param string $dir path to directory
   * @param boolean $dont_ignore_sys if true also system files will be returned
   * @return boolean
   */
  public static function dirContent($dir, $dont_ignore_sys = false)
  {
    if (!is_dir($dir))
    {
      return false;
    }

    $dont_consider_array = array('.', '..');

    if (!$dont_ignore_sys)
    {
      array_push($dont_consider_array,'.DS_Store', 'thumbs.db', '.svn');
    }

    $files = scandir($dir);

    if (is_array($files))
    {
      foreach ($files as $id => &$f)
      {
        if(in_array($f, $dont_consider_array))
        {
          unset($files[$id]);
        }
      }
    }
    $handle = @opendir($dir);

    return $files;
  }

  /**
   * Returna array with file Type and icon name
   * @param string $file path to file
   * @return array|false
   */
  public static function checkMimeExt ( $file )
  {
    $mimeTypes = array(
        "Simple text" => array(
            "mime"=>array("text/plain"),
            "ext"=>array("txt"),
            "icon"=>"text-plain.png"),

        "HTML"=>array(
            "mime"=>array("text/html"),
            "ext"=>array("html", "xhtml"),
            "icon"=>"text-html.png"),

        "CSS"=>array(
            "mime"=>array("text/css"),
            "ext"=>array("css"),
            "icon"=>"text-css.png"),

        "JavaScript"=>array(
            "mime"=>array("application/javascript", "application/json"),
            "ext"=>array("js", "json"),
            "icon"=>"application-javascript.png"),

        "XML"=>array(
            "mime"=>array("application/xml"),
            "ext"=>array("xml"),
            "icon"=>"application-xml.png"),

        "Video"=>array(
            "mime"=>array("application/x-shockwave-flash", "video/x-flv", "video/quicktime", "video/x-generic", "video/x-mng"),
            "ext"=>array("swf", "flv", "qt", "mov"),
            "icon"=>"video.png"),

        "Vector"=>array(
            "mime"=>array("image/svg+xml", "application/postscript"),
            "ext"=>array("svg", "ai", "eps", "ps"),
            "icon"=>"vector.png"),

        "Archive"=>array(
            "mime"=>array("application/zip",
                "application/x-rar-compressed", "application/vnd.ms-cab-compressed"),
            "ext"=>array("zip", "rar", "cab"), "icon"=>"archive.png"),

        "EXE"=>array(
            "mime"=>array("application/x-msdownload"),
            "ext"=>array("exe", "msi"),
            "icon"=>"executable.png"),

        "Audio"=>array(
            "mime"=>array("audio/mpeg", "audio/aac", "audio/ac3", "audio/basic", "audio/midi", "audio/mp4", "audio/mpeg", "audio/prs.sid", "audio/vn.rn-realmedia", "audio/vn.rn-realvideo", "audio/vnd.rn-realaudio", "audio/vnd.rn-realvideo", "audio/x-adpcm", "audio/x-aiff", "audio/x-flac", "audio/x-flac+ogg", "audio/x-generic", "audio/x-matroska", "audio/x-mod", "audio/x-monkey", "audio/x-mp2", "audio/x-mpegurl", "audio/x-ms-asx", "audio/x-ms-wma", "audio/x-musepack", "audio/x-pn-realaudio-plugin", "audio/x-scpls", "audio/x-speex+ogg", "audio/x-vorbis+ogg", "audio/x-wav"),
            "ext"=>array("mp3", "mp4", "wma", "wav", "ogg"),
            "icon"=>"audio.png"),

        "PDF"=>array(
            "mime"=>array("application/pdf"),
            "ext"=>array("pdf"),
            "icon"=>"application-pdf.png"),

        "Image Manipulation"=>array(
            "mime"=>array("image/vnd.adobe.photoshop"),
            "ext"=>array("psd", "xcf"),
            "icon"=>"image-x-generic.png"),

        "Document"=>array(
            "mime"=>array("application/msword", "application/rtf", "application/vnd.oasis.opendocument.text", "x-office/document"),
            "ext"=>array("doc", "rtf", "odt"),
            "icon"=>"application-msword.png"),

        "Spreadsheet"=>array(
            "mime"=>array("application/vnd.ms-excel", "application/vnd.oasis.opendocument.spreadsheet", "x-office/spreadsheet"),
            "ext"=>array("xls", "ods"),
            "icon"=>"application-vnd.ms-excel.png"),

        "Presentation"=>array(
            "mime"=>array("application/vnd.ms-powerpoint", "vnd.oasis.opendocument.presentation"),
            "ext"=>array("ppt", "odp"),
            "icon"=>"application-vnd.ms-powerpoint.png"),

        "image"=>array(
            "mime"=>array("image/png", "image/jpeg", "image/gif", "image/bmp", "image/vnd.microsoft.icon", "image/tiff"),
            "ext"=>array("png", "jpeg", "jpg", "bmp", "ico", "tif", "tiff", "gif"),
            "icon"=>"image-x-generic.png"),
        "EPUB"=>array(
            "mime"=>array("application/epub+zip"),
            "ext"=>array("epub"),
            "icon"=>"epub.png"),
        "MOBIPOCKET"=>array(
            "mime"=>array("application/x-mobipocket-ebook"),
            "ext"=>array("mobi"),
            "icon"=>"mobi.png"),
    );

    $trova_punto = explode(".", $file);
    $ext = strtolower($trova_punto[count($trova_punto) - 1]);

    if (!$ext AND !$mime)
    {
      return false;
    }

    foreach ( $mimeTypes as $name=>$arr_values )
    {
      if ( $mime )
      {
        if ( in_array($mime, $arr_values['mime']) )
        {
          return array($name, $arr_values['icon']);
        }
      }

      if( $ext )
      {
        if ( in_array($ext, $arr_values['ext']) )
        {
          return array($name, $arr_values['icon']);
        }
      }
    }
    return (array("Unknown filetype", "unknown.png"));
  }

  /**
   * Upload file and returns information about uploaded file
   * @param string $upload_dir  Directrory where the file will be uploaded
   * @param boolean $sanitize if true filename will be cleaned
   * @param int|false $resize if true (file should be an image) the image will be resided to fit dimensions
   */
  public static function upload($upload_dir, $sanitize = false, $resize = false)
  {
    $allowedExtensions = array();

    $sizeLimit = 8 * 1024 * 1024;

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

    $result = $uploader->handleUpload($upload_dir . '/', false);

    if ($result['success'] && $sanitize)
    {
      $oPath = $result['path'] . $result['filename'] . '.' . $result['ext'];

      $result['filename'] = str_replace(
          array(' ', '{', '}', ',','"', "'", '[', ']', '(', ')'),
          array('_', null, null, null, null, null, null, null),
          $result['filename']);
      $result['ext'] = strtolower($result['ext']);

      $nPath = $result['path'] . $result['filename'] . '.' . $result['ext'];

      if ($oPath != $nPath)
      {
        @rename($oPath, $nPath);
        unlink($oPath);
      }

      if ($resize && cfg::get('max_img_size'))
      {
        imgMng::resize(
          $result['path'] . $result['filename'] . '.' . $result['ext'],
          cfg::get('max_img_size'),
          cfg::get('max_img_size'),
          72,
          true
          );
      }

    }

    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
  }

  /**
   * Returns array after string is exploded using custom delimiter
   * @param string $string String to explode
   * @param string $delimiter Delimiter
   * @param string $escape
   * @return array
   */
  public static function csv_explode($string, $delimiter = ',', $escape ="\\") {

    $string = str_replace($delimiter .' ', $delimiter, $string);

    if ( preg_match('/' . $delimiter . '/i', $string) )
    {
      if ( preg_match('/' . $escape . $delimiter . '/i', $string) )
      {
        $string = str_replace($escape . $delimiter, '@@delimiter@@', $string);
        $changed = true;
      }
      $array = array_filter(explode($delimiter, $string));


      if ($changed)
      {
        foreach ($array as &$a)
        {
          $a = str_replace('@@delimiter@@', $delimiter, $a);
        }
      }

      return $array;

    }
    else
    {
      return array($string);
    }
  }

  /**
   * Returns array of available languages with information about code, name and href
   * @param string $lang
   * @return array
   */
  public static function getLanguages($lang = false)
  {
    $lang_arr = array(
      array(
        'code'=> cfg::get('sys_lang'),
        'string' => cfg::get('sys_lang_string'),
        'is_current' => (($lang == cfg::get('sys_lang')) || !$lang ? true : false)
        )
      );
    // other languages
    $languages = cfg::get('languages');

    if (is_array($languages))
    {
      foreach($languages as $ll)
      {
        if ($ll['published'] == '1')
        {
          array_push($lang_arr, array(
            'code'=> $ll['id'],
            'string' => $ll['string'],
            'is_current' => ( $ll['id'] == $lang ?  true : false)
            ));
        }
      }
    }

    $rewriteBase = ( !cfg::get('rewriteBase') || cfg::get('rewriteBase') === '/' ? '' : cfg::get('rewriteBase') . '/');


    $cleaned_request_uri = str_replace($rewriteBase, null, $_SERVER['REQUEST_URI']);


    foreach($lang_arr as &$langThis)
    {
      if($lang)
      {
        if (preg_match('/\/' . $lang . '/', $_SERVER['REQUEST_URI']) || preg_match('/lang=' . $lang . '/', $_SERVER['REQUEST_URI']))
        {
          $langThis['href'] = str_replace(
              array('/' . $lang, 'lang=' . $lang),
              array('/' . $langThis['code'], 'lang=' . $langThis['code']),
              $_SERVER['REQUEST_URI']);
        }
        else
        {
          $langThis['href'] = $rewriteBase . $langThis['code'] . '/' . $cleaned_request_uri;
        }

      }
      else
      {
        $langThis['href'] = $langThis['code'] . '/' . $cleaned_request_uri ;
      }

      $langThis['href'] = str_replace('//', '/', $langThis['href']);
    }

    return $lang_arr;
  }

  /**
   * Writes $text in $file. If $file does not exist, will be created
   * @param string $file path to file
   * @param string|array $text  text to write, can be a string or an array (if type is JSON)
   * @param string $type gz|json|false. If gz the $file will be gzipped, if json $text will be json_encoded
   * @return boolean
   */
  public static function write_in_file ( $file, $text = '', $type = false )
  {
    if (file_exists($file) && !is_writable($file))
    {
      error_log('File ' . $file . ' is not writable');
      return false;
    }

    if ($type === 'json')
    {
      $text = json_encode($text, ((phpversion() >= 5.4) ? JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT : false));
    }

    if ($type === 'gz')
    {
      $file .= '.gz';
      $text = gzencode($text, 9);
    }

    $result = @file_put_contents($file, $text);

    if ($result === false)
    {
      error_log('Can not write in ' . $file);
      return false;
    }
    return true;

  }

  /**
   * Filters multidimensional arrays
   * @param array $arr  multidimensional array to filter
   * @return array
   */
  public static function recursiveFilter($arr)
  {
    foreach ($arr as &$a)
    {
      if (is_array($a))
      {
        $a = self::recursiveFilter($a);
      }
    }
    return array_filter($arr);
  }

  /**
   * Returns current language
   * @return string
   */
  public static function getCurrLang()
  {
    return ($_SESSION['lang'] && $_SESSION['lang'] !== cfg::get('sys_lang') ? $_SESSION['lang'] : '');
  }

  /**
   *
   * @return type
   */
  public static function update_htaccess()
  {
    $file = file_exists('.htaccess-new') ? '.htaccess-new' : '.htaccess';

    $rewriteBase = cfg::get('rewriteBase');

    $otherDirectives = cfg::get('moreHtaccessDirectives');

    if (!$rewriteBase || $rewriteBase === '/')
    {
      $rewriteBase = '/';
    }

    $htaccess_arr = file($file);

    $startDelete = false;

    foreach($htaccess_arr as &$row)
    {

      if (
        cfg::get('enableWWW') == 1 &&
        (
          trim($row) == 'RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]'
          ||
          trim($row) == 'RewriteRule ^(.*)$ http://%1/$1 [R=301,L]'
        )

      ){
        $row = '#' . $row;
      }

      if(
        cfg::get('enableWWW') != 1 &&
        (
          trim($row) == '#RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]'
          ||
          trim($row) == '#RewriteRule ^(.*)$ http://%1/$1 [R=301,L]'
        )
        ){
        $row = str_replace('#', null, $row);
      }

      if (strpos($row, 'RewriteBase') === 0 )
      {
        $row = 'RewriteBase ' . $rewriteBase . "\n";
      }

      if (strpos($row, '# Additional directives') === 0)
      {
        $startDelete = true;
      }

      if ($startDelete)
      {
        $row = '';
      }
    }


    if ($otherDirectives)
    {
      $otherDirectives = "\n\n# Additional directives\n" . $otherDirectives;
    }

    $htaccessText = implode("", $htaccess_arr) . $otherDirectives;

    $htaccessText = str_replace(array(
      "\r\n\r\n\r\n", "\n\n\n"
    ),
      array(
        "\r\n\r\n", "\n\n"
      ), $htaccessText);

    $ret = self::write_in_file($file, $htaccessText);

    if ($file === '.htaccess')
    {
      return $ret;
    }

    if (!$ret)
    {
      return false;
    }

    if(!@copy($file, '.htaccess'))
    {
      error_log('Can not copy file: ' . $file . ' to .htaccess');
      return false;
    }

    if (!@unlink($file))
    {
      error_log('Can not delete file: ' . $file);
      return false;
    }

    return true;
  }

  /**
   * Reads log file with login attempt and checks the timestamp
   * if any record is found for present IP address. Returns true is attempt is valid
   * (no log available, or max time is greater than $time) or false if atempts is not valid
   * @param  string|false  $logfile full path to log file, if false default (admin) log file wil be used
   * @param  integer $time Minimum time in millisecond between two attemps; default 1000 (1sec)
   * @return boolean       true if is a valid attempt, false if attemmpt is not allowed
   */
  public static function checkAttemptTime($logfile = false, $time = 1000)
  {
    if (!$logfile)
    {
      $logfile = MAIN_DIR . 'logs/logAttempts.log';
    }

    $logfile = MAIN_DIR . 'logs/logAttempts.log';

    $ip = $_SERVER['REMOTE_ADDR'];

    $now = microtime(true);

    if (file_exists($logfile))
    {
      $lastAttempt = file($logfile);
      $lastIP = trim(str_replace(array("\n", "\r\n"), null, $lastAttempt[0]));
      $lastTime = floatval(trim(str_replace(array("\n", "\r\n"), null, $lastAttempt[1])));

      if ($lastIP === $id)
      {
        return ($now >= ($lastTime + $time) );
      }
    }
    return utils::write_in_file($logfile, $ip . "\n" . $now);
  }

  /**
   * Send mail, similar to PHP's mail
   * (https://developer.wordpress.org/reference/functions/wp_mail/)
   *
   * @param string|array $to          Array or comma-separated list of email addresses to send message.
   * @param string       $subject     Email subject
   * @param string       $message     Message contents
   * @param string|array $headers     Optional. Additional headers.
   * @param string|array $attachments Optional. Files to attach.
   * @return bool Whether the email contents were sent successfully.
   */
   public static function sendMail( $to, $subject, $message, $headers = '', $attachments = array() )
   {
     if (!is_array($attachments) )
     {
       $attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
     }

     $phpmailer = new PHPMailer( true );

     // Headers
     if ( empty( $headers ) )
     {
       $headers = array();
     }
     else
     {
       if (!is_array($headers) )
       {
         $tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
       }
       else
       {
         $tempheaders = $headers;
       }
       // Reset $headers
       $headers = array();
       $cc = array();
       $bcc = array();

       // If it's actually got contents
       if ( !empty( $tempheaders ) )
       {
         // Iterate through the raw headers
         foreach ( (array) $tempheaders as $header )
         {
           if ( strpos($header, ':') === false )
           {
             if ( false !== stripos( $header, 'boundary=' ) )
             {
               $parts = preg_split('/boundary=/i', trim( $header ) );
               $boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
             }
             continue;
           }
           // Explode them out
           list( $name, $content ) = explode( ':', trim( $header ), 2 );

           // Cleanup crew
           $name = trim($name);
           $content = trim($content);

           switch ( strtolower( $name ) )
           {
             // Mainly for legacy -- process a From: header if it's there
             case 'from':
              $bracket_pos = strpos( $content, '<' );
              if ( $bracket_pos !== false )
              {
               // Text before the bracketed email is the "From" name.
               if ( $bracket_pos > 0 )
               {
                 $from_name = substr( $content, 0, $bracket_pos - 1 );
                 $from_name = str_replace( '"', '', $from_name );
                 $from_name = trim( $from_name );
               }

               $from_email = substr( $content, $bracket_pos + 1 );
               $from_email = str_replace( '>', '', $from_email );
               $from_email = trim( $from_email );
               // Avoid setting an empty $from_email.
              }
              elseif ( '' !== trim( $content ) )
              {
                $from_email = trim( $content );
              }
             break;
             case 'content-type':
               if ( strpos( $content, ';' ) !== false )
               {
                 list( $type, $charset_content ) = explode( ';', $content );
                 $content_type = trim( $type );
                 if ( false !== stripos( $charset_content, 'charset=' ) )
                 {
                   $charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
                 }
                 elseif ( false !== stripos( $charset_content, 'boundary=' ) )
                 {
                   $boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
                   $charset = '';
                 }

                 // Avoid setting an empty $content_type.
               }
               elseif ( '' !== trim( $content ) )
               {
                 $content_type = trim( $content );
               }
             break;
             case 'cc':
              $cc = array_merge( (array) $cc, explode( ',', $content ) );
             break;
             case 'bcc':
              $bcc = array_merge( (array) $bcc, explode( ',', $content ) );
             break;
             default:
              // Add it to our grand headers array
              $headers[trim( $name )] = trim( $content );
             break;
           }
         }
       }
     }


     // Empty out the values that may be set
     $phpmailer->ClearAllRecipients();
     $phpmailer->ClearAttachments();
     $phpmailer->ClearCustomHeaders();
     $phpmailer->ClearReplyTos();

     // From email and name
     // If we don't have a name from the input headers
     if ( !isset( $from_name ) )
     $from_name = 'BraDyCMS';

     /* If we don't have an email from the input headers default to bradycms@$sitename
     * Some hosts will block outgoing mail from this address if it doesn't exist but
     * there's no easy alternative. Defaulting to admin_email might appear to be another
     * option but some hosts may refuse to relay mail from an unknown domain. See
     * https://core.trac.wordpress.org/ticket/5007.
     */

     if ( !isset( $from_email ) )
     {
       // Get the site domain and get rid of www.
       $sitename = strtolower( $_SERVER['SERVER_NAME'] );
       if ( substr( $sitename, 0, 4 ) == 'www.' )
       {
         $sitename = substr( $sitename, 4 );
       }
       $from_email = 'bradycms@' . $sitename;
     }

     $phpmailer->From = $from_email;

     $phpmailer->FromName = $from_name;

     // Set destination addresses
     if ( !is_array( $to ) )
     {
       $to = explode( ',', $to );
     }

     foreach ( (array) $to as $recipient )
     {
       try {
         // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
         $recipient_name = '';
         if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) )
         {
           if ( count( $matches ) == 3 )
           {
             $recipient_name = $matches[1];
             $recipient = $matches[2];
           }
         }
         $phpmailer->AddAddress( $recipient, $recipient_name);
       }
       catch ( phpmailerException $e )
       {
         continue;
       }
     }

     // Set mail's subject and body
     $phpmailer->Subject = $subject;
     $phpmailer->Body    = $message;

     // Add any CC and BCC recipients
     if ( !empty( $cc ) )
     {
       foreach ( (array) $cc as $recipient )
       {
         try
         {
           // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
           $recipient_name = '';
           if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) )
           {
             if ( count( $matches ) == 3 )
             {
               $recipient_name = $matches[1];
               $recipient = $matches[2];
             }
           }
           $phpmailer->AddCc( $recipient, $recipient_name );
         }
         catch ( phpmailerException $e )
         {
           continue;
         }
       }
     }

     if ( !empty( $bcc ) )
     {
       foreach ( (array) $bcc as $recipient)
       {
         try
         {
           // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
           $recipient_name = '';
           if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) )
           {
             if ( count( $matches ) == 3 )
             {
               $recipient_name = $matches[1];
               $recipient = $matches[2];
             }
           }
           $phpmailer->AddBcc( $recipient, $recipient_name );
         }
         catch ( phpmailerException $e )
         {
           continue;
         }
       }
     }

     // Set to use PHP's mail()
     $phpmailer->IsMail();

     // Set Content-Type and charset
     // If we don't have a content-type from the input headers
     if ( !isset( $content_type ) )
     {
       $content_type = 'text/plain';
     }

     $phpmailer->ContentType = $content_type;

     // Set whether it's plaintext, depending on $content_type
     if ( 'text/html' == $content_type )
     {
       $phpmailer->IsHTML( true );
     }

     // If we don't have a charset from the input headers
     if ( isset( $charset ) )
     {
       $phpmailer->CharSet = $charset;
     }

     // Set custom headers
     if ( !empty( $headers ) )
     {
       foreach ( (array) $headers as $name => $content )
       {
         $phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
       }

       if ( false !== stripos( $content_type, 'multipart' ) && ! empty($boundary) )
       {
         $phpmailer->AddCustomHeader( sprintf( "Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary ) );
       }
     }

     if ( !empty( $attachments ) )
     {
       foreach ( $attachments as $attachment )
       {
         try
         {
           $phpmailer->AddAttachment($attachment);
         }
         catch ( phpmailerException $e )
         {
           continue;
         }
       }
     }

     // Send!
     try
     {
       return $phpmailer->Send();
     }
     catch ( phpmailerException $e )
     {
       error_log(
        'Error sending email' .
        " to: " . implode(', ', (array)$to) .
        "\n from email: " . $from_email .
        ($from_name ? "\n from name: " . $from_name : '') .
        "\n subject: " . implode(', ', (array)$subject) .
        "\n message: " . implode(', ', (array)$message) .
        (!empty($header) ? "\n  headers: " . implode(', ', (array)$headers) . "\n\t"  : '') .
        (!empty($attachments) ? "\n  attachments:" . implode(', ', (array)$attachments) : '')
        );
      return false;
     }
   }



}
