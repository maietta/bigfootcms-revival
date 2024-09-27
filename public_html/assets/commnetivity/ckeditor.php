<?php

$javascript .= <<<EOF
$('a[test=x]').click(function(){
	var theEditor = $('textarea.editor').ckeditorGet();
	theEditor.destroy(theEditor);
	$('div[id=content]').html('<div id="content"></div>').fadeIn('fast');
	$('div[id=content]').html("");
		
	$.ajax({
		url: '/commnetivity/content', type: "POST", data: { vpath: "$vpath" }, dataType: "json", cache: false,
		success: function(response) {
			$('a[id=editor]').show();        
				$('div[id=content]').html(response.display);
		}, error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert("AJAX received no data for could not parse.");
		}
	});
	return false;
});
	
$('div[id=commnetivity]').append('<div id="commnetivity_editor_toolbar_top" style="top: 0px; position: abolute;"></div>');
$('div[id=commnetivity]').append('<div id="commnetivity_editor_toolbar_bottom" style="bottom: 0px; position: abolute;"></div>');
	
var min_height = $('div[id=content]').height();
	
var toolbars = [
	['Source','Preview','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteFromWord','-', 'SpellChecker', 'Scayt'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
		'/',
	['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
	['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
	['Image','Flash','Table','HorizontalRule','SpecialChar'],
	['BidiLtr', 'BidiRtl'],
	['Link','Unlink','Anchor'],
	'/',
	['Styles','Format','Font','FontSize'],
	['TextColor','BGColor'],
	['Maximize', 'ShowBlocks']
];

	$('textarea.editor').ckeditor({
		extraPlugins: 'magicline',
		magicline_color: 'blue',
		allowedContent: true,
		
		
		resize_maxWidth:'%',

		pasteFromWordPromptCleanup: true,
		Scayt: true,
		uiColor: '#9ba088',
    });
EOF;

?>