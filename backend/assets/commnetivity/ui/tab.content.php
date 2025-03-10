<?php

function initialize($framework, $ui, $params) {
	$subnavigation  = '<ul id="control_panel_subnav_tabs">';
	$subnavigation .= '<li class="commnetivity_ui_left"><a href="/commnetivity/tab/content/basics" title="Give this resource a title, some keyworks and a short description so search engines can easily find this page.">Basic/SEO Information</a></li>';
	$subnavigation .= '<li class="commnetivity_ui_middle"><a href="/commnetivity/tab/content/extended" title="Control who, what, when where and why resource becomes available.">Distribution Controls</a></li>';
	$subnavigation .= '<li class="commnetivity_ui_right"><a href="/commnetivity/tab/content/navigation" title="Specify the navigational &quot;targets&quot; where this resource can be made available.">Visibility in Navigation</a></li>';
	$subnavigation .= '<li class="commnetivity_ui_seperator"></li>';
	//$subnavigation .= '<li class="left"><a href="/commnetivity/tab/content/revisions" title="A running list of archived versions of document.">Revisions</a></li>';
	// $subnavigation .= '<li class="middle"><a href="/commnetivity/tab/content/drafts" title="Saved drafts">Drafts</a></li>';
	// $subnavigation .= '<li class="right"><a href="/commnetivity/tab/content/notes" title="Notes about this document.">Notes</a></li>';
	$subnavigation .= '</ul>';
	$first_screen = (object) resource_selection($framework, $ui, $params);
	$subnavigation .= $first_screen->display;
	return array("display"=>$subnavigation);
}

function resource_selection($framework, $ui, $params) {
	$vpath = VPATH;
	$framework = new Framework;
	$content = (object) $framework->query($vpath, "content");
	if ($content->virtual_path) {
		$basics = (object) basics($framework, $ui, $params);
		$xhtml = $xhtml . $basics->display;
	} else {
		$fixed_parent_parts = explode("/", dirname(VPATH));
		array_pop($fixed_parent_parts);
		$fixed_parent = implode("/", $fixed_parent_parts)."/".DIRECTORY_DEFAULT;
		
		$xhtml .= '<script>$(\'ul[id=control_panel_subnav_tabs]\').html("");</script>';
		$xhtml .= '<ul>';
		$xhtml .= '<div class="head">Virtual path <b>'.VPATH.'</b> is not defined.</div>';
		$xhtml .= '<li>Initially, this resource will be filed under "'.$fixed_parent.'".</li>';
		$xhtml .= '<li>Would you like to create a record here?'.'</li>';
		
		$xhtml .= "<li>Type \"yes\" to activate:" . $ui->make_input("", array("name"=>"vpath_activation", "maxlength"=>"3"), "").'</li>';
		$xhtml .= '</ui>';
	}
	return array("display"=>$xhtml);	
}

function editor($framework, $ui, $params) {
	// create a special div to land toolbar from editor into.
	return array("display"=>"$xhtml"); 
}

