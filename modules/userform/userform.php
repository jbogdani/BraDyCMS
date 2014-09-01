<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since				Aug 29, 2013
 */


class userform_ctrl extends Controller
{
	private $data;
	
	
	public function createNew()
	{
		$name = $this->get['param'][0] . '.json';
    
    try
    {
      if (file_exists('./sites/default/modules/userforms/' . $name))
      {
        throw new Exception(tr::get('error_formid_exixts') );
      }

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
            'label' => '',
            'placeholder' => '',
            'type' => 'text|longtext|select',
            'options' => 'if type is select options array is required',
            'is_required' => 'true|false',
            'email' => 'true|false',
          )
          )
        );


      if (!is_dir('./sites/default/modules/userforms'))
      {
        @mkdir('./sites/default/modules/userforms', 0777, true);
      }

      if (utils::write_in_file('./sites/default/modules/userforms/' . $name, $text, 'json'))
      {
        $resp = array('status' => 'success', 'text' => tr::get('ok_form_config_saved') );
      }
      else
      {
        throw new Exception(tr::get('error_form_config_not_saved') );
      }
    }
    catch (Exception $e)
    {
      $resp = array('status' => 'error', 'text' => $e->getMessage() );
    }
    
    echo json_encode($resp);
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
      
      if ($data['customsubject'])
      {
        $this->data['subject'] = $data['customsubject'];
        unset($data['customsubject']);
      }

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
        
        if ($el['email'] && $el['email'] !== 'false' && !filter_var($data[$el['name']], FILTER_VALIDATE_EMAIL))
				{
					$error .= '<p>' . tr::sget('invalid_email', $el['name']) . '</p>';
          continue;
				}
        
				if ($el['type'] === 'upload')
				{
					if (file_exists($data[$el['name']]))
					{
						$attach[] = $data[$el['name']];
					}
				}
				else
				{
					$text .= "\n" 
            . ($el['label'] ? $el['label'] . ': ' : ($el['placeholder'] ? $el['placeholder'] . ': ' : '')) 
            . htmlentities($data[$el['name']]);
				}
			}

			if ($error)
			{
				throw new Exception($error);
			}
			
			try
			{
        $message = new PHPMailer();
        $message->setFrom($this->data['from_email'], $this->data['from_name']);
        $message->addReplyTo($this->data['from']);
        $message->addAddress($this->data['to']);
        $message->Subject = $this->data['subject'];
        $message->Body = $text;
        
				if (is_array($attach))
				{
					foreach ($attach as $file)
					{
            $message->addAttachment($file);
					}
				}
				
        $message->send();
				
				echo json_encode(array('status' => 'success', 'text' => $this->data['success_text']));
			}
      
      catch (phpmailerException $e)
      {
        error_log($e->getTraceAsString());
				
				throw new Exception($this->data['error_text']);
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
   *  Optional value: subject, overrites the config subject
	 * @return string
	 */
	public function showForm($param)
	{
		$this->loadForm($param['content']);
		  
    if ($param['inline'])
		{
			$form_class = 'form-inline';
			$label_class = 'col-md-3';
			$input_class = 'col-md-9';
			$buttons_class = 'col-md-offset-3';
		}
		
		if (!$this->data)
		{
			return '<p class="text-danger">Error loading data for user form <strong>' . $param['content'] . '</strong></p>';
		}
		
		$html = '<form action="javascript:void(0)" class="' . $form_class . '" id="' . $param['content'] . '">';
		
    if ($param['subject'])
    {
      $html .= '<input type="hidden" name="customsubject" value="' . $param['subject'] . '" />';
    }
		
		foreach ($this->data['elements'] as $el)
		{
      
			$checkClass = ($el['is_required'] ? ' required' : '') . ($el['is_email'] ? ' email' : '');
			
			$html .= '<div class="form-group">' .
							( $el['label'] ? '<div class="' . $label_class . ' control-label"><strong>' . $el['label'] . '' . ($el['is_required'] ? '<span style="color:red"> *</span> ' : '') . '</strong></div>' : '' ) .
							'<div class="' . $input_class . '">';
      
			switch ($el['type'])
			{
				case 'text':
				default:
					$html .= '<input type="text" ' . 
            ( $el['placeholder'] ? ' placeholder="' . $el['placeholder'] . '"' : '' ) .
            ' name="' . $el['name'] . '" data-label="' . $el['label'] . '" class="form-control' . $checkClass . '" />';
					break;
				
				case 'longtext';
					$html .= '<textarea ' .
            ( $el['placeholder'] ? ' placeholder="' . $el['placeholder'] . '"' : '' ) .
            'name="' . $el['name'] . '" data-label="' . $el['label'] . '" rows="10" class="form-control' . $checkClass . '"></textarea>';
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
		$html .= '<div class="clearfix">' .
							'<div class="' . $input_class . ' ' . $buttons_class . '">' .
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
