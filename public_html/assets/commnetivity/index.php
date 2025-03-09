<?php

//ini_set("display_startup_errors", "1");
//ini_set("display_errors", "1");
//error_reporting(E_ALL);

session_name("Commnetivity");

require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/simple_html_dom.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/commnetivity/htmLawed.php');
require($_SERVER['DOCUMENT_ROOT'] . '/assets/commnetivity/framework.class.php');

$framework = new Framework;

session_start();

header('HTTP/1.1 200 OK');

$_SESSION['last_seen'] = time();
$url = $_SERVER['REQUEST_URI'];

/* Old method to drive VPATH. Replaced in latest versions. */
$vpath = $framework->vpath($url);
$sitelevel = $framework->sitelevel($vpath);
if ($sitelevel == '/commnetivity') {
    $commnetivity = true;
    if ( $_REQUEST['vpath'] != "" ) {
        define("VPATH", $framework->vpath($_REQUEST['vpath']));
        define("SITELEVEL", $framework->sitelevel(VPATH));
        define("RAIL", $vpath); // Rail is /commnetivity/whatever
        define("RAILLEVEL", SITELEVEL);
    } else {
        define("VPATH", "/index.html");
        define("SITELEVEL", "/");
        define("RAIL", $framework->vpath($vpath)); // Rail is /commnetivity/whatever
        define("RAILLEVEL", "/");
     }
} else {
    define("VPATH", $vpath);
    define("SITELEVEL", $sitelevel);
}

$_SESSION['referrer'] = VPATH;

if ( $url->path == '/robots.txt' ) { echo $framework->robots(); exit; }
if ( $url->path == '/favicon.ico' ) { header("Location: http://www.testing-area.net/favicon.ico"); exit; }

require(PATH.COMMNETIVITY.'/dashboard.php');

if ( $framework->redirects($url->path)->virtual_target ) header("Location: http://" . HOSTNAME . $virtual_target);

$content = $framework->query(VPATH, "content");
	$security = (object) unserialize($content->security);
	$meta = (object) unserialize($content->meta_data);
	$theme = (object) unserialize($content->theme);

$overrides = $framework->check_override(SITELEVEL, "overrides");

$trumper = (object) $overrides;
$profile = ( $_SESSION['username'] ) ? $framework->profile($_SESSION['username']) : 0;
$permissions = (object) unserialize($profile->permissions);

// If CMS is available, let's set global flag. This needs to get moved into CMS plugin.
if ( $permissions->cms ) {
	$controls = (object) $permissions->cms;
	if ( $controls->content ) { define("EDITABLES", TRUE); } else { define("EDITABLES", FALSE); }
} else {
	define("EDITABLES", FALSE);
}
// Set SSL trigger override as needed.
if ( $trumper->ssl_enforce ) { // override
	$trigger_pattern = ($trumper->ssl_enforce . $_ENV['SERVER_PORT']);
} elseif ( $security->ssl_required ) {
	$trigger_pattern = ($security->ssl_required . $_ENV['SERVER_PORT']);
} else { // default.
	$trigger_pattern = ("O" . $_ENV['SERVER_PORT']);
}
$header_css_text = array();

// If sitelevel (override) ssl trigger is set, we'll ignore $content->ssl_required Y/N trigger.
if (defined('SSL_HOST')) {
	switch ($trigger_pattern) {
		case 'N80'; break;
		case 'Y80'; header("Location: https://".SSL_HOST.VPATH); exit; break;
		case 'Y443'; break;
		case 'N443'; header("Location: http://".SSL_HOST.VPATH); exit; break;
		default; break;
	}
} else {
	if ( $_ENV['SERVER_PORT'] == 443 ) {
		header("Location: http://" . HOSTNAME . (( TOOLBOX == 'cms' ) ? "/" . TOOL . VPATH : VPATH));
		exit;
	}
}

