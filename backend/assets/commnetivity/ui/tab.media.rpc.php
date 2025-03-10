<?php

//require_once(PATH.COMMNETIVITY.'/interface.class.php');
$name = mysql_real_escape_string($_REQUEST['name']);
$value = mysql_real_escape_string($_REQUEST['value']);
$filename = mysql_real_escape_string($_REQUEST['selected']);
$callback_map = array();
$allowed_standard_fields = array("description", "filename", "group");

function commnetivity_leftnav_search($framework, $ui, $params){
	$callback = $ui->callback("warn", "commnetivity_leftnav_search not yet available");
	return array("announce"=>$callback);
}

function page_titles($framework, $ui, $params) {
	if ( $params->value == 'remove' || $params->value == 'delete' || $params->value == 'erase' ) {
		$result = mysql_query("DELETE FROM `commnetivity_overrides` WHERE `top_level_pattern`='".SITELEVEL."' LIMIT 1;");
		if(mysql_affected_rows() > 0){
			$response = $ui->callback("ok", "Deactivated. The next time you select this tab, sitelevel will be gone.");
			return array("announce"=>$response, "screen"=>"/commnetivity/tab/sitelevels");
		} else {
			$response = $ui->callback("error", "There was a problem removing $sitelevel as a record in \"overrides\".");
            return array("announce"=>$response);
		}
	}
}

