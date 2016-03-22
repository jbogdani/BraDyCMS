var userform = {

  upload_inputs: [],

  whatchForm: function(formId){

    $('#' + formId).on('submit', function(){

      var messageContainer = $(this).find('.message');

      messageContainer.removeClass('text-danger, text-success').html('Loading...');
      var stop = false;
      $.each($(this).find(':input:not(.btn)'), function(i, el){

        val = $(el).val();

        // Check for required
        if ($(el).hasClass('required') && !val && !stop){
          $(el).focus();
          messageContainer.html('<p class="text-danger">Field <strong><u>' + $(el).data('label') + '</u></strong> is required! Please fill all required fields to continue!</p>');
          stop = true;
        }

        // EMAIL
        if ($(el).hasClass('email') && !stop){
          var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
          if ( !emailPattern.test(val) ) {
            $(el).focus();
            stop = true;
            messageContainer.html('<p class="text-danger">Field <strong><u>' + $(el).data('label') + '</u></strong> is not a valid email address! Please enter a valid email address to continue!</p>');
          }
        }
      });

      if (!stop){
        $.post('controller.php?obj=userform_ctrl&method=process&param[]=' + formId, $(this).serialize(), function(data){
          if (data.status == 'success'){
            messageContainer.removeClass('text-danger').addClass('text-success').html('<i class="icon ion-checkmark"></i>');
            $('#' + formId + ' :input:not(.btn)').val('');
          } else{
            messageContainer.removeClass('text-success').addClass('text-danger');
          }
          messageContainer.html(data.text);

        }, 'json');
      }
    });
  },

  upload_file: function(formId, elclass, opts){
    if (!opts) opts = {};
    var el = $('#' + formId).find('.' + elclass),
        input = el.siblings('input'),
        preview = el.siblings('.preview');
    new qq.FileUploader({
      element: el.get(0),
      action: 'controller.php?obj=utils&method=upload&param[]=./tmp',
      allowedExtensions: opts.allowedExtensions ? opts.allowedExtensions : [],
      sizeLimit: opts.sizeLimit ? opts.sizeLimit : 0,
      onComplete: function(id, filename, responseJSON){
        if (responseJSON.success) {
          var filePath = responseJSON.path + responseJSON.filename + '.' + responseJSON.ext;
          input.val(filePath);
          preview.html('<img src="' + filePath + '" class="img-responsive" />');
        }
      }
    });

  }
};
