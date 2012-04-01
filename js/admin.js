var gui = {
		loading : '<img src="../css/loader.gif"  alt="loading" />',
		
		openInTab: function(module, get, title){
			title = '<span>&nbsp;</span>' + title;
			
			// write url to request with ajax
			var url = 'loader.php?mod=' + module + ( get ? '&' + get : '' );
			
			
			// define dialog id using module name + table
			var mod_id = 'mod' + module.replace(/\//, '');

			var $tabs = $('#tabs');
			
			if ( $('#' + mod_id).length) {
				
				var div_container = $('#' + mod_id).parent();
				
	
				var index = $( "div.ui-tabs-panel", $tabs ).index( div_container );
				
				$tabs.tabs("select", index)
					.tabs("url", index, url)
					.tabs("load", index);
				
			} else {
				
				$tabs.tabs( "add", url, title);
				
			}
		},
		openInDialog: function (url, opt) {
			
			if ( !opt ) {	var opt = {};	}
			
			// set default dialog dimensions: 600 x400
			if ( !opt.width ) { opt.width = 600; }
			if ( !opt.height ) { opt.height = 400; }

			// defualt: on close dialog is killed
			opt.close = function() {			$(this).remove();			};

			// write url to request with ajax
			url = 'loader.php?mod=' + url;
			
			var div = $('<div id="dialog">' + gui.loading + '</div>');
				
			// content is then loaded inside dialog
			div.load(url).dialog(opt);
		}
		
}

menu = {
	article : {
		form: function(id){
			if (id)
				{
				gui.openInTab('article/edit_form', 'id=' + id, 'Modifica articolo');
				}
			else
				{
				gui.openInTab('article/edit_form', false, 'Aggiungi articolo');
				}
		},
		erase: function(id){
			$('<div></div>')
				.html('<div class="ui-widget">'
						+ '<div style="padding: 0 .7em;" class="ui-state-error ui-corner-all">'
						+ '<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>'
						+ '<strong>Attenzione:</strong> Questa azione non può essere annullata!</p>'
						+'</div></div')
				.dialog({
					title: 'Sei sicuro di volere cancellare questo articolo per sempre?',
					modal:true,
					buttons:{
						Procedi: function(){
							$.get('loader.php?nw=1&mod=article/action2json&a=erase&id=' + id,
									function(data){
										$().toastmessage('showToast', {text: data.text,type: data.type});
										
										if (data.type == 'success')
											{
											menu.article.showall();
											}
								},
								'json');
							$(this).dialog('close');
						},
						Annulla: function(){
							$(this).dialog('close');
						}
					}
				});
		},
		showall: function(){
			gui.openInTab('article/list', false, 'Tutti gli articoli');
		}
	
	},
	
	translate:{
		list: function(context){
			gui.openInTab('translate/list', 'context=' + context, 'Gestione traduzioni ' + ( (context == 'article') ? 'articoli' : 'menu') );
		},
		form: function(context, id, lang){
			gui.openInTab('translate/form_' + context, 'id=' + id + '&lang=' + lang, 'Traduci');
		}
	},
	
	file: {
		showall: function(upload_dir){
			gui.openInTab('file/list', (upload_dir ? 'upload_dir=' + upload_dir : ''), 'Gestione file');
		},
		erase: function ( file, upload_dir ){
			$('<div></div>')
			.html('<div class="ui-widget">'
					+ '<div style="padding: 0 .7em;" class="ui-state-error ui-corner-all">'
					+ '<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>'
					+ '<strong>Attenzione:</strong> Questa azione non può essere annullata!</p>'
					+'</div></div')
			.dialog({
				title: 'Sei sicuro di volere cancellare questo file/cartella per sempre?',
				modal:true,
				buttons:{
					Procedi: function(){
						$.get('loader.php?nw=1&mod=file/erase&file=' + file,
								function(data){
									$().toastmessage('showToast', {text: data.text,type: data.type});
									
									if (data.type == 'success')
										{
										menu.file.showall(upload_dir);
										}
							},
							'json');
						$(this).dialog('close');
					},
					Annulla: function(){
						$(this).dialog('close');
					}
				}
			});
		}
	},
	menu: {
		list: function (menu){
			gui.openInTab('menu/list', 'menu=' + menu, 'Gestione menu');
		},
		edit: function(id, menu_name){
			gui.openInDialog(
				'menu/edit_form&id=' + id + '&menu=' + menu_name,
				{
					modal:true,
					title: 'Modifica voce menu',
					buttons: {
						Salva: function(){
							var dia = this;
							$.post(
								'loader.php?nw=1&mod=menu/action2json&a=edit',
								$('#menu_edit').serialize(),
								function(data){
									$().toastmessage('showToast', {text: data.text,type: data.type});
									
									if (data.type == 'success')
										{
										menu.menu.list($('#menu_edit :input[name=menu]').val());
										$(dia).dialog('close');
										}
								},
								'json'
							);
						},
						Annulla: function(){
							$(this).dialog('close');
						}
					}
				}
			);
		},
		add_new: function(menu_name){
			gui.openInDialog(
				'menu/edit_form' + (menu_name ? '&menu=' + menu_name : ''),
				{
					modal:true,
					title: 'Aggiungi nuovo menu',
					buttons: {
						Salva: function(){
							var dia = this;
							$.post(
								'loader.php?nw=1&mod=menu/action2json&a=add',
								$('#menu_edit').serialize(),
								function(data){
									$().toastmessage('showToast', {text: data.text,type: data.type});
									
									if (data.type == 'success')
										{
										menu.menu.list($('#menu_edit :input[name=menu]').val());
										$(dia).dialog('close');
										}
								},
								'json'
							);
						},
						Annulla: function(){
							$(this).dialog('close');
						}
					}
				}
			);
		},
		erase: function(id, menu_name){
			$('<div />')
				.html('<div class="ui-widget">'
						+ '<div style="padding: 0 .7em;" class="ui-state-error ui-corner-all">' 
						+ '<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>' 
						+ '<strong>Attenzione:</strong> Questa azione è irrevocabile</p>'
					+ '</div>'
				+ '</div>')
				.dialog(
				{
					modal:true,
					title: 'Sei sicuro di volere cancellare questa voce di menu?',
					buttons: {
						'Cancella voce': function(){
							var dia = this;
							$.post(
								'loader.php?nw=1&mod=menu/action2json&a=erase&id=' + id,
								$('#menu_edit').serialize(),
								function(data){
									$().toastmessage('showToast', {text: data.text,type: data.type});
									
									if (data.type == 'success')
										{
										menu.menu.list(menu_name);
										$(dia).dialog('close');
										}
								},
								'json'
							);
						},
						Annulla: function(){
							$(this).dialog('close');
						}
					}
				}
			);			
		}
	}
};

/*
 * Funzioni list per la parte sx:
 */

function tinymce_load(el){
	
	$(el).tinymce({
		script_url : './tiny_mce/tiny_mce.js',
		theme : 'advanced',
		plugins : 'pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist',

		theme_advanced_toolbar_location : 'top',
		extended_valid_elements : 'script[language|type]',
		theme_advanced_toolbar_align : 'left',
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,|,ltr,rtl,|,fullscreen,|,attribs",
        theme_advanced_statusbar_location : 'bottom',
		theme_advanced_resizing : true,
        content_css : "./sites/default/css/styles.css",
	});
}

var layout = {
		content: function(){
			$('body').append('<div id="tabs">'
					+ '<ul>'
						+ '<li><a href="loader.php?mod=main">Home</a></li>'
					+ '</ul>'
					+ '</div>');
			$('#tabs').tabs({
				cache: true,
				tabTemplate: "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
				spinner: '<img src="./css/arrows-loader.gif"  alt="loading" />',
				add: function(event, ui) {
					$('#tabs').tabs('select', '#' + ui.panel.id);
		 		}
			});
			$( "#tabs span.ui-icon-close" ).live("click", function() {
				var tabs = $('#tabs');
				var index = $( "li", tabs ).index( $( this ).parent() );
				tabs.tabs( "remove", index );
			});
			$('<div id="waiting_main" />')
				.appendTo($('body'))
				.hide()
				.ajaxStart(function(){	$(this).show(); })
				.ajaxStop(function(){	$(this).hide();	});
		},
		login: function(message){
			gui.openInDialog('./login_form&log_message=' + message, {
				modal: true,
				closeOnEscape: false,
				resizable: false,
				title: 'Log in',
				height: 300,
				buttons: {
					Login: function(){ $('#loginform').submit(); },
					'Vai al sito': function(){ window.location = './'} 
					
				}
			});
			$('a.ui-dialog-titlebar-close').remove();
		}
}