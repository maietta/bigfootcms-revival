<?php

/* CONTENT 

$security = unserialize($content->security);
$meta = unserialize($content->meta_data);
$theme = unserialize($content->theme);
$commnetivity_content = array("nav_title", "bookmarkability", "enable_in_navigation", "internal_path", "internal_notes", "page_title", "parent_id", "ssl_required", "auth_required", "keywords", "description", "template" );
//// Used for serialization.$security_fields_available = array("ssl_required","auth_required");
$meta_fields_available = array("keywords", "description");
$theme_fields_available = array("template");

/* */
function vpath_activation($framework, $ui, $params) {
	global $vpath;
    $id = mysql_real_escape_string($_REQUEST['id']);
    $name = mysql_real_escape_string($_REQUEST['name']);
    $value = mysql_real_escape_string($_REQUEST['value']);
    $fixed_parent_parts = explode("/", dirname(VPATH));
    array_pop($fixed_parent_parts);
    $fixed_parent = strtolower(implode("/", $fixed_parent_parts)."/index.html");

    $fixed_parent = dirname(VPATH) . "/index.html";
    
   if ( $value == 'yes' ) {
		$now = date('Y-m-d H:i:s');
		$username = $_SESSION['username'];
		$sql = "INSERT INTO `commnetivity_content` (`virtual_path`, `date_recorded`, `modified_by`,`parent_id`) VALUES ('".VPATH."', '".$now."', '".$username."', '".$fixed_parent."');";
		$result = mysql_query($sql);
		if ( mysql_affected_rows() > 0 ) {
			$javascript = '		$(\'title\').html("Untitled New Document");'."\n";
			$javascript .= '	$(\'a[id=editor]\').html("<a id=\"editor\" class=\"edit_link\" href=\"'.VPATH.'\">Use an editor</a></li>"); ';
			$javascript .= '	$(\'div[id=content]\').html("<h3>Awesome!</h3><p>You may now edit this document!</p>");'."\n";
         	return array("announce"=>$ui->callback($response[0], "Activated. You may now use the WSIWYG Editor to create your page."), "screen"=>"/commnetivity/tab/content", "javascript"=>"$javascript");
		} else {
            return array("announce"=>$ui->callback($response[0], "There was a problem adding record in \"content\"."));
		}
	} else {
            return array("announce"=>$ui->callback($response[0], "You must type \"yes\" to activate this virtual path. $fixed_parent"));
	}
}

function page_title($framework, $ui, $params) { // Tested on Jan 16th 2010. Shortened and tested on Jan 17th 2010
    $id = mysql_real_escape_string($_REQUEST['id']);
    //$name = mysql_real_escape_string($_REQUEST['name']);
	$name = "page_title";
    $value = mysql_real_escape_string($_REQUEST['value']);
	
	if ( $value == "erase" || $value == "remove" || $value == "delete") {
		$result = mysql_query("SELECT * FROM `commnetivity_content` WHERE `virtual_path`='".VPATH."'");
    	if(mysql_num_rows($result) > 0 ){
        	$result = mysql_query("DELETE FROM `commnetivity_content` WHERE `virtual_path`='".VPATH."'") ;
        	$callback = (mysql_affected_rows() > 0) ? array("ok", "This page has been removed but is still in revision history.") : array("error", "Content resource could not be removed.");
			 if ( $callback['0'] == "ok" ) {
				$javascript  = '$(\'title\').html("Removed!!!");'."\n";
				$javascript .= '$(\'a[id=editor]\').html("<a id=\"editor\" href=\"'.VPATH.'\"></a>"); ';
				$javascript .= '$(\'div[id=content]\').html("<h3>Removed!!!</h3>\n<p>This virtual path and content resource has been removed from the content table. Any revisions will still available for re-publishing or review at any time.!</p>");'."\n";
				$javascript .= '$(\'ul[id=control_panel_subnav_tabs]\').fadeOut(300, function(){$(this).html("")});';
			}
		} else {
			$response = array("error", "Whoah, you should not see this messege because the form should not show unless there was a resource.");
		}
		return array("announce"=>$ui->callback($callback['0'], $callback['1']), "javascript"=>"$javascript", "screen"=>"/commnetivity/tab/content");
	} else {
    	$result = mysql_query("SELECT * FROM `commnetivity_content` WHERE `virtual_path`='" . VPATH . "'");
    	if(mysql_num_rows($result) > 0 ){
        	$result = mysql_query("UPDATE `commnetivity_content` SET `".$name."`='".$value."' WHERE `virtual_path`='".VPATH."'") ;
        	$callback = (mysql_affected_rows() > 0) ? array("ok", "Updated content field $name.") : array("error", "No records were updated.");        
	} else {
		$callback = array("error", "You must first create a page.");
    	}
    	if ( $callback['0'] == "ok" ) {
			if ( strlen($value) == 0 ) {
				$javascript = "	$('title').html(\"Untitled Document\");";
			} else {
				$javascript = "	$('title').html(\"$value\");";
			}
    	}
		return array("announce"=>$ui->callback($callback['0'], $callback['1']), "javascript"=>"$javascript");
	}   
}

