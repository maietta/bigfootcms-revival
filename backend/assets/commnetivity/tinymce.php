<?

$head .= "<script type=\"text/javascript\" src=\"".CKEDITOR."/ckeditor.js?" . filemtime(PATH.CKEDITOR.'/ckeditor.js') . "\"></script>\n";
$head .= "<script type=\"text/javascript\" src=\"".CKEDITOR."/adapters/jquery.js?" . filemtime(PATH.CKEDITOR.'/adapters/jquery.js') . "\"></script>\n";

$content = $framework->query(mysql_real_escape_string($_REQUEST['vpath']), "content");
$from_db = ( $content->virtual_path ) ? stripslashes(base64_decode($content->encoded_content)) : "";

//$xhtml = str_get_html($from_db);
// foreach($xhtml->find('link') as $element) { if ($element->href) { $stylesheets[] = $element->href; } $xhtml->find('link')->outertext = null; }
// foreach($xhtml->find('script') as $element) { if ($element->src) { $header_js[] = $element->src; $xhtml->find('script')->outertext = null; } else { $header_js_text[] = $element->innertext; $xhtml->find('script')->outertext = null; } }
// foreach($xhtml->find('style') as $element) { $header_css_text[] = $element->innertext;  $xhtml->find('style')->outertext = null; }
//$html->find('div[id=content]', 0)->innertext = $xhtml;
//$db_xhtml = $html;

$js_from_db = ( $content->virtual_path ) ? stripslashes(base64_decode($content->encoded_javascript)) : "";
$css_from_db = ( $content->virtual_path ) ? stripslashes(base64_decode($content->encoded_stylesheet)) : "";
//print_r($header_js);
$width = mysql_real_escape_string($_REQUEST['width']);
$height = mysql_real_escape_string($_REQUEST['height']);


$width = $width . "px";
$height = $height . "px";

