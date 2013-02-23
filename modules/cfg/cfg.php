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
		
		$html = '';
		
		foreach ($data as $key => $val)
		{
			$dontedit = false;
			if (in_array($key, array('prefix', 'friendly_url')))
			{
				$dontedit = true;
			}
			
			if (is_string($val))
			{
				$html .= self::input($key, $val, $dontedit);
			}
			else if (is_array($val))
			{
				$html .= '<hr /><p class="lead">' . tr::get('cfg-' . $key) . '</p>';
				
				foreach ($val as $k=>$v)
				{
					$html .= self::doubleInput($key, $k, $v, ($k == 'jbogdani@gmail.com' ? true : false));
				}
				$html .= self::doubleInput($key, '', '');
			}
		}
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'cfg/tmpl'), unserialize(CACHE));
		echo $twig->render('form.html', array(
				'html' => $html,
				'save_text' => tr::get('save'),
				'uid' => uniqid('uid')
		));
	}
	
	public static function input($key, $value, $readonly = false)
	{
		return '<div class="control-group">'
			. '<label class="control-label">' . tr::get('cfg-' . $key) . '</label>'
			. '<div class="controls">'
				. '<input type="text" name="' . $key . '" value="' . $value . '" class="span8" ' . ($readonly ? ' readonly="true" ' : '') . ' />'
			. '</div>'
		. '</div>';
	}
	
	public static function doubleInput($main, $key, $value, $readonly = false)
	{
		return '<div class="control-group">'
			. '<div class="controls">'
				. '<input type="text" value="' . $key . '" class="span4 change" data-main="' . $main . '" ' . ($readonly ? ' readonly="true" ' : '') . ' />'
				. ' <code>»</code> '
				. '<input type="text" name="' . $main . '[' . $key . ']" value="' . $value . '" class="span4" ' . ($readonly ? ' readonly="true" ' : '') . ' />'
			. '</div>'
		. '</div>';
	}
	
	public static function save($post)
	{
		$post = utils::recursiveFilter($post);
		cfg::save($post);
	}
}