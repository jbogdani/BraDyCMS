<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2012
 * @license			MIT, See LICENSE file
 * @since			Aug 12, 2012
 */


class sys_translate_ctrl extends Controller
{
	public function showList($opened_lang=false)
	{
		$langs = utils::dirContent(LOCALE_DIR);
		
		$uid = uniqid('transl');
		
		$this->render('sys_translate', 'list', array(
				'opened_lang' => $opened_lang,
				'langs' => $langs
		));
	}
	
	
	public function showForm()
	{
		$lng = $this->get['param'][0];
		require LOCALE_DIR . 'en.php';
		$en = $_lang;
		unset($_lang);
		
		require LOCALE_DIR . $lng . '.php';
		$edit_lang = $_lang;
		unset($_lang);
		
		$this->render('sys_translate', 'form', array(
				'lng' => $lng,
				'en' => $en,
				'edit_lang' => $edit_lang
		));
	}
	
	
	public function add_locale()
	{
		$lang = $this->get['param'][0];
		
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
	
	
	public function save()
	{
		$post = $this->post;
		
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