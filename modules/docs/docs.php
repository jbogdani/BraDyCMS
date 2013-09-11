<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 21, 2012
 */
 
class docs_ctrl extends Controller
{
	public function tmpl()
	{
		$file = $this->get['param'][0];
		
		if (file_exists(MOD_DIR . 'docs/tmpl/' . $file . '.twig'))
		{
			$this->render('docs', $file, array(
					'art_arr'=>$art_array
			));
		}
	}
}