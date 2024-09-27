<?php
$record = RECORD;
$panel = PANEL;
if ( ACTION != "" ) {
	// use switch(ACTION) here.
	switch(ACTION) {
		case 'details':
			$screen_function = ACTION;
			$screen = (object) $screen_function($framework, $ui, $params);
			return array("details"=>$screen->xhtml, "javascript"=>$screen->javascript);			
		break;
		case 'preview':
			$screen_function = ACTION;
			$screen = $screen_function($framework, $ui, $params);
			echo $screen;
		break;
	}
	exit;
}
$extentions = $framework->get_extentions_from_mimetype(SCREEN."/%");
$xhtml .= '<div id="commnetivity_leftnav_container">';
$xhtml .= '<div id="commnetivity_leftnav_search"><input name="commnetivity_leftnav_search" value="Search"></div>';
 $xhtml .= '<div id="commnetivity_leftnav" class="scrollable vertical"><div class="items">'."\n";
 
$sql = "SELECT distinct `group` FROM `commnetivity_media` WHERE `group` != '';";
$results = mysql_query($sql);
$counter = 0;


$items = array();

$list = array_reverse(glob($_SERVER['DOCUMENT_ROOT'].'/my_account/roles/*.php'));
foreach($list as $role) {
	$role_basename = basename("$role", ".php");
	preg_match('/^\d/', $role_basename, $rank);
	$role_basename = str_replace("_", " ", str_replace($rank[0].'_', "", $role_basename));
	$items[$rank[0]] = $role_basename;
}
		
	
	$xhtml .= commnetivity_ui_leftnav_builditems_html($items);
	//$xhtml .= "</div>";
	$counter++;

$xhtml .= "\t".'</div>'."\n"; //leftnav
$xhtml .= "\t".'</div>'."\n"; //leftnav_container
$xhtml .= <<<EOF
<div id="commnetivity_windows">
	<div id="commnetivity_details"><div class="head">Please select a role profile.</div></div>
	<div id="commnetivity_preview"></div>
</div>
EOF;
//$xhtml .= '</textarea>';
$javascript = <<<EOF
	commnetivity_ui_leftnav_enable();
	//commnetivity_ui_preview_disable();
	//commnetivity_ui_details_disable();
EOF;
return array(
	"javascript"=>"$javascript",
	"display"=>"$xhtml"
);


function get_any_id_of_group($group){
	$query = mysql_query("SELECT id FROM `commnetivity_media` WHERE `group` = '".$group."' LIMIT 1;");
	$result = mysql_fetch_object($query);
	return $result->id;
}

function commnetivity_ui_leftnav_builditems_html($items) {
	$xhtml_version = "";
	$counter = 0;
	
	foreach($items as $id=>$title) {
		if ( $counter == 0  ) { $xhtml_version .= "\t".'<div>'."\n"; }
		//$random_id_of_group = get_any_id_of_group($result->group);
	
		$xhtml_version .= "\t\t".'<div class="item"><a id="'.$id.'" href="#" title="'.$title.'">'.$title.'</a></div>'."\n";
		if ( $counter > 3 ) { $xhtml_version .= "\t".'</div>'."\n\t".'<div>'."\n"; }
		if ( $counter == 4 ) { $counter = 0; }
	
		$counter++;
	}
	//$xhtml_version .= "\t".'</div>';
	return $xhtml_version;
}

?>