<?php

// This framework is assembled when the following block is matched.


class Jeditable {
	var $connection;
	var $jeditables;
	var $structure;
	var $xhtml;
	
	function Jeditable(){
		$this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
		mysql_select_db(DB_NAME) or die(mysql_error());
	}

	function field ($table, $id, $name, $extra = "" ) {
		if (function_exists('init')) { init(); } else { echo "Editable failed."; exit; }
		if ( EDITABLES ) {	
			$framework = new Framework;
			$structure = (object) $this->table_structure_by_id($table, $id, $name);
			$current_value = $structure->current_value;
			$current_value = ($value) ? $value : "n/a";
			if ( $structure->type == "enum" ) { $method = "&_method=flip"; }
			$jeditable .= "	$(\"'edit[id=".$id."][name=".$name."]'\")".$extra.".editable('".VPATH."?action=_updateJeditableField".$method."', {\n";
			$jeditable .= "		indicator : 'Saving...', onblur : 'submit', style : 'display: inline; inherit;', tooltip  : 'Click to edit...',"."\n";
			$jeditable .= "		loadurl : '".VPATH."?action=_getJeditableField&name=$name"."',";
			$jeditable .= '		submitdata : function(value, settings) {';
			//$jedibable .= '			var id = $(this).attr("id"); var name = $(this).attr("name");';
			$jeditable .= '			return { id: "'.$id.'" , name: "'.$name.'"};';
			$jeditable .= '		}'."\n";		

			if ( $structure->type == "enum" ) {
				$jeditable .= ",		type   : 'select' " . "\n";
			}
			if ( $structure->type == "text" ) {
				$jeditable .= ",		type   : 'textarea', rows: 3, cols: " . $structure->legnth . "\n";
			}	
			$jeditable .= "	});\n";	
			$current_value = ($structure->current_value) ? $structure->current_value : "n/a";
			$xhtml .= '<edit id="'.$id.'" name="'.$name.'" table="'.$table.'">'.$current_value.'</edit>';
			return (object) array("js"=>"$jeditable", "xhtml"=>"$xhtml");
		} else {
			return (object) array("js"=>"", "xhtml"=>$structure->current_value);	 
		}
	}

	function _getJeditableField () {
		if (function_exists('init')) { init(); $custom_initialization = true; }
			$id = mysql_real_escape_string($_REQUEST['id']); $name = mysql_real_escape_string($_REQUEST['name']);
		if ( $id != "" && $name != "" ) {
			$structure = (object) $this->table_structure_by_id($table, $id, $name);
			if (function_exists('run_extended_display')) {
			run_extended_display($structure, $id, $name);
			exit;
		} else {	
			if ( $structure->type == "enum" ) {
				$values = (array) $structure->values;
				if ( $custom_initialization == true ) {
					if ( is_array($selects_with_others) ) {
						if ( in_array("$name", $selects_with_others) ) {
							$values['other'] = "other";
							// Run function to pull info for selector.
							/*				print "<script>$(document).ready(function(){alert(\"You clicked the pet field.\");});</script>"; */
							}
						}
					}
					$structure->values = (object) $values;
					print json_encode($structure->values);
					exit;
				} else {
					if ( $structure->current_value != "" ) {
						echo $structure->current_value;
						exit;
					} else {
						echo $structure->default_value;
						exit;
					}
				}
			} 
		}
		echo "$value";
	}
	function _updateJeditableField () {
		if (function_exists('init')) { init(); $custom_initialization == true; }
		$id = mysql_real_escape_string($_REQUEST['id']);
		$name = mysql_real_escape_string($_REQUEST['name']);
		$value = mysql_real_escape_string($_REQUEST['value']);
		$structure = (object) $this->table_structure_by_id($table, $id, $name);	
		$chosen_value = $value;
		if ( $id != "" && $name != "" && $value != "" ) {
			if ( $_REQUEST['_method'] == "flip" ) {
				$values = (array) $structure->values;
				$corrected_value = $values[$value];
				if ( $value == "other" ) {
					if (function_exists('run_extended_jeditable')) {
						echo run_extended_jeditable($table, $id, $name, $structure, $value);
						//echo "$value";
					} else {
						echo run_core_jeditable($table, $id, $name, $structure, $value);
					}	
				} else {
					echo ( function_exists('run_extended_jeditable') ) ? run_extended_jeditable($table, $id, $name, $structure, $corrected_value) : $framework->run_core_jeditable($table, $id, $name, $structure, $corrected_value);
				}
			} else {
				$value_to_inject = ( $corrected_value ) ? $corrected_value : $chosen_value;
				if (function_exists('run_extended_jeditable')) {
					echo run_extended_jeditable($table, $id, $name, $structure, $value_to_inject);
				} else {
					echo $framework->run_core_jeditable($table, $id, $name, $structure, $value_to_inject);
				}
			}
			exit;
		} else {
			echo "<script>$(document).ready(function(){alert(\"Error: Not enough information was passed to attempt to change the table $table.\");});</script>";
			print json_encode($array);
			exit;
		}
	}
	