if ( $sitelevel == "/commnetivity" ) {
    $rails = explode("/", $vpath);
    define("TAB", $rails['2']);
    define("PANEL", $rails['3']); //4
    define("SCREEN", $rails['4']); //5
	define("ACTION", $rails['5']); //7
	if ($rails['6'] != DIRECTORY_DEFAULT) { //6
    	define("RECORD", $rails['6']); //6
	}
    switch (TAB) {
        case "search":
            ob_start();
            require_once(PATH.'/dynamics/search.rpc.php');
            $commnetivity = ob_get_contents();
            ob_end_clean();
            echo $commnetivity; unset($commnetivity);
            break;
        exit;
        case "upload":
            ob_start();
            require_once(PATH.COMMNETIVITY.'/upload.rpc.php');
            $commnetivity = ob_get_contents();
            ob_end_clean();
            echo $commnetivity; unset($commnetivity);
            break;
        case "update_content":
            ob_start();
            require_once(PATH.COMMNETIVITY.'/update_content.rpc.php');
            $commnetivity = ob_get_contents();
            ob_end_clean();
            echo $commnetivity; unset($commnetivity);
            break;
        case "content":
		require(PATH.COMMNETIVITY.'/editor.php');
	        break;
        case "script_content":
            if (file_exists(PATH.mysql_real_escape_string($_REQUEST['internal_path']))) {
            	ob_start();
            	require_once(PATH.mysql_real_escape_string($_REQUEST['internal_path']));
            	$commnetivity = ob_get_contents();
            	ob_end_clean();
            	echo $commnetivity; unset($commnetivity);
            } else {
                echo "The scripted resource is unavailable. Please double check the path and spelling and that the script echos or prints out content.";
            }
            break;
        case "init":
            ob_start();
            require_once(PATH.COMMNETIVITY.'/control_panel.php');
			$commnetivity  = ob_get_contents();
            ob_end_clean();
            echo $commnetivity;
			unset($commnetivity);
            break;
        case $rails['2'] :// This hook extends the request for control panel tabs to the core framework for processing.
			echo ($_SERVER['REQUEST_METHOD'] == "POST") ?
				$framework->cpuixhrrpc(PATH.'/assets/commnetivity/ui/tab.'.$rails['3'].'.rpc.php') :
				$framework->cpuixhrrpc(PATH.'/assets/commnetivity/ui/tab.'.$rails['3'].'.php');		
			break;
        default:
			echo json_encode(array("display"=>"<h1>No results for core</h1><p></p>"));
            break;
    }
    exit;
}

$_SESSION['referrer'] = VPATH;
$_SESSION['next_url'] = VPATH;

$trumper_theme = (object) unserialize($trumper->theme);
if ( isset($trumper_theme->template) ) {
	$sitelevel_template = $trumper_theme->template;
}
if ( isset($theme->template) ) {
	$vpath_template = $theme->template;
}
if ( isset($sitelevel_template) ) {
	if ( isset($vpath_template) ) {
		/* Okay, we have two to choose from... which shall it be? */
		$template = ( isset($trumper_setting) && $trumper_setting == "sitelevel" ) ? $sitelevel_template : $vpath_template;
	} else {
		$template = $sitelevel_template;
	}
} else {
	/* No sitelevel template is available... */
	if ( !isset($vpath_template) ) {
		/* No $vpaath_template available.. use default */
		$template = "default.dwt";
	} else {
		$template = $vpath_template;
	}
}

$template = TEMPLATES . $template;

//if ( $dynamic->template ) { $template = $dynamic->template; }
//if ( !file_exists("$template") ) { $template = TEMPLATES . TEMPLATE_DEFAULT; }
$html = file_get_html($template);
//$html = str_get_html(preg_replace('/<!-- TemplateBeginEditable name="(.*?)" -->/i', '<template name="$1">', $html));
//$html = str_get_html(str_replace('<!-- TemplateEndEditable -->', '</template>', $html));
$header = $html->find('head', 0)->innertext;
$header = str_get_html(str_replace($html->find('template[name=head]', 0)->outertext, "", $header));

//global $header_js;
//global $header_js_text;
//global $players;
//global $stylesheets;
//global $header_css; 
//global $header_css_text;
if ( is_array($permissions->cms) && SITELEVEL != "/my_account") {
	$init .= "\t".'setTimeout(function(){ $.ajax({ url: "/commnetivity/init", type: "POST", data: { vpath: "'.$vpath.'" }, dataType: "json", success: function(commnetivity){ $(\'body\').prepend(commnetivity.control_panel); $(\'body\').prepend(commnetivity.script); } }); }, 0);';
}

$header_js = array();
$header_js_text = array("".$init."");
foreach($header->find('script') as $element) {
	if ($element->src) {
		array_push($header_js, $element->src);
	} else {
		array_push($header_js_text, $element->innertext);
	}
}

$players = array();
$stylesheets = array();

foreach($header->find('link') as $element) {
        
		if ($element->href) {
			array_push($stylesheets, $element->href);
		}
	
}


// Set array to include target divs from internally operated script.
$divs_in_template = ( $dynamic->targets ) ? $dynamic->targets : array();
//$divs_in_template = array();
$js_collection = array(); $css_collection = array(); $browser_collection = array();
// What's missing? <style>.*?</style>
foreach($html->find('div') as $element) {
	array_push($divs_in_template, $element->id);
	if ( $element->js ) { array_push($js_collection, $element->js); }
	if ( $element->css ) { array_push($css_collection, $element->css); }
	if ( $element->browser ) { array_push($browser_collection, $element->browser); }
}