function orig_filename($framework, $ui, $params) {
	$value = mysql_real_escape_string($_REQUEST['value']);
	$name = mysql_real_escape_string($_REQUEST['name']);
	$id = mysql_real_escape_string($_REQUEST['id']);
	if ( $value == "remove" || $value == "erase" || $value == "delete" ) {
		$response = remove_record($framework, $ui, $params);
		$javascript .= '$(\'a[id='.RECORD.']\').html("...removing ...");';
		$javascript .= '$(\'a[id='.RECORD.']\').parent().slideUp(\'9000\').html("Removed record #'.RECORD.'").slideDown(\'slow\');';
		$javascript .= '$(\'div[id=commnetivity_details]\').slideUp(\'fast\').html();';
		$javascript .= '$(\'div[id=commnetivity_preview]\').slideUp(\'slow\').empty();';
		$javascript .= '$(\'div[id=commnetivity_details]\').slideDown(\'slow\').html("Please select another resource to work with.");';
		$javascript .= '$(\'div[id=commnetivity_preview]\').slideDown(\'slow\');';
		return array("announce"=>$ui->callback($response[0], $response[1]));
	} else {
		if ( strlen($value) < 3 ) {
			return array("announce"=>$ui->callback("error", "Filenames MUST be at least 3 characters in legnth."));
			//exit;
		} else {
			if(mysql_num_rows(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id`='" . RECORD . "'")) > 0 ){
				$result = mysql_query("UPDATE `commnetivity_media` SET `".$name."`='".$value."' WHERE `id`='".RECORD."'") ;
				$response =  (mysql_affected_rows() > 0) ? array("ok", "Updated content field $name.") : array("error", "No records were updated.");
			} else {
				$response = array("error", "There is no record for \"" + RECORD + "\".");
			}
			if ( $response[0] == "ok" ) {
				$javascript .= "$('a[id=".RECORD."]').html(\"$value\");";
				return array("announce"=>$ui->callback($response[0], $response[1]));
				//exit;
			} else {
				return array("announce"=>$ui->callback($response[0], $response[1]));
			}
		}
	}	
} /* End func orig_filename */

function search($framework, $ui, $params) {
	$count = 25;
	
	return array("display"=>$results, "count"=>$count);	
}


function change_hex_colors($framework, $ui, $params) {
$ui = new screen;
$name = mysql_real_escape_string($_REQUEST['name']);
$value = mysql_real_escape_string($_REQUEST['value']);
$filename = mysql_real_escape_string($_REQUEST['selected']);
$record = RECORD;
	if (ctype_xdigit($value)) {
		$response = modify_flash_vars("$record", "$name", "flash_vars", "$value");
		$ui->callback($response[0], $response[1]);
		$javascript .= '  $(\'label[for='.$name.']\').css(\'border-color\', \'#'.$value.'\');'."\n";
		$javascript .= '  $(\'span[name='.$name.']\').css(\'background-color\', \'#'.$value.'\');'."\n";
} else {
		$ui->callback("warn", "The string $testcase does not consist of all hexadecimal digits.");
		
}
exit;
}
function remove_presentation($framework, $ui, $params) { // Not called directly
	$result = mysql_query("DELETE FROM `commnetivity_presentation` WHERE `id`='".RECORD."' LIMIT 1;");
	if (mysql_affected_rows()==1) {
		$ui->callback("okay", "Player was successfully removed.");
		exit;
	} else { 
		$ui->callback("error", "There was a problem removing the player.");
		exit;
	}
}
function activate_presentation($framework, $ui, $params) { // $params->id will hold the id of the group.
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	
	$query = mysql_query("SELECT * FROM `commnetivity_presentation` WHERE `id` = '".$result->id."' LIMIT 1;");
	if ($params->value != "yes") {
		return array("announce"=>$ui->callback("warn", "Please enter the word \"yes\" to create a player presentation for this group."));
	}
	
	if (mysql_num_rows($query)==1) {
		return array("announce"=>$ui->callback("error", "The presentation of \"".$result->group."\" already exists."));
		exit;
	} else {
		$query = mysql_query("INSERT INTO `commnetivity_presentation` (`group`) VALUES ('".$result->group."');");
		if (mysql_affected_rows()==1) {
			return array("announce"=>$ui->callback("okay","Player presentation was successfully added."), "details"=>"/commnetivity/tab/media/players/details/".$result->id);
		} else { 
			return array("announce"=>$ui->callback("error", "There was a problem adding player presentation."));
		}
		
	}
	
} /* End activate_presentation */


function get_group_from_an_id($id){
	$query = mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$id."' LIMIT 1;");
	$result = mysql_fetch_object($query);
	return $result->group;
}
function get_any_id_of_group($group){
	$query = mysql_query("SELECT id FROM `commnetivity_media` WHERE `group` = '".$group."' LIMIT 1;");
	$result = mysql_fetch_object($query);
	return $result->id;
}

function count_files_by_group($group) {
	$sql = "SELECT * FROM `commnetivity_media` WHERE `group` = '".$group."';";
	$query = mysql_query($sql);
	$count = mysql_num_rows($query);
	return $count;
}

function remove_record($framework, $ui, $params) {
   $query = mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".RECORD."' LIMIT 1;");
   if ( mysql_num_rows($query) == 1 ) {
		$record = (object) mysql_fetch_assoc($query);
		$result = mysql_query("DELETE FROM `commnetivity_media` WHERE `id`='".RECORD."' LIMIT 1;");
		if(mysql_affected_rows() > 0){
			unlink(PATH."/media/".$record->real_filename);
			if (file_exists(PATH."/media/".$record->real_filename)) {
				$response = array("error", "Record removed as resource, but could not be deleted.");
			} else {
			$response = array("ok", "The resource $id was removed successfully.");
			}
		} else {
		$response = array("error", "There was a problem removing $id.");
		}
	}
	return $response; // need to convert to array for json callback
} /* End remove_record */;


function width($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$group = $result->group;
	//$response = modify_flash_vars($group, "width", "flash_vars", $params->value);
	//$response = modify_standard_field($id, $name, $value);

	$sql = "UPDATE `commnetivity_presentation` SET `width`='".$params->value."' WHERE `group`='".$group."';";
	$result = mysql_query($sql);
	$rows_affected = mysql_affected_rows();
	if(mysql_affected_rows() > 0){
		$response = array("okay", ucfirst($in) . " updated.");
	} else {
		$response = array("error", ucfirst($in) . " not updated.");
	}	
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function height($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$group = $result->group;
	//$response = modify_flash_vars($group, "width", "flash_vars", $params->value);
	//$response = modify_standard_field($id, $name, $value);

	$sql = "UPDATE `commnetivity_presentation` SET `height`='".$params->value."' WHERE `group`='".$group."';";
	$result = mysql_query($sql);
	$rows_affected = mysql_affected_rows();
	if(mysql_affected_rows() > 0){
		$response = array("okay", ucfirst($in) . " updated.");
	} else {
		$response = array("error", ucfirst($in) . " not updated.");
	}	
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function playlist($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$group = $result->group;
	$response = modify_flash_vars($group, "playlist", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function playlistsize($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$group = $result->group;
	$response = modify_flash_vars($group, "playlistsize", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function autostart($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$response = modify_flash_vars($result->group, "autostart", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function stretching($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$response = modify_flash_vars($result->group, "stretching", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function repeat($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$response = modify_flash_vars($result->group, "repeat", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function set_shuffle($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$response = modify_flash_vars($result->group, "presentation_shuffle", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function wmode($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$group = $result->group;
	$response = modify_flash_vars($group, "wmode", "params", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function align($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$group = $result->group;
	$response = modify_flash_vars($group, "align", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function hspace($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$group = $result->group;
	$response = modify_flash_vars($group, "hspace", "params", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function vspace($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$group = $result->group;
	$response = modify_flash_vars($group, "vspace", "params", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}

function group($framework, $ui, $params) {
	$query = mysql_query("UPDATE `commnetivity_media` SET `group`='".mysql_real_escape_string($_REQUEST['value'])."' WHERE `id`=".RECORD." LIMIT 1;");
	$response = (mysql_affected_rows() > 0) ? array("ok", "Changed group name for record."): array("error", "There changing the group ID for this record.");
	return array("announce"=>$ui->callback($response[0], $response[1]));
} /* End group */

function description($framework, $ui, $params) {
	$query = mysql_query("UPDATE `commnetivity_media` SET `description`='".mysql_real_escape_string($_REQUEST['value'])."' WHERE `id`=".RECORD." LIMIT 1;");
	$response = (mysql_affected_rows() > 0) ? array("ok", "Changed description name for record."): array("error", "The descript was not updated for this record..");
	return array("announce"=>$ui->callback($response[0], $response[1]));
}

function remove_player($framework, $ui, $params) {
	$query = mysql_query("SELECT * FROM `commnetivity_presentation` WHERE `id` = '".RECORD."' LIMIT 1;");
	if ( mysql_num_rows($query) == 1 ) {
		$record = (object) mysql_fetch_object($query);
		$result = mysql_query("DELETE FROM `commnetivity_presentation` WHERE `id`='".$record->id."' LIMIT 1;");
		if(mysql_affected_rows() > 0){
			unlink(PATH."/media/".$record->real_filename);
			if (file_exists(PATH."/media/".$record->real_filename)) {
				$response = array("error", "Record removed as resource, but could not be deleted.");
			} else {
				$response = array("ok", "The resource $id was removed successfully.");
			}
		} else {
			//$ui->callback("error", "There was a problem removing $vpath as a record in \"content\"."); 
			$response = array("error", "There was a problem removing $id.");
		}
	}
	return $response;
}
	
	
function modify_standard_field($id, $name, $value) {
$rail = str_replace("/commnetivity/", "", VPATH);
$rails = explode("/", VPATH);
	$sql = "SELECT * FROM `commnetivity_".TAB."` WHERE `id`='".$id."';";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0 ){
		$sql = "UPDATE `commnetivity_".TAB."` SET `".$name."`='".$value."' WHERE `id`='".$id."';";
		$result = mysql_query($sql);
		if(mysql_affected_rows() > 0){
			$response = array("ok", "Updated $name for $id. $sql");
		} else {
			$response = array("warn", "No modification made. Debug: ($id) $name=$value");
		}
	} else {
		$response = array("error", "No record for $id. $sql");
	}
	return $response;
}

function modify_presentation_flash_vars($framework, $ui, $params) {
	explode($params);	
	$response = array("ok", "TEST FROM modify_presentation_flash_vars! Updated $name for $id in \"flash_vars\".");
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function frontcolor($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$response = modify_flash_vars($result->group, "frontcolor", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function backcolor($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$response = modify_flash_vars($result->group, "backcolor", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function lightcolor($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$response = modify_flash_vars($result->group, "lightcolor", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function screencolor($framework, $ui, $params){
	$result = mysql_fetch_object(mysql_query("SELECT * FROM `commnetivity_media` WHERE `id` = '".$params->id."' LIMIT 1;"));
	$response = modify_flash_vars($result->group, "screencolor", "flash_vars", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function modify_flash_vars($group, $field, $in, $value) { // Need to switch for the $framework, $ui, $params method. Is $field the same things as $params->name?
	$sql = "SELECT $in FROM `commnetivity_presentation` WHERE `group`='".$group."'";
	$result = mysql_query($sql);
	$record = mysql_fetch_assoc($result);
	if(mysql_num_rows($result) > 0 ){
		$unpacked = unserialize($record[$in]);
		if ( array($unpacked) ) {
			if ( strlen($value) > 0 ) {
				$unpacked[$field] = "$value";
			} else {
				unset($unpacked[$field]);
			}
		} else {
			// No array setup.
			if ( strlen($value) > 0 ) {	
				$unpacked = array("$field"=>"$value");
			} else {
				$unpacked = array();
			}
		}
		$packed = serialize($unpacked);
		$sql = "UPDATE `commnetivity_presentation` SET `".$in."`='".$packed."' WHERE `group`='".$group."';";
		$result = mysql_query($sql);
		$rows_affected = mysql_affected_rows();
		if(mysql_affected_rows() > 0){
			$response = array("okay", ucfirst($in) . " updated.");
		} else {
		$response = array("error", ucfirst($in) . " not updated. $sql");
		}
	} else {
		if ( strlen($value) > 0 ) {
			$unpacked = array("$field"=>"$value");
		} else {
			$unpacked = array();
		}
		$packed = serialize($unpacked);
		$sql = "UPDATE `commnetivity_presentation` SET `".$in."`='".$packed."' WHERE `group`='".$group."';";
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