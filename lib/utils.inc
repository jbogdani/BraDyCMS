<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Dec 1, 2012
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
   * @param boolean $removeSpaces
   */
	public static function upload($upload_dir, $removeSpaces = false)
	{
		$allowedExtensions = array();
		
		$sizeLimit = 8 * 1024 * 1024;
		
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		
		$result = $uploader->handleUpload($upload_dir . '/', false, $removeSpaces);
		
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
	public static function write_in_file ( $file, $text = false, $type = false )
	{
		if ( $type != 'gz' )
		{
			$f = @fopen($file, 'w');
				
			if (!$f) return false;
				
			if ($text)
			{
				if ( !@fwrite ( $f, ($type == 'json'  ?  json_encode($text, ((phpversion() >= 5.4) ? JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT : false)) : $text ) ) ) return false;
			}
				
			@fclose($f);
				
			return true;
		}
		else
		{
			$file .= '.gz';
				
			$gzh = @gzopen($file,'w9');
				
			if ( !$gzh ) return false;
	
			if ( !@gzwrite($gzh, $text) ) return false;
				
			@gzclose($gzh);
				
			return true;
		}
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
	
	
	
}
