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
						
					//$file_obj[$file]['src'] = './img/folder.png';
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
		
		$this->render('media', 'list', array(
				'error_create' => $error_create,
				'path_arr'=> $path_arr,
				'path' => $path,
				'rel_path' => $rel_path,
				'files' => $file_obj,
				'tr' => new tr()
		));
	}
	
	public function edit()
	{
		$file = IMG_DIR . implode('/', $this->get['param']);
		
		$this->render('media', 'edit_form', array(
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
		try
		{
			$ok = imgMng::process_convert($ofile, $nfile, $type, $details);
		
			$out['status'] = 'success';
			$out['text'] = tr::get($ok);
		}
		catch (Exception $e)
		{
			$out['status'] = 'error';
			$out['text'] = tr::get($e->getMessage());
		}
		
		echo json_encode($out);
	}
	
}