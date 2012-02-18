/**
 * @url: http://jqueryui.com/demos/autocomplete/#combobox
 * Modified by Jbogdani:
 * @example: html: <input type="text" id="elementid" /> <datalist id="elementid-dl"><option value="ciao">Ciao</option></datalist> 
 */
(function( $ ) {
	$.widget( "ui.combobox", {
		_create: function() {
			var self = this,
				datalist = $('#' + this.element.attr('id') + '-dl');
				

			var input = $(self.element)
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: function( request, response ) {
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
						response( datalist.children( "option" ).map(function() {
							var text = $(this).text();
							if ( this.value && ( !request.term || matcher.test(text) ) )
								return {
									label: text.replace(
										new RegExp(
											"(?![^&;]+;)(?!<[^<>]*)(" +
											$.ui.autocomplete.escapeRegex(request.term) +
											")(?![^<>]*>)(?![^&;]+;)", "gi"
										), "<strong>$1</strong>" ),
									value: text,
									option: this
								};
						}) );
					},
					select: function( event, ui ) {
						ui.item.option.selected = true;
						self._trigger( "selected", event, {
							item: ui.item.option
						});
					}
				});

			input.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
			};

			this.button = $( "<button type='button'><span class='ui-icon ui-icon-triangle-2-n-s'></span></button>" )
				.attr( "tabIndex", -1 )
				.insertAfter( input )
				.removeClass( "ui-corner-all" )
				.addClass( "ui-corner-right ui-button-icon" )
				.click(function() {
					// close if already visible
					if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
						input.autocomplete( "close" );
						return;
					}

					// work around a bug (likely same cause as #5265)
					$( this ).blur();

					// pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
					input.focus();
				})
				.position({
					'my' : 'right center',
					'at' : 'right center',
					'of' : input
				});
		}
	});
})( jQuery );