<?php

if ( isset($_POST['vpath'])  ) {

	$initial_content = "This page is now ready to edit.";

	mysql_connect(DB_SERVER, DB_USER, DB_PASS);
	mysql_select_db(DB_NAME);
	$vpath_to_create = mysql_real_escape_string($_POST['vpath']);
	
	//$initial_content = str_replace("\\r\\n", "\n", $initial_content);
	$initial_content = addslashes($initial_content);
	$initial_content = base64_encode($initial_content);

	$sql = "INSERT INTO `commnetivity_content` (`virtual_path`, `encoded_content`) VALUES ('$vpath_to_create', '$initial_content');";
 //	$sql = "UPDATE `commnetivity_content` SET `encoded_content`='" . $updated_content . "' WHERE `virtual_path`='" . $vpath_to_update . "' LIMIT 1;";
	mysql_query($sql);
	
	if ( SSL_HOST ) {
		header("Location: https://" . HOSTNAME . "/edit" . "$vpath_to_create");
	} else {
		header("Location: http://" . HOSTNAME . "/edit" . "$vpath_to_create");
	}
	exit;
} else {
	echo "Direct access to this script is denied!";
}

?>