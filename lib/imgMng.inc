<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2013
 * @license			All rights reserved
 * @since			May 16, 2013
 */
 

class imgMng
{
	public static function convert($ofile, $nfile = false)
	{
		return self::process_convert($ofile, $nfile, 'convert');
	}
	
	public static function crop($ofile, $nfile = false, $details)
	{
		return self::process_convert($ofile, $nfile, 'crop', $details);
	}
	
	public static function resize($ofile, $nfile = false, $details)
	{
		return self::process_convert($ofile, $nfile, 'resize', $details);
	}
	
	/**
	 * 
	 * @param string $ofile
	 * @param string $nfile
	 * @param string $details	thumb dimensions: eg. 200x200
	 * @return true;
	 */
	public static function thumb($ofile, $nfile = false, $details)
	{
		return self::process_convert($ofile, $nfile, 'thumb', $details);
	}
	
	public static function process_convert($ofile, $nfile = false, $type = false, $details = false)
	{
		$type = $type ? $type : 'convert';
	
		if (!$nfile)
		{
			$nfile = TMP_DIR . uniqid('file');
			$overwriteOriginal = true;
		}
	
		$exec_path = cfg::get('paths');
			
		switch($type)
		{
			case 'convert':
				$convert = $exec_path['convert'] . ' ' . $ofile . ' ' . $nfile;
				$ok = 'ok_converting_file';
				$error = 'error_converting_file';
				break;
					
			case 'crop':
				$convert = $exec_path['convert'] . " -crop '" . $details . "' " . ' ' . $ofile . ' ' . $nfile;
				$ok = 'ok_cropping_file';
				$error = 'error_cropping_file';
				break;
	
			case 'resize':
				$convert = $exec_path['convert'] . " -resize '" . $details . "'  " . $ofile . ' ' . $nfile;
				$ok = 'ok_resizing_file';
				$error = 'error_resizing_file';
				break;
	
			case 'thumb':
				$convert = $exec_path['convert'] . ' -resize ' . $details . '^ ' . $ofile . ' -background none -gravity center -extent ' . $details . ' ' . $nfile;
				$ok = 'thumbnail_created';
				$error = 'thumbnail_not_created';
				break;
					
			default:
				return;
				break;
		}
	
		@exec($convert, $a, $b);
	
		if (!file_exists($nfile))
		{
			throw new Exception(tr::get($error));
		}
			
		if ($overwriteOriginal)
		{
			@rename($nfile, $ofile);
		}
	
		return $ok;
	
	}
}