function basics($framework, $ui, $params) {
	//global $framework, $ui, $params, $content, $security, $meta, $theme;

	$content = $framework->query(VPATH, "content");
	$security = (object) unserialize($content->security);
	$meta = (object) unserialize($content->meta_data);
	$theme = (object) unserialize($content->theme);

	
	if ( $content->virtual_path ) {
		$page_title = $content->page_title;
		$keywords = $meta->keywords;
		$internal_path = $content->internal_path;
		//$xhtml .= '<div id="commnetivity_windows">';
		$xhtml .= <<<"EOF"
	
	<form>
		<div class="row container" style="max-width: 100%;">
			<div class="head">Describe this resource</div>
			<p>Note: This information is used and presented by search engines to help people find your website and this resource.</p>
		</div>
		<div class="row">
			<div class="large-6 columns left">
				<div class="large-12 columns">
					<div class="small-3 columns">
						<label for="page_title" class="inline right">Page title</label>
					</div>
					<div class="small-9 columns">
						<input type="text" name="page_title" value="$page_title" placeholder="Untitled Document">
					</div>
				</div>
				<div class="large-12 columns">
					<div class="small-3 columns">
						<label for="keywords" class="inline right">Keywords</label>
					</div>
					<div class="small-9 columns">
						<input type="text" name="keywords" value="$keywords" placeholder="Keyword1, Keyword2, etc.">
					</div>
				</div>
				<div class="large-12 columns">
					<div class="small-3 columns">
						<label for="description" class="inline right">Description</label>
					</div>
					<div class="small-9 columns">
						<textarea name="description" maxlength="255" placeholder="The first paragraph of this page will become the description of this page. Please enter in a description to avoid this default behavior.">$meta->description</textarea>
					</div>
				</div>
				
			</div>
			<div class="large-6 columns left">
				<div class="row">
					<div class="small-3 columns">
						<label for="internal_path" class="right inline">PHP Script</label>
					</div>
					<div class="small-9 columns">
						<input type="text" maxlegth="75" name="internal_path" value="$internal_path" placeholder="/path/to/script.php">
					</div>
				</div>
			</div>
		</div>
	</form>
	
</div>
EOF;
	} else {
	$xhtml .= <<<"EOF"
	<form class="custom">
		<div class="row" style="max-width: 100%;">
			<div class="head">Resource creation panel</div>
			<p>There is no content, static or dynamic for this path "$vpath". Would you like to create a record here?</p>
			<div class="large12 columns left">
				<div class="row">
					<div class="small-5 columns">
						<label for="vpath_activation" class="right inline">Yes/No</label>
					</div>
					<div class="small-7 columns">
						<input type="text" maxlegth="3" name="vpath_activation" value="" placeholder="yes">
					</div>
				</div>
			</div>
		</div>
	</form>
EOF;


		//$xhtml .= '<div style="clear: both;"></div>';
	}
	return array("display"=>"$xhtml"); 
}

function extended($framework, $ui, $params) {
	$vpath  = VPATH;
	$content = $framework->query("$vpath", "content");
	$security = (object) unserialize($content->security);
	$meta = (object) unserialize($content->meta_data);
	$theme = (object) unserialize($content->theme);

	if ( $content->virtual_path ) {
		$xhtml .='<div class="row">';
		$xhtml .='<div class="large-12 columns">';
		//$xhtml .= '<div id="commnetivity_leftnav_container" class="blueborder"></div>';
		$xhtml .= '<div class="large-6 columns">';
		$xhtml .= '<div class="head">Controls how this resource is <i>accessable</i>.</div>';
		$xhtml .='<div>Visitors to this page '.$ui->make_select("", "auth_required", $security->auth_required, array("O"=>"may always","Y"=>"must be logged in to")).' access it.</div>';
		$xhtml .='<div>Visitors '.$ui->make_select("", "bookmarkability", $security->ssl_required, array("O"=>"may follow link or bookmark","N"=>"must follow site navigation")).' to access this resource.</div>';

		if (!defined('SSL_HOST')) {
		 	$xhtml .='<div>Visit\'s '. $ui->make_select("", "ssl_required", $security->ssl_required, array("O"=>"are optionally","Y"=>"are always")) . 'encrypted by way of SSL.</div>';
		} else {
			$xhtml .='<div>Users will not be able to see this page in SSL mode. There is no SSL_HOST defined in core config.</div>';
		}
		$xhtml .= '</div>';
		$xhtml .= '<div class="large-6 columns">';
		$xhtml .= '<div class="head">Controls how this resource is <i>presented</i>.</div>';
		$dh = opendir(TEMPLATES);
		//$xhtml .= '<li>' . $ui->make_select("Templates?", "ssl_required", $theme->template, array("O"=>"Optional","Y"=>"Always")) . '</li>';
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
            $xhtml .= '<div>'.$ui->make_select("Template:", "template", TEMPLATE_DEFAULT, $templates); $xhtml .= "</div>";
        } else {
            if ( file_exists(TEMPLATES . $theme->template) ) {
                $contents = file_get_contents(TEMPLATES . $theme->template);
                preg_match('/<title>([^>]*)<\/title>/si', $contents, $match );
                if (isset($match) && is_array($match) && count($match) > 0) {
                    $title = strip_tags($match[1]);
                    $xhtml .= '<div class="large-3 columns">'.$ui->make_select("Template:", "template", $theme->template, $templates).'</div>';
                } else {
                    $xhtml .= '<div class="large-3 columns">'.$ui->make_select("Template:", "template", TEMPLATE_DEFAULT, $templates).'</div>';
                }
				$xhtml .= '<div class="three columns">Flag this resource as '. $ui->make_select("", "enable_in_navigation", $content->enable_in_navigation, array("Y"=>"enabled","N"=>"disabled")) . ' in navigation.</div>';
				
				$xhtml .='</div>';
            }
			
        }
		$xhtml .= '</div>';
		$xhtml .= '</div>';
		return array("display"=>$xhtml);
    } else {
		/* No virtual path is defined. */
		return array("display"=>_noContentResource($framework, $ui, $params));
    }
}

