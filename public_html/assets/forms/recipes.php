<?php

/*
	Rail mapping example:						Function called:		Passed in from:			Inejected data:

	/commnetivity/tab/recipe					initialize();			XHR from subnav			VPATH, RAIL
		The /forms SCREEN will issues a request to initialize(), as long as no raile PANEL is defined. The panel is the trailing /recipe.
	
	/commnetivity/forms/recipe/details/495		details();				XHR from leftnav		VPATH, RAIL, RECORD, NAME/VALUE PAIR
		The trailing /495 is detected intelligently as a RECORD because we have SCREEN and PANEL defined, and supplying the record to the details() function.
	
*/

function initialize($framework, $ui, $params) {
	if (SCREEN == 'search') {
		$sql = ( defined('RECORD') ) ? "SELECT * FROM `recipes` WHERE `title` LIKE '%".RECORD."%' LIMIT 15;"
									 : "SELECT * FROM `recipes` WHERE 1";
	} else {
		$xhtml .= '<div id="commnetivity_leftnav_search"><input name="commnetivity_leftnav_search" value="Search" style="text-decoration: italic; width: 183px; font-style: 0; background-color: transparent; border: 0px; margin: auto;"></div>';
	    $xhtml .= '<div id="commnetivity_leftnav" class="scrollable vertical"><div class="items">'."\n";
		$sql = "SELECT * FROM `recipes` WHERE 1;";
	}
	$results = mysql_query($sql);
	$counter = 0;
    if ( mysql_num_rows($results) > 0 ) {
        while($result = mysql_fetch_object($results)) {
				if ( $counter == 0  ) { $xhtml .= "\t".'<div>'."\n"; }
				$xhtml .= "\t\t".'<div class="item"><a id="'.$result->id.'" href="'.$result->title.'">'.str_replace('.'.$result->title, "", $result->title).'</a></div>'."\n";
				if ( $counter > 3 ) { $xhtml .= "\t".'</div>'."\n\t".'<div>'."\n"; }
				if ( $counter == 4 ) { $counter = 0; }
			$counter++;
        }
    } else {
        $xhtml .= "No results available for ".RECORD;
    }
	if (SCREEN == 'search') {
		$javascript = '$(".scrollable").scrollable({ vertical: true, mousewheel: true });';
		return array("leftnav"=>$xhtml, "sql"=>$sql, "javascript"=>"$javascript");
		exit;
	}
    $xhtml .= "\t".'</div>'."\n"; //leftnav
    $xhtml .= <<<EOF
</div>
</div>

<div id="commnetivity_toolbar" style="top: 0px; width: auto; background-color: black; color: white; height: 24px; lineheight: 24px;">
<a href="close" style="align: right;">[ X ]</a>
<a href="add" style="color: blue; text-decoration: underline;">New Record</a>[ItemB][ItemC]</div>
<div id="commnetivity_toolbar_dialog" style="top: 24px; width: auto; background-color: purple; color: black; font-size: 16px;"></div>

<div id="commnetivity_details">Please select an image file to begin working.</div>
EOF;
$javascript = <<<EOF

	$('div[id=commnetivity_actions]').empty();
	$('div[id=commnetivity_actions]').html('<a class="prev">&laquo; Back</a><a class="next">More recipes &raquo;</a>');
	$(".scrollable").scrollable({ vertical: true, mousewheel: true });
	$('input[name=commnetivity_leftnav_search]').each(function() {
	    var default_value = this.value;
    	$(this).css('color', '#666'); // this could be in the style sheet instead
    	$(this).focus(function() {
    	    if(this.value == default_value) {
    	        this.value = '';
    	        $(this).css('color', '#333');
				$(this).css('font-style', 'normal');
				$(this).css('background', 'url(/assets/commnetivity/ui/images/magnifying_glass.png) 160px top 3px');
				$(this).css('background-repeat', 'no-repeat');
				$(this).css('padding-left', '0px');
    	    }
    	});
    	$(this).blur(function() {
    	    if(this.value == '') {
    	        $(this).css('color', '#666');
				$(this).css('font-style', 'italic');
				$(this).css('background', 'url(/assets/commnetivity/ui/images/magnifying_glass.png) right 3px');
				$(this).css('background-repeat', 'no-repeat');
				$(this).css('padding-left', '8px');
    	        this.value = default_value;
    	    }
    	});
	});
//	$('div[id=commnetivity]').attr("screen", "recipes");
	

EOF;
    return array("javascript"=>"$javascript", "display"=>"$xhtml");


}



function add($framework, $ui, $params) {
	$xhtml  .= '<li>'.$ui->make_input("Title of new record:", array("name"=>"add_recipe", "maxlength"=>"75"), $result->title).'</li>';
	return array("display"=>"$xhtml");
}

function details($framework, $ui, $params) {
	$xhtml .= <<<EOF
<style type="text/css">
#recipes label {
		
}
</style>
EOF;
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
	//alert(window_height);
	//$('div[id=commnetivity_leftnav]').load('/commnetivity/recipes/leftnav/'.RECORD);
EOF;
    return array("details"=>$xhtml, "javascript"=>$javascript);
}




?>
