<?php
/**
 * 
 * Removes all items in tmp dir older more then 3 days from now! 
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 27, 2011
 */

class utils
{
	/**
	 * 
	 * checks file creation time in tmp dir and if file is older than 3 days it's removed
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
						error_log('Impossibile cancellare ' . $f . "\n", 3, ERR_LOG);
					}
				}
			}
		}
	}
	
	/**
	 * 
	 * Returns array of files in folder or false if empty (or an error occours)
	 * @param string $dir	directory to parse
	 */
	
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
	
	
	/**
	 * 
	 * Takes mime or extension and return array with first element the file type (verbose) and second element the image icon
	 * @param string $file	full path to file
	 * @param strinf $ext	file extension
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
				"ext"=>array("png", "jpeg", "jpg", "bmp", "ico", "tif", "tiff"), 
				"icon"=>"image-x-generic.png"),
		);
		
		$trova_punto = explode(".", $file);
		$ext = $trova_punto[count($trova_punto) - 1];
		
		/*
		if (!file_exists($file))
		{
			$mime = false;
		}
		else
		{
			if (function_exists('finfo_open'))
			{
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime = finfo_file($finfo, $file);
				finfo_close($finfo);
			}
		}
		*/
		
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
	 * 
	 * Writes text in file. If file does not exit, it will be created
	 * @param string $file	complete path to file
	 * @param string $text	text to write in file
	 */
	public static function write_in_file ( $file, $text, $gz = false )
	{
		if ( !$gz )
		{
			$f = fopen($file, 'w');
			
			if ( fwrite ( $f, $text ) )
			{
				fclose($f);
				return true;
			}
			else
			{
				fclose($f);
				return false;
			}
		}
		else
		{
			$file .= '.gz';
			$gzh = @gzopen($file,'w9');
			if ( !gzh ) return false; 
			if ( !gzwrite($gzh, $text) ) return false;
			gzclose($gzh);
			return true;
		}
	}
	
	/**
	 * 
	 * Echoes reference to js files .
	 * If type is false the precompiled compact archive (1 minified file) will be requested
	 * If type['mini'] the compact archive (1 minified file) will be created
	 * If type['debug'] the single files will be requested
	 *  
	 * @param array $files	array of js files (with .js extension) to be requested 
	 * @param string type 	defines the way js will be requested. Can be: mini | debug | false
	 */
	public static function js( $files, $type = false )
	{
		if ( $type['mini'] )
		{
			$str = "/*\n * BDUS.CMS javascripts minified archive includes different sources and licenses\n */";
	
			foreach ($files as $file) {
				
				$file = ltrim($file);
				
				if ( file_exists( './js/' . $file ) )
				{
					$str .= "\n\n". JSMin::minify ( file_get_contents ( './js/' . $file ) );
				}
				else if ( file_exists( './' . $file ) )
				{
					$str .= "\n\n". JSMin::minify ( file_get_contents ( './' . $file ) );
				}
				
			}
			self::write_in_file ( './js/_mini.js', $str);
	
			echo "\n\t".'<script language="JavaScript" type="text/JavaScript" src="./js/_mini.js"></script>';
	
		}
		else if ( $type['debug'] )
		{
			foreach ( $files as $file )
			{
				$file = ltrim($file);
				
				if ( file_exists( './js/' . $file ) )
				{
					echo "\n\t".'<script language="JavaScript" type="text/JavaScript" src="./js/' . $file .'"></script>';
				}
				else if ( file_exists( './' . $file ) )
				{
					echo "\n\t".'<script language="JavaScript" type="text/JavaScript" src="./' . $file .'"></script>';
				}
			}
		}
		else
		{
			echo "\n\t".'<script language="JavaScript" type="text/JavaScript" src="./js/_mini.js"></script>';
		}
	}
	
	/**
	 * 
	 * Echoes reference to css files and favicon in page.
	 * If type is false the precompiled compact archive (1 minified file) will be requested
	 * If type['mini'] the compact archive (1 minified file) will be created
	 * If type['debug'] the single files will be requested
	 *  
	 * @param array $files	array of css files (with .css extension) to be requested 
	 * @param string type 	defines the way js will be requested. Can be: mini | debug | false
	 */
	
	public static function css ( $files, $type )
	{
		if ( $type['mini'] )
		{
			$str = "/*\n * BDUS.CMS css minified archive includes different sources and licenses\n */";
	
			foreach ( $files as $file )
			{
				$file = ltrim($file);
				
				if ( file_exists('./css/' . $file ) )
				{
					$str .= "\n\n". Minify_CSS_Compressor::process(file_get_contents ( './css/' . $file ) );
				}
			}
			
			self::write_in_file('./css/_mini.css', $str);
	
			echo "\n\t" . '<link type="text/css" media="screen" rel="stylesheet" href="./css/_mini.css" />';
	
		}
		else if ( $type['debug'] )
		{
			foreach($files as $file)
			{
				$file = ltrim($file);
				
				if ( file_exists('./css/' . $file) )
				{
					echo "\n\t".'<link type="text/css" media="screen" rel="stylesheet" href="./css/' .  $file .'" />';
				}
			}
		}
		else
		{
			echo "\n\t".'<link type="text/css" media="screen" rel="stylesheet" href="./css/_mini.css" />';
		}
		
		echo "\n\t".'<link rel="shortcut icon" href="./css/favicon.ico">';
	}
	
	
	/**
	 * 
	 * Translation function
	 * @param string $string strint to translate
	 * @param boolean $esc	escape string or not?
	 */
	public static function tr( $string, $esc = false )
	{
		global $_lang;
		$ret = $_lang[$string] ? $_lang[$string] : $string;
		return $esc ? str_replace("'", "\'", $ret) : $ret;
	}
	
	public static function getLang()
	{
		global $cfg;
		
		if ($_GET['lang'] AND ( (array_key_exists($_GET['lang'], $cfg['languages'])) OR $_GET['lang'] == $cfg['sys_lang']) )
		{
			$_SESSION['lang'] = $_GET['lang'];
		}
		else if ($_SESSION['lang'])
		{
			$_SESSION['lang'];
		}	
		else
		{
			$_SESSION['lang'] = $cfg['sys_lang'];
		}
		
		return $_SESSION['lang'];
	}
	
}