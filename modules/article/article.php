<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 1, 2012
 */
 

class article_ctrl
{
	
	public static function check_duplicates($val)
	{
		$db = new DB();
		
		$res = $db->executeQuery('SELECT count(*) as `tot` FROM  `' . PREFIX . '__articles` WHERE `text_id` = :text_id', array(':text_id' => $val), 'read');
		
		if ($res[0]['tot'] > 0)
		{
			echo tr::sget('duplicate_text_id', $val);
		}
	}
	
	public static function all()
	{
		$article = new Article(new DB());
		$art_array = $article->getAll();
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'article/tmpl'), unserialize(CACHE));
		echo $twig->render('list.html', array(
				'art_arr'=>$art_array,
				'tr' => new tr()
		));
	}
	
	public static function edit($id = false)
	{
		$article = new Article(new DB());
		
		if ($id)
		{
			$art = $article->getArticle($id, false, false, true);
		}
		
		// check if art_img files exist
		$art_img = cfg::get('art_img');
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
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'article/tmpl'), unserialize(CACHE));
		echo $twig->render('form.html', array(
				'art'=>$art,
				'custom_fields' => cfg::get('custom_fields'),
				'date' => date('Y-m-d'),
				'uid' => uniqid('id'),
				'imploded_tags' => '"' . implode('","', $article->getTags()) . '"',
				'sections' => $article->getSections(),
				'tr' => new tr,
				'art_imgs' => $article_images,
				'tmp_path' => TMP_DIR
		));
	}
	
	public static function addNew()
	{
		self::edit();
		return;
	}
	
	
	public static function delete($id)
	{
		$article = new Article(new DB());
		
		try
		{
			if (!$article->delete( $id ))
			{
				throw new Exception(tr::sget('delete_article_error', $id));
			}
			try
			{
				self::delete_art_img($id, true);
			}
			catch(Exception $e)
			{
				
			}
			$out['type'] = 'success';
			$out['text'] = tr::get('delete_article_ok');
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			$out['type'] = 'error';
			$out['text'] = $e->getMessage();
		}
		
		echo json_encode($out);
	}
	
	public static function save($id = false, $post)
	{
		try
		{
			$article = new Article(new DB());
			
			if ($id)
			{
				if (!$article->update($id, $post))
				{
					throw new Exception(tr::sget('update_article_error', $id));
				}
				$out['type'] = 'success';
				$out['text'] = tr::get('update_article_ok');
				$out['id'] = $id;
			}
			else
			{
				$id = $article->add($post);
				if (!$id)
				{
					throw new Exception(tr::get('save_article_error'));
				}
				$out['type'] = 'success';
				$out['text'] = tr::get('save_article_ok');
				$out['id'] = $id;
			}
		}
		catch (Exception $e)
		{
			$out['type'] = 'error';
			$out['text'] = $e->getMessage();
		}
		echo json_encode($out);
	}
	
	public static function delete_art_img($id, $return = false)
	{
		try
		{
			$dimensions = cfg::get('art_img');
			
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
	public static function attachImage($id, $file)
	{
		try{
			$dimensions = cfg::get('art_img');
			
			// check if cfg::art_img is available
			if (!is_array($dimensions))
			{
				throw new Exception(tr::get('cfg_art_img_missing'));
			}
			
			// loop in dimensions
			foreach($dimensions as $dim)
			{
				// de'fine image directory
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
				
				imgMng::thumb(TMP_DIR . $file, $output, $dim);
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
}