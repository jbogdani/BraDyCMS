/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 */

var _ignorehash;

var admin = {

  wysiwyg: {
    add: function(id, opts){

      opts = opts || {};

      var default_opts = {
        selector: '#' + id,
        content_css: './sites/default/css/styles.css',
        plugins: [
         "advlist autolink link image lists charmap preview hr anchor",
         "searchreplace wordcount visualblocks visualchars code fullscreen media nonbreaking",
         "save table contextmenu directionality paste textcolor template imagetools"
        ],
        paste_as_text: true,
        toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image anchor | print preview media fullpage | forecolor backcolor | code",
        image_advtab: true,
        removed_menuitems: 'newdocument',
        templates: [
          {title: 'download', content: '[[download]]editme[[/download]]'},
          {title: 'fig', content: '[[fig path="./sites/default/images/"]]caption[[/fig]]'},
          {title: 'gallery', content: '[[gallery]]editme[[/gallery]]'},
          {title: 'link', content: '[[link]]editme[[/link]]'},
          {title: 'map', content: '[[map]]editme[[/map]]'},
          {title: 'userform', content: '[[userform]]editme[[/userform]]'},
          {title: 'vimeo', content: '[[vimeo]]editme[[/vimeo]]'},
          {title: 'youtube', content: '[[youtube]]editme[[/youtube]]'}
        ]
      };
      $.extend(default_opts, opts);

      tinymce.init(default_opts);

    },

    preSave: function(){

    },

    setValue: function(id, text){
      tinymce.get(id).setContent(text);
    }
  },

  removeNotValid: function(input, opts){
    /**
     * available opts:
     *  replace: string
     *  toLower: boolean
     *  permit: array
     */
     opts = opts || {};

    var pattern = "_,=\.\\s\\?'\"\\-\$:;’°";

    if (opts.permit){
      $.each(opts.permit, function(i, item){
        pattern = pattern.replace(item, '');
      });
    }

    input.on('keyup', function(e){
      if (
        //http://www.cambiaresearch.com/articles/15/javascript-char-codes-key-codes
        e.keyCode === 13  || //enter
        e.keyCode === 37  || //left-arrow
        e.keyCode === 38  || //up-arrow
        e.keyCode === 39  ||  //right-arrow
        e.keyCode === 40  ||  //down-arrow
        e.keyCode === 8  ||  //backspace
        e.keyCode === 46  ||  //delete
        e.keyCode === 16  ||  //shift
        e.keyCode === 17  ||  //CTRL
        e.keyCode === 18  ||  //alt
        e.keyCode === 91  ||  //left window
        e.keyCode === 93  //select key
        ){
        return;
      }

      var val = $(this).val();
      if (val && val !== ''){
        var newVal = val
          .replace(new RegExp("([" + pattern + "])", "gi"), opts.replace ? opts.replace : '');
        newVal = opts.toLower ? newVal.toLowerCase() : newVal;
        if (opts.replace){
          newVal = newVal.replace(new RegExp(opts.replace + '+', "gi"), opts.replace)
            .replace(new RegExp(opts.replace + '$'), '')
            .replace(new RegExp('^' + opts.replace), '');
        }
        $(this).val(newVal);
      }
    });

  },

  /**
   *
   * opts.sizeLimit
   * opts.minSizeLimit
   * opts.allowedExtensions
   * opts.loaded(id, filename, responseJSON)
   */
  upload: function(element, action, opts){
    opts = opts || {};

    new qq.FileUploader({
      element: element,
      action: action,
      allowedExtensions: opts.allowedExtensions ? opts.allowedExtensions : [],
      sizeLimit: opts.sizeLimit ? opts.sizeLimit : 0,
      minSizeLimit: opts.minSizeLimit ? opts.minSizeLimit : 0,
      onComplete: function(id, filename, responseJSON){
        return opts.loaded ? opts.loaded(id, filename, responseJSON) : false;
      }
    });
  },


		/**
		 * Filters elements with class search-container by text contained in search-text
		 * @param string filter	string to use as filter
		 * @param obj container jQuery element to look in
		 * @param boolean parent_class if false search-container and search-text are the same element, otherwise container contains element
		 * @returns {undefined}
		 */
		filterList: function(filter, container, parent_class){

			if (filter && filter !== '') {
				if (parent_class){
					container.find('.search-text:not(:Contains(' + filter + '))').parents('.search-container').fadeOut();
					container.find('.search-text:Contains(' + filter + ')').parents('.search-container').fadeOut();

				} else {

					container.find('.search-text:not(:Contains(' + filter + '))').fadeOut();
					container.find('.search-text:Contains(' + filter + ')').fadeIn();
				}

			} else {
		     container.find( '.' + (parent_class ? 'search-container' : 'search-text') ).fadeIn();
			}
		},

		/**
		 *
		 * @param opts
		 * 		title
		 * 		html
		 * 		loaded
		 * 		buttons: []
		 * 			text
		 * 			href
		 * 			click
		 * 			action: 'close'
		 * 			addclass
     *      glyphicon
		 */
		dialog: function(opts){
			if ($('#modal').length > 0){
				$('#modal').modal('hide');
			}

			var dialog =  $('<div />').attr('id', 'modal').addClass('modal fade').append(
						'<div class="modal-dialog">' +
							'<div class="modal-content">' +
								(opts.title ? '<div class="modal-header"><h4>' + opts. title + '</h4></div>' : '') +
							'</div>' +
						'</div>'

						).appendTo('body');

			if (opts.width){
					dialog.css({
						width:opts.width,
						'margin-left':'-' + (opts.width/2) + 'px'
					});
			}

			var body = $('<div />').addClass('modal-body').appendTo(dialog.find('div.modal-content')),
				URLstring = 'controller.php?';

			if (opts.buttons && typeof opts.buttons == 'object'){

				var footer = $('<div />').addClass('modal-footer').appendTo(dialog.find('div.modal-content'));

				$.each(opts.buttons, function(index, but){
					var a = $('<a />')
            .addClass('btn'+ (but.addclass ? ' ' + but.addclass : ' btn-primary'))
            .html((but.glyphicon ? '<i class="glyphicon glyphicon-' + but.glyphicon + '"></i> ' : '') + but.text);

					if (but.href){
						a.attr('href', but.href);
					}

					if (but.click){
						if (but.click === 'close'){
							a.attr('data-dismiss', 'modal');
						} else{
							a.click(function(){ but.click(dialog); });
						}
					}

					if (but.action === 'close'){
						a.attr('data-dismiss', 'modal');
					}

					a.appendTo(footer);
				});
			}

			dialog.modal({'keyboard':true});

			dialog.on('hidden.bs.modal', function(){
				$('body').removeClass('modal-open');
				dialog.remove();
			});


			if (opts.html){
				body.html(opts.html);

				if (opts.loaded){
					opts.loaded(body);
				}

			} else if (opts.obj && opts.method){
				dialog.find('.modal-body').html('<i class="icon ion-load-c ion-loading-c" style="font-size: 3em;"></i>');
				$.ajax({
	        		'type': opts.post ? 'POST' : 'GET',
	        		'url': 'controller.php?obj=' + opts.obj + '&method=' + opts.method + (opts.param ? '&param[]=' + opts.param.join('&param[]=') : '' ),
	        		'data': opts.post
	        	})
	        	.done(function(data){
	        		dialog.find('.modal-body').html(data);
	        		if (opts.loaded){
	        			opts.loaded(dialog);
	        		}
	        	})
            .fail(function(){
              dialog.find('.modal-body').html('<p class="text-danger">' + admin.tr('error_check_log') + '</p>');
            });
			}
		},
		/**
		 *
		 * @param text
		 * @param title
		 * @param type : false, info, success, error
		 * @param sticky
		 */
		message: function(text, type, title, sticky){
      new PNotify({
        title: title ?  title : false,
        text: text,
        type: type ? type : false,
        hide: sticky ? false : true,
        styling: 'bootstrap3'
      });
		},

	tabs : {
	    tab: '',
	    set: function(el){
	        if (typeof el == 'string'){
	            this.tab = $(el);
	        } else {
	            this.tab = el;
	        }
	    },
	    start: function(){
	        tab = this.tab;
	        tab.find('a').click(function (e) {
	            e.preventDefault();
	            $(this).tab('show');
	        });

	        tab.find('button.close').click(function(e){
	            var li = $(this).parents('li');
	            admin.tabs.closeTab(li);
	            return false;
	        });
	    },
	    /**
	     *
	     * @param opts object
	     * 		title
	     *
	     * 		html
	     *
	     * 		obj
	     * 		method
	     */
	    add: function(opts){

	    	var title = opts.title ? opts.title : '',
	    	tab = this.tab,
	    	id = Math.floor(Math.random()*1000) + '' + tab.find('li').length;

	        this.tab.append('<li><a href="#added' + id + '">' + title + '<button class="close" type="button">×</button></a></li>');

	        if (opts.html){
	        	this.tab.next('div.tab-content').append('<div class="tab-pane" id="added' + id + '">' + opts.html + '</div>');
	        	this.start(tab);
        		this.tab.find('li a:last').tab('show');
						this.tab.find('li:last').data('url', location.hash.substring(1));
						this.tab.find('li:last').data('opts', opts);

	        } else if (opts.obj && opts.method){
	        	admin.tabs.tab.next('div.tab-content')
              .append('<div class="tab-pane" id="added' + id + '"><i class="icon ion-load-c ion-loading-c" style="font-size: 3em;"></i></div>');

	        	admin.tabs.start(tab);

        		admin.tabs.tab.find('li a:last').tab('show');
						this.tab.find('li:last').data('url', location.hash.substring(1));
						this.tab.find('li:last').data('opts', opts);
	        	$.ajax({
	        		'type': opts.post ? 'POST' : 'GET',
	        		'url': 'controller.php?obj=' + opts.obj + '&method=' + opts.method + (opts.param ? '&param[]=' + opts.param.join('&param[]=') : '' ),
	        		'data': opts.post
	        	})
	        	.done(function(data){
	        		$('#added' + id).html(data);
	        	})
            .fail(function(){
              $('#added' + id).html('<p class="text-danger">' + admin.tr('error_check_log') + '</p>');
            });
	        } else {
	        	return false;
	        }
          this.tab.find('li:last').on('click', function(){
            _ignorehash = true;
            location.hash = '#' + $(this).data('url');
          });
	    },

	    reloadActive: function(){
        this.reload();
	    },

      reloadThis: function(el){
        if(!(el instanceof jQuery)){
          el = $(el);
        }
        var index = el.parents('.tab-pane').index();
        if (index){
          this.reload(index);
        }
      },

      reload: function(index){
        var pane, opts;

        if (!index){
          pane = $('div.tab-content div.active');
          opts = tab.find('li.active').data('opts');
        } else {
          pane = $('div.tab-content .tab-pane').get(index);
          pane = $(pane);
          opts = $(tab.find('li').get(index)).data('opts');
        }

	    	var url = 'controller.php?obj=' + opts.obj + '&method=' + opts.method + (opts.param ? '&param[]=' + opts.param.join('&param[]=') : '' );

	    	pane.html('<i class="icon ion-load-c ion-loading-c" style="font-size: 3em;"></i>');

        $.ajax({
	        		'url': url,
	        		'data': opts.data
	        	})
	        	.done(function(data){
	        		pane.html(data);
	        	})
            .fail(function(){
              pane.html('<p class="text-danger">' + admin.tr('error_check_log') + '</p>');
            });
	    },

	    closeActive: function(state){
	    	var active = tab.find('li.active');
	    	admin.tabs.closeTab(active, state);
	    },

	    closeTab: function(li, state){

        // only one tab is left, the Welcome tab. This should never be closed!
				if(tab.find('li').length === 1){
					if (state){
            location.hash = '#' + state;
					}
					return;
				}

				// I'm closing the active tab
				if (li.hasClass('active')){

          // if state is defined load new tab and then remove old one
					if (state){
						var actualState = location.hash.substr(1);


            if (actualState === state){
              admin.tabs.reloadActive();
              return;
              //  $(window).trigger( "hashchange" );

            } else {
              location.hash = '#' + state;
              //$(window).trigger( "hashchange" );

              tab.find('li a:last').tab('show');
            }

            // remove tab-pane
            $('#' + li.find('a').attr('href').replace('#', '')).remove();

            // remove li element
            li.remove();

          } else {
            // remove tab-pane
            $('#' + li.find('a').attr('href').replace('#', '')).remove();

            // remove li element
            li.remove();

            tab.find('li a:last').tab('show');

            var prevState = tab.find('li:last').data('url');

            if (prevState && prevState !== 'undefined'){
              _ignorehash = true;
              location.hash = '#' + prevState;
            } else{
              location.hash = '#';
            }
          }
        // I'm closing an not active tab: just remove the tab and do nothing!
				} else {
					li.remove();
				}
	    }
	},

	tr: function (string) {

		return lang[string] ? lang[string] : string;
	},

	media: {
		go2dir: function(dir){
			admin.tabs.closeActive('media/all/' + dir);
		},

		deleteDir: function(path){
			$.get('controller.php?obj=media_ctrl&method=delete&param[]=' + path, function(data){
				admin.message(data.text, data.status);
				if(data.status == 'success'){
					admin.media.go2dir(data.new_path);
				}
			}, 'json');
		},


		deleteFile: function(full_path){
			admin.dialog({
				title: admin.tr('pay_attention_please'),
				html: admin.tr('confirm_delete_file'),
				buttons:[
          {
            text: '<i class="glyphicon glyphicon-trash"></i>  ' + admin.tr('delete'),
            addclass: 'btn-danger',
            action: 'close',
            click: function(){
              $.get('controller.php?obj=media_ctrl&method=delete&param[]=' + full_path, function(data){
                admin.message(data.text, data.status);
                if (data.status == 'success'){
                  admin.tabs.closeActive('media/all/' + data.new_path);
                }
              }, 'json');
            }
          },
          {
            text: admin.tr('close'),
            action: 'close'
          },
        ]
			});

		},

		filterDir: function(filter, uid){
			if (filter) {
		      $('#media-' + uid + ' div.searcheable:not(:Contains(' + filter + '))').parents('li').fadeOut();
		      $('#media-' + uid + ' div.searcheable:Contains(' + filter + ')').parents('li').fadeIn();
		    } else {
		      $('#media-' + uid + ' li').fadeIn();
		    }
		}
	}
};




