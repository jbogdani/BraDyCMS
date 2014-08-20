
/**
 * userformID id the id of the form. This will be automatically changed to match the form id when the plugin is used with javascript enabled (default)
 */
$('#userformID').on('submit', function(){
	
	var messageContainer = $(this).find('.message');

		messageContainer.removeClass('text-danger, text-success').html('<img src="./img/spinner.gif" alt="loading..." /> Loading...');
		var stop = false;
		$.each($(this).find(':input:not(.btn)'), function(i, el){
			
			val = $(el).val();
			
			// REQUIRED
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
			$.post('controller.php?obj=userform_ctrl&method=process&param[]=userformID', $(this).serialize(), function(data){
				if (data.status == 'success'){
					messageContainer.removeClass('text-danger').addClass('text-success').html('<i class="icon ion-checkmark"></i>');

					$('#userformID :input:not(.btn)').val('');
				} else{
					messageContainer.removeClass('text-success').addClass('text-danger');
				}
				messageContainer.html(data.text);

			}, 'json')
		}
	});



function upload_file(el, opts){
	if (!opts) opts = {};
	
		// load CSS
	if($('head').find('link[href="./css/fineuploader-3.8.2.css"]').length < 1){
		$('head').append( $('<link />').attr({'type':'text/css', 'rel':'stylesheet', 'href':'./css/fineuploader-3.8.2.css'}) );
	}
		
	if (typeof qq == 'undefined'){
		// LOAD JS
		$.getScript('./js/jquery.fineuploader-3.8.2.min.js', function(){
			upload_file(el, opts);
		}).fail(
			function(jqxhr, settings, exception) {
				console.log('fineuploader', exception);
			});
	} else {
		el = $('#userformID').find('.' + el);
		var input = el.siblings('input');
		var preview = el.siblings('.preview');
		
		
		el.fineUploader({
			request: {
				endpoint: 'controller.php?obj=utils&method=upload&param[]=./tmp'
			},
				
			validation:{
				allowedExtensions: opts.allowedExtensions,
				sizeLimit: opts.sizeLimit
			}
		}).on('complete', function(event, id, fileName, responseJSON){
			if (responseJSON.success) {
				var filePath = responseJSON.path + responseJSON.filename + '.' + responseJSON.ext;
				input.val(filePath);
				preview.html('<img src="' + filePath + '" class="img-responsive" />')
			}
		});
	}
}