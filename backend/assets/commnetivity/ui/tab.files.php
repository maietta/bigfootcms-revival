<?

function initialize($framework, $ui, $params){
    $xhtml .= '<ul id="control_panel_subnav_tabs">';
    $xhtml .= '<li class="commnetivity_ui_left"><a href="/commnetivity/tab/files/uploaded">Local FTP Server</a></li>';
    $xhtml .= '<li class="commnetivity_ui_middle"><a href="/commnetivity/tab/files/ftp">Remote FTP Servers</a></li>';
    $xhtml .= '<li class="commnetivity_ui_middle"><a href="/commnetivity/tab/files/cnd">Content Delivery Network</a></li>';
	$xhtml .= '<li class="commnetivity_ui_right"><a href="/commnetivity/tab/files/activity">Recent Activity</a></li>';
    
    
    $xhtml .= '</ul>';
	$xhtml .= '<div id="commnetivity_windows">';
	$xhtml .= '<div class="head">Digital Asset Management (Your files)</div>';
	$xhtml .= '<ul><li>Images, Audio & Video (Media files) are handled under the Media tab. For all other files, use this area.</li></ul>';
	$xhtml .= '</div>';

	return array("display"=>"$xhtml");
}



function uploaded($framework, $ui, $params) {
	$xhtml = '<div id="commnetivity_windows">';
	$xhtml .= '<ul>';
	$xhtml .= '<div class="head">File Manager</div>';
	$xhtml .= '<li>Note: This screen is just a placeholder.</li><li>Description: Commnetivity is planning to develop a user interface for handling management of files not considered media files. This would include such files as ZIP, DOC, DOCX, TIFF and other files that are not inherently web-safe or web-friendly.</li>';
	$xhtml .= '</ul>';
	$xhtml .= '</div>';
	return array("display"=>$xhtml);
}

function activity($framework, $ui, $params) {
	$dir=PATH;
	$comparedatestr="2010-05-19 00:00:00";
	$now = time();
	$day_ago = ( $now - 86400 );
	$comparedate = $day_ago;
	$items = directory_tree($dir,$comparedate);

	$xhtml = '<div id="commnetivity_windows">';
	$xhtml .= '<ul>';
	$xhtml .= '<div class="head">Recent Activity</div>';
	$xhtml .= '<li>'.$items.'</li>';
	$xhtml .= '</ul>';
	$xhtml .= '</div>';
	return array("display"=>$xhtml);
}

function directory_tree($address,$comparedate){
	$framework = new Framework;
	@$dir = opendir($address); 
	if(!$dir){ return 0; } 
	while($entry = readdir($dir)){
		if(is_dir("$address/$entry") && ($entry != ".." && $entry != ".")){                             
			$xhtml .= directory_tree("$address/$entry",$comparedate);
		} else {
			if($entry != ".." && $entry != ".") {
				$fulldir=$address.'/'.$entry;
		    	$last_modified = filemtime($fulldir);
		    	$last_modified_str= date("Y-m-d h:i:s", $last_modified);
		    	if($comparedate < $last_modified)  {
					$epoch = strtotime($last_modified_str);
					$file = str_replace(PATH, '', $fulldir);
					$xhtml .= $file.' ('.$framework->makeAgo($epoch).")<br>\n";
					//$array[$epoch] = "$xhtml";
					
				}
			
			}
		}
	}
	return $xhtml;
}

function ftp($framework, $ui, $params) {
	$xhtml = '<div id="commnetivity_windows">';
	$xhtml .= '<ul>';
	$xhtml .= '<div class="head">Remote FTP Servers</div>';
	$xhtml .= '<li>We will integrate tools for webmasters to move files between FTP servers.</li>';
	$xhtml .= '</ul>';
	$xhtml .= '</div>';
	return array("display"=>$xhtml);
}

function cnd($framework, $ui, $params) {
	$xhtml = '<div id="commnetivity_windows">';
	$xhtml .= '<ul>';
	$xhtml .= '<div class="head">Content Delivery Networks</div>';
	$xhtml .= '<li>Description: Content Delivery Networks (CDN) are great for organizations that share content across multiple websites. We\'ll help you manage that content using this section of the Commnetivity CMS interface.</li>';
	$xhtml .= '</ul>';
	$xhtml .= '</div>';
	return array("display"=>$xhtml);
	
}

function all($framework, $ui, $params) {
    $results = mysql_query("SELECT * FROM `commnetivity_media` WHERE `extention` != 'jpg' AND `extention` != 'png' AND `extention` != 'gif' AND `extention` != 'mp3' AND `extention` != 'mov' AND `extention` != 'mp4' AND `extention` != 'flv'");
    if ( mysql_num_rows($results) > 0 ) {
        while($result = mysql_fetch_object($results)) {
            if (file_exists(PATH."/media/".$result->real_filename) ) {
               // $xhtml .= '<div class="item"><a href="'.$result->real_filename.'">'.str_replace('.'.$result->extention, "", $result->orig_filename).'</option>';
            } else {
               // mysql_query("DELETE FROM `commnetivity_media` WHERE `real_filename`='".$result->real_filename."' LIMIT 1;");
            }
        }
    } else {
        $xhtml .= "No files available.";
    }
    

    return array("display"=>"$xhtml");
}

?>