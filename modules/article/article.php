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
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'article/tmpl'), unserialize(CACHE));
		echo $twig->render('form.html', array(
				'art'=>$art,
				'custom_fields' => cfg::get('custom_fields'),
				'date' => date('Y-m-d'),
				'uid' => uniqid('id'),
				'imploded_tags' => '"' . implode('","', $article->getTags()) . '"',
				'sections' => $article->getSections(),
				'tr' => new tr
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
}