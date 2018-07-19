<?php
/**
* @author      Julian Bogdani <jbogdani@gmail.com>
* @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
* @license      MIT, See LICENSE file
* @since        Aug 29, 2013
*/


class userform_ctrl extends Controller
{
  private $data;


  public function createNew()
  {
    $name = $this->get['param'][0] . '.json';

    try {

      if (file_exists('./sites/default/modules/userforms/' . $name)) {

        throw new Exception(tr::get('error_formid_exixts') );
      }

      $text = [
        'to'=>'',
        'from_email'=>'',
        'from_name'=>'',
        'subject'=>'',
        'success_text'=>'',
        'error_text'=>'',
        'to_user' => '',
        'confirm_text' => '',
        'inline' => 'true|false',
        'smtp_host' => 'smtp1.example.com;smtp2.example.com',
        'smtp_auth' => true,
        'smtp_username' => 'username here',
        'smtp_password' => 'password here',
        'smtp_secure' => 'tls|ssl',
        'smtp_port' =>'587 or other',

        'elements'=> [
          [
            'name' => '',
            'label' => '',
            'placeholder' => '',
            'type' => 'text|longtext|select',
            'options' => 'if type is select options array is required',
            'is_required' => 'true|false',
            'email' => 'true|false',
          ]
        ]
      ];


      if (!is_dir('./sites/default/modules/userforms')) {
        @mkdir('./sites/default/modules/userforms', 0777, true);
      }

      if (utils::write_in_file('./sites/default/modules/userforms/' . $name, $text, 'json')) {

        $resp = ['status' => 'success', 'text' => tr::get('ok_form_config_saved') ];

      } else {

        throw new Exception(tr::get('error_form_config_not_saved') );
      }
    } catch (Exception $e) {

      $resp = ['status' => 'error', 'text' => $e->getMessage() ];
    }

    echo json_encode($resp);
  }

  public function save()
  {
    if (utils::write_in_file('./sites/default/modules/userforms/' . $this->get['param'][0], $this->post['data'], 'json')) {

      echo json_encode( ['status' => 'success', 'text' => tr::get('ok_form_config_saved') ] );

    } else {

      echo json_encode( ['status' => 'error', 'text' => tr::get('error_form_config_not_saved') ] );

    }
  }

  public function erase()
  {
    if (unlink('./sites/default/modules/userforms/' . $this->get['param'][0])) {

      echo json_encode([ 'status' => 'success', 'text' => tr::get('ok_form_deleted')] );

    } else {

      echo json_encode( ['status' => 'error', 'text' => tr::get('error_form_ot_deleted') ] );
    }
  }

  public function edit_form()
  {
    $form = $this->get['param'][0];

    $content = file_get_contents('./sites/default/modules/userforms/' . $form);
    $this->render('userform', 'edit_form', [
      'form' => $form,
      'content'=> $content
    ]);
  }


  public function view()
  {
    $this->render('userform', 'list', ['forms' => utils::dirContent('./sites/default/modules/userforms') ] );
  }