$(function () {


	admin.tabs.set('#tabs');
	admin.tabs.start();


    $(window).on( 'hashchange', function(e) {

			if (_ignorehash){
				_ignorehash = false;
				return;
			}

    	var url = location.hash.substring(1);

    	if (!url || url.match(/nt-/)){

				return;
    	} else {

    		var url_arr = url.split('/');

    		admin.tabs.add({
    			'title': url_arr[0] + '/' + url_arr[1] + (url_arr[2] ? '/' + url_arr[2].substr(0, 5) + '...' : ''),
    			'obj': url_arr[0] + '_ctrl',
    			'method': url_arr[1],
    			'param': url_arr.slice(2)
    		});
    	}
    }).trigger('hashchange');

});


jQuery.expr[':'].Contains = function(a,i,m){
    return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
};

$('a.ftpopover').popover({html:true});

$(document).ready(function () {

  $('.toggleMenu').on('click', function(e){
    $('#menu, #content').toggleClass('shown');
    if ($(this).attr('href') === '#'){
      e.preventDefault();
    }
  });
  $('#content').on('click', function(){
    $('#menu, #content').removeClass('shown');
    $('#menu ul.sub').removeClass('show');
  });

  $('#menu a').on('click', function(){
    if ($(this).attr('href') !== '#'){
      $('#menu ul.sub').removeClass('show');
      $('#menu, #content').removeClass('shown');
    }
  });

  $('#menu a.sub-toggle').on('click', function(e){
    $(this).next('ul.sub').toggleClass('show');
    $('#menu ul.sub').not($(this).next('ul.sub')).removeClass('show');

    e.preventDefault();
    return false;
  });

  if(!('ontouchstart' in document.documentElement)){
    $('[data-toggle="tooltip"]').tooltip();
  }
});
