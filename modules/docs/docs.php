<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 21, 2012
 */
 
class docs_ctrl
{
	public static function tmpl($file)
	{
		if (file_exists(MOD_DIR . 'docs/tmpl/' . $file . '.html'))
		{
			$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'docs/tmpl'), unserialize(CACHE));
			echo $twig->render($file . '.html', array(
					'art_arr'=>$art_array,
					'uid' => uniqid('uid')
			));
		}
	}
}