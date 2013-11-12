<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Feb 1, 2013
 */
 
class template_ctrl extends Controller
{
	
	public function compile()
	{
		try
		{
			$less = new lessc();
			$less->checkedCompile(SITE_DIR .  "css/styles.less", SITE_DIR . "css/styles.css");
			
			$ret['status'] = 'success';
			$ret['text'] = tr::get('ok_compiling_less');
		}
		catch (Exception $e)
		{
      error_log($e->getTraceAsString());
			$ret['status'] = 'error';
			$ret['text'] = tr::get('error_compiling_less');
		}
		
		echo json_encode($ret);
	}
	
	public function edit()
	{
		$file = $this->get['param'][0];
		$type = $this->get['param'][1];
		
		switch ($type)
		{
			case 'twig':
				$file = SITE_DIR . $file;
				break;
				
			case 'css':
			case 'less':
				$file = SITE_DIR . 'css/' . $file;
				break;
				
			default:
				return false;
				break;
		}
		
		$content = file_get_contents($file);

		$this->render('template', 'editor', array(
				'filename' => $file,
				'content'=>$content,
				'lang' => $file
		));
	}
	
	public function dashboard()
	{
		foreach (utils::dirContent(SITE_DIR) as $f)
		{
			if (preg_match('/\.twig/', $f))
			{
				$twig[] = $f;
			}
		}
		
		
		foreach (utils::dirContent(SITE_DIR . 'css') as $f)
		{
			if (preg_match('/\.css/', $f))
			{
				$css[] = $f;
			}
			
			if (preg_match('/\.less/', $f))
			{
				$less[] = $f;
			}
		}
		
		$this->render('template', 'list', array(
			files => array(
			'twig'=> $twig,
			'css' => $css,
			'less' => $less
				)
		));
	}
	
	public function save()
	{
		$file = $this->get['param'][0];
		$post = $this->post;
		
		if ($file)
		{
			try
			{
				if (!utils::write_in_file($file, $post['text']))
				{
					throw new Exception('Can not write in file ' . $full_file);
				}
				$ret = array('status' => 'success', 'text' => 'File updated');
			}
			catch(Exception $e)
			{
				$ret = array('status' => 'error', 'text' => $e->getMessage());
			}
			
			echo json_encode($ret);

		}
	}
	
	
}