function enable_in_navigation($framework, $ui, $params) {
	$response = _replace_field("enable_in_navigation", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));	
}	

function keywords($framework, $ui, $params) { /* Tested Jan 22nd 2010 */
	$value = mysql_real_escape_string($_REQUEST['value']);
	$response = _replace_serialized_element("keywords", "meta_data", "$value");
	return array("announce"=>$ui->callback($response[0], $response[1]));
}

function description($framework, $ui, $params) { /* Tested Jan 22nd 2010 */
	$value = mysql_real_escape_string($_REQUEST['value']);
	$response = _replace_serialized_element("description", "meta_data", "$value");
	return array("announce"=>$ui->callback($response[0], $response[1]));	
}
function ssl_required($framework, $ui, $params) { /* Tested Jan 22nd 2010 */
	$value = mysql_real_escape_string($_REQUEST['value']);
	$response = _replace_serialized_element("ssl_required", "security", "$value");
	return array("announce"=>$ui->callback($response[0], $response[1]));	
}

function template($framework, $ui, $params) {
	$response = _replace_serialized_element("template", "theme", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));	
}

function left($framework, $ui, $params) {
	$response = _update_navigation($params->name, $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));	
}
function right($framework, $ui, $params) {
	$response = _update_navigation($params->name, $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));	
}
function top($framework, $ui, $params) {
	$response = _update_navigation($params->name, $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1].$params->value));	
}
function bottom($framework, $ui, $params) {
	$response = _update_navigation($params->name, $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));	
}
function internal_notes($framework, $ui, $params) { // Tested on Jan 16th 2010. Shortened and tested on Jan 17th 2010
    $ui = new screen;
    
    $id = mysql_real_escape_string($_REQUEST['id']);
    $name = mysql_real_escape_string($_REQUEST['name']);
    $value = mysql_real_escape_string($_REQUEST['value']);
    
    $result = mysql_query("SELECT * FROM `commnetivity_content` WHERE `virtual_path`='" . VPATH . "'");
    if(mysql_num_rows($result) > 0 ){
        $result = mysql_query("UPDATE `commnetivity_content` SET `".$name."`='".$value."' WHERE `virtual_path`='".VPATH."'") ;
        $response = (mysql_affected_rows() > 0) ? array("ok", "Updated content field $name.") : array("error", "No records were updated.");        
    } else {
    	$response = array("error", "You must first create a page.");
    }
    //if ( $reponse['0'] ) {
        //$javascript = '	$(\'title\').html("'.$value.'"); alert("Changed to '.$value.'");'."\n";
    //}
    return array("announce"=>$ui->callback($response['0'], $response['1']), "javascript"=>"$javascript");
}

