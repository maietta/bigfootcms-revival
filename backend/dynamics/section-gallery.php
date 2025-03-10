<?php
$string = ''.$vpath.'';
$pieces = explode("/",$string);
$page = array_pop($pieces);
$location = str_replace(".html"," ",$page);
$section = '/'.$pieces[1].'/%';

//$result = mysql_query("SELECT * FROM `commnetivity_content` WHERE `parent_id` = '$vpath' AND `virtual_path` != '$vpath' ORDER BY id;");
$query = "SELECT * FROM `commnetivity_media` WHERE `group` = '$pieces[2]' ";
//echo ''.$query.'<hr />';
$result = mysql_query($query);
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_object($result)){ 
			$pageloc = $row->virtual_path;
			$pagetitle = $row->descreption;
			
					echo '<img src="/media/'.$row->real_filename.'" alt="'.$row->description.'" style="margin:5px 0px 5px 0px;" />';
				}
			
	}else {echo '<img src="http://placehold.it/400x300&text=['.$location.']" />';}

?>