<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 9, 2012
 */
 
class media_ctrl
{
	public static function all($path = false)
	{
		$upload_dir = './sites/default/images/';
		
		if ($path)
		{
			$upload_dir .= str_replace('-', '/', $path);
			$path_arr = explode('-', $path);
			array_unshift($path_arr, '.');
		}
		else
		{
			$path_arr = array('.');
		}
		
		if (!is_dir($upload_dir))
		{
			$tmp_str = preg_replace('/\/$/', null, $upload_dir);
			
			if ( !mkdir($upload_dir, 0777, 1) )
			{
				$error_create = tr::sget('create_dir_error', $upload_dir);
				$upload_dir = './sites/default/images';
			}
		}
		
		if (preg_match('/sites\/default\/images\/\.\./', $upload_dir) )
		{
			$upload_dir = './sites/default/images/';
		}
		
		$files = utils::dirContent($upload_dir);
		
		
		if (is_array($files))
		{
			sort($files);
				
			$file_obj = array();
				
			foreach ($files as $file)
			{
				$file_obj[$file]['href'] = $path ? $path . '-' . $file : $file;
				$file_obj[$file]['name'] = $file;
			
				if ( is_file($upload_dir . '/'. $file ) )
				{
					$file_obj[$file]['type'] = 'file';
			
					$ftype = utils::checkMimeExt($upload_dir . '/' . $file);
						
					if ($ftype[0] == 'image')
					{
						$file_obj[$file]['src'] = $upload_dir . '/' . $file;
					}
					else
					{
						$file_obj[$file]['src'] = './img/ftype_icons/' . $ftype[1];
					}
				}
				else
				{
					$file_obj[$file]['type'] = 'folder';
						
					$file_obj[$file]['src'] = './img/folder.png';
				}
			}
		}
		
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'media/tmpl'), unserialize(CACHE));
		echo $twig->render('list.html', array(
				'error_create' => $error_create,
				'path_arr'=> $path_arr,
				'path' => $path,
				'upload_dir' => $upload_dir,
				'files' => $file_obj,
				'uniqid' => uniqid(),
				'tr' => new tr()
		));
	}
	
	public static function rename($dir, $ofile, $nfile)
	{
		$dir .= '/';
		
		try
		{
			if (!file_exists($dir . $ofile))
			{
				throw new Exception(tr::sget('original_file_not_found', $dir . $ofile));
			}
			
			if (file_exists($dir . $nfile))
			{
				throw new Exception(tr::sget('file_exists', $dir . $nfile));
			}
			
			@rename($dir . $ofile, $dir . $nfile);
			
			if (!file_exists($dir . $nfile))
			{
				throw new Exception(tr::sget('moving_file_error', $dir . $nfile));
			}
			
			$out['status'] = 'success';
			$out['text'] = tr::get('moving_file_ok');
		}
		catch (Exception $e)
		{
			$out['status'] = 'error';
			$out['text'] = $e->getMessage();
		}
		
		echo json_encode($out);
	}
	
	public static function delete($ofile)
	{
		if (!preg_match('/sites\/default\/images/', $ofile))
		{
			$file = './sites/default/images/' . str_replace('-', '/', $ofile);
		}
		else
		{
			$file = $ofile;
		}
		
		try
		{
			if ( is_dir ( $file ) )
			{
				if ( !@rmdir($file) )
				{
					throw new Exception(tr::get('delete_dir_error'));
				}
				
				if(preg_match('/-/', $ofile))
				{
					$path_arr = explode('-', $ofile);
				
					if (is_array($path_arr))
					{
						array_pop($path_arr);
							
						$out['new_path'] = implode('-', $path_arr);
					}
				}
			}
			else if ( is_file ( $file ) )
			{
				if ( !@unlink($file) )
				{
					throw new Exception(tr::get('delete_file_error'));
				}
			}
			
			$out['status'] = 'success';
			$out['text'] = tr::get('deletion_ok');
			
			$out['file'] = $file;
				
		}
		catch(Exception $e)
		{
			$out['status'] = 'error';
			$out['text'] = $e->getMessage();
		}
		echo json_encode($out);
	}
}