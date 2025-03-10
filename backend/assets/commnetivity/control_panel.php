<?php

$vpath = mysql_real_escape_string($_REQUEST['vpath']);

$content = $framework->query($vpath, "content");
$security = (object) unserialize($content->security);
$meta = (object) unserialize($content->meta_data);
$theme = (object) unserialize($content->theme);

$profile = ( $_SESSION['username'] ) ? $framework->profile($_SESSION['username']) : 0;
$permissions = (object) unserialize($profile->permissions);

// If CMS is available, let's set global flag. This needs to get moved into CMS plugin.
if ( $permissions->cms ) {
	$controls = (object) $permissions->cms;
	if ( $controls->content ) { define("EDITABLES", TRUE); } else { define("EDITABLES", FALSE); }
} else {
	define("EDITABLES", FALSE);
}

$script_to_return .= '<!-- control_panel.css loaded from control panel-->';

ob_start();

$controls = (object) $permissions->cms;
 
$sitelevel = SITELEVEL;
//$rpc_location = RPC_PATH;

echo '<div id="commnetivity" vpath="'.$vpath.'">';
echo <<<EOF
<div id="control_panel">
	<img id="commnetivity_logo" src="/assets/commnetivity/images/branding166x35.png" width="166" height="35">
	<ul id="control_panel_main_tabs">
		<li class="commnetivity_ui_left"><a href="/commnetivity/tab/content" title="Manage the content that is assigned to $vpath">Current Webpage</a></li>
		<li class="commnetivity_ui_middle"><a href="/commnetivity/tab/media" title="Upload, delete and organize your Photos, Audio and Video.">Web Media</a></li>
		<li class="commnetivity_ui_right"><a href="/commnetivity/tab/files" title="Upload, delete and organize your downloadable file.">File Media</a></li>
		<li class="commnetivity_ui_seperator"></li>
		<li class="commnetivity_ui_left"><a href="/commnetivity/tab/sitelevels" title="Map $sitelevel/* to software, set theme, security or distribution options.">Section / Area</a></li>
		<li class="commnetivity_ui_right"><a href="/commnetivity/tab/dynamics" title="Assign PHP scripts and HTML documents to div tags by id. (Really.)">Dynamic Content</a></li>
		<li class="commnetivity_ui_seperator"></li>
		<li class="commnetivity_ui_left"><a href="/commnetivity/tab/users" title="Manage Users on Commnetivity's System">User Accounts</a></li>
		<!-- <li class="commnetivity_ui_middle"><a href="/commnetivity/tab/forms" title="Website specific interface forms for easy content management.">Custom Tools</a></li>-->
		<li class="commnetivity_ui_right"><a href="/commnetivity/tab/system" title="View and manage system settings.">System Admin</a></li>
		<li class="commnetivity_ui_seperator"></li>
		<ul id="hotdrop" title="Drop your files here or click to open a dialog."></ul>
	</ul>
	<div id="control_panel_status_bar" class="shadow idle">
		<div id="control_panel_actions"></div>
	</div>
	<div id="control_panel_window">
		<p>Welcome to Commnetivity. You may select any tab to begin managing this website.</p>
	</div>
</div>
	
<div class="control_panel_init">
	<div id="outsidebox">
		<div id="leftinsidebox"></div>
		<div id="bottombox"></div>
		<div id="rightinsidebox"></div>
		<div id="box_1">
			<a id="control_panel_toggler" class="open edit_link" href="#">Open Panel</a>
EOF;

echo ( $content->virtual_path && $controls->content )
        ? '<a id="editor" class="edit_link" href="'.$edit_link.'">Load CKEditor</a>'
        : '<a id="editor" href="'.$edit_link.'"></a>' ;
        //$decoded_content = stripslashes(base64_decode($content->encoded_content));
       $vpath = $_REQUEST['vpath'];
       $tab = $rails['3'];
       $rail = base64_encode(VPATH);
       $username = ucfirst($_SESSION['username']);
echo <<<EOF
       </div>
</div>
    </div></!-- control_panel_init_tab -->
</div><!-- commnetivity -->
EOF;

$testing_return = ob_get_contents();
ob_end_clean();



$commnetivity_js = file_get_contents($_SERVER['DOCUMENT_ROOT']."/assets/commnetivity/control_panel.js");

$commnetivity_js = str_replace('$vpath', $vpath, $commnetivity_js);

echo json_encode(array(
	"control_panel"=>$testing_return,
	"script"=>'<script>'.$commnetivity_js.'</script>'
));

exit;

?>
