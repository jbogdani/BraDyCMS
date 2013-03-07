<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 11, 2012
 */
 
class changelog_ctrl
{
	public static function show()
	{
		$html = '<h1>BraDy CMS</h1>'
				. '<h3>Current version: <?php echo version::current(); ?></h3>'
				. '<h3>Changelog</h3>'
				.'<ul>';
		$chl = version::changelog();
		
		if (is_array($chl))
		{
			foreach ($chl as $v=>$arr)
			{
				$html .= '<li>' . $v
				. '<ul>'
					. '<li>' . implode('</li><li>', $arr) . '</li>'
				. '</ul>' 
				. '</li>';
			}
		}
		
		$html .= '</ul>';
		
		echo $html;
	} 
}