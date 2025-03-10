<?php

function initialize($framework, $ui, $params) {
    $xhtml .= '<ul id="control_panel_subnav_tabs">';
	$xhtml .= '<li class="commnetivity_ui_left"><a href="/commnetivity/tab/system/templates" title="Setup your website\'s look and feel.">Templates</a></li>';
	$xhtml .= '<li class="commnetivity_ui_right"><a href="/commnetivity/tab/system/navigation" title="Define the div containers that hold your navigations.">Navigation</a></li>';
	$xhtml .= '</ul>';
	$xhtml .= '<div id="commnetivity_windows">';
	$xhtml .= '<div class="head">System Management</div>';
	$xhtml .= '<ul><li>System Managment Area, please select a sub-navigation tab above to get started.</li></ul>';
	$xhtml .= '</div>';
	return array("display"=>$xhtml);
}

function templates($framework, $ui, $params) {
	$xhtml .= '<div id="commnetivity_windows"><ul><div class="head">Templates management coming soon.</ul></div>';
	return array("display"=>$xhtml);
}

function navigation($framework, $ui, $params) {
	$xhtml .= '<div id="commnetivity_windows"><ul><div class="head">Specify wich divs in the currently loaded template gets navigation</ul></div>';
	return array("display"=>$xhtml);
}

?>
