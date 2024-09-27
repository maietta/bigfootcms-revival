<?php

function initialize($framework, $ui, $params) {
	$panel = PANEL;
	$screen = SCREEN;

	$lis = "";
	$list = array_reverse(glob($_SERVER['DOCUMENT_ROOT'].'/my_account/roles/*.php'));
	
	
	foreach($list as $role) {
		
		$role_basename = basename("$role", ".php");
		preg_match('/^\d/', $role_basename, $rank);
		$role_basename = str_replace("_", " ", str_replace($rank[0].'_', "", $role_basename));
		$items[$rank[0]] = $role_basename;
	}
	$items = array_reverse($items);
		
	$last = count($items);
	
	foreach($items as $id=>$title) {
		$count++;
		$position = "middle";
		if ( $count == 1 ) { $position = "left"; }
		if ( $count == "$last" ) { $position = "right"; }
		$lis .= '<li class="commnetivity_ui_'.$position.'"><a href="'.sha1($id).'">'.$title.'</a></li>';
		
	}
	
	$xhtml .= <<<EOF
	<ul id="control_panel_subnav_tabs">
		<!-- <li class="commnetivity_ui_left"><a href="/commnetivity/tab/users/SuperAdmins">Super Admins</a></li> -->
		<!-- <li class="commnetivity_ui_right"><a href="/commnetivity/tab/users/RoleAssignments">Role Assignments</a></li> -->
		$lis
	</ul>
	<div id="commnetivity_windows">
		<div class="head"></div>
		<ul>
			<li>This panel has no content. Click a tab to get another panel.</li>
			<li>To draw content here, you must echo content from a function in $panel.php called $panel.</li>
		</ul>
	</div>
	
	<script>
		$('ul[id=control_panel_subnav_tabs] li a').click(function(){
		
			var id = $(this).attr('href');
			
			$('div[id=commnetivity_windows]').fadeOut('fast', function(){
				$.ajax({
					url: "/my_account/roles.php", dataType: 'html', type: 'GET', data: {id: id}, async: true,
					success: function(XHR) {
						$('div[id=commnetivity_windows]').html(XHR).slideDown('slow');
					}, error: function(XMLHttpRequest, textStatus, errorThrown) {
						$(this).html("<div class=\"head\">Problem fetching panel.</div>").slideDown('slow');
					}
				});
			
				
				
			});
			
			return false;
		});
	</script>
EOF;
	return array("display"=>$xhtml, "javascript"=>"$javascript");
}

function RoleAssignments() {
	global $framework, $ui, $params;
	global $permissions;
	
	include("RoleAssignments_leftnav.php");
	return array("javascript"=>"$javascript", "display"=>"$xhtml");
	
}

function commnetivity_leftnav() {
	$xhtml = "";
	$list = array_reverse(glob($_SERVER['DOCUMENT_ROOT'].'/my_account/roles/*.php'));
	foreach($list as $role) {
		$role_basename = basename("$role", ".php");
		preg_match('/^\d/', $role_basename, $rank);
		$role_basename = str_replace("_", " ", str_replace($rank[0].'_', "", $role_basename));
		
		$xhtml .= $role_basename . '(' .$rank[0] . ')' . '<br />';
	}
	return $xhtml;
}


function details($framework, $ui, $params) {
    $sql = "SELECT * FROM `commnetivity_users` WHERE 1";
    $result = mysql_query($sql);
	$counter = 0;
	$record = mysql_fetch_object($result);
	$permissions = (object) unserialize($record->permissions);
	$preferences = (object) unserialize($record->preferences);
	if ( $record->fname && $record->lname ) {
		if ( $record->fname && $record->lname ) {
    	$full_name = $record->fname . ' ' . $record->lname;
		} elseif ( $record->fname ) {
    		$full_name = $record->fname;
		} else {
			$full_name = $record->lname;
		}
	} else {
        $full_name = "No name";
	}

	$xhtml = '<div class="head">Details for account belonging to \"<i><u>$full_name</u></i>\".</div>';
	

	if (!$permissions->cms == 1) {
		$xhtml .= "$full_name has CMS privilages. They are as follows:";
		$cms = (object) $permissions->cms;
		if ( !$cms->content ) {
			$xhtml .= "<li>Content management</li>";
		}
	} else {
		$xhtml .= "$full_name has no CMS privilages.";
	}
	return array("xhtml"=>$xhtml);
}

function SuperAdmins($framework, $ui, $params) {
		$record = RECORD;
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
$javascript .=<<<EOF

EOF;
	$xhtml .= '<div id="commnetivity_leftnav_container">';
	if (SCREEN == 'search') {

		$sql = ( defined('RECORD') ) ? "SELECT * FROM `commnetivity_media` WHERE `extention` = 'mp3' AND `orig_filename` LIKE '%".RECORD."%' LIMIT 15;"
									 : "SELECT * FROM `commnetivity_media` WHERE `extention` = 'mp3' LIMIT 15;";
	} else {	
		$xhtml .= '<div id="commnetivity_leftnav_search"><input name="commnetivity_leftnav_search" value="Search"></div>';
	    $xhtml .= '<div id="commnetivity_leftnav" class="scrollable vertical"><div class="items">'."\n";
		$sql = "SELECT * FROM `commnetivity_media` WHERE `extention` = 'mp3'";
	}
	
    $sql = "SELECT * FROM `commnetivity_users` WHERE 1";
    $results = mysql_query($sql);
	$counter = 0;
    while($result = mysql_fetch_object($results)){
        $permissions = (object) unserialize($result->permissions);
		$preferences = (object) unserialize($result->preferences);
		//print_r($result);
		if ( $result->fname && $result->lname ) {
			if ( $result->fname && $result->lname ) {
                $full_name = $result->fname . ' ' . $result->lname;
            } elseif ( $result->fname ) {
                $full_name = $result->fname;
            } else {
                $full_name = $result->lname;
			}
		} else {
            $full_name = "No name";
        }
        $full_name = ($result->fname || $result->lname) ? $result->fname . ' ' . $result->lname : "Unidentified";
		$xhtml .= '<div class="item"><a id="'.$result->id.'" href="#'.$result->username.'">'.$full_name.' ('.$result->username.')</a></div>';
		unset($full_name);
		if ( $counter > 3 ) { $xhtml .= "\t".'</div>'."\n\t".'<div>'."\n"; }
		if ( $counter == 4 ) { $counter = 0; }
		$counter++;
    }
	if (SCREEN == 'search') {
		$javascript = '$(".scrollable").scrollable({ vertical: true, mousewheel: true });';
		return array("leftnav"=>$xhtml, "sql"=>$sql, "javascript"=>"$javascript");
		exit;
	}
    $xhtml .= "\t".'</div>'."\n"; //leftnav
    $xhtml .= "\t".'</div>'."\n"; //leftnav_container
    $xhtml .= <<<EOF
</div>
</div>
    <div id="commnetivity_windows">
	<ul><li>Please select an image file to begin working.</li></ul>
	<div id="commnetivity_preview"></div>
</div>
EOF;
return array("display"=>$xhtml, "javascript"=>$javascript);

}


?>