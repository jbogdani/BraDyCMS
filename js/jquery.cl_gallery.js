/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2013
 * @license			All rights reserved
 * @since			Apr 9, 2013
 * @example			$('ul.cl_gallery').cl_gallery();
 * 
 * Transforms a ul to a image gallery
 * <ul data-rel="sth" class="a b c noimg_gallery">
 * 	<li data-img="img_src" data-thumb="thumb_img_src">caption text</li>
 * 	...
 * </ul>
 * 
 * to
 * 
 * <ul class="cl_gallery a b c">
 * 	<li>
 * 		<a href="big_img_src" class="fancybox" rel="sth" title="caption text">
 * 			<img src="thumb_img_src" alt="caption text" />
 *		</a>
 *		<div class="caption">caption text</div>
 * 	</li>
 * 	...
 * </ul>
 */
(function( $ ){
	$.fn.cl_gallery = function() {
		var ul = this;
		if (!ul.is(':visible')){
			return;
		}
		
		ul.removeClass('cl_gallery');
		
		var tmp = $('<ul />').addClass(ul.attr('class'));
		
		$.each(ul.find('li'), function(i, el){
			var text = $(el).text()
				oSrc = $(el).data('img')
				thSrc = $(el).data('thumb')
				rel = ul.data('rel')
				id = $(el).data('id');
			
			tmp.append('<li data-id="' + id + '">' +
					'<a href="' + oSrc + '" class="fancybox" ' + (rel ? ' rel="' + rel + '"' : '') + ' title="' + text + '">' +
					'<img src="' + thSrc + '" alt="' + text + '" />' +
					'</a>' +
					'<div class="caption">' + text + '</div>' +
					'</li>');
		});
		
		ul.replaceWith(tmp);
	  };
})( jQuery );