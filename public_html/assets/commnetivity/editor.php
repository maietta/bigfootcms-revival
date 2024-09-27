<?

$content = $framework->query(mysql_real_escape_string($_REQUEST['vpath']), "content");
$html_from_db = ( $content->virtual_path ) ? stripslashes(base64_decode($content->encoded_content)) : "";
$javascript_from_db = ( $content->virtual_path ) ? stripslashes(base64_decode($content->encoded_javascript)) : "";
$stylesheet_from_db = ( $content->virtual_path ) ? stripslashes(base64_decode($content->encoded_stylesheet)) : "";

$width = mysql_real_escape_string($_REQUEST['width']);
$height = mysql_real_escape_string($_REQUEST['height']);
$width = (strlen($width)>0) ? $width . "px" : 'auto';
$height = (strlen($width)>0) ? $height . "px" : '500px';

if ( mysql_real_escape_string($_REQUEST['mode']) == "editor" ) {
$content_to_return = <<<EOF
<div id="commnetivity_content_editor_container" style="width: $width; height: $height;">
    <ul id="commnetivity_content_editor_tabs" style="width: $width;">
        <li class="left"><a name="cms_content" href="#">Content</a></li>
        <li class="middle"><a name="cms_javascript" href="#">Javascript</a></li>
        <li class="right"><a name="cms_stylesheet" href="#">Stylesheet</a></li>
        <a id="save" href="#" style="margin-left: 10px;">Save changes.</a>
        <li class="close" style="float: right; display: inline-block;"><a test="x" href="#">X</a></li>
    </ul>
    <div id="commnetivity_content_editor_windows">
		<div name="cms_content" ><textarea id="cms_content" name="cms_content" class="editor" style="width: $width; height: $height; color: black; background-color: transparent;">$html_from_db</textarea></div>
		<div name="cms_javascript" ><textarea id="cms_javascript" name="cms_javascript" style="width: $width; height: $height; color: black; background-color: transparent;">$javascript_from_db</textarea></div>
        <div name="cms_stylesheet" ><textarea id="cms_stylesheet" name="cms_stylesheet" style="width: $width; height: $height; color: black; background-color: transparent;">$stylesheet_from_db</textarea></div>
	</div>
</div>
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
/* old X button */
    $('a[id=save]').live('click', function(){
        var vpath = window.location.pathname;
        var content_window = $('div[id=content]');
        $(window).scrollTop();
        var content = $('textarea[id=cms_content]').val();
        var js = $('textarea[id=cms_javascript]').val();
        var css = $('textarea[id=cms_stylesheet]').val();
        $('div[id=control_panel_status_bar]').removeClass('idle').addClass('animated').show();
		$('a[id=save]').html("Saving...");
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
	//exit;
}

$profile = (object) $framework->profile();
$profile->editor = "ckeditor"; /* Remove, and move into actual framework */
switch ($profile->editor) {
	case "tinymce":
		include(PATH.COMMNETIVITY."/tinymce.php");
	break;
	case "ckeditor":
		include(PATH.COMMNETIVITY."/ckeditor.php");
	break;
	default:
		//echo "Sorry, you do not have editing permissions.";
	break;
}

if ( mysql_real_escape_string($_REQUEST['mode']) == "editor" ) {
	echo json_encode(array("display"=>$content_to_return, "javascript"=>"$javascript"));
} else {
	$javascript = <<<EOF
	$('<div class="rpc_msg_warn">Sorry, you do not have editing permissions.</div>').fadeIn(300).insertAfter($('body')).delay(2000).animate({"top":"-=80px"},1500).animate({"top":"-=0px"},1000).animate({"opacity":"0"},700);
EOF;
	echo json_encode(array("display"=>$html_from_db, "javascript"=>"$javascript"));
}

exit;

?>
