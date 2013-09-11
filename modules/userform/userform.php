<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since				Aug 29, 2013
 */


class userform_ctrl extends Controller
{
	private $data;
	
	
	public function createNew()
	{
		$name = $this->get['param'][0] . '.json';
		
		$text = array(
			'to'=>'',
			'from_email'=>'',
			'from_name'=>'',
			'subject'=>'',
			'success_text'=>'',
			'error_text'=>'',
			'elements'=>array(
				array(
					'name' => '',
					'type' => 'text|longtext|select',
					'options' => 'if type is select options array is required',
					'is_required' => 'true|false',
					'email' => 'true|false',
				)
				)
			);
		
		
		if (utils::write_in_file('./sites/default/modules/userforms/' . $name, $text, 'json'))
		{
			echo json_encode(array('status' => 'success', 'text' => tr::get('ok_form_config_saved') ));
		}
		else
		{
			echo json_encode(array('status' => 'error', 'text' => tr::get('error_form_config_not_saved') ));
		}
	}
	
	public function save()
	{
		if (utils::write_in_file('./sites/default/modules/userforms/' . $this->get['param'][0], $this->post['data'], 'json'))
		{
			echo json_encode(array('status' => 'success', 'text' => tr::get('ok_form_config_saved') ));
		}
		else
		{
			echo json_encode(array('status' => 'error', 'text' => tr::get('error_form_config_not_saved') ));
		}
	}
	
	public function erase()
	{
		if (unlink('./sites/default/modules/userforms/' . $this->get['param'][0]))
		{
			echo json_encode(array('status' => 'success', 'text' => tr::get('ok_form_deleted') ));
		}
		else
		{
			echo json_encode(array('status' => 'error', 'text' => tr::get('error_form_ot_deleted') ));
		}
	}
	
	public function edit_form()
	{
		$form = $this->get['param'][0];
		
		$content = file_get_contents('./sites/default/modules/userforms/' . $form);
		$this->render('userform', 'edit_form', array(
				'form' => $form,
				'content'=> $content
		));
	}
	
	
	public function view()
	{
		$this->render('userform', 'list', array(
				'forms' => utils::dirContent('./sites/default/modules/userforms')
		));
	}
	
	/**
	 * Loads json file with form data in $this->data 
	 */
	private function loadForm($form)
	{
		if (!$this->data)
		{
			$filename = './sites/default/modules/userforms/' . $form . '.json';

			if (file_exists($filename))
			{
				$this->data = json_decode(file_get_contents($filename), true);
			}
		}
	}
	