	function run_core_jeditable ($table, $id, $name, $structure, $value, $display_value=null) {
		mysql_query("UPDATE `".$table."` SET `".$name."`='".$value."' WHERE `id`=".$id." LIMIT 1;");
		$display_value = ( $display_value ) ? $display_value : $value;
		return (mysql_affected_rows() >= 1) ? $display_value : $structure->current_value."<script>$(document).ready(function(){alert(\"SQL Error: The following query was submitted to $table\n\n$sql.\");});</script>";
	}
	
	function table_structure_by_id($table, $id, $name) {
		if ( $id == "" ) { return true; }
		if ( $name == "" ) { return true; }
		if ( $table == "" ) { return true; }
		$query = mysql_query("SELECT * FROM `".$table."` WHERE id = '".$id."';");
		if ( mysql_num_rows($query) > 0 ) {
			$record = mysql_fetch_object($query);
			$field_count = mysql_num_fields($query);
			if ( $field_count > 0 ) {
				for ( $i = 0; $i < $field_count; $i++ ) {
					$names[] = mysql_field_name($query, $i );
					$type[]  = mysql_field_type($query, $i);
					$len[]   = mysql_field_len($query, $i);
					$flags[] = mysql_field_flags($query, $i);
				}
			} else {
				$names = array();
				$type = array();
				$len = array();
				$flags = array();	
				$record = (object) array();
			}
			unset($field_count);
			$fields = (object) array("names"=>$names,"types"=>$type, "len"=>$len, "flags"=>$flags);
			$keys = (object) array_flip($fields->names);
			$key = $keys->$name;
			$current_value = $record->$name;
			$type  = $fields->types[$key];
			$legnth = $fields->len[$key];
			$flags = $fields->flags[$key];
			$flags = (object) explode(' ', $flags);
			$flags = (object) array_flip($flags);

			//echo "$table $id $name $value<br>\n";
			$query = mysql_query("SHOW COLUMNS FROM `".$table."` WHERE Field = '".$name."'");
			if ( mysql_num_rows($query) > 0 ) {
				$structure = mysql_fetch_object($query);
				//echo mysql_errno() . ": " . mysql_error(). "\n";
				//echo mysql_info($query);
				if ( isset($flags->enum) ) {
					$type = "enum";
					$off  = strpos($structure->Type,"("); $values = explode(",",substr($structure->Type, ($off+1), strlen($structure->Type)-$off-2)); 
					for( $n = 0; $n < Count($values); $n++) {
						$val = substr( $values[$n], 1,strlen($values[$n])-2); $val = str_replace("''","'",$val); $values[$n] = array( $val, $val );
					}
					$values = (object) array_unique($this->array_values_recursive($values));
					unset($structure->key); unset($structure->Type);
				} elseif (isset($flags->set)) {
					$type = "set";
					$off  = strpos($structure->Type,"("); $values = explode(",",substr($structure->Type, ($off+1), strlen($structure->Type)-$off-2)); 
					for( $n = 0; $n < Count($values); $n++) {
						$val = substr( $values[$n], 1,strlen($values[$n])-2); $val = str_replace("''","'",$val); $values[$n] = array( $val, $val );
					}
					$values = (object) array_unique($this->array_values_recursive($values));
					unset($structure->key); unset($structure->Type);
				} else {
					$values = (object) array();
				}
				$submitted_value = mysql_real_escape_string($_REQUEST['value']);	
				if ( $structure->Field == $name ) {
					unset($structure->Field);
					return array("submitted_value"=>"$submitted_value","current_value"=>"$current_value","default_value"=>$structure->Default,"type"=>"$type","legnth"=>"$legnth","flags"=>$flags,"values"=>$values,"colums"=>$structure);
				} else {
					return array();
				}
			}
		} else {
			return false;	
		}
	}
	function array_values_recursive($array) {
		$list = array();
		foreach( array_keys($array) as $key ){
			$value = $array[$key];
			if (is_scalar($value)) {
				$list[] = $value;
			} elseif (is_array($v)) {
				$list = array_merge( $list, $this->array_values_recursive($key));
			}
		}
		return $list;
	}
} // End of class

?>