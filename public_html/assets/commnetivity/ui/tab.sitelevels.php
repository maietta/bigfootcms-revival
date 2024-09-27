<?php

function initialize($framework, $ui, $params) {
	$xhtml = '<ul id="control_panel_subnav_tabs"></ul>';
	
	
	$override = $framework->query_sitelevel_data(SITELEVEL);
	$options = (object) unserialize($override->options);
	$security = (object) unserialize($override->security);
	$meta = (object) unserialize($override->meta_data);
	$theme = (object) unserialize($override->theme);
	if ( $override->top_level_pattern ) {
    	$query = mysql_query("SELECT * FROM `commnetivity_overrides` WHERE `top_level_pattern` LIKE '".SITELEVEL."%';");
    	$count = mysql_num_rows($query);

		$xhtml .= '<div id="commnetivity_leftnav_container"></div>';
    	$xhtml .= '<div id="commnetivity_windows">';
		$xhtml .= '<div class="head">Mapping '.SITELEVEL.' to whatever you want.</div>';
//		$xhtml .= '<ul>';
//    	$xhtml .= '<h1>There area '.$count.' pages that will be affected by the actions of your changes here.</h1>';
//		$xhtml .= '<li>Information about Site Levels Sitelevels are the first directory from the website root. Typically, a site level can be thought of as a set of defaults or overrides. However, sitelevels are also a way to assign a directory to a script so the script can be utilized to generate content with virtualized web addresses.<br>To remove a record, simply type "delete" or "remove" in the page titles field.</li>';
	//	$xhtml .= '<li>';
	//	$xhtml .= (SITELEVEL == '/') ? 'Default settings for static and dynamic content in site\'s root.' : 'Default controls for static/dynamic where virtual path starts with <b>'.SITELEVEL.'/*</b>';
	//	$xhtml .= '</li>';
	//	$xhtml .= '</ul>';
		$xhtml .= '<ul>';
		$xhtml .= '<li>'.$ui->make_input("Default Page Titles:", array("name"=>"page_titles", "maxlength"=>"75"), $meta->page_titles).'</li>';
		$xhtml .= '<li>'.$ui->make_input("Default Keywords:", array("name"=>"keywords", "maxlength"=>"255"), $meta->keywords).'</li>';
		$xhtml .= '<li>'.$ui->make_textarea("Default Description:", array("name"=>"description", "maxlength"=>"255"), $meta->description).'</li>';
		$xhtml .= '</ul>';
		$xhtml .= '<ul>';
		$xhtml .= '<li>'.$ui->make_input("Internal script:", array("name"=>"internal_path", "maxlength"=>"75"), $override->internal_path).'</li>';
		$xhtml .= '<li>'.$ui->make_select("Require SSL?", "ssl_required", $security->ssl_required, array("O"=>"Optional","Y"=>"Always"), $security->ssl_required).'</li>';
		$xhtml .= '<li>'.$ui->make_select("Require Auth?","auth_required", $security->auth_required, array("O"=>"Optional","Y"=>"Always"), $security->auth_required).'</li>';
		
		
		$xhtml .= '<li>'.$ui->make_select("Template:", "template", $theme->template, array("default.dwt"=>"Default DWT","fullpage.dwt"=>"Full Page"), $theme->template).'</li>';
		$xhtml .= '</ul>';
	    $xhtml .= '</div>';
		return array("display"=>"$xhtml");
	} else {
	    $xhtml  = '<div id="commnetivity_windows">';
		$xhtml .= '<div id="commnetivity_leftnav_container"></div>';
		$xhtml .= '<ul>';
		$xhtml .= '<li>The proposed "<b>'.SITELEVEL.'</b>" is not required, but allows for assignment of scripts, default page titles and theme options, including restricting access by user groups and more.</li>';
		$xhtml .= '<li>Would you like to create a site level?</li>';
	    $xhtml .= '<li>' .$ui->make_input("Type \"yes\" to activate:", array("name"=>"sitelevel_activation", "maxlength"=>"3"), "").'</li>';
		$xhtml .= '</ul>';
	    $xhtml .= '</div>';
		return array("display"=>"$xhtml");
	}
}

?>