<?php

	switch(SITELEVEL) {
		case "/cert";
			ob_start();
			include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/cert.php");
			$dynamic->content .= ob_get_clean();
			break;
		case "/voad";
			ob_start();
			include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/voad.php");
			$dynamic->content .= ob_get_clean();
			break;
		case "/dart";
			ob_start();
			include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/dart.php");
			$dynamic->content .= ob_get_clean();
			break;
			
		case "/nhn";
			$zone = dirname(str_replace(SITELEVEL, "", VPATH).'/');
			if ( strstr($zone, '/zone-') == true ) {
				switch($zone) {
					case "/zone-1";
						ob_start();
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/nhn_zones_checkpoint.php");
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/show_10_blog_excerpts.php");
						$dynamic->content .= ob_get_clean();
						break;
					case "/zone-2";
						ob_start();
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/nhn_zones_checkpoint.php");
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/show_10_blog_excerpts.php");
						$dynamic->content .= ob_get_clean();
						break;
					case "/zone-3";
						ob_start();
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/nhn_zones_checkpoint.php");
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/show_10_blog_excerpts.php");
						$dynamic->content .= ob_get_clean();
						break;
					case "/zone-4";
						ob_start();
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/nhn_zones_checkpoint.php");
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/show_10_blog_excerpts.php");
						$dynamic->content .= ob_get_clean();
						break;
					case "/zone-5";
						ob_start();
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/nhn_zones_checkpoint.php");
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/show_10_blog_excerpts.php");
						$dynamic->content .= ob_get_clean();
						break;
					case "/zone-6";
						ob_start();
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/nhn_zones_checkpoint.php");
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/show_10_blog_excerpts.php");
						$dynamic->content .= ob_get_clean();
						break;
					case "/zone-7";
						ob_start();
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/nhn_zones_checkpoint.php");
						include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/show_10_blog_excerpts.php");
						$dynamic->content .= ob_get_clean();
						break;
					default: break;
				}
			} else {
				/* VPATH does not contain request to an NHN Zone, assume we are now sitting at /nhn/* but not /nhn/zone* */
				ob_start();
				//include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/nhn_zones_checkpoint.php");
				include($_SERVER['DOCUMENT_ROOT']."/scripts/blogs/nhn.php");
				$dynamic->content .= ob_get_clean();
				break;
			}
		default;
		//	$dynamic->content .= '<p>This page is restricted by blog rules, however, there is no path assignment to script. (Webmaster, see /scripts/universal_blog_system.php)</p>';
			break;
	}
	
?>