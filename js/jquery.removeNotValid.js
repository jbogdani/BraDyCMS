/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Nov 15, 2013
 * @example			$('input.nospaces').removeNotValid();
 * 
 * Adds an onkeyup listener to an input or testarea and removes the following 
 * charactes from it's value: -, +, ., white space (space or tab)
 * The list of the characters to removed can be customised by adding a valid 
 * RegExp as function parameter:
 * $('input.nospaces').removeNotValid('\\s'); // olny whitespaces will be removed
 */

(function($){
  $.fn.removeNotValid = function(regex){
    $(this).on('keyup', function(){
      
      if (!regex){
        var regex = '-+=_\.\\s';
      }
      
      var val = $(this).val();
      
      $(this).val(val.replace(new RegExp('([' + regex + '])'), ''));
    });
  };
})(jQuery);