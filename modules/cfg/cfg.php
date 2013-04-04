<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Feb 23, 2013
 */
 
class cfg_ctrl
{
	public static function edit()
	{
		$data = cfg::get();
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'cfg/tmpl'), unserialize(CACHE));
		echo $twig->render('form.html', array(
				'data' => $data,
				'tr' => new tr(),
				'uid' => uniqid('uid')
		));
	}
	
	public static function save($post)
	{
		$post = utils::recursiveFilter($post);
		cfg::save($post);
	}
	
	public static function empty_cache()
	{
		$error = utils::recursive_delete(CACHE_DIR, true);
		
		if(count($error) > 0)
		{
			$ret = array('status' => 'error', 'text' => tr::get('cache_not_emptied') . '. ' . implode('; ', $error));
		}
		else
		{
			$ret = array('status' => 'success', 'text' => tr::get('cache_emptied'));
		}
		
		echo json_encode($ret);
	}
}