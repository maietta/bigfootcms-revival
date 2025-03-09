<?php

/* $prepared->matches_pattern */

$prepared = (object) array(); // change to $content_discovery;
if ( $_SERVER['REDIRECT_STATUS'] != 200 ) {
	if ( $_SERVER['REDIRECT_STATUS'] == 403 ) {
		$prepared->status = 200;
		$prepared->debug = "Directory listing denied.";
	} else {
		$prepared->status = $_SERVER['REDIRECT_STATUS'];
		$prepared->debug = "Directory listing denied.";
	}
}

if ( isset($dynamic->content) ) {
	$prepared->internal = $dynamic->content;
	if ( isset($pattern_used) ) {
		$prepared->vpath_pattern = $pattern_used;
	}
	$prepared->debug = "Commnetivity's internal system derrived the content for this page.";
} elseif ( $content->internal_path ) {
	if (file_exists(PATH . $content->internal_path)) {
		$prepared->script = PATH . $content->internal_path;
		$prepared->debug = "VirtualPath defined script \"".$content->internal_path."\" integrated into the rendered template.";
	} else {
		$prepared->debug = "VirtualPath defined script \"".$content->internal_path."\" does not exist or has incorrect permissions.";
	}
} elseif ( $content->virtual_path ) {
	/* There is indeed a record in the database .. */
	if ( $trumper->internal_path ) {
		if (file_exists(PATH . $trumper->internal_path)) {
			$prepared->script = PATH . $trumper->internal_path;
			$prepared->debug = "SiteLevel defined script \"".$trumper->internal_path."\" integrated into the rendered template.";
		} else {
			if (isset($content->encoded_content)) {
				$prepared->content = $content->encoded_content;
				$prepared->debug = "SiteLevel and VirtualPath defined scripts were trumped using static content.";
			} elseif (file_exists(PATH . $content->internal_path)) {
				$prepared->script = PATH . $content->internal_path;
				$prepared->debug = "SiteLevel trumped using VirtualPath internally defined script.";
			} else {
				$prepared->debug = "No SiteLevel, VirtualPath or StaticContent available.";
			}
		}
	} else {
		if ( $content->virtual_path ) {
			if ( $content->encoded_content ) {
				$prepared->content = $content->encoded_content;
				$prepared->debug = "VirtualPath defined script was trumped using static content.";
			} else {
				if (file_exists(PATH . $content->virtual_path)) {
					$prepared->script = PATH . $content->virtual_path;
					$prepared->debug = "VirtualPath defined script \"". $content->virtual_path."\" integrated into the rendered template.";
				} else {
					$prepared->debug = "VirtualPath defined script \"". $content->virtual_path."\" does not exist or has incorrect permissions.";
				}
			}
		} else {
			if ( isset($content->encoded_content) && strlen($content->encoded_content) > 0 ) {
				$prepared->content = $content->encoded_content;
			} else {
				$prepared->debug = htmlentities("No ");
			}
		}
	}
} elseif ($trumper->internal_path) {
	if (file_exists(PATH . $trumper->internal_path)) {
		$prepared->script = PATH . $trumper->internal_path;
		$prepared->debug = "SiteLevel defined script \"".$trumper->internal_path."\" integrated into the rendered template.";
	} else {
		$prepared->debug = "Sitelevel defined script \"".$trumper->internal_path."\" does not exist or has incorrect permissions.";
	}
} else {
	/* Error 404 */
	$prepared->debug = "No content was defined internally, via vpath, sitelevel or via encoded_content.";
}

if ( $prepared->internal ) {
	$prepared->status = 200;
	$prepared->response = $prepared->internal;
} elseif ( isset($prepared->content) || isset($prepared->script) ) {
	if ( $prepared->script ) {
		$prepared->status = 200;
		// "Run script";
		ob_start();
		include($prepared->script);
		$content_area = ob_get_contents();
		//$internal = (object) unserialize($content_area);
		ob_end_clean();
		$prepared->response = $content_area;
	} else {
		if ( isset($prepared->content) ) {
			$prepared->status = 200;
			$prepared->response = stripslashes(base64_decode($prepared->content));
		}
	}
} else {
	if ( !is_array($permissions->cms) ) {
		$prepared->status = 404;
		$prepared->response  = "<h4>404 Not Found</h4>\n";
		$prepared->response .= "<p>The requested resource could not be found but may be available again in the future.</p>";
	} else {
		$prepared->status = 404;
		$prepared->response  = "<h4>Ready for content</h4>\n";
		$prepared->response .= "<p>Until static or dynamic content is assigned to this VirtualPath or SiteLevel, the public will see a general Error 404 response.</p>";
	}
}

$dynamics_in_content = array();
$content_html = str_get_html($prepared->response);
foreach($content_html->find('div') as $element) {
	//array_push($dynamics_in_content, $element->id);
	if ( $element->id ) { array_push($dynamics_in_content, $element->id); }
}

while (list(, $dynamic_item) = each($dynamics_in_content)) {
	$dynamic_path = (PATH."/dynamics/".$dynamic_item.".php");
	if ( file_exists($dynamic_path) ) {
		ob_start();
		require_once($dynamic_path);
		$dyn_out = ob_get_contents();
		if ( strlen(trim($dyn_out)) == 0 ) { $dyn_out = " "; }
		$widget = str_get_html($dyn_out);
		foreach($widget->find('link') as $element) {
			if ($element->href) {
				$stylesheets[] = $element->href;
			}
			$element->outertext = "";
		}
		foreach($widget->find('player') as $element) {
			if ($element->id) { $players[] = $element->id; }
		}
 		foreach($widget->find('gallery') as $element) { if ($element->id) { $galleries[] = $element->id; } }
		foreach($widget->find('script') as $element) {
			if ($element->method != "sticky") {
				if ($element->src) {
					$header_js[] = $element->src;
					$element->outertext = "\n";
				} else {
					$header_js_text[] = $element->innertext;
					$element->innertext = "\n";
					$element->outertext = "\n";
				}
			}
		}
		ob_end_clean();
	} else {
		$content_html->find('div[id='.$dynamic_item.']', 0)->innertext = null;
		unset($widget);
	}
	$content_html->find('div[id='.$dynamic_item.']', 0)->innertext = ($widget) ? $widget : null;
}

$html->find('div[id=content]', 0)->innertext = $content_html->innertext;

?>