function internal_path($framework, $ui, $params) {
	$value = mysql_real_escape_string($_REQUEST['value']);
	$vpath = VPATH;
	$response = _replace_field("internal_path", "$value");

if ($value=="") {
$javascript = <<<EOF
$.ajax({ url: "/commnetivity/content?vpath=$vpath", type: "GET", cache: false, dataType: "json", success: function(response) { $('div[id=content]').html(response.display); } });
EOF;
} else {
$javascript = <<<EOF
	$.ajax({ url: "/commnetivity/script_content?internal_path=$value", type: "GET", cache: false, dataType: "html", success: function(results) { var content = results; $('div[id=content]').html(content); var content = null; } });
EOF;
}
	return array("announce"=>$ui->callback($response[0], $response[1]), "javascript"=>$javascript);
}

function nav_title($framework, $ui, $params) {
	$response = _replace_field("nav_title", "$value");
	return array("announce"=>$ui->callback($response[0], $response[1]));	
}

function _replace_field($name, $value) {
	$sql = "SELECT * FROM `commnetivity_content` WHERE `virtual_path`='" . VPATH . "'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0 ){
		$sql = "UPDATE `commnetivity_content` SET `".$name."`='".$value."' WHERE `virtual_path`='".VPATH."'";
		$result = mysql_query($sql) ;
		if(mysql_affected_rows() > 0){
			$response = array("ok", "Updated content field $name.");
		} else {
			$response = array("error", "No records were updated.");
		}
	} else {
		$response = array("error", "You must first create a page.");
	}
	return $response;
}

function _update_navigation($name, $value) {
	$sql = "SELECT * FROM `commnetivity_navigation` WHERE `virtual_path`='" . VPATH . "'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0 ){
		$sql = "UPDATE `commnetivity_navigation` SET `".$name."`='".$value."' WHERE `virtual_path`='".VPATH."'";
		$result = mysql_query($sql) ;
		if(mysql_affected_rows() > 0){
			$response = array("ok", "The $name navigation parameter has been changed.");
		} else {
			$response = array("error", "No records were updated.");
		}
	} else {
		$result = mysql_query("INSERT INTO `commnetivity_navigation` (`virtual_path`) VALUES ('".VPATH."');");
		$sql = "UPDATE `commnetivity_navigation` SET `".$name."`='".$value."' WHERE `virtual_path`='".VPATH."'";
		$result = mysql_query($sql) ;
		if(mysql_affected_rows() > 0){
			$response = array("ok", "The $name navigation parameter has been created.");
		} else {
			$response = array("error", "Wow... that is one hell of a fail on Commnetivity's part. Must be a big on your Windows&trade;.");
		}
	}
	return $response;
}


function _replace_serialized_element($field, $in, $value) {
	$sql = "SELECT $in FROM `commnetivity_content` WHERE `virtual_path`='".VPATH."'";
	$result = mysql_query($sql);
	$record = mysql_fetch_assoc($result);
	if(mysql_num_rows($result) > 0 ){
		$unpacked = unserialize($record[$in]);
		if ( array($unpacked) ) {
			if ( strlen($value) > 0 ) {
				$unpacked[$field] = "$value";
			} else {
				if ($field != $value) {
					
				} else {
					unset($unpacked[$field]);
				}
				
			}
		} else { // No array setup.
			if ( strlen($value) > 0 ) {
				$unpacked = array("$field"=>"$value");
			} else {
				$unpacked = array();
			}
		}
		$packed = serialize($unpacked);
		$sql = "UPDATE `commnetivity_content` SET `".$in."`='".$packed."' WHERE `virtual_path`='".VPATH."';";
		$result = mysql_query($sql);
		$rows_affected = mysql_affected_rows();
		if(mysql_affected_rows() > 0){
			$response = array("okay", ucfirst($in) . " updated.");
		} else {
			$response = array("error", ucfirst($in) . " not updated.");
		}
	} else {
		if ( strlen($value) > 0 ) {
			$unpacked = array("$field"=>"$value");
		} else {
			$unpacked = array();
		}
		$packed = serialize($unpacked);
		$sql = "UPDATE `commnetivity_content` SET `".$in."`='".$packed."' WHERE `virtual_path`='".VPATH."';";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0){
			$response = array("okay", ucfirst($in) . " updated.");
		} else {
			$response = array("error", ucfirst($in) . " not updated.");
		}
	}
	return $response;
}



?>