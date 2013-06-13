var admin = {
		
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
		 */
		dialog: function(opts){
			var dialog =  $('<div />').addClass('modal hide fade').attr('id', 'modal')
				.attr({'role':'dialog'})
				.append(
						(opts.title ? '<div class="modal-header"><h2>' + opts.title + '</h2></div>' : ''),
						'<div class="modal-body">' + ( opts.html ? opts.html : '') + '</div>'
						);
			if (opts.buttons && typeof opts.buttons == 'object'){
				
				var footer = $('<div />').addClass('modal-footer');
				
				dialog.append(footer);
				
				$.each(opts.buttons, function(index, but){
					var a = $('<a />').addClass('btn').html(but.text);
					
					if (but.href){
						a.attr('href', but.href);
					}
					
					if (but.click){
						a.click(function(){ but.click(); });
					}
					
					if (but.action == 'close'){
						a.attr('data-dismiss', 'modal');
						dialog.on('hidden', function(){
							dialog.remove();
						});
					}
					
					if(but.addclass){
						a.addClass(but.addclass);
					}
					
					footer.append(a);
				});
				
			}
			dialog.modal();
			
			if (opts.html && opts.loaded){
				dialog.on('shown', function(){;
					opts.loaded(dialog);
				})
			}
			
			if (opts.obj && opts.method){
				dialog.find('.modal-body').html('<img src="./img/spinner.gif" />');
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
		    $.pnotify({
		        title: title ?  title : false,
		        text: text,
		        type: type ? type : false,
		        hide: sticky ? false : true
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
	        } else if (opts.obj && opts.method){
	        	admin.tabs.tab.next('div.tab-content')
    				.append('<div class="tab-pane" id="added' + id + '"><img src="img/spinner.gif" alt="loading..." /></div>');
	        	admin.tabs.start(tab);
        		admin.tabs.tab.find('li a:last').data('opts', opts).tab('show');
	        	$.ajax({
	        		'type': opts.post ? 'POST' : 'GET',
	        		'url': 'controller.php?obj=' + opts.obj + '&method=' + opts.method + (opts.param ? '&param[]=' + opts.param.join('&param[]=') : '' ),
	        		'data': opts.post
	        	})
	        	.done(function(data){
	        		$('#added' + id).html(data);
	        	});
	        } else {
	        	return false;
	        }
	    },
	    
	    reloadActive: function(){
	    	var d_active = $('div.tab-content div.active'), 
	    	opts = tab.find('li.active a').data('opts');
	    	
	    	var url = 'controller.php?obj=' + opts.obj + '&method=' + opts.method + (opts.param ? '&param[]=' + opts.param.join('&param[]=') : '' );
	    	
	    	d_active.html('<img src="img/spinner.gif" alt="loading..." />').load(url, opts.data);
	    },
	    
	    closeActive: function(state){
	    	var active = tab.find('li.active');
	    	admin.tabs.closeTab(active, state);
	    },
	    
	    closeTab: function(li, state){
	    	$('#' + li.find('a').attr('href').replace('#', '')).remove();
            if (li.hasClass('active')){
            	if (state){
            		var actualState = $.bbq.getState();
                	if (actualState[state] == ''){
                		$(window).trigger( "hashchange" );
                	} else {
                		$.bbq.pushState('#' + state);
                		tab.find('li a:last').tab('show');
                	}
                	li.remove();
                } else {
                	li.remove();
                	$.bbq.pushState('#');
                	tab.find('li a:last').tab('show');
                }
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
				        	 text: admin.tr('close'),
				        	 action: 'close'
				         },
				         {
				        	 text: admin.tr('delete'),
				        	 addclass: 'btn-danger',
				        	 action: 'close',
				        	 click: function(){
				        		 $.get('controller.php?obj=media_ctrl&method=delete&param[]=' + full_path, function(data){
				        			 admin.message(data.text, data.status);
				        			 if (data.status == 'success'){
				        				 admin.tabs.closeActive('media/all/' + data.new_path);
				        			 }
				        		 }, 'json')
				        	 }
				         }
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
    	var url = $.param.fragment();
    	
    	if (!url){
    		return;
    	} else if (url.match(/nt-/)){
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
    
    tinyMCE.init({
    	// General options
    	mode : "exact",
    	theme : 'advanced',
    	plugins : 'pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist',
    	
    	// Theme options
    	theme_advanced_toolbar_location : 'top',
    	theme_advanced_toolbar_align : 'left',
    	extended_valid_elements : 'script[language|type]',
    	theme_advanced_resizing : true,
    	content_css : './css/bootstrap.min.css,./sites/default/css/styles.css',
    	
    	theme_advanced_statusbar_location : 'bottom',
    	
    	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
    	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote|,undo,redo,",
    	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup",
    	theme_advanced_buttons4 : "charmap,iespell,media,|,ltr,rtl,|,fullscreen,|,attribs,|,link,unlink,anchor,image,cleanup,code,|,preview,|,forecolor,backcolor",
    	
    	forced_root_block: '',
    	force_p_newlines: true,
    	theme_advanced_blockformats: "p,address,pre,h1,h2,h3,h4,h5,h6,div"
	});
});


jQuery.expr[':'].Contains = function(a,i,m){
    return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
};

$('a.ftpopover').popover({html:true});
