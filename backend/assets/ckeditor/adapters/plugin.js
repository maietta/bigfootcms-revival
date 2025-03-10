CKEDITOR.plugins.add('ajaxsave', {
	init: function(editor) {
		var pluginName = 'ajaxsave';
		editor.addCommand( pluginName, {
			exec : function(editor) {
				var vpath = window.location.pathname;
				var content = $('textarea[id=content]').val();
				var js = $('textarea[id=javascript]').val();
				var css = $('textarea[id=stylesheet]').val();
				$('div[id=control_panel_status_bar]').removeClass('idle').addClass('animated').show();
				$.ajax({
					url: "/commnetivity/update_content",
					data: {vpath: vpath, content: content, js: js, css: css},
	   
					cache: false,
					success: function(message) {
					// $('#control_panel_window').empty().append(message);
					$('div[id=control_panel_status_bar]').removeClass('animated').addClass('idle').show();
						alert("Saved.");
					}
				});
			},
			canUndo : true
		});
		editor.ui.addButton('Ajaxsave', { label: 'Save to database.', command: pluginName, className : 'cke_button_save' });
	}
});

