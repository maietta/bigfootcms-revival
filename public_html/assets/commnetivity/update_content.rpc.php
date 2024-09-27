<?php

//if ( isset($_REQUEST['content'])  ) {
	//mysql_connect(DB_SERVER, DB_USER, DB_PASS);
	//mysql_select_db(DB_NAME);
        
        $vpath_to_update = $framework->vpath(mysql_real_escape_string($_REQUEST['vpath'])); // Fixed Dec 21st, 2010. Instead of /contact_us/ on save, it now becomes /contact_us/index.html on save.
        
	$updated_content = mysql_real_escape_string($_REQUEST['content']);
        $updated_js = mysql_real_escape_string($_REQUEST['js']);
        $updated_css = mysql_real_escape_string($_REQUEST['css']);

	//"SELECT id";
	$sql = "SELECT * FROM `commnetivity_content` WHERE virtual_path = '".$vpath_to_update."' LIMIT 1";
//        echo '<script>alert("'.$sql.'");</script>';
	$result = mysql_fetch_array(mysql_query($sql));
	$time = time();
	$sql = "INSERT INTO `commnetivity_content_hist` (`virtual_path`, `page_title`, `nav_title`, `parent_id`, `cleartext_excerpts`, `encoded_content`, `date_archived`, `publishers_level`, `security`, `meta_data`, `updated_by`, `hits`) VALUES ('".$vpath_to_update."', '".$result[internal_path]."', '".$result[page_title]."', '".$result[parent_id]."', '".$result[cleartext_exerpts]."', '".$result[encoded_content]."', NOW(), '".$result[publishers_level]."', '".$result[security]."', '".$result[meta_data]."', '".$result[modified_by]."', '".$result[hits]."');";
	mysql_query($sql);
	$updated_content = str_replace("\\r\\n", "\r\n", $updated_content);
        $updated_content = str_replace("\\n", "\n", $updated_content);
	$updated_content = stripslashes($updated_content);
	$updated_content = base64_encode($updated_content);
        
        $updated_js = str_replace("\\r\\n", "\r\n", $updated_js);
        $updated_js = str_replace("\\n", "\n", $updated_js);
	$updated_js = stripslashes($updated_js);
	$updated_js = base64_encode($updated_js);

        $updated_css = str_replace("\\r\\n", "\r\n", $updated_css);
        $updated_css = str_replace("\\n", "\n", $updated_css);
	$updated_css = stripslashes($updated_css);
	$updated_css = base64_encode($updated_css);
        
 	$sql = "UPDATE `commnetivity_content` SET `encoded_content`='" . $updated_content . "' WHERE `virtual_path`='" . $vpath_to_update . "' LIMIT 1;";
	mysql_query($sql);
        $sql = "UPDATE `commnetivity_content` SET `encoded_javascript`='" . $updated_js . "' WHERE `virtual_path`='" . $vpath_to_update . "' LIMIT 1;";
	mysql_query($sql);
        $sql = "UPDATE `commnetivity_content` SET `encoded_stylesheet`='" . $updated_css . "' WHERE `virtual_path`='" . $vpath_to_update . "' LIMIT 1;";
	mysql_query($sql);
//echo '<script>alert("'.$sql.'");</script>';
 	$sql = "UPDATE `commnetivity_content` SET `modified_by`='" . $_SESSION['username'] . "' WHERE `virtual_path`='" . $vpath_to_update . "' LIMIT 1;";
	mysql_query($sql);
        // output something here.... ?
//        echo '<script>alert("'.$sql.'");</script>';

	if ( defined(SSL_HOST) ) {
//		header("Location: https://" . HOSTNAME . "/edit" . "$vpath_to_update");
	} else {
//		header("Location: http://" . HOSTNAME . "/edit" . "$vpath_to_update");
	}
	//exit;
//} else {
//	echo "Direct access to this script is denied!";
//}

?>