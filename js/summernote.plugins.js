/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net, Julian Bogdani <jbogdani@gmail.com>
 * @license			See file LICENSE distributed with this code
 * @since				Jan 2, 2015
 */

(function ($) {
  // template, editor
  var tmpl = $.summernote.renderer.getTemplate();
  var editor = $.summernote.eventHandler.getEditor();

  // add plugin
  $.summernote.addPlugin({
    name: 'customTag', // name of plugin
    buttons: { // buttons
      customTag: function () {
        
        var list = [
//          {value: 'addThis', text: 'addThis'},
//          {value: 'cl_gallery', text: 'Gallery'},
//          {value: 'disqus', text: 'disqus'},
          {value: 'download', text: 'download'},
//          {value: 'fb_comments', text: 'fb_comments'},
//          {value: 'fb_like', text: 'fb_like'},
//          {value: 'fb_like_box', text: 'fb_like_box'},
//          {value: 'fb_send', text: 'fb_send'},
          {value: 'fig', text: 'fig'},
//          {value: 'flash', text: 'flash'},
          {value: 'gallery', text: 'gallery'},
          {value: 'link', text: 'link'},
          {value: 'map', text: 'map'},
//          {value: 'prezi', text: 'prezi'},
//          {value: 'skype', text: 'skype'},
//          {value: 'soundcloud', text: 'soundcloud'},
//          {value: 'twitter', text: 'twitter'},
          {value: 'userform', text: 'userform'},
          {value: 'vimeo', text: 'vimeo'},
          {value: 'youtube', text: 'youtube'},
        ];
        
        var dropdown = '<ul class="dropdown-menu">';
        $.each(list, function(i, el){
          dropdown += '<li><a data-event="customTag" href="javascript:void(0);" data-value="' + el.value +'">' + el.text + '</a></li>';
        });
        dropdown += '</ul>';

        return tmpl.iconButton('fa fa-bolt', {
          title: 'Custom tags',
          hide: true,
          dropdown : dropdown
        });
      }

    },

    events: { // events
      customTag: function (event, editor, layoutInfo, value) {
        // Get current editable node
        var $editable = layoutInfo.editable();
        editor.insertText($editable, ' [[' + value + ']]editme[[/' + value + ']] ');
      }
    }
  });
})(jQuery);