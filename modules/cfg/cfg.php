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
			//	'html' => $html,
				'save_text' => tr::get('save'),
				'uid' => uniqid('uid')
		));
	}
	
	public static function save($post)
	{
		$post = utils::recursiveFilter($post);
		cfg::save($post);
	}
}