while (list(, $dynamic_item) = each($divs_in_template)) {
	$dynamic_path = (PATH."/dynamics/".$dynamic_item.".php");
	
	if ( file_exists($dynamic_path) ) {
		ob_start();
		require_once($dynamic_path);
		$dyn_out = ob_get_contents();
		if ( strlen(trim($dyn_out)) == 0 ) { $dyn_out = " "; }
		
		$widget = str_get_html($dyn_out);

		foreach($widget->find('link') as $element) { if ($element->href) { $stylesheets[] = $element->href; } $element->outertext = ""; }
		foreach($widget->find('player') as $element) { if ($element->id) { $players[] = $element->id; } }

 		foreach($widget->find('gallery') as $element) {
			if ($element->id) {
				$galleries[] = $element->id;
			}
		}
		
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
		$html->find('div[id='.$dynamic_item.']', 0)->innertext = null;
		unset($widget);
	}
	$html->find('div[id='.$dynamic_item.']', 0)->innertext = ($widget) ? $widget : null;
}

if ( isset($dashboard) ) { $html->find('div[id=dashboard]', 0)->innertext = $dashboard; }

$areas_requiring_checkpoint = explode("\n", trim(file_get_contents($_SERVER['DOCUMENT_ROOT']."/restricted.txt")));
$areas_overriding_checkpoint = explode("\n", trim($profile->notes));

foreach($areas_requiring_checkpoint as $pattern) {
	if (fnmatch($pattern, VPATH) && !in_array($pattern, $areas_overriding_checkpoint)) {
		if ( !is_array($dynamic) ) {
			//$dynamic->content = "<p>Restricted by ruleset.</p>";			
			if ( isset($_SESSION['username']) ) {
			//	$roles = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/my_account/'.$_SESSION['username'].'.txt');
				if (file_exists($_SERVER['DOCUMENT_ROOT'].'/my_account/'.$_SESSION['username'].'.ini')) {
					$roles = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/my_account/'.$_SESSION['username'].'.ini', true);
					if ( isset($roles['whitelist']) ) {
						$whitelist = $roles['whitelist'];
						foreach($whitelist as $pattern_to_check=>$pattern_name) {
							if (fnmatch($pattern_to_check, VPATH)) {
								$has_permission = true;
							}
						}
					}
				}
				if ( !isset($has_permission) ) {
					$dynamic->content  = "<p>At present, this account does not have enough \"permissions\" to gain access to this resource.</p>";
					$dynamic->content .= "<p>To view a listed of resources you are allowed access, please <a href=\"/my_account/permissions\">follow this link</a>.</p>";
				}
			} else {
				$_SESSION['next_vpath'] = VPATH;
				header("Location: /login");
			}
		}
	}
}




/* Hook pre-content-switcher */
if ( file_exists($_SERVER['DOCUMENT_ROOT']."/vpath_map.txt") ) {
	$areas_to_emulate_blogs = explode("\n", trim(file_get_contents($_SERVER['DOCUMENT_ROOT']."/vpath_map.txt")));
	foreach($areas_to_emulate_blogs as $line) {
		list($pattern, $patterned_script) = explode("=", $line);
		$pattern = trim($pattern); $patterned_script = trim($patterned_script);
		if (fnmatch($pattern, VPATH) && file_exists($_SERVER['DOCUMENT_ROOT'].$patterned_script)) {
			include($_SERVER['DOCUMENT_ROOT'].$patterned_script);
		}
	}
}
include(PATH . COMMNETIVITY . "/content_switcher.php");
/* Hook post-content-switcher */



if ( !isset($page_title) ) { $page_title = "Untitled Document"; }


$head  = '<meta charset="utf-8" />'."\n";
$head .= '<meta name="viewport" content="width=device-width" />'."\n";


$document_title = ($trumper->page_titles)?$trumper->page_titles:(($content->page_title)?$content->page_title:(($dynamic->title)?$dynamic->title:(($content->virtual_path)?"Untitled Document":"$page_title")));
if ( VPATH != '/' . DIRECTORY_DEFAULT) {
	$head .= "\n<title>" . stripslashes($document_title) . str_replace("www.", "", strtolower(AMMEND_TITLES)) . "</title>\n";
} else {
	$head .= ( $document_title ) ? "\n<title>".stripslashes($document_title)."</title>\n" : "\n<title></title>\n";
}
if ( $internal->title ) { $head = "\n<title>" . $internal->title . str_replace("www.", "", strtolower(AMMEND_TITLES))."</title>\n"; }

$head .= ( $meta->keywords )
	? "<meta name=\"keywords\" content=\"" . $meta->keywords . "\" />\n"
	: "<meta name=\"keywords\" content=\"" . DEFAULT_KEYWORDS . "\" />\n";
$head .= ( $meta->description )
	? "<meta name=\"description\" content=\"" . $meta->description . "\" />\n"
	: "<meta name=\"description\" content=\"" . DEFAULT_DESCRIPTION . "\" />\n";
