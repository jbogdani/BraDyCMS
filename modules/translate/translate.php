<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 15, 2012
 */
 
class translate_ctrl
{
	public static function menu($id = false, $lang = false)
	{
		$translate = new Translate();
		
		if ($id)
		{
			$data = $translate->get_menu_translation($id, $lang);
			
			$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'translate/tmpl'), unserialize(CACHE));
			echo $twig->render('menu_form.html', array(
					'data'=>$data[0],
					'lang' => $lang,
					'id' => $id,
					'uid' => uniqid()
			));
		}
		else
		{
			$langs = cfg::get('languages');
			if (is_array($langs))
			{
				$data = array();
				foreach ($langs as $ll)
				{
					array_push($data,
					array(
					'id' => $ll['id'],
					'string' => $ll['string'],
					'data' => $translate->get_menu_list($ll['id'])
					)
					);
				}
			
				$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'translate/tmpl'), unserialize(CACHE));
				echo $twig->render('menu_list.html', array(
						'data'=>$data,
						'uid' => uniqid(),
						'tr' => new tr()
				));
			}
			else
			{
				echo '<div class="alert alert-block">'
						.'<p><i class="icon-exclamation-sign"></i> <strong>Warning!</strong></p>'
						.'<p>No language support for this website found. Please edit the main configuration file.</p>'
								.'</div>';
			}
		}
	}
	
	
	public static function article($id = false, $lang = false)
	{
		$translate = new Translate();
		
		if ($id)
		{
			$data = $translate->get_article_translation($id, $lang);
			
			$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'translate/tmpl'), unserialize(CACHE));
			echo $twig->render('article_form.html', array(
					'data'=>$data[0],
					'lang' => $lang,
					'id' => $id,
					'uid' => uniqid(),
					'tr' => new tr()
			));
		}
		else
		{
			$langs = cfg::get('languages');
			if (is_array($langs))
			{
				$data = array();
				foreach ($langs as $ll)
				{
					array_push($data,
						array(
							'id' => $ll['id'],
							'string' => $ll['string'],
							'data' => $translate->get_article_list($ll['id'])
						)
					);
				}
				
				$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'translate/tmpl'), unserialize(CACHE));
				echo $twig->render('article_list.html', array(
						'data' => $data,
						'uid' => uniqid(),
						'tr' => new tr()
				));
			}
			else
			{
				echo '<div class="alert alert-block">'
						.'<p><i class="icon-exclamation-sign"></i> <strong>Warning!</strong></p>'
						.'<p>No language support for this website found. Please edit the main configuration file.</p>'
					.'</div>';
			}
		}
	}
	
	public static function save($context, $lang, $o_id, $post)
	{
		try
		{
			$translate = new Translate();
			
			$ret = $translate->save_translation($context, $lang, $o_id, $post);
			
			if ($ret)
			{
				$out['text'] = "Translation saved";
				$out['type'] = 'success';
			}
			else
			{
				throw new Exception('Error. Translation not saved');
			}
		}
		catch(Exception $e)
		{
			$out['text'] = $e->getMessage();
			$out['type'] = 'error';
		}
		
		echo json_encode($out);
	}
}