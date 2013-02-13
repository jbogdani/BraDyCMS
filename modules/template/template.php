<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Feb 1, 2013
 */
 
class template_ctrl
{
	public static function edit($file)
	{
		switch ($file)
		{
			case 'index':
				$full_file = './sites/default/index.html';
				break;
				
			case 'css':
				if (file_exists('./sites/default/css/styles.css'))
				{
					$full_file = './sites/default/css/styles.css';
				}
				break;
				
			default:
				return false;
				break;
		}
		
		if ($full_file)
		{
			$content = file_get_contents($full_file);
			
			$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'template/tmpl'), unserialize(CACHE));
			echo $twig->render('editor.html', array(
					'filename' => $full_file,
					'content'=>$content,
					'no_tab_content' => str_replace("\t", '   ', $content),
					'lang' => $file
			));
		
		}
		
	}
	
	public static function save($file, $post)
	{
		switch ($file)
		{
			case 'index':
				$full_file = './sites/default/index.html';
				break;
		
			case 'css':
				$full_file = './sites/default/css/styles.css';
				break;
		
			default:
				return false;
				break;
		}
		
		if ($full_file)
		{
			try
			{
				$f = @fopen($full_file, 'w');
				
				if (!$f)
				{
					throw new Exception('Can not open file ' . $full_file);
				}
				
				if ( !@fwrite ( $f, $post['text'] ) )
				{
					throw new Exception('Can not write in file ' . $full_file);
				}
				
				@fclose($f);
				
				$ret = array('status' => 'success', 'text' => 'File udated');
				
			}
			catch(Exception $e)
			{
				$ret = array('status' => 'error', 'text' => $e->getMessage());
			}
			
			echo json_encode($ret);

		}
	}
	
	public static function index()
	{
		self::edit('index');
	}
	
	public static function css()
	{
		self::edit('css');
	}
	
}