if ( $meta->author ) { $head .= "<meta name=\"author\" content=\"" . $meta->author . "\" />\n"; }


while ( list ($key, $javascript ) = each ( $header_js ) ) {
	$javascript = str_replace(PATH, "", $javascript);
	$head .= (file_exists(PATH.$javascript))?"<script src=\"".$javascript."\"></script>\n":"<script src=\"".$javascript."\"></script>\n";
}
while ( list ($key, $javascript ) = each ( $js_collection ) ) {
	$javascript = str_replace(PATH, "", $javascript);
        if (strpos($javascript, '/media/js/') === false) {
            $head .= (file_exists(PATH.$javascript))?"<script src=\"".$javascript."\"></script>\n":"<script src=\"".$javascript."\"></script>\n";
        } else {
            $head .= '<script src="'.$javascript.'"></script>'."\n";
        }
}

foreach($html->find('div') as $element)  { $element->css = null; $element->js = null;  }


if ( $permissions->cms ) {
	$controls = (object) $permissions->cms;
	if ( $controls->content ) {
		$head  .= '<script src="/assets/ckeditor/config.js"></script>'."\n";
		$head  .= '<script src="/assets/ckeditor/ckeditor.js"></script>'."\n";
		

		$head  .= '<script src="/assets/ckeditor/adapters/jquery.js"></script>'."\n";
	}
	$head .= '<script src="/assets/commnetivity/cms_xhr_upload.js"></script>'."\n";
	//$head .= '<script src="/assets/edit_area/edit_area_full.js"></script>'."\n";
	
	$head .= '<link rel="stylesheet" href="/assets/commnetivity/control_panel.css" />'."\n";
}

while ( list ($key, $stylesheet ) = each ( $stylesheets ) ) { //stylesheets from header
	$head .= (file_exists(PATH . $stylesheet))
		? "<link href=\"$stylesheet?" . filemtime(PATH . $stylesheet) . "\" rel=\"stylesheet\" />\n"
		:"\n";
}
while ( list ($key, $stylesheet ) = each ( $css_collection ) ) { //stylesheets
	$stylesheet = str_replace(PATH, "", $stylesheet);
	$head .= (file_exists(PATH . $stylesheet))
		? "<link rel=\"stylesheet\" href=\"$stylesheet?" . filemtime(PATH . $stylesheet) . "\" />\n"
		: "\n";
}


if ( count($header_js_text) > 0 ) { $head .= "<script>\n"; }

$js_head = "";
while ( list ($key, $header_js_text_filling ) = each ( $header_js_text ) ) {
	$js_head .= "\t$header_js_text_filling\n";
}
$head .= trim($js_head);
if ( count($header_js_text) > 0 ) { $head .= "\n</script>\n"; }

if ( count($header_css_text) > 0 ) { $head .= "<style>"; }
while ( list ($key, $header_css_text_filling ) = each ( $header_css_text )) { $head .= "$header_css_text_filling"; }
if ( count($header_css_text) > 0 ) {  $head .= "</style>\n"; }

$html->find('head', 0)->innertext = $head;
//$html->find('template[name=head]', 0)->outertext = "";
$template_content = $html->find('template[name=content]', 0)->innertext;
$html->find('div[id=content]', 0)->innertext;
//$html->find('template[name=content]', 0)->outertext = "$template_content";


// tell content switcher how to lift copies of js and css for pushing through rpc call.
if ($_REQUEST['target']=="content") {
    echo $html->find('div[id=content]', 0)->innertext;
} else {

	// $processed = htmLawed("$html");
	// echo $processed;
	
	$body = $html->find('body', 0)->innertext;
	$out = hl_tidy("$body", 't', 'div');
	$html->find('body', 0)->innertext = $out;
	$html = str_replace("<head>", "\n<head>\n", $html);
	$html = str_replace("</head>", "\n</head>\n", $html);
	$html = str_replace("<style></style>", "", $html);
	$html = str_replace('});</script>', ';\n</script>', $html);
	$html = str_replace("\t</script>", "\n\t</script>\n", $html);
	$html = str_replace("</script>", "</script>\n", $html);
	$html = str_replace("\t</body>", "</body>", $html);
	$html = str_replace(" </body>", "<body>", $html);
	$html = str_replace("</html>", "\n</html>", $html);
	$html = str_replace("\n\n", "\n\n", $html);
	
	//$html = str_replace("\n</script>", "</script>", $html);
	//$html = str_replace("</div>><script>", "</div>\n    <script>", $html);
	//$html = str_replace("><script", ">\n    <script", $html);
	//$html = str_replace("</script><!--", "</script>\n\n    <!--", $html);
	
	echo $html;
}
exit;
 
?>