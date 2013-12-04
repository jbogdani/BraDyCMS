<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Dec 1, 2012
 */
 

class article_ctrl extends Controller
{
	
	public function all()
	{
		if (!empty($this->request['param'][0]))
		{
			$art_arr = Article::getByTag($this->request['param'], false, false, true);
		}
		else
		{
			$art_arr = Article::getAll();
		}
		$tags = Article::getTags();
    
    if (!is_array($tags))
    {
      $tags = array();
    }
		
		$this->render('article', 'list', array(
      'art_arr'=>$art_arr,
      'tags' => $tags,
      'imploded_tags' => '"' . implode('","', $tags) . '"',
      'active_tags' => $this->request['param'],
      'cfg_langs' => cfg::get('languages'),
      'delete_tag' => (!$art_arr && count($this->request['param']) == 1 ? $this->request['param'][0] : false)
      ));
	}
	
	public function deleteTag()
	{
		try
		{
			Article::deleteTag($this->get['tag']);
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			
			echo tr::get('error_delete_tag');
		}
	}

	public function deleteAllTags()
	{
		try
		{
			Article::deleteUnusedTags();
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			
			echo tr::get('error_delete_tag');
		}
	}

	public function check_duplicates()
	{
		$val = $this->get['param'][0];
		
		$res = Article::getByTextid($val, false, true);
		
		if ($res)
		{
			echo tr::sget('duplicate_textid', $val);
		}
	}
	
	
	public function edit()
	{
		$id = $this->get['param'][0];
		
		// check if art_img files exist
		$art_img = cfg::get('art_img');
    $art_img[] = 'orig';
		
    // get list of (different dimensions of) article's images, if present
		if (is_array($art_img))
		{
			foreach($art_img as $dimension)
			{
				if (file_exists(IMG_DIR . 'articles/' . $dimension . '/' . $id . '.jpg'))
				{
					$article_images[$dimension] = IMG_DIR . 'articles/' . $dimension . '/' . $id . '.jpg';
				}
			}
		}
    
    // get list of all available image files
    if (is_dir(IMG_DIR . 'articles/media/' . $id))
    {
      $art_media = utils::dirContent(IMG_DIR . 'articles/media/' . $id);
      
      if(($key = array_search('thumbs', $art_media)) !== false)
      {
        unset($art_media[$key]);
      }
    }
		
		$art = Article::getById($id);
		
		$available_tags = Article::getTags();
		
		if (!is_array($available_tags))
		{
			$available_tags = array();
		}
		
		$this->render('article', 'form', array(
      'art'=>$art,
      'custom_fields' => cfg::get('custom_fields'),
      'date' => date('Y-m-d'),
      'imploded_tags' => '"' . implode('","', $available_tags) . '"',
      'art_imgs' => $article_images,
      'tmp_path' => TMP_DIR,
      'cfg_langs' => cfg::get('languages'),
      'art_media' => $art_media
		));
	}
	
	public function saveTransl()
	{
		$data = $this->post;
		$art_id = $this->get['param'][0];
		
		try
		{
			Article::translate($art_id, $data);
			
			$out['text'] = tr::get('ok_translation_saved');
			$out['status'] = 'success';
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			$out['text'] = tr::get('error_translation_not_saved');
			$out['status'] = 'error';
		}
		
		echo json_encode($out);
	}
	
	/**
	 * Displays articles's translation form
	 * param[0] is translation language
	 * param[1] is article's id
	 */
	public function translate()
	{
		$id = $this->get['param'][1];
		$lang = $this->get['param'][0];
		
		$article = Article::getById($id, false, true);
		$translations = $article->ownArttrans;
		
		if (is_array($translations))
		{
			foreach ($translations as $trans)
			{
				if ($trans->lang == $lang)
				{
					$art_translation = $trans->export();
				}
				
			}
		}
		
		if (!$art_translation)
		{
			$art_translation = array('lang' => 'en', 'status' => 0, 'art_id' => $id);
		}
		
		$article_array = $article->export();
		
		foreach (cfg::get('languages') as $langs)
		{
			if ($lang == $langs['id'])
			{
				$lang_arr = $langs;
			}
		}
		
		$this->render('article', 'transl_form', array(
      'art'=>$article_array, 
      'custom_fields' => cfg::get('custom_fields'),
      'transl' => $art_translation,
      'lang' => $lang_arr
      ));
		
	}
	
	
	/**
	 * Alias for article_ctrl::edit
	 */
	public function addNew()
	{
		$this->edit();
	}
	
	
	public function delete()
	{
		$id = $this->get['param'][0];
		try
		{
			Article::delete($id);
			$this->delete_art_img($id, true);

			$out['type'] = 'success';
			$out['text'] = tr::get('delete_article_ok');
		}
		catch (Exception $e)
		{
			error_log(tr::sget('delete_article_error'));
			$out['type'] = 'error';
			$out['text'] = $e->getMessage();
		}
		
		echo json_encode($out);
	}
	
	public function save()
	{
		$id = $this->get['param'][0];
		$data = $this->post;
		
		try
		{
			$new_id = Article::save($id, $data);
			
			if (!$new_id)
			{
				throw new Exception(tr::get('save_article_error'));
			}
			$out['type'] = 'success';
			$out['text'] = tr::get('save_article_ok');
			$out['id'] = $new_id;
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			$out['type'] = 'error';
			$out['text'] = tr::get('save_article_error');
		}
		echo json_encode($out);
	}
	