function navigation($framework, $ui, $params) {
 $content = $framework->query(VPATH, "content");
if ( $content->enable_in_navigation == "Y" ) {
		$navigation = (object) $framework->general_query("navigation", "SELECT * FROM commnetivity_navigation WHERE virtual_path = '".VPATH."'");
		$xhtml .= '<div class="row">';
		$xhtml .= '<div class="head">Controls <i>where</i> this resource is available in navigation.</div>';
		$xhtml .= '<div class="large-6 columns">';
		//$xhtml .= '<ul>';
		
		$xhtml .= '<div>' . $ui->make_input("Navigation Title:", array("name"=>"nav_title", "maxlength"=>"75"), $content->nav_title).'</div>';
		$xhtml .= '<div><p>If you are using a drop-down navigation system please set this page\'s setting, default setting is parent</p></div>';
		$xhtml .='<div>'. $ui->make_select("Drop-down menu setting", "navlvl", $navigation->navlvl, array("topnav"=>"parent","subnav"=>"child")) . '</div>';
		//$xhtml .= '</ul>';
		$xhtml .= '</div>';
		$xhtml .= '<div class="large-6 columns">';
		//$xhtml .= '<ul>';
		
		$xhtml .= '<div><p>Please select where you would like the link to show up in your navigation.</p></div>';
		$xhtml .='<div>'. $ui->make_select("Left side", "left", $navigation->left, array("N"=>"not available","Y"=>"available")) . '</div>';
		$xhtml .='<div>'. $ui->make_select("Right side", "right", $navigation->right, array("N"=>"not available","Y"=>"available")) . '</div>';
		$xhtml .='<div>'. $ui->make_select("Upper/Top", "top", $navigation->top, array("N"=>"not available","Y"=>"available")) . '</div>';
		$xhtml .='<div>'. $ui->make_select("Lower/Bottom", "bottom", $navigation->bottom, array("N"=>"not available","Y"=>"available")) . '</div>';
		//$xhtml .='</ul>';
		$xhtml .= '</div>';
		
} else {
		$xhtml .= '<div class="large-6 columns">';
		$xhtml .= '<ul>';
		$xhtml .= '<div class="head">Navigation is disabled.</div>';
		$xhtml .= '<li>To manage the locations in your templates where navigational links to this resource are available, <a href="/commnetivity/tab/content/extended">click here</a>.</li>';
		$xhtml .='</ul>';
		$xhtml .= '</div>';
		
}
		$xhtml .= '</div>';
	$javascript = "";
	return array("display"=>"$xhtml", "javascript"=>"$javascript");
}

?>