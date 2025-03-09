<?php
if ( xhr === true ) {
    require(PATH.COMMNETIVITY.'/ui/tabs/content.rpc.php');
    exit;
}
require_once(PATH.COMMNETIVITY.'/framework.class.php');
require_once(PATH.COMMNETIVITY.'/interface.class.php');
$ui = new screen;
$vpath = $framework->vpath(mysql_real_escape_string($_REQUEST['vpath']));
$sitelevel = $framework->sitelevel($vpath);
//echo $vpath;

if (SCREEN != "") {
    $screen = SCREEN;
    if (function_exists($screen)) {
        $response = $screen($vpath, $framework, $ui);
        if (is_array($response)) {
            if ( isset($response['display']) || isset($response['javascript']) ) {
                echo json_encode($response);
            } else {
                echo json_encode(array("display"=>"No display was set by this script."));
            }
        } else {
            echo json_encode(array("display"=>"$response"));
        }
    } else {
        echo json_encode(array("display"=>"<div id=\"white_form\"><p>This tab does not have a function by the $screen.</p></div>"));
    }
    exit;
} else {
    $subnavigation = '<ul id="control_panel_subnav_tabs">';
    $subnavigation .= '<li class="left"><a href="/commnetivity/tab/content/glance">At a glance</a></li>';
    $subnavigation .= '<li class="middle"><a href="/commnetivity/tab/content/collaboration">Collaboration</a></li>';
    $subnavigation .= '<li class="middle"><a href="/commnetivity/tab/content/presentation">Presentation</a></li>';
    $subnavigation .= '<li class="middle"><a href="/commnetivity/tab/content/availability">Availability</a></li>';
    $subnavigation .= '<li class="right"><a href="/commnetivity/tab/content/accessability">Accessability</a></li>';
    $subnavigation .= '<li class="seperator"></li>';
    $subnavigation .= '<li class="left"><a href="/commnetivity/tab/content/revisions">Revisions</a></li>';
    $subnavigation .= '<li class="middle"><a href="/commnetivity/tab/content/drafts">Drafts</a></li>';
    $subnavigation .= '<li class="right"><a href="/commnetivity/tab/content/notes">Notes</a></li>';
    $subnavigation .= '<li class="seperator"></li>';
    $subnavigation .= '<li><a href="/commnetivity/tab/content/help">?</a></li>';
    $subnavigation .= '</ul>';
    //$subnavigation .= introduction();
    echo json_encode(array("display"=>$subnavigation."<h3>This is a test from content.</h2>", "announce"=>"Hello."));        
}   
exit;

function introduction() {
    
    return ("Introduction to Commnetivity's CMS.");
}

