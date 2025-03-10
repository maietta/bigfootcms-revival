<?php

	if ( isset($_SESSION['username']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/my_account/'.$_SESSION['username'].'.txt') ) {
		$roles = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/my_account/'.$_SESSION['username'].'.txt');
		echo '<textarea>';
		print_r($roles);
		echo '</textarea>';
	} else {

	}
//echo "Hello from VOAD Blog system. At this point, no security checkpoint was given because 1, it wasn't restricted by any rule, and two, it's considered a public post.";

?>