  /**
  * Loads json file with form data in $this->data
  */
  private function loadForm($form)
  {
    if (!$this->data) {

      $filename = './sites/default/modules/userforms/' . $form . '.json';

      if (file_exists($filename)) {

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

    try {

      $error = [];

      // Get form id
      $form = $this->get['param'][0];

      // Load form data
      $this->loadForm($form);

      // load user data
      $data = $this->post;

      // change user subject if custom subject is set
      if ($data['customsubject']) {

        $this->data['subject'] = $data['customsubject'];
        unset($data['customsubject']);
      }

      // Validate reCAPTCHA
      if (reCAPTCHA::isProtected()) {

        try {

          reCAPTCHA::validate($this->post['g-recaptcha-response']);

        } catch(Exception $e) {

          array_push($error, tr::get('captcha_error'));

        }
      }

      // Placeholders & values array
      $replacables = [];

      /**
      * Check for error in POST data
      */
      foreach ($this->data['elements'] as $el) {

        // Overwrite default subject using field with name subject
        if ($el['name'] === 'subject' && !empty($data[$el['name']]) ) {
          $data['subject'] = $data[$el['name']];
        }

        $replacables['%' . $el['name'] . '%'] = $data[$el['name']];

        // Check required element
        if ($el['is_required'] && !$data[$el['name']]) {

          array_push($error, tr::sget('missing_required', $el['name']));
        }

        if ($el['email'] && $el['email'] !== 'false' && !filter_var($data[$el['name']], FILTER_VALIDATE_EMAIL)) {
          array_push($error, tr::sget('invalid_email', $el['name']));
          continue;
        }

        if ($el['type'] === 'upload') {

          if (file_exists($data[$el['name']])) {

            $attach[] = $data[$el['name']];

          }

        } else {

          $text .= "\n"
          . ($el['label'] ? $el['label'] . ': ' : ($el['placeholder'] ? $el['placeholder'] . ': ' : ''))
          . htmlentities($data[$el['name']]);
        }
      }

      if (!empty($error)) {

        throw new Exception(implode("<br>", $error));
      }

      // check if a copy to user should be send, identical or with a custom text
      if ($this->data['to_user'] && $data[$this->data['to_user']] && filter_var($data[$this->data['to_user']], FILTER_VALIDATE_EMAIL) ) {

        $to_user = $data[$this->data['to_user']];

        if ($this->data['confirm_text'] && is_array($replacables)) {
          $confirm_text = str_replace(
            array_keys($replacables),
            array_values($replacables),
            $this->data['confirm_text']
          );
        }
      }

      try {

        $message = new PHPMailer();

        if ($this->data['smtp_host'] && $this->data['smtp_username'] && $this->data['smtp_password'] && $this->data['smtp_port']) {
          $message->isSMTP();
          $message->Host = $this->data['smtp_host'];
          $message->SMTPAuth = $this->data['smtp_auth'];
          $message->Username = $this->data['smtp_username'];
          $message->Password = $this->data['smtp_password'];
          $message->SMTPSecure = $this->data['smtp_secure'];
          $message->Port = $this->data['smtp_port'];
        }

        $message->setFrom($this->data['from_email'], $this->data['from_name']);
        $message->addReplyTo($this->data['from_email']);
        $message->addAddress($this->data['to']);

        // Send a copy to the user (no custom text):
        if ( $to_user && !$confirm_text) {
          $message->addAddress($to_user);
        }
        $message->Subject = $this->data['subject'];
        $message->Body = $text;

        if (is_array($attach)) {

          foreach ($attach as $file) {

            $message->addAttachment($file);
          }
        }

        if (!$message->send()) {

          error_log($message->ErrorInfo);
          throw new Exception("Error sending email to . " . $this->data['to']);
        }

        if ($to_user && $confirm_text) {

          $um = new PHPMailer();

          if ($this->data['smtp_host'] && $this->data['smtp_username'] && $this->data['smtp_password'] && $this->data['smtp_port']) {
            $um->isSMTP();
            $um->Host = $this->data['smtp_host'];
            $um->SMTPAuth = $this->data['smtp_auth'];
            $um->Username = $this->data['smtp_username'];
            $um->Password = $this->data['smtp_password'];
            $um->SMTPSecure = $this->data['smtp_secure'];
            $um->Port = $this->data['smtp_port'];
          }

          $um->setFrom($this->data['from_email'], $this->data['from_name']);
          $um->addReplyTo($this->data['from']);
          $um->addAddress($to_user);
          $um->Subject = $this->data['subject'];
          $um->Body = $confirm_text;

          if (!$um->send()) {
            throw new Exception("Error sending email to . " . $this->data['to']);
          }
        }

        echo json_encode( ['status' => 'success', 'text' => $this->data['success_text'] ] );

      } catch (phpmailerException $e) {

        error_log($e->getTraceAsString());

        throw new Exception($this->data['error_text']);

      } catch (Exception $e) {

        error_log($e->getTraceAsString());

        throw new Exception($this->data['error_text']);
      }

    } catch (Exception $e) {

      echo json_encode( ['status'=>'error', 'text' => $e->getMessage() ] );
    }
  }


  /**
  * Formats and return HTML with form data
  * @param array $param general parameters.
  *  Mandatory value: $param['content']: the form to show
  *  Optional value: subject, overrites the config subject
  * @param object Instance of the main Out object
  * @return string
  */
  public function showForm($param, Out $out)
  {
    $uid = uniqid( 'form' );
    $this->loadForm($param['content']);

    if ($param['inline'] || $this->data['inline']) {
      $form_class = 'form-inline';
    }

    if (!$this->data) {
      return '<p class="text-danger">Error loading data for user form <strong>' . $param['content'] . '</strong></p>';
    }

    $html = '<div class="userform ' . $param['content'] . '">' .
      '<form action="javascript:void(0)" '
        . 'data-name="' . $param['content'] . '"'
        . ($form_class ? 'class="' . $form_class . '"' : '')
        . ' id="' . $uid . '">';

    if ($param['subject']) {
      $html .= '<input type="hidden" name="customsubject" value="' . $param['subject'] . '" />';
    }

    foreach ($this->data['elements'] as $el) {

      $checkClass = ($el['is_required'] ? ' required' : '') . ($el['is_email'] ? ' email' : '');

      $html .= '<div class="form-group ' . $el['name'] . '">';

      if ($el['label']) {

        $html .= '<label class="' . $label_class . ' control-label">'
          . $el['label']
          . ($el['is_required'] ? '<span style="color:red"> *</span> ' : '')
        . '</label>';
      }

      $html .= '<div class="' . $input_class . '">';

      switch ($el['type']) {

        case 'text':
        default:
          $html .= '<input type="text" '
            . ( $el['placeholder'] ? ' placeholder="' . $el['placeholder'] . '"' : '' )
            . ' name="' . $el['name'] . '" '
            . 'data-label="' . $el['label'] . '" class="form-control' . $checkClass
            . ($el['type'] === 'date' ? ' datepicker' : '') . '" />';
            if($el['type'] === 'date') {
              $load_date = true;
            }
        break;

        case 'longtext';
          $html .= '<textarea ' .
          ( $el['placeholder'] ? ' placeholder="' . $el['placeholder'] . '"' : '' ) .
          'name="' . $el['name'] . '" data-label="' . $el['label'] . '" rows="10" class="form-control' . $checkClass . '"></textarea>';
        break;

        case 'select':
          $html .= '<select name="' . $el['name'] . '" data-label="' . $el['label'] . '" class="form-control' . $checkClass . '">' .
          '<option></option>';
          foreach ($el['options'] as $opt) {
            $html .= '<option>' . $opt . '</option>';
          }

          $html .= '</select>';
        break;

        case 'upload':
          $upload[$el['name']] = [];

          if ($el['allowedExtensions']) {
            $upload[$el['name']]['allowedExtensions'] = $el['allowedExtensions'];
          }

          if ($el['sizeLimit']) {
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
    $html .= '<div class="clearfix">';

    // Show Google reCAPTCHA if settings are present
    $sitekey = cfg::get('grc_sitekey');

    if ($sitekey) {

      if ($_SESSION['adm_lang']) {
        $captcha_lang = "'hl': '" . $_SESSION['adm_lang'] . "',";
      }

      $html .= <<<EOD
<div class="g-recaptcha" data-sitekey="{$sitekey}"></div>
<script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit"></script>
<script>
if (typeof recaptchaCallback !== 'function') {
  var called = false;
  function recaptchaCallback(){
    if (called) return;
    $('.g-recaptcha').each(function(i, el){
      grecaptcha.render(el, {
        {$captcha_lang}
        'sitekey' : $(el).data('sitekey')
      });
    });
    called = true;
  }
}
</script>
EOD;

    }
    $html .= '<div class="' . $input_class . ' ' . $buttons_class . '">' .
        '<div class="message"></div>' .
        '<input class="btn btn-success" type="submit" /> ' .
        '<input class="btn btn-default" type="reset" />' .
      '</div>' .
    '</div>' .
  '</form>' .
  '</div>';

    if (!$data['nojs']) {
      $js = [];

      $out->setQueue('modules', "\n" . '<script src="' . $out->link2() .  MOD_DIR . 'userform/userform.js'. '"></script>', true);
      array_push($js, "userform.whatchForm('" . $uid . "');");

      if (is_array($upload)) {
        $out->setQueue('modules', "\n" . '<link type="text/css" rel="stylesheet" href="./bower_components/fine-uploader/dist/fine-uploader.min.css" />', true);
        $out->setQueue('modules', "\n" . '<script src="./bower_components/fine-uploader/dist/fine-uploader.min.js"></script>', true);

        foreach($upload as $el=>$opts) {
          array_push($js, "userform.upload_file('" . $param['content']. "', 'upl_" . $el . "', " . json_encode($opts). ");");
        }
      }

      if ($load_date) {

        $out->setQueue('modules', "\n" . '<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.min.css" />', true);
        $out->setQueue('modules', "\n" . '<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>', true);
        $out->setQueue('modules', "\n" . '<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/locales/bootstrap-datepicker.' . $out->user_lang . '.min.js"></script>', true);
        $out->setQueue('modules', "\n" . '<script>$(document).ready(function(){$(\'.datepicker\').datepicker({language:\'' . $out->user_lang . '\'});});</script>', true);
      }
      $out->setQueue('modules', "\n" . '<script>$(document).ready(function(){' . implode("\n", $js) . '});</script>', true);
    }

    return $html;
  }
}

?>
