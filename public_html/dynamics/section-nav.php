<?php
$string = ''.$vpath.'';
$pieces = explode("/",$string);
$page = array_pop($pieces);
$location = str_replace(".html"," ",$page);
$section = '/'.$pieces[1].'/index.html';
//echo $section;
echo '<div class="white-seventy round blackborder sm-padding">';
echo '<h6>'.strtoupper($pieces[1]).'</h6>';
echo '<ul class="side-nav">';
$query = "SELECT * FROM `commnetivity_content` WHERE `parent_id` = '$section' AND `virtual_path` != '/$pieces[1]/index.html' ";
//$query = "SELECT * FROM `commnetivity_content` WHERE `virtual_path` LIKE '$section' AND `virtual_path` != '/$pieces[1]/index.html' GROUP BY `parent_id` ";
//echo ''.$query.'<hr />';
$result = mysql_query($query);
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_object($result)){ 
			$pageloc = $row->virtual_path;
			$pagetitle = $row->page_title;
			
					echo '<li><a href="'.$pageloc.'">'.$pagetitle.'</a></li>';
				}
			
	}else {echo 'We can\'t seem to find what should be here';}
	echo '</ul>';
	echo '</div>';
?>