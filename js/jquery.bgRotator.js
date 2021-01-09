/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Apr 20, 2013
 * @example    $('ul.bgRotator').bgRotator();
 */
(function( $ ){
	$.fn.bgRotator = function(random) {

		var ul = this;

		function rotate(){

			var current = (ul.find('li.show') ?  ul.find('li.show') : ul.find('li:first'));

	    if ( current.length === 0 ) current = ul.find('li:first');

			var next = ((current.next().length) ? ((current.next().hasClass('show')) ? ul.find('li:first') : current.next()) : ul.find('li:first'));

			if (random){
				var sibs = current.siblings();
			    var rndNum = Math.floor(Math.random() * sibs.length );
			    next = $(sibs[rndNum]);
			}

			next.css({opacity: 0.0}).addClass('show').animate({opacity: 1.0}, 1000);

			current.animate({opacity: 0.0}, 1000).removeClass('show');
		}

		ul.find('li').css({opacity: 0.0});

		ul.find('li:first').css({opacity: 1.0});

		setInterval(function(){
			rotate();
		}, 6000);

		ul.fadeIn(1000);
	    ul.find('li').fadeIn(1000); // tweek for IE
	  };
})( jQuery );