if ( mysql_real_escape_string($_REQUEST['mode']) == "editor" ) {

$content_to_return = <<<EOF
<script type="text/javascript" src="/assets/tinymce/jscripts/tiny_mce/jquery.tinymce.js" /></script>
<script type="text/javascript" src="/assets/edit_area/edit_area_full.js" /></script>
<script type="text/javascript" src="/assets/ckeditor_3.5/ckeditor.js" /></script>
<script type="text/javascript" src="/assets/ckeditor_3.5/adapters/jquery.js" /></script>
<div id="commnetivity_content_editor_container" style="width: $width; height: $height;">
    <ul id="commnetivity_content_editor_tabs" style="width: $width;">
        <li class="left"><a name="cms_content" href="#">Content</a></li>
        <li class="middle"><a name="cms_javascript" href="#">Javascript</a></li>
        <li class="right"><a name="cms_stylesheet" href="#">Stylesheet</a></li>
        <a id="save" href="#" style="margin-left: 10px;">Save changes.</a>
        <li class="close" style="float: right; display: inline-block;"><a test="x" href="#">X</a></li>
    </ul>
    <div id="commnetivity_content_editor_windows">
		<div name="cms_content" ><textarea id="cms_content" name="cms_content" class="editor" style="width: $width; height: $height; color: black; background-color: transparent;">$from_db</textarea></div>
		<div name="cms_javascript" ><textarea id="cms_javascript" name="cms_javascript" style="width: $width; height: $height; color: black; background-color: transparent;">$js_from_db</textarea></div>
        <div name="cms_stylesheet" ><textarea id="cms_stylesheet" name="cms_stylesheet" style="width: $width; height: $height; color: black; background-color: transparent;">$css_from_db</textarea></div>
	</div>
</div>
<script>

		
		
		$('#cms_content').tinymce({
			// Location of TinyMCE script
			script_url : '/assets/tinymce/jscripts/tiny_mce/tiny_mce.js',
        // General options
        mode : "exact",
        elements : "elm1",
        theme : "advanced",
        skin : "o2k7",
		skin_variant : "silver",
        plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,|,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Example content CSS (should be your site CSS)
        content_css : "/Templates/default/wireframe.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});
		

</script>

EOF;

$vpath = VPATH;

$javascript .= <<<EOF
	$('div[id=commnetivity_content_editor_tabs]').css('width', '$width');
    

	$('div[id=commnetivity_content_editor_container] div[name=cms_javascript]').hide();
	$('div[id=commnetivity_content_editor_container] div[name=cms_stylesheet]').hide();
    
	$('div[id=commnetivity_content_editor_container] a[name=cms_content]').css('color', 'white');
	$('div[id=commnetivity_content_editor_container] a[name=cms_javascript]').css('color', 'black');
	$('div[id=commnetivity_content_editor_container] a[name=cms_stylesheet]').css('color', 'black');
	$('div[id=commnetivity_content_editor_container] a').css('text-decoration', 'none');
	$('div[id=commnetivity_content_editor_windows]').css('border','0px');
	$('div[id=commnetivity_content_editor_container] a').live('click',function(){
    
   
		var clicked_tab = $(this).attr('name');
		if ( clicked_tab != "" ) {
			if (clicked_tab == "cms_content") {
            	$('div[id=commnetivity_content_editor_container] div[name=cms_content]').show();
	            $('div[id=commnetivity_content_editor_container] div[name=cms_javascript]').hide();
                $('div[id=commnetivity_content_editor_container] div[name=cms_stylesheet]').hide();
            }
			if (clicked_tab == "cms_javascript") {
            	 $('div[id=commnetivity_content_editor_container] div[name=cms_content]').hide();
	            $('div[id=commnetivity_content_editor_container] div[name=cms_javascript]').show();
                $('div[id=commnetivity_content_editor_container] div[name=cms_stylesheet]').hide();
            }
			if (clicked_tab == "cms_stylesheet") {
            	$('div[id=commnetivity_content_editor_container] div[name=cms_content]').hide();
	            $('div[id=commnetivity_content_editor_container] div[name=cms_javascript]').hide();
                $('div[id=commnetivity_content_editor_container] div[name=cms_stylesheet]').show();
            }
			$('div[id=commnetivity_content_editor_container] a[name=cms_content]').css('color', 'black');
			$('div[id=commnetivity_content_editor_container] a[name=cms_javascript]').css('color', 'black');
			$('div[id=commnetivity_content_editor_container] a[name=cms_stylesheet]').css('color', 'black');
			$('div[id=commnetivity_content_editor_container] a[name='+clicked_tab+']').css('color', 'white');
        	return false;
		}

    });


	$('a[test=x]').click(function(){

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
    $('a[id=save]').live('click', function(){
        var vpath = window.location.pathname;
        var content_window = $('div[id=content]');
        $(window).scrollTop();
        var content = $('textarea[id=cms_content]').val();
        var js = $('textarea[id=cms_javascript]').val();
        var css = $('textarea[id=cms_stylesheet]').val();
        $('div[id=control_panel_status_bar]').removeClass('idle').addClass('animated').show();

		$('a[id=save]').html("Saving...");
/*
        $('<div class="rpc_msg_warn">... saving ...</div>').fadeIn(0).insertAfter($('body')).delay(2000).animate({"top":"-=80px"},600).animate({"top":"-=0px"},1000).animate({"opacity":"0"},700);
*/
        $.ajax({
            url: "/commnetivity/update_content",
            data: {vpath: vpath, content: content, js: js, css: css},
		type: "POST",
            cache: false,
            success: function(message) {
				$('a[id=save]').html("Saved!").delay(2000).html("Save changes.");
	            $('div[id=content]').scrollTop();
                $('#control_panel_window').empty().append(message);
                $('div[id=control_panel_status_bar]').removeClass('animated').addClass('idle').show();
                $('<div class="rpc_msg_ok">Your changes have been saved.</div>').fadeIn(300).insertAfter($('body')).delay(2000).animate({"top":"-=80px"},1500).animate({"top":"-=0px"},1000).animate({"opacity":"0"},700);
            }
        });
		return false;
   });





EOF;

        echo json_encode(array("display"=>$content_to_return, "javascript"=>"$javascript"));
        exit;
     } else {
         echo json_encode(array("display"=>$from_db));

         exit;
        }
        
?>
