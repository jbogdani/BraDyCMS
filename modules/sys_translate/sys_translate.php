<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2012
 * @license			All rights reserved
 * @since			Aug 12, 2012
 */

class sys_translate_ctrl
{
	public static function showList($opened_lang=false)
	{
		$langs = utils::dirContent(LOCALE_DIR);
		
		$uid = uniqid('transl');
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'sys_translate/tmpl'), unserialize(CACHE));
		
		echo $twig->render('list.html', array(
				'opened_lang' => $opened_lang,
				'langs' => $langs,
				'uid' => $uid,
				'tr' => new tr()
		));
	}
	
	public static function showForm($lng)
	{

		require LOCALE_DIR . 'it.php';
		$it = $_lang;
		unset($_lang);
		
		require LOCALE_DIR . $lng . '.php';
		$edit_lang = $_lang;
		unset($_lang);
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'sys_translate/tmpl'), unserialize(CACHE));
		
		echo $twig->render('form.html', array(
				'uid' => uniqid('translate'),
				'lng' => $lng,
				'it' => $it,
				'edit_lang' => $edit_lang,
				'tr' => new tr()
		));
	}
	
	public static function add_locale($lang)
	{
		if (!file_exists(LOCALE_DIR . $lang . '.php') && utils::write_in_file(LOCALE_DIR . $lang . '.php', ''))
		{
			$msg['text'] = tr::get('ok_lang_create');
			$msg['status'] = 'success';
		}
		else
		{
			$msg['text'] = tr::get('error_lang_create');
			$msg['status'] = 'error';
		}
		
		echo json_encode($msg);
	}
	
	public static function save($post)
	{
		$lang = $post['edit_lang'];
		unset($post['edit_lang']);

		foreach ($post as $k => $v)
		{
			$text[]='$_lang[\'' . $k . '\'] = "' . str_replace(array('"', "\r\n"), array('\'', '\\n'), $v) . '";'; 
		}
		
		if(utils::write_in_file(LOCALE_DIR . $lang .'.php', '<?php' . "\n" . implode("\n", $text)))
		{
			echo json_encode(array('text'=>tr::get('ok_language_update'), 'status'=>'success'));
		}
		else
		{
			echo json_encode(array('text'=>tr::get('error_language_update'), 'status'=>'error'));
		}
		
	}
		
}