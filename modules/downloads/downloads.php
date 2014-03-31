<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Mar 25, 2013
 */
 
class downloads_ctrl extends Controller
{
	private $path = DOWNLOADS_DIR;
	 
	public function all()
	{
    if (!is_dir($this->path))
    {
      mkdir($this->path, 0777, true);
    }
    
		$all_downloads = utils::dirContent($this->path);
		
		asort($all_downloads);
		
		$this->render('downloads', 'list', array(
      'nodes' => $all_downloads
      ));
	} 
	
	public function edit()
	{
		$node_content = utils::dirContent($this->path . $this->get['param'][0]);
		
		$lang = $this->get['param'][1];
		
		arsort($node_content);
		
		if (file_exists($this->path . $this->get['param'][0] . '/data.json'))
		{
			$data = json_decode(file_get_contents($this->path . $this->get['param'][0] . '/data.json'), true);
		}
		
		if ($lang)
		{
			$orig = $data;
			
			unset($data);
			
			if (file_exists($this->path . $this->get['param'][0] . '/data_' . $lang . '.json'))
			{
				$data = json_decode(file_get_contents($this->path . $this->get['param'][0] . '/data_' . $lang . '.json'), true);
			}
		}
		
    $files = array();
		foreach ($node_content as $file)
		{
			if ($file !== 'data.json' && !preg_match('/\.json/', $file))
			{
        $formattedName = str_replace('.', '__x__', $file);
				$files[] = array(
						'name' => $file,
						'formattedName' => $formattedName,
						'fullpath' => $this->path . $this->get['param'][0] . '/' . $file,
            'title' => $data[$formattedName]['title'] ? $data[$formattedName]['title'] : $file,
						'description' => $data[$formattedName]['description'],
            'sort' => $data[$formattedName]['sort'] ? $data[$formattedName]['sort'] : count($files) + 1
						); 
			}
		}
    
    usort($files, function($a, $b){
      
      if ($a['sort'] === $b['sort'])
      {
        return 0;
      }
      return ($a['sort'] > $b['sort']) ? -1 : 1;
    });
		
		$this->render('downloads', 'editNode', array(
				'node'=> $this->get['param'][0],
				'files'	=> $files,
				'thumbs'=> $thumbs,
				'upload_dir'=> $this->path . $this->get['param'][0],
				'langs' => cfg::get('languages'),
				'translation' => $lang
				));
		
	}
	
	public function saveData()
	{
		$json_file = $this->get['param'][0] . '/data' . ($this->get['param'][1] ? '_' . $this->get['param'][1] : '') . '.json';
		
		if (utils::write_in_file($json_file, $this->post, 'json'))
		{
			$ret = array('status' => 'success', 'text' => tr::get('download_node_updated')); 
		}
		else
		{
			$ret = array('status' => 'error', 'text' => tr::get('download_node_not_updated'));
		}
		
		echo json_encode($ret);
	}
	
	public function add()
	{
		try{
			
			$node_name = $this->path . strtolower(str_replace(array(' ', "'", '"'), '_', $this->get['param'][0]));
			
			
			if (is_dir($node_name))
			{
				throw new Exception(tr::get('download_node_exists'));
			}
			@mkdir($node_name, 0777, true);
			@mkdir($node_name . '/thumbs', 0777, true);
			
			if (!is_dir($node_name))
			{
				throw new Exception(tr::get('download_node_not_created'));
			}
			
			$msg['text'] = tr::get('download_node_created');
			$msg['status'] = 'success';
		}
		catch (Exception $e)
		{
			$msg['text'] = $e->getMessage();
			$msg['status'] = 'error';
		}
		echo json_encode($msg);
	}
	
	public function deleteFile()
	{
		try
		{
			$file = $this->get['param'][0] . '/' . $this->get['param'][1];
			
			if(file_exists($file))
			{
				@unlink($file);
				
				if (file_exists($file))
				{
					throw new Exception(tr::get('img_not_deleted'));
				}
			}
			
			$data_file[] = $this->get['param'][0] . '/data.json';
			
			foreach (cfg::get('languages') as $lng)
			{
				$data_file[] = $this->get['param'][0] . '/data_' . $lng['id']. '.json';
			}
			
			
			foreach ($data_file as $d_file)
			{
				if (file_exists($d_file))
				{
					$json = json_decode(file_get_contents($d_file), true);
				
					unset($json[str_replace('.', '__x__', $this->get['param'][1])]);
				
					if (!utils::write_in_file($d_file, $json, 'json'))
					{
						$warning_json = true;
					}
				}
			}
			
			if ($warning_json)
			{
				$ret['status'] = 'warning';
				$ret['text'] = tr::get('file_deleted_json_not_deleted');
			}
      else
			{
				$ret['status'] = 'success';
				$ret['text'] = tr::get('file_data_deleted');
			}
		}
		catch (Exception $e)
		{
			$ret['status'] = 'error';
			$ret['text'] = $e->getMessage();
		}
		
		echo json_encode($ret);
	}
	
	public function deleteNode()
	{
		$error = utils::recursive_delete($this->get['param'][0]);
		
		if ($error)
		{
			$msg['status'] = 'error';
			$msg['text'] = tr::get('download_node_not_deleted');
			error_log(implode("\n", $error)); 
		}
		else
		{
			$msg['status'] = 'success';
			$msg['text'] = tr::get('download_node_deleted');
		}
		
		echo json_encode($msg);
	}
	
	
}