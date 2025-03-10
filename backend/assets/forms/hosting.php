<?php

/*
	Rail mapping example:						Function called:		Passed in from:			Inejected data:

	/commnetivity/forms/recipe					initialize();			XHR from subnav			VPATH, RAIL
		The /forms SCREEN will issues a request to initialize(), as long as no raile PANEL is defined. The panel is the trailing /recipe.
	
	/commnetivity/forms/recipe/details/495		details();				XHR from leftnav		VPATH, RAIL, RECORD, NAME/VALUE PAIR
		The trailing /495 is detected intelligently as a RECORD because we have SCREEN and PANEL defined, and supplying the record to the details() function.
	
*/

function initialize($framework, $ui, $params) {
	require_once(PATH.'/assets/cpanel/xmlapi.php');
    $ui = new screen();
    $xhtml  = '<div id="commnetivity_leftnav" class="scrollable vertical">';
    $xhtml .= '<div class="items">';
    $results = mysql_query("SELECT * FROM `recipes` WHERE 1;");
    if ( mysql_num_rows($results) > 0 ) {
        while($result = mysql_fetch_object($results)) {
            $xhtml .= '<div class="item"><a href="#'.$result->title.'" id="'.$result->id.'">'.$result->title.'</a></div>';
        }
    } else {
        $xhtml .= '<div class="item">No accounts on VPS.</div>';
    }
    $xhtml .= '</div>'; //items
    $xhtml .= '</div>'; //leftnav
    $vpath = mysql_real_escape_string($_REQUEST['vpath']);


    $xhtml .= '</div>';
	

	$xhtml .= '<div id="commnetivity_details">';
	$xhtml .= '<p>Using the menu on the left, you may select a record to review and/or manage.</p>';
	$xhtml .= '</div>';
    $javascript = <<<EOF
	
	$('div[id="add_new_record"]').hide();
	
    $('div[id=commnetivity_actions]').empty();
    $('div[id=commnetivity_actions]').html('<a class="prev">&laquo; Back</a><a class="next">Next accounts &raquo;</a>');
    $(".scrollable").scrollable({ vertical: true, mousewheel: true });
EOF;
    return array("display"=>$xhtml, "javascript"=>$javascript);
}

function details($framework, $ui, $params) {
	//$structure = $framework->table_structure_by_id("recipes", $params->id, $params->name);
	
//	print_r($structure);
	$xhtml .= '<ul id="recipes">';
    $results = mysql_query("SELECT * FROM `recipes` WHERE `id` = '".RECORD."';");
    if ( mysql_num_rows($results) > 0 ) {
        $result = mysql_fetch_object($results);
		$xhtml  .= '<li>'.$ui->make_input("Title:", array("name"=>"title", "maxlength"=>"75"), $result->title).'</li>';
		$xhtml .= '<li>'.$ui->make_textarea("Ingredients:", array("name"=>"ingredients", "rows"=>"8", "cols"=>"80"), $result->ingredients).'</li>';
		$xhtml .= '<li>'.$ui->make_textarea("Notes/Instructions:", array("name"=>"notes", "size"=>"15"), $result->notes).'</li>';
    } else {
        $xhtml = "No records available.";
    }
	$xhtml .= '</li>';
	$javascript = <<<EOF
	var window_height = $('div[id=control_panel_window]').height();
	//$('div[class=items]').height(height);
	alert(window_height);
	
EOF;
    return array("details"=>$xhtml, "javascript"=>$javascript);
}


?>
