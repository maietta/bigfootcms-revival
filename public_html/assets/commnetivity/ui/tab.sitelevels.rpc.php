<?

function sitelevel_activation($framework, $ui, $params) {
	if ( $params->value == 'yes' ) {
		$result = mysql_query("INSERT INTO `commnetivity_overrides` (`top_level_pattern`) VALUES ('".SITELEVEL."');");
		if(mysql_affected_rows() > 0){
			return array("announce"=>$ui->callback("ok", "Site level has been activated."), "screen"=>"/commnetivity/tab/sitelevels");
		} else {
			return array("announce"=>$ui->callback("warn", "Sitelevel could not be created."), "screen"=>"/commnetivity/tab/sitelevels");
		}
	} else {
		return array("announce"=>$ui->callback("error", "You must type \"yes\" to create a sitelevel."));
	}
}
function page_titles($framework, $ui, $params) {
	if ( $params->value == 'remove' || $params->value == 'delete' || $params->value == 'erase' ) {
		$result = mysql_query("DELETE FROM `commnetivity_overrides` WHERE `top_level_pattern`='".SITELEVEL."' LIMIT 1;");
		if(mysql_affected_rows() > 0){
			$response = $ui->callback("ok", "Sitelevel \"".SITELEVEL."\" has been removed from the database.");
			return array("announce"=>$response, "screen"=>"/commnetivity/tab/sitelevels");
		} else {
			$response = $ui->callback("error", "There was a problem removing sitelevel \"".SITELEVEL."\" as a record in \"overrides\".");
			return array("announce"=>$response);
		}
	} else {
		$result = mysql_query("UPDATE `commnetivity_overrides` SET `page_titles`='".$params->value."' WHERE `top_level_pattern`='".SITELEVEL."' LIMIT 1;");
		$response = (mysql_affected_rows() > 0) ? $ui->callback("ok", "The page title has been updated.") : $ui->callback("error", "There was a problem updating the record.");
        return array("announce"=>$response);
	}
}
function keywords($framework, $ui, $params) {
	$response = _private_function_modify_serialized_field("keywords", "meta_data", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function description($framework, $ui, $params) {
	$response = _private_function_modify_serialized_field("description", "meta_data", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function ssl_required($framework, $ui, $params) {
	$response = _private_function_modify_serialized_field("ssl_required", "security", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function auth_required($framework, $ui, $params) {
	$response = _private_function_modify_serialized_field("auth_required", "security", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function template($framework, $ui, $params) {
	$response = _private_function_modify_serialized_field("template", "theme", $params->value);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function _private_function_modify_serialized_field($field, $in, $value) {
	$result = mysql_query("SELECT $in FROM `commnetivity_overrides` WHERE `top_level_pattern`='".SITELEVEL."'");
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
		$result = mysql_query("UPDATE `commnetivity_overrides` SET `".$in."`='".$packed."' WHERE `top_level_pattern`='".SITELEVEL."';");
		if(mysql_affected_rows() > 0){
			$response = array("okay", ucfirst($in) . " updated.");
		} else {
			$response = array("error", ucfirst($in) . "$sql not updated.");
		}
	} else {
		if ( strlen($value) > 0 ) {
			$unpacked = array("$field"=>"$value");
		} else {
			$unpacked = array();
		}
		$packed = serialize($unpacked);
		$sql = "UPDATE `commnetivity_overrides` SET `".$in."`='".$packed."' WHERE `top_level_pattern`='".SITELEVEL."';";
		$result = mysql_query($sql);
		if(mysql_affected_rows() > 0){
			$response = array("okay", ucfirst($in) . " updated.");
		} else {
			$response = array("error", ucfirst($in) . " not updated.");
		}
	}
	return $response;
}
function internal_path($framework, $ui, $params) {
	$result = mysql_query("UPDATE `commnetivity_overrides` SET `internal_path`='".$params->value."' WHERE `top_level_pattern`='".SITELEVEL."' LIMIT 1;");
	if (mysql_affected_rows() > 0) {
		
		if ( strlen($params->value) > 0 ) {
			$javascript = "$.ajax({ url: \"/commnetivity/script_content?internal_path=".$params->value."\", type: \"GET\", cache: false, dataType: \"html\", success: function(XHR) { $('div[id=content]').html(XHR); } });";
		} else {
			$javascript = "$.ajax({ url: \"/commnetivity/content?vpath=".VPATH."\", type: \"GET\", cache: false, dataType: \"json\", success: function(XHR) { $('div[id=content]').html(XHR.display); } });";
		}
		
		return array("announce"=>$ui->callback("ok", "The internal_script has been updated."), "javascript"=>$javascript);
	} else {
		$response = $ui->callback("error", "There was a problem updating the record.");
		return array("announce"=>$response);
	}
	exit;
}



?>