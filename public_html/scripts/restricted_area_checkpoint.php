<?php

/* THIS SCRIPT IS TO BE CALLED ONLY WHEN THERE IS A REQUIREMENT FOR PERMISSIONS TO BE CHECKED */

/* This system will check user profile for permissions for this resource. If none is granted, then set a $dynamic->content with content. */

if ( isset($_SESSION['username']) ) {	
	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/my_account/'.$_SESSION['username'].'.ini')) {
		$roles = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/my_account/'.$_SESSION['username'].'.ini', true);
		print_r($roles);
		if ( isset($roles['whitelist']) ) {
			$whitelist = $roles['whitelist'];
			print_r($whitelist);
		}
	}

	$dynamic->content  = d"<p>At present, this account doeds not have enough \"permissions\" to gain access to this resource.</p>";
	$dynamic->content .= "<p>To view a listed of resources you are allowed access, please <a href=\"/my_account/permissions\">follow this link</a>.</p>";
} else {
	/* */
	$_SESSION['next_vpath'] = VPATH;
	header("Location: /login");
}
?>