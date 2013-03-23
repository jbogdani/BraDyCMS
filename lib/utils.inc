<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 1, 2012
 */
 
class utils
{
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
	
	public static function dirContent($dir)
	{
		$handle = @opendir($dir);
	
		$dont_consider_array = array('.', '..', '.DS_Store', 'thumbs.db', '.svn');
	
		if($handle)
		{
			while (false !== ($file = readdir($handle)))
			{
				if (!in_array($file, $dont_consider_array))
				{
					$dir_content[]=$file;
				}
			}
			closedir($handle);
			if (count($dir_content)> 0)
			{
				return $dir_content;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
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
		);
		
		$trova_punto = explode(".", $file);
		$ext = $trova_punto[count($trova_punto) - 1];
		
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
	
	
	public static function upload($upload_dir)
	{
		
		$allowedExtensions = array();
		
		$sizeLimit = 8 * 1024 * 1024;
		
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload($upload_dir . '/');
		
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}
	
	public static function csv_explode($string, $delimiter = ',') {
	
		$string = str_replace($delimiter .' ', $delimiter, $string);
	
		if ( preg_match('/' . $delimiter . '/i', $string) )
		{
			return array_filter(explode($delimiter, $string));
				
		}
		else
		{
			return array($string);
		}
	}
	
	public static function format_link($href, $lang = false)
	{
		$base = '.';
		
		// absolute url is not processed
		if (preg_match('/^http:\/\/(.+)/', $href))
		{
			return $href;
		}
		
		// incomplete absolute url is completed with http:// prefix and not processed further
		if (preg_match('/^www\.(.+)/', $href))
		{
			return 'http://' . $href;
		}
		
		// links to home page are not processed
		if ($href == '#' || $href == './')
		{
			return $base . $href;
		}
		
		// if language variable is not present in href string, it will be added!
		if (!preg_match('/lang=/', $href) AND $lang)
		{
			// question mark is added to make href a wel formatted query string
			if (!preg_match('/\?/', $href))
			{
				$href = './?' . $href;
			}
			if ($href != '#')
			{
				$href .= '&lang=' . $lang;
			}
		}
		
		// if friendly url is turned off, the formatted string is returned
		if ( !cfg::get('friendly_url'))
		{
			return $base . $href;
		}
		
		$href = str_replace(array('index.php?', './?'), null, $href);
		
		$word_patt = '[a-zA-Z0-9_-]+';
		
		$path = $_REQUEST['lang'] ? '..' : '.';
		
		$pattern = array (
					
				//section + lang
				'/^section=(' . $word_patt . ')&lang=([a-z]{2})$/',

				//art (id) + lang
				'/^art=([0-9]+)&lang=([a-z]{2})$/',

				//art_title + lang
				'/^art_title=(' . $word_patt . ')&lang=([a-z]{2})$/',

				// secion, no lang
				'/^section=(' . $word_patt . ')$/',

				// art (id), no lang
				'/^art=([0-9]+)$/',

				// art_title, no lang
				'/^art_title=(' . $word_patt . ')$/',

				// homepage
				'/^lang=([a-z]{2})$/',
		);

		$replacement = array (

				// lang support
				$path . '/$2/$1.all', // ex.: ./en/news.all
					
				$path . '/$2/id-$1',	// ex.: ../en/id-5

				$path . '/$2/$1', // ex: ../en/about

				// no lang
				'$1.all', // ex.: ./news.all
					
				'id-$1', // ex.: ./id-5

				'/$1', //ex: /about
					
				$path . '/$1/' // ex.: ../en
		);

		$href = preg_replace($pattern, $replacement, $href);
		return $href;
		
	}
	
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
		
		foreach($lang_arr as &$langThis)
		{
			if($lang)
			{
				if (preg_match('/\/' . $lang . '/', $_SERVER['REQUEST_URI']) || preg_match('/lang=' . $lang, $_SERVER['REQUEST_URI'] . '/'))
				{
					$langThis['href'] = str_replace(
							array('/' . $lang, 'lang=' . $lang),
							array('/' . $langThis['code'], 'lang=' . $langThis['code']),
							$_SERVER['REQUEST_URI']);
				}
				else
				{
					$langThis['href'] = '/' . $langThis['code'] . $_SERVER['REQUEST_URI'];
				}
				
			}
			else
			{
				if (cfg::get('friendly_url'))
				{
					$langThis['href'] = $langThis['code'] . $_SERVER['REQUEST_URI'];
				}
				else
				{
					$langThis['href'] = $_SERVER['REQUEST_URI'] . '?lang=' . $langThis['code'];
				}
			}
		}
				
		return $lang_arr;
	}
	
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