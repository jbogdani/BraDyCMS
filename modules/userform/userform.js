var userform = {

  upload_inputs: [],

  whatchForm: function(formId){

    $('#' + formId).on('submit', function(){

      var messageContainer = $(this).find('.message'),
        name = $(this).data('name');

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
        $.post('controller.php?obj=userform_ctrl&method=process&param[]=' + name, $(this).serialize(), function(data){
          if (data.status == 'success'){
            messageContainer.removeClass('text-danger').addClass('text-success').html('<i class="fa fa-check"></i>');
            $('#' + formId + ' :input:not(.btn)').val('');
          } else{
            messageContainer.removeClass('text-success').addClass('text-danger');
          }
          messageContainer.html(data.text);

        }, 'json');
      }
    });

    $('#' + formId).on('reset', function(){
      $(this).find('.message').html('');
    });
  },

  upload_file: function(formId, elclass, opts){
    if($('#qq-template').length < 1){
      var script = $('<script>').attr('type', 'text/template').attr('id', 'qq-template').appendTo('body');
      script.html('<div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">' +
      '    <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">' +
      '        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>' +
      '    </div>' +
      '    <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>' +
      '        <span class="qq-upload-drop-area-text-selector"></span>' +
      '    </div>' +
      '    <div class="qq-upload-button-selector qq-upload-button">' +
      '        <div>Upload a file</div>' +
      '    </div>' +
      '        <span class="qq-drop-processing-selector qq-drop-processing">' +
      '            <span>Processing dropped files...</span>' +
      '            <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>' +
      '        </span>' +
      '    <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">' +
      '        <li>' +
      '            <div class="qq-progress-bar-container-selector">' +
      '                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>' +
      '            </div>' +
      '            <span class="qq-upload-spinner-selector qq-upload-spinner"></span>' +
      '            <span class="qq-upload-file-selector qq-upload-file"></span>' +
      '            <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>' +
      '            <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">' +
      '            <span class="qq-upload-size-selector qq-upload-size"></span>' +
      '            <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>' +
      '            <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>' +
      '            <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>' +
      '            <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>' +
      '        </li>' +
      '    </ul>' +
      '' +
      '    <dialog class="qq-alert-dialog-selector">' +
      '        <div class="qq-dialog-message-selector"></div>' +
      '        <div class="qq-dialog-buttons">' +
      '            <button type="button" class="qq-cancel-button-selector">Close</button>' +
      '        </div>' +
      '    </dialog>' +
      '' +
      '    <dialog class="qq-confirm-dialog-selector">' +
      '        <div class="qq-dialog-message-selector"></div>' +
      '        <div class="qq-dialog-buttons">' +
      '            <button type="button" class="qq-cancel-button-selector">No</button>' +
      '            <button type="button" class="qq-ok-button-selector">Yes</button>' +
      '        </div>' +
      '    </dialog>' +
      '' +
      '    <dialog class="qq-prompt-dialog-selector">' +
      '        <div class="qq-dialog-message-selector"></div>' +
      '        <input type="text">' +
      '        <div class="qq-dialog-buttons">' +
      '            <button type="button" class="qq-cancel-button-selector">Cancel</button>' +
      '            <button type="button" class="qq-ok-button-selector">Ok</button>' +
      '        </div>' +
      '    </dialog>' +
      '</div>');
    }

    if (!opts) opts = {};
    var el = $('#' + formId).find('.' + elclass),
        input = el.siblings('input'),
        preview = el.siblings('.preview');

    new qq.FineUploader({
      request: {
        endpoint: 'controller.php?obj=utils&method=upload&param[]=./tmp'
      },
      element: el.get(0),
      validation:{
        allowedExtensions: opts.allowedExtensions ? opts.allowedExtensions : [],
        sizeLimit: opts.sizeLimit ? opts.sizeLimit : 0
      },
      callbacks:{
        onComplete: function(id, filename, responseJSON){
          if (responseJSON.success) {
            var filePath = responseJSON.path + responseJSON.filename + '.' + responseJSON.ext;
            input.val(filePath);
            preview.html('<img src="' + filePath + '" class="img-responsive" />');
          }
        }
      }
    });
  }
};