function codllaboration($vpath, $framework, $ui) {
   // echo "Collaboration not yet implimented.";
    return array("display"=>"<h3>Collaboration in tab.content.php is not available.</h2>"); 
}
function presentation($vpath, $framework, $ui) {
    $content = $framework->query("$vpath", "content");
    $security = (object) unserialize($content->security);
    $meta = (object) unserialize($content->meta_data);
    $theme = (object) unserialize($content->theme);
    $xhtml = "";
    if ( $content->virtual_path ) {
		$xhtml .= '<div id="commnetivity_content_presentation_form">';
		$xhtml .= '<div id="commnetivity_content_presentation_form_input">';
        $xhtml .= $ui->make_select("Require SSL?", "ssl_required", $security->ssl_required, array("O"=>"Optional","Y"=>"Always"));
		$xhtml .= '</div>';
		$xhtml .= '<div id="commnetivity_content_presentation_form_input">';
        $xhtml .= $ui->make_select("Require Auth?", "auth_required", $security->auth_required, array("O"=>"Optional","Y"=>"Always"));
		$xhtml .= '</div>';
		$xhtml .= '<div>';
        $dh = opendir(TEMPLATES);
        while (false !== ($file = readdir($dh))) {
            if (!is_dir(TEMPLATES."$file")) {
                $file = preg_replace('/\..*$/', '', $file);
                //$templates[$file] =  htmlspecialchars(ucfirst());
                if ( file_exists(TEMPLATES . $file . ".dwt") ) {
                    $contents = file_get_contents(TEMPLATES . $file . ".dwt");
                    preg_match('/<title>([^>]*)<\/title>/si', $contents, $match );
                    if (isset($match) && is_array($match) && count($match) > 0) {
                        $templates[$file.'.dwt'] = strip_tags($match[1]);
                    }
                }
            }
        }
        closedir($dh); unset($dh); unset($file);
        if ( !$theme->template ) {
            $xhtml .= $ui->make_select("Template:", "template", TEMPLATE_DEFAULT, $templates); $xhtml .= "<br>";
        } else {
            if ( file_exists(TEMPLATES . $theme->template) ) {
                $contents = file_get_contents(TEMPLATES . $theme->template);
                preg_match('/<title>([^>]*)<\/title>/si', $contents, $match );
                if (isset($match) && is_array($match) && count($match) > 0) {
                    $title = strip_tags($match[1]);
                    $xhtml .= $ui->make_select("Template:", "template", "Found.", $templates);
                } else {
                    $xhtml .= $ui->make_select("Template:", "template", "No title", $templates);
                }
            }
        }
        //echo '</div>'; // End box
    } else {
        //echo '<div id="white_form">';
	$xhtml .= 'There is no content, static or dynamic for this path. '.$vpath.'<br>';
	$xhtml .= 'Would you like to create a record here?<br>';
	$xhtml .= $ui->make_input("Type \"yes\" to activate:", array("name"=>"vpath_activation", "maxlength"=>"3"), "");
        //echo '</div>';
    }
    $xhtml .= '</fieldset>';
    
    return array("display"=>"$xhtml");
}
function availability($vpath, $framework, $ui) {
    return true;
}
function accessability($vpath, $framework, $ui) {
    return true;
}
function revisions($vpath, $framework, $ui) {
            $num_archived_records = mysql_num_rows(mysql_query("SELECT * FROM `commnetivity_content_hist` WHERE `virtual_path` = '".$vpath."' ORDER by `date_archived` DESC"));
        if ( $num_archived_records > 1 ) {
            $human_readable_count = "(There are $num_archived_records versions archived.)";
        } elseif ( $num_archived_records == 1 ) {
            $human_readable_count = "(There is another version available.)";
        } else {
            $human_readable_count = "(There have been no revisions.)";
        }
        //SELECT virtual_path, page_title FROM `commnetivity_content` WHERE `parent_id` = '/index.html'
        $sql = "SELECT * FROM `commnetivity_content` WHERE `parent_id` = '".$vpath."' AND `virtual_path` != '".$vpath."';";
        $num_child_records = mysql_num_rows(mysql_query($sql));
        if ( $num_child_records > 1 ) {
            $dependants = "There are $num_child_records virtual paths that are linked to in navigation from this content resource. Deleting this resource will cause these resources to become abandoned unless linked to from inside your content or scripts.";
        } elseif ( $num_child_records == 1 ) {
            $dependants = "This virtual path has 1 other content resource that is found this navigation from this content resource. Deleting this resource will cause this resource to become abandoned unless it is linked to from static or dynamic content.";
        } else {
            $dependants = "This resource has no links in navigation to any other virtual paths. Deleting static or dynamic content at this web address will not cause any other content resources to become abandoned.";
        }
        //echo '<div id="white_form">';
        //    echo '<legend style="color: #08215A;">About this resource</legend>';
        //echo "<p><b><u>Revisions</u>:</b> $human_readable_count. Removing this resource will not remove this history. Any traces of content at this address must be removed by an administrator manually.</p>";
        //echo "<p><b><u>Warning</u>:</b> $dependants</p>";
        //echo '</div>';
        return true;
}
function notes($vpath, $framework, $ui){
    $content = $framework->query("$vpath", "content");
    $security = (object) unserialize($content->security);
    $meta = (object) unserialize($content->meta_data);
    $theme = (object) unserialize($content->theme);
    $xhtml  = '<div id="white_form">'.$vpath . " vs " . VPATH . "<br>";
    $xhtml .= '<strong>Use this form to keep private notes about this document. They will not be available for publishing</strong>.';
    //print_r($content);
    $xhtml .= $ui->make_textarea("Scratchpad: ", array("name"=>"internal_notes", "cols"=>"120", "rows"=>"20"), $content->internal_notes);
    $xhtml .= '</div>';
    return $xhtml;
}
function drafts($vpath, $framework, $ui){
    return '<div id="white_form">Sorry, the WYSYWIG drafts feature will be enabled soon.</div>';
}
function optional_settings($framework, $ui) {
    //echo '<div id="blue_form" class="round">';
	echo '<style>
	#commnetivity_drafts_presentation_form {
	border:1px solid #000;
	padding:3px;
	}
	#commnetivity_drafts_presentation_form H1 {
	font-size:18px;
	}
	#commnetivity_drafts_presentation_select {
	display:block;
	}
	</style>';
	echo '<div id="commnetivity_drafts_presentation_form">';
    echo '<div id="commnetivity_drafts_presentation_select">';
    echo $ui->make_select("Require SSL?", "ssl_required", $security->ssl_required, array("O"=>"Optional","Y"=>"Always"));
	echo '</div>';
    echo '<div id="commnetivity_drafts_presentation_select">';
    echo $ui->make_select("Require Auth?", "auth_required", $security->auth_required, array("O"=>"Optional","Y"=>"Always"));
	echo '</div>';
    echo '<div id="commnetivity_drafts_presentation_select">';
    $dh = opendir(TEMPLATES);
    while (false !== ($file = readdir($dh))) {
        if (!is_dir(TEMPLATES."$file")) {
            $file = preg_replace('/\..*$/', '', $file);
            //$templates[$file] =  htmlspecialchars(ucfirst());
            if ( file_exists(TEMPLATES . $file . ".dwt") ) {
                $contents = file_get_contents(TEMPLATES . $file . ".dwt");
                preg_match('/<title>([^>]*)<\/title>/si', $contents, $match );
                if (isset($match) && is_array($match) && count($match) > 0) {
                    $templates[$file.'.dwt'] = strip_tags($match[1]);
                }
            }
        }
    }
    closedir($dh); unset($dh); unset($file);
    if ( !$theme->template ) {
        echo $ui->make_select("Template:", "template", TEMPLATE_DEFAULT, $templates); echo "<br>";
    } else {
        if ( file_exists(TEMPLATES . $theme->template) ) {
            $contents = file_get_contents(TEMPLATES . $theme->template);
            preg_match('/<title>([^>]*)<\/title>/si', $contents, $match );
            if (isset($match) && is_array($match) && count($match) > 0) {
                $title = strip_tags($match[1]);
                echo $ui->make_select("Template:", "template", "Found.", $templates);
                echo '</div>';
                echo '</div>';
            } else {
                echo $ui->make_select("Template:", "template", "No title", $templates);
            }
        }
    }
    //echo "</div>";
    return true;
}
function glance($vpath, $framework, $ui) {
	//echo '<fieldset>';
    $content = $framework->query("$vpath", "content");
    $security = (object) unserialize($content->security);
    $meta = (object) unserialize($content->meta_data);
    $theme = (object) unserialize($content->theme);
   
   // return array("display"=>"No information from glance function.");
    $xhtml = "<form class=\"niceform\">";
    
    if ( $content->virtual_path ) {
        
        $xhtml .= '<fieldset>';
        $xhtml .= '<legend>Page tile and meta tags</legend>';
		$xhtml .= '<dl>';
        $xhtml .= '<li>' . $ui->make_input("Page Title:", array("name"=>"page_title", "size"=>"50", "maxlength"=>"75"), $content->page_title) . '</li>';
        $xhtml .= '<li>' . $ui->make_input("Keywords:", array("name"=>"keywords", "size"=>"50", "maxlength"=>"255"), $meta->keywords) . '</li>';
        $xhtml .= '<li>' . $ui->make_textarea("Description:", array("name"=>"description", "size"=>"50", "cols"=>"50", "rows"=>"2", "maxlength"=>"255"), $meta->description) . '</li>';;
        $xhtml .= '<H1>Taxonomy (establishes hiarchy)</H1>';
        $xhtml .= '<li>' . $ui->make_parent_id_selector("Subpage of:", $content->parent_id) . '</li>';
        $xhtml .= '<H1>Allowable Discovery (from direct access and web crawlers)</H1>';
        $xhtml .= '<li>' . $ui->make_select("Bookmarkable?", "bookmarkability", $security->ssl_required, array("O"=>"Optional","N"=>"No")) . '</li>';
        $xhtml .= '<li>' . $ui->make_select("Enable in Navigation?", "enable_in_navigation", $content->enable_in_navigation, array("Y"=>"Yes","N"=>"No")) . '</li>';
        $xhtml .= '<li>' . $ui->make_input("Navigation Title:", array("name"=>"nav_title", "maxlength"=>"75"), $content->nav_title) . '</li>';
        $xhtml .= '<li>' . $ui->make_input("PHP Script?", array("name"=>"internal_path", "size"=>"30", "maxlegth"=>"75"), $content->internal_path) . '</li>';
		$xhtml .= '</dl>';
        $xhtml .= '</fieldset>'; // End box
        
        
    } else {
        
        $xhtml .= '<div id="white_form">';
	$xhtml .= 'There is no content, static or dynamic for this path. '.$vpath.'<br>';
	$xhtml .= 'Would you like to create a record here?<br>';
	$xhtml .= $ui->make_input("Type \"yes\" to activate:", array("name"=>"vpath_activation", "maxlength"=>"3"), "");
        $xhtml .= '</div>';
        
    }
    $xhtml .= "</form>";
    return array("display"=>"$xhtml",); 
}
function help ($vpath, $framework, $ui) {
    echo <<<EOF
   <div id="white_form">
   <h2>SEO Required</h2>
   <p>Commnetivity offers a simple way to rapidly deploy web pages. At minimum, you should give this document its own page title, keywords and description. If your website uses automatic navigation, you should also provide a short menu friendly title.</p>
   <h2>Optional</h2>
   <p>You may also change the security, accessability and presentation of this resource.</p>
   <h2>Notes</h2>
   <p>When preparing your document for the web, have you ever wanted to annotate along the way? Well, soon you will have a private notepad to keep track of things you dont want to write down.</p>
   <h2>Status & Revisions</h2>
   <p>At a glance list of important facts about this document, including full revision history, ownership and more.</p>
   <h2>Saved Drafts</h2>
   <p>We will soon add support for automatic and manually saved drafts. These are version of documents that are not yet ready for the world to see.</p>
    </div>
EOF;
}
function get_revision_data () {
    //echo "Hello.";
   //exit;
}
?>