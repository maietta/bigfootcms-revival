<?php

/*

*/


function add_recipe($framework, $ui, $params) {
	if ( strlen($params->value) < 4 ) { // Minimum number of characters should be set...
		$response = $ui->callback("warn", "You must have a minimum of 3 alphanumeric characters");
		return array("announce"=>$response, "javascript"=>"");
		exit;
	
	}	
	
    $result = mysql_query("SELECT * FROM `recipes` WHERE `title` = '".$params->value."';");
    if (mysql_num_rows($result) == 0) {
        $result = mysql_query("INSERT INTO `recipes` (`title`) VALUES ('".$params->value."');");
        if (mysql_affected_rows() > 0) {
			$response = $ui->callback("ok", "Record was added successfully.");
			return array("announce"=>$response, "screen"=>"/commnetivity/tab/recipes");
			exit;
        } else {
            $response = $ui->callback("error", "The record could not be created.");
			return array("announce"=>$response);
			exit;
		}
    } else {
        $response = $ui->callback("error", "There is already a title by that name.");
		return array("announce"=>$response);
		exit;
    }
}

function testing_field($framework, $ui, $params) {
	$response = $ui->callback("ok", "...fetching data...");
	return array("announce"=>"".$response, "leftnavs"=>"/commnetivity/tab/recipes/leftnav");
}

function subcat($framework, $ui, $params) {		
	if ( strlen($params->value) < 4 ) {
		$response = $ui->callback("warn", "You must have a minimum of 3 alphanumeric characters");
		return array("announce"=>$response, "javascript"=>"");
		exit;
	
	}
	$xhtml = "<h2>Success!</h2>";
	$query = mysql_query("SELECT * FROM `recipes` WHERE `id`='" . $params->id . "';");
	if(mysql_num_rows($query) > 0) {
		$results = mysql_query("UPDATE `recipes` SET `".$params->name."`='".$params->value."' WHERE `id`='".$params->id."';");
		$response = (mysql_affected_rows() > 0) ? array("ok", "Updated ".$params->name." for record: ".RECORD.".") : array("warn", "No modification made.");
		
$javascript = <<<EOF
				$('div[id=content]').html("$xhtml");
EOF;
	} else {
		$response = array("error", "No record for id: ".$params->id);
		$javascript = "";
	}
	
	
	
	return array("announce"=>$ui->callback($response[0], $response[1]), "javascript"=>"$javascript");
}


function remove_record($framework, $ui, $params) {
	$result = mysql_query("DELETE FROM `recipes` WHERE `id`='".$params->id."' LIMIT 1;");
	$response = (mysql_affected_rows() > 0) ? $ui->callback("ok", "The resource ".$params->id." was removed successfully."): $ui->callback("error", "There was a problem removing record having ID ".$params->id);
	return array("announce"=>$response);
}

function title($famework, $ui, $params) {
	if ($params->value == 'remove' || $params->value == 'erase' || $params->value == 'delete' ) {
		return remove_record($framework, $ui, $params);
		exit;
	}
	
	$response = _private_update_recipe_table($params);
	$javascript = ($response[0]=='ok') ? "$('a[id=".$params->id."]').text('".$params->value."');": '';
	return array("announce"=>$ui->callback($response[0], $response[1]), "javascript"=>$javascript);
}

function ingredients($framework, $ui, $params) {
	$response = _private_update_recipe_table($params);
	return array("announce"=>$ui->callback($response[0], $response[1]));
}
function notes($framework, $ui, $params) {
	$response = _private_update_recipe_table($params);
	return array("announce"=>$ui->callback($response[0], $response[1]));	
}

function _private_update_recipe_table($params) {
	$query = mysql_query("SELECT * FROM `recipes` WHERE `id`='" . $params->id . "';");
	if(mysql_num_rows($query) > 0) {
		$results = mysql_query("UPDATE `recipes` SET `".$params->name."`='".$params->value."' WHERE `id`='".$params->id."';");
		$response = (mysql_affected_rows() > 0) ? array("ok", "Updated ".$params->name." for record: ".RECORD.".") : array("warn", "No modification made.");
	} else {
		$response = array("error", "No record for id: ".$params->id);
	}
	return $response;
}

?>