	public function delete_art_img($return = false)
	{
		$id = $this->get['param'][0];
		try
		{
			$dimensions = cfg::get('art_img');
      $dimensions[] = 'orig';
			
			// check if cfg::art_img is available
			if (!is_array($dimensions))
			{
				throw new Exception(tr::get('cfg_art_img_missing'));
			}
			foreach($dimensions as $dim)
			{
				$file = IMG_DIR . 'articles/' . $dim . '/' . $id . '.jpg';
				
				if (file_exists($file))
				{
					@unlink($file);
					
					if (file_exists($file))
					{
						$error[] = $file;
					}
					else
					{
						$ok = $file;
					}
				}
			}
		}
		catch(Exception $e)
		{
			if ($return)
			{
				throw new Exception($e);
			}
			else
			{
				$msg['status'] = 'error';
				$msg['text'] = $e->getMessage();
			}
		}
		
		if ($return)
		{
			return array('ok'=> $ok, 'error' => $error);
		}
		else
		{
			if (count($error) > 0 && count($ok) > 0)
			{
				$msg['status'] = 'error';
				$msg['text'] = tr::sget('art_img_partially_deleted', implode(', ', $error));
			}
			else if (count($error) > 0 && count($ok) == 0)
			{
				$msg['status'] = 'error';
				$msg['text'] = tr::get('art_img_not_deleted');
			}
			else if (count($error) == 0 && count($ok) > 0)
			{
				$msg['status'] = 'success';
				$msg['text'] = tr::get('art_img_deleted');
			}
		}
		
		echo json_encode($msg);
	}
	
	/**
	 * Creates image files using configuration dimensions 
	 * and places this images in appropriate directories (./sites/images/articles/dim_widthxdim_height/art_id.jpg)
	 * 
	 * @param int $id article ID
	 * @param string $file full path to uploaded file in temporary dire
	 * @throws Exception on erros
	 */
	public function attachImage()
	{
		$id = $this->get['param'][0];
		$file = $this->get['param'][1];
    
    try{
      
			$dimensions = cfg::get('art_img');
      $dimensions[] = 'orig';
      
			// check if cfg::art_img is available
			if (!is_array($dimensions))
			{
				throw new Exception(tr::get('cfg_art_img_missing'));
			}
			
			// loop in dimensions
			foreach($dimensions as $dim)
			{
        // define image directory
				$dir = IMG_DIR . 'articles/' . $dim;
				
				// if image dir does not exist, try to create it
				if(!is_dir($dir))
				{
					@mkdir($dir, 0777, true);
				}
				
				// if image dir does not exist, throw exception
				if(!is_dir($dir))
				{
					throw new Exception(tr::sget('create_dir_error', $dir));
				}
				
				// if image dir is not writeable, throw exception				
				if(!is_writable($dir))
				{
					throw new Exception(tr::sget('dir_is_not_writable', $dir));
				}
				
				
				// make thumbnails in jpg format, using original file
				$output = $dir . '/' . $id . '.jpg';
				
        if ($dim === 'orig')
        {
          @copy(TMP_DIR . $file, $output);
          if (!file_exists($output))
          {
            throw new Exception(tr::get('error_copying_file'));
          }
        }
        else
        {
          try
          {
            imgMng::thumb(TMP_DIR . $file, $output, $dim);
          }
          catch (Exception $e)
          {
            throw new Exception(tr::get($e->getMessage()));
          }
        }
			}
			
			$msg['status'] = 'success';
			$msg['text'] = tr::get('art_images_created');
		}
		catch (Exception $e)
		{
			$msg['status'] = 'error';
			$msg['text'] = $e->getMessage();
		}
		echo json_encode($msg);
	}
  
  
  public function attachMedia()
	{
		$id = $this->get['param'][0];
		$file = $this->get['param'][1];
    
    
    // define image directory
    $dir = IMG_DIR . 'articles/media/' . $id;
    
    try
    {
      // if image dir does not exist, try to create it
      if(!is_dir($dir))
      {
        @mkdir($dir, 0777, true);
      }

      // if image dir does not exist, throw exception
      if(!is_dir($dir))
      {
        throw new Exception(tr::sget('create_dir_error', $dir));
      }

      // if image dir is not writeable, throw exception				
      if(!is_writable($dir))
      {
        throw new Exception(tr::sget('dir_is_not_writable', $dir));
      }
      
      // set output file full path
      $output = $dir . '/' . $file;
      
      // copy file to destination
      @copy(TMP_DIR . $file, $output);
      if (!file_exists($output))
      {
        throw new Exception(tr::get('error_copying_file'));
      }
      
      $msg['status'] = 'success';
			$msg['text'] = tr::get('ok_file_uploaded');
      $msg['all_media'] = utils::dirContent($dir);
    }
    catch (Exception $e)
    {
      $msg['status'] = 'error';
			$msg['text'] = tr::get('error_file_not_uploaded');
    }
    
    echo json_encode($msg);
	}
  
  
}