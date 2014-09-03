<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Mar 25, 2013
 */
 
class galleries_ctrl extends Controller
{
	private $path = GALLERY_DIR;
	 
	public function all()
	{
		$all_galls = utils::dirContent($this->path);
		
		asort($all_galls);
		
		$this->render('galleries', 'listAllGalleries', array(
      'galleries' => $all_galls
      ));
	} 
	
	/**
	 * Displays GUI for gallery editing
	 * available parameters:
	 * 	gallery_name (string)
	 * 	lang (string|false)
	 */
	public function edit()
	{
		$gal_content = utils::dirContent($this->path . $this->get['param'][0]);
		
		$lang = $this->get['param'][1];
		
		arsort($gal_content);
		
		if (is_dir($this->path . $this->get['param'][0] . '/thumbs'))
		{
			$thumbs = utils::dirContent($this->path . $this->get['param'][0] . '/thumbs');
		}
		
		if (file_exists($this->path . $this->get['param'][0] . '/data.json'))
		{
			$data = json_decode(file_get_contents($this->path . $this->get['param'][0] . '/data.json'), true);
		}
		
		if ($lang)
		{
			$orig = $data;
			
			unset($data);
			
			if (file_exists($this->path . $this->get['param'][0] . '/data_' . $lang . '.json'))
			{
				$data = json_decode(file_get_contents($this->path . $this->get['param'][0] . '/data_' . $lang . '.json'), true);
			}
		}
		
		foreach ($gal_content as $file)
		{
			if ($file != 'thumbs' && $file != 'data.json' && !preg_match('/\.json/', $file))
			{
				$files[] = array(
						'name' => $file,
						'formattedName' => str_replace('.', '__x__', $file),
						'fullpath' => $this->path . $this->get['param'][0] . '/' . $file,
						'thumb' => (file_exists($this->path . $this->get['param'][0] . '/thumbs/' . $file) ? $this->path . $this->get['param'][0] . '/thumbs/' . $file : ''),
						'description' => $data[str_replace('.', '__x__', $file)],
						'finfo' => getimagesize($this->path . $this->get['param'][0] . '/' . $file),
						'orig_descr' => ($orig ? $orig[str_replace('.', '__x__', $file)] : false)
						); 
			}
		}
		
		$this->render('galleries', 'editGal', array(
				'gallery'=> $this->get['param'][0],
				'files'	=> $files,
				'thumbs'=> $thumbs,
				'upload_dir'=> $this->path . $this->get['param'][0],
				'langs' => cfg::get('languages'),
				'translation' => $lang,
        'thumb_dimensions' => cfg::get('thumbs_dimensions') ? cfg::get('thumbs_dimensions') : '200x200'
				));
		
	}
	
	/**
	 * Writes $post data in data.json file in $gallery folder
	 * available parameters:
	 * 	gallery_name (string)
	 * 	lang (string|false)
	 */
	public function saveData()
	{
		$json_file = $this->get['param'][0] . '/data' . ($this->get['param'][1] ? '_' . $this->get['param'][1] : '') . '.json';
		
		if (utils::write_in_file($json_file, $this->post, 'json'))
		{
			$ret = array('status' => 'success', 'text' => tr::get('gallery_updated')); 
		}
		else
		{
			$ret = array('status' => 'error', 'text' => tr::get('gallery_not_updated'));
		}
		
		echo json_encode($ret);
	}
	
	/**
	 * Creates thumbnail image for $image in $gallery
	 */
	public function makeThumbs()
	{
		$path = $this->get['param'][0];
		$file = $this->get['param'][1];
		$thumbs_dimensions = $this->get['param'][2];
    
    if (!$thumbs_dimensions || !preg_match('/^([0-9]{1,4})x([0-9]{1,4})$/', $thumbs_dimensions))
    {
      $thumbs_dimensions = cfg::get('thumbs_dimensions') ? cfg::get('thumbs_dimensions') : '200x200';
    }
    
    error_log($thumbs_dimensions);
    
		if (!is_dir($path . '/thumbs'))
		{
			mkdir($path . '/thumbs', 0777, true);
		}
		
		$thumb_path = $path . '/thumbs/' . $file;
		
		try{
			imgMng::thumb($path . '/' . $file, $thumb_path, $thumbs_dimensions);
			
			$msg['text'] = tr::get('thumbnail_created');
			$msg['status'] = 'success';
			$msg['thumb'] = $thumb_path;
		}
		catch (Exception $e)
		{
			$msg['text'] = tr::get('thumbnail_not_created');
			$msg['status'] = 'error';
		}
		
		echo json_encode($msg);
	}
	
	/**
	 * Creates $gallery folder
	 * Creates thumbs folder in $gallery folder
	 */
	public function addGallery()
	{
		try{
			
			$gal_name = $this->path . strtolower(str_replace(array(' ', "'", '"'), '_', $this->get['param'][0]));
			
			
			if (is_dir($gal_name))
			{
				throw new Exception(tr::get('gallery_exists'));
			}
			@mkdir($gal_name, 0777, true);
			@mkdir($gal_name . '/thumbs', 0777, true);
			
			if (!is_dir($gal_name))
			{
				throw new Exception(tr::get('gallery_not_created'));
			}
			
			if (!is_dir($gal_name . '/thumbs'))
			{
				throw new Exception(tr::get('gallery_partially_created'));
			}
			
			$msg['text'] = tr::get('gallery_created');
			$msg['status'] = 'success';
		}
		catch (Exception $e)
		{
			$msg['text'] = $e->getMessage();
			$msg['status'] = 'error';
		}
		echo json_encode($msg);
	}
	
	/**
	 * Deletes $image in $gallery folder
	 * Removes data from data.json file
	 */
	public function deleteImg()
	{
		try
		{
			$file = $this->get['param'][0] . '/' . $this->get['param'][1];
			
			if(file_exists($file))
			{
				@unlink($file);
				
				if (file_exists($file))
				{
					throw new Exception(tr::get('img_not_deleted'));
				}
			}
			
			if (file_exists($this->get['param'][0] . '/thumbs/' . $this->get['param'][1]))
			{
				@unlink($this->get['param'][0] . '/thumbs/' . $this->get['param'][1]);
				
				if (file_exists($this->get['param'][0] . '/thumbs/' . $this->get['param'][1]))
				{
					$warning_thumb = true;
				}
			}
			
			$data_file[] = $this->get['param'][0] . '/data.json';
			
			foreach (cfg::get('languages') as $lng)
			{
				$data_file[] = $this->get['param'][0] . '/data_' . $lng['id']. '.json';
			}
			
			
			foreach ($data_file as $d_file)
			{
				if (file_exists($d_file))
				{
					$json = json_decode(file_get_contents($d_file), true);
				
					unset($json[str_replace('.', '__x__', $this->get['param'][1])]);
				
					if (!utils::write_in_file($d_file, $json, 'json'))
					{
						$warning_json = true;
					}
				}
			}
			
			if (!$warning_thumb && !$warning_json)
			{
				$ret['status'] = 'success';
				$ret['text'] = tr::get('img_thumb_data_deleted');
			}
			else if ($warning_thumb && $warning_json)
			{
				$ret['status'] = 'warning';
				$ret['text'] = tr::get('img_deleted_thumb_json_not_deleted');
			}
			else if ($warning_thumb)
			{
				$ret['status'] = 'warning';
				$ret['text'] = tr::get('img_deleted_thumb_not_deleted');
			}
			else if ($warning_json)
			{
				$ret['status'] = 'warning';
				$ret['text'] = tr::get('img_deleted_json_not_deleted');
			}
		}
		catch (Exception $e)
		{
			$ret['status'] = 'error';
			$ret['text'] = $e->getMessage();
		}
		
		echo json_encode($ret);
	}
	
	/**
	 * Deletes all image files inside $gallery folder
	 * Deletes all files inside thumbs folder in $gallery folder
	 * Deletes thumbs folder in $gallery folder
	 * Deletes data.json file in $gallery folder
	 * Deletes $gallery folder
	 */
	public function deleteGallery()
	{
		$error = utils::recursive_delete($this->get['param'][0]);
		
		if ($error)
		{
			$msg['status'] = 'error';
			$msg['text'] = tr::get('gallery_not_deleted');
			error_log(implode("\n", $error)); 
		}
		else
		{
			$msg['status'] = 'success';
			$msg['text'] = tr::get('gallery_deleted');
		}
		
		echo json_encode($msg);
	}
  
  public function renameGallery(){
    
    $old_name = $this->get['param'][0];
    $new_name = $this->get['param'][1];
    
    $path = './sites/default/images/galleries/';
    
    try
    {
      if(!file_exists($path. $old_name))
      {
        throw new Exception('error_gallery_does_not_exist');
      }
    
      if(file_exists($path . $new_name))
      {
        throw new Exception('error_new_gallery_name_exist');
      }
      
      @rename($path . $old_name, $path . $new_name);
      
      if(file_exists($path. $old_name) || !file_exists($path . $new_name))
      {
        throw new Exception('error_new_gallery_not_renamed');
      }
      
      echo json_encode(array('status' => 'success', 'text' => tr::get('ok_gallery_renamed')));
    }
    catch (Exception $e)
    {
      echo json_encode(array('status' => 'error', 'text' => tr::get($e->getMessage())));
    }
  }
	
	
}