	/**
	 * Processes post data and echoes json with status: error|success and verbous text
	 * @throws Exception
	 */
	public function process()
	{
		try
		{
			$form = $this->get['param'][0];

			$this->loadForm($form);

			$data = $_POST;

			/**
			 * Check for error in POST data
			 */
			foreach ($this->data['elements'] as $el)
			{
				// Check required element
				if ($el['is_required'] && !$data[$el['name']])
				{
					$error .= '<p>' . tr::sget('missing_required', $el['name']) . '</p>';
				}
				
				// Check for email pattern
				// TODO!
			}

			if ($error)
			{
				throw new Exception($error);
			}
			
			foreach ($this->data['elements'] as $el)
			{
				if ($el['type'] == 'upload')
				{
					if (file_exists($data[$el['name']]))
					{
						$attach[] = $data[$el['name']];
					}
				}
				else
				{
					$text .= "\n" . $el['label'] . ": " . $data[$el['name']];
				}
			}
			
			try
			{
				$message = Swift_Message::newInstance()
				
					->setSubject($this->data['subject'])

					->setFrom($this->data['from_email'], $this->data['from_name'])

					->setTo($this->data['to'])

					->setBody($text, 'text/plain')

					->setReplyTo($this->data['from'])

					;
				
				if (is_array($attach))
				{
					foreach ($attach as $file)
					{
						$message->attach(Swift_Attachment::fromPath($file));
					}
				}
				
				$mailer = Swift_Mailer::newInstance(Swift_MailTransport::newInstance());
				
				$mailer->send($message);
				
				echo json_encode(array('status' => 'success', 'text' =>$this->data['success_text']));
			}
			
			catch (Exception $e)
			{
				error_log($e->getTraceAsString());
				
				throw new Exception($this->data['error_text']);
			}
			
			
			
		/*
			if ($_SESSION['queue']['file_path'] AND is_array($_SESSION['queue']['file_path']))
			{

				foreach ($_SESSION['queue']['file_path'] as $fp)
				{
					$message->attach(Swift_Attachment::fromPath($fp));
				}
			}
	 
	 */
			
		}
		catch (Exception $e)
		{
			echo json_encode(array('status'=>'error', 'text'=>$e->getMessage()));
		}
	}

	
	/**
	 * Formats and return HTML with form data
	 * @param array $param general parameters.
	 *	Mandatory value: $param['content']: the form to show
	 * @return string
	 */
	public function showForm($param)
	{
		$this->loadForm($param['content']);
		
		if (!$this->data)
		{
			return '<p class="text-danger">Error loading data for user form <strong>' . $param['content'] . '</strong></p>';
		}
		
		$html = '<form action="javascript:void(0)" class="form-horizontal" id="' . $param['content'] . '">';
		
		
		foreach ($this->data['elements'] as $el)
		{
			$checkClass = ($el['is_required'] ? ' required' : '') . ($el['is_email'] ? ' email' : '');
			
			$html .= '<div class="form-group" >' .
							'<div class="col-md-3 control-label"><strong>' . $el['label'] . '' . ($el['is_required'] ? '<span style="color:red"> *</span> ' : '') . '</strong></div>' .
							'<div class="col-md-9">';
			
			switch ($el['type'])
			{
				case 'text':
				default:
					$html .= '<input type="text" name="' . $el['name'] . '" data-label="' . $el['label'] . '" class="form-control' . $checkClass . '" />';
					break;
				
				case 'longtext';
					$html .= '<textarea name="' . $el['name'] . '" data-label="' . $el['label'] . '" rows="10" class="form-control' . $checkClass . '"></textarea>';
					break;
				
				case 'select':
					$html .= '<select name="' . $el['name'] . '" data-label="' . $el['label'] . '" class="form-control' . $checkClass . '">' .
						'<option></option>';
					foreach ($el['options'] as $opt)
					{
						$html .= '<option>' . $opt . '</option>';
					}
					
					$html .= '</select>';
					break;
				
				case 'upload':
					$upload[$el['name']] = array();
					
					if ($el['allowedExtensions'])
					{
						$upload[$el['name']]['allowedExtensions'] = $el['allowedExtensions'];
					}
					
					if ($el['sizeLimit'])
					{
						$upload[$el['name']]['sizeLimit'] = $el['sizeLimit'];
					}
					
					$html .= '<div class="upload_content">' .
						'<div class="upl_' . $el['name'] . '"></div>' .
						'<input type="hidden" class="filepath" name="' . $el['name'] . '" />' .
						'<div class="preview"></div>' .
						'</div>';
					break;
				
			}
			
			$html .= '</div>' .
						'</div>';
		}
		$html .= '<div class="row">' .
							'<div class="col-md-9 col-md-offset-3">' .
								'<div class="message"></div>' .
								'<input class="btn btn-success" type="submit" /> ' .
								'<input class="btn btn-default" type="reset" />' .
							'</div>' .
						'</div>' .
						'</form>';
		
		if (!$data['nojs'])
		{
			$js = file_get_contents(MOD_DIR . 'userform/userform.js');
			
			$html .= '<script>' .
				str_replace('userformID', $param['content'], $js);
			
			if (is_array($upload))
			{
				foreach($upload as $el=>$opts)
				{
					$html .= "\n upload_file('upl_" . $el . "', " . json_encode($opts). ");";
				}
			}
			$html .=  '</script>';
		}
		
	return $html;
	}
	
}

?>
