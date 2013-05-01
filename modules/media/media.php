<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 9, 2012
 */
 
class media_ctrl extends Controller
{
	public function all()
	{
		$rel_path = implode('/', $this->get['param']);
		$path = IMG_DIR . $rel_path;
		
		if (!is_dir($path))
		{
			if ( !mkdir($path, 0777, 1) )
			{
				$error_create = tr::sget('create_dir_error', $path);
				$path = IMG_DIR;
			}
		}
		
		
		$files = utils::dirContent($path);
		
		
		if (is_array($files))
		{
			sort($files);
				
			$file_obj = array();
				
			foreach ($files as $file)
			{
				$file_obj[$file]['href'] = ($rel_path ? $rel_path . '/' : '') . $file;
				$file_obj[$file]['name'] = $file;
			
				if ( is_file($path . '/'. $file ) )
				{
					$file_obj[$file]['type'] = 'file';
			
					$ftype = utils::checkMimeExt($path . '' . $file);
						
					if ($ftype[0] == 'image')
					{
						$file_obj[$file]['src'] = $path . '/' . $file;
						$file_obj[$file]['image'] = true;
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
		
		if(!empty($this->request['param'][0]))
		{
			$path_arr = $this->request['param'];
			array_unshift($path_arr, '.');
		}
		else
		{
			$path_arr = array('.');
		}
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'media/tmpl'), unserialize(CACHE));
		echo $twig->render('list.html', array(
				'error_create' => $error_create,
				'path_arr'=> $path_arr,
				'path' => $path,
				'rel_path' => $rel_path,
				'files' => $file_obj,
				'uniqid' => uniqid(),
				'tr' => new tr()
		));
	}
	
	public function edit()
	{
		$file = IMG_DIR . implode('/', $this->get['param']);
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'media/tmpl'), unserialize(CACHE));
		echo $twig->render('edit_form.html', array(
				'file' => $file,
				'uid' => uniqid(),
				'finfo' => getimagesize($file),
				'pathinfo' =>pathinfo($file),
				'tr' => new tr()
		));
	}
	
	public function copy()
	{
		$this->request['param'][3] = true;
		self::rename();
		
	}
	
	public function rename()
	{
		$dir = $this->request['param'][0];
		$ofile = $this->request['param'][1];
		$nfile = $this->request['param'][2];
		$copy = $this->request['param'][3];
		
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
			
			$copy ? @copy($dir . $ofile, $dir . $nfile) : @rename($dir . $ofile, $dir . $nfile);
			
			if (!file_exists($dir . $nfile))
			{
				throw new Exception(tr::sget($copy ? 'copying_file_error' : 'moving_file_error', $dir . $nfile));
			}
			
			$out['status'] = 'success';
			$out['text'] = tr::get($copy ? 'copying_file_ok' : 'moving_file_ok');
		}
		catch (Exception $e)
		{
			$out['status'] = 'error';
			$out['text'] = $e->getMessage();
		}
		
		echo json_encode($out);
	}
	
	public function delete()
	{
		$ofile = IMG_DIR . $this->request['param'][0];
		
		if(preg_match('/\//', $this->request['param'][0]))
		{
			$path_arr = explode('/', $this->request['param'][0]);
		
			array_pop($path_arr);
					
			$out['new_path'] = implode('/', $path_arr);
		}
		
		if (!$out['new_path'])
		{
			$out['new_path'] = '';
		}
		
		try
		{
			if ( is_dir ( $ofile ) )
			{
				@unlink($ofile . '/.DS_Store');
				@unlink($ofile . '/.Thumb.db');
				if ( !@rmdir($ofile) )
				{
					throw new Exception(tr::get('delete_dir_error'));
				}
				
				if(preg_match('/\//', $ofile))
				{
					$path_arr = explode('/', $ofile);
				
					if (is_array($path_arr))
					{
						array_pop($path_arr);
							
						$out['new_path'] = implode('/', $path_arr);
					}
				}
			}
			else if ( is_file ( $ofile ) )
			{
				if ( !@unlink($ofile) )
				{
					throw new Exception(tr::get('delete_file_error'));
				}
			}
			
			$out['status'] = 'success';
			$out['text'] = tr::get('deletion_ok');
			
			$out['file'] = $ofile;
				
		}
		catch(Exception $e)
		{
			$out['status'] = 'error';
			$out['text'] = $e->getMessage();
		}
		echo json_encode($out);
	}
	
	public function crop()
	{
		$ofile = $this->request['param'][0];
		$crop = $this->request['param'][1];
		
		$this->process_convert($ofile, false, 'crop', $crop);
	}
	
	
	public function resize()
	{
		$ofile = $this->request['param'][0];
		$size = $this->request['param'][1];
		
		$this->process_convert($ofile, false, 'resize', $size);
	}
	
	
	public function convert()
	{
		$ofile = $this->request['param'][0];
		$nfile = $this->request['param'][1];
		
		$this->process_convert($ofile, $nfile);
	}
	
	private function process_convert($ofile, $nfile, $type = false, $details = false)
	{
		$type = $type ? $type : 'convert';
		if (!$nfile)
		{
			$nfile = TMP_DIR . uniqid('file');
			$overwriteOriginal = true;
		}
		
		
		if (!$nfile)
		{
			$nfile = TMP_DIR . uniqid('file');
			$overwriteOriginal = true;
		}
		
		try
		{
			if (file_exists($nfile))
			{
				throw new Exception(tr::sget('file_exists', $nfile));
			}
			
			$exec_path = cfg::get('paths');
			
			switch($type)
			{
				case 'convert':
					$convert = $exec_path['convert'] . ' ' . $_SERVER['DOCUMENT_ROOT'] . '/' . $ofile . ' ' .$_SERVER['DOCUMENT_ROOT'] . '/' . $nfile;
					$ok = 'ok_converting_file';
					$error = 'error_converting_file';
					break;
					
				case 'crop':
					$convert = $exec_path['convert'] . " -crop '" . $details . "' " . ' ' . $_SERVER['DOCUMENT_ROOT'] . '/' . $ofile . ' ' .$_SERVER['DOCUMENT_ROOT'] . '/' . $nfile;
					$ok = 'ok_cropping_file';
					$error = 'error_cropping_file';
					break;
						
				case 'resize':
					$convert = $exec_path['convert'] . " -resize '" . $details . "'  " . $_SERVER['DOCUMENT_ROOT'] . '/' . $ofile . ' ' .$_SERVER['DOCUMENT_ROOT'] . '/' . $nfile;
					$ok = 'ok_resizing_file';
					$error = 'error_resizing_file';
					break;
					
				default:
					return;
					break;
			}
			
			@exec($convert, $a, $b);
			
			if (file_exists(!$nfile))
			{
				error_log($convert . ': ' . var_export($a, 1));
				throw new Exception(tr::get($error));
			}
			
			if ($overwriteOriginal)
			{
				@rename($nfile, $ofile);
			}
			
			$out['status'] = 'success';
			$out['text'] = tr::get($ok);
		}
		catch (Exception $e)
		{
			$out['status'] = 'error';
			$out['text'] = $e->getMessage();
		}
		
		echo json_encode($out);
	}
	
}