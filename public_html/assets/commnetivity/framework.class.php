<?

class Framework {
	var $connection;
	var $jeditables;
	var $xhtml;
        function Framework(){
		$this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
		mysql_select_db(DB_NAME) or die(mysql_error());
	}

	function calcDist($lat_A, $long_A, $lat_B, $long_B) { 
		return (rad2deg(acos(sin(deg2rad($lat_A)) * sin(deg2rad($lat_B)) + cos(deg2rad($lat_A)) * cos(deg2rad($lat_B)) * cos(deg2rad($long_A - $long_B))))) * 69.09; 
	}

	function convert_datetime($str) {
        list($date, $time) = explode(' ', $str);
        list($year, $month, $day) = explode('-', $date);
        list($hour, $minute, $second) = explode(':', $time);
        $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
    	return $timestamp;
	}
	
    function makeAgo($timestamp){
        $difference = time() - $timestamp;
        $periods = array("sec", "min", "hr", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");
        for($j = 0; $difference >= $lengths[$j]; $j++)
        $difference /= $lengths[$j];
        $difference = round($difference);
        if($difference != 1) $periods[$j].= "s";
        $text = "$difference $periods[$j] ago";
    	return $text;
	}
	function query($path, $name){
	 	$query = "SELECT * FROM commnetivity_$name WHERE virtual_path = '$path'";
                $result = mysql_query($query, $this->connection);
                ${$name} = (object) mysql_fetch_object($result);
		return ${$name};
	}
	function general_query($query, $sql) {;
		${$query} = mysql_fetch_object((mysql_query($sql, $this->connection)));
		return ${$query};
	}
	function robots($path){
	 	$sql = "SELECT * FROM commnetivity_content WHERE virtual_path = '".VPATH."'";
		$content = mysql_fetch_object((mysql_query($sql, $this->connection)));
		return $content;
	}
	function query_sitelevel_data($sitelevel){
	 	$sql = "SELECT * FROM commnetivity_overrides WHERE top_level_pattern = '".$sitelevel."'";
		${$override} = mysql_fetch_object((mysql_query($sql, $this->connection)));
		return ${$override};
	}
	function profile2($username){
	 	$sql = "SELECT * FROM commnetivity_users WHERE username = '$username'";
		return mysql_fetch_object((mysql_query($sql, $this->connection)));
	}
	function check_override($path, $name) {
		//$vpath = substr(VPATH, 0, -strlen(substr(strrchr(VPATH, '/'), 1)));
		${$name} = mysql_fetch_object((mysql_query("SELECT * FROM commnetivity_overrides WHERE top_level_pattern = '" . $path . "'", $this->connection)));
		return ${$name};
	}
	function navigation($position, $limit=5, $filter_regex="") {
		$sql = "SELECT virtual_path FROM commnetivity_navigation WHERE `".$position."` = 'Y' LIMIT $limit;";
		
		$results = mysql_query($sql, $this->connection);
		$virtual_links = array();
		while ( $result = mysql_fetch_object($results)) {
			array_push($virtual_links, $result->virtual_path);
		}
		$sql = "SELECT virtual_path, page_title, nav_title FROM `commnetivity_content` WHERE `virtual_path` IN('".implode("', '", $virtual_links)."') ";
		$virtual_links = array();
		$results = mysql_query($sql, $this->connection);
		while ( $result = mysql_fetch_object($results)) {
			$result->page_title = (strlen($result->nav_title) > 0) ? $result->nav_title : $result->page_title; 
			array_push($virtual_links, array("virtual_path"=>$result->virtual_path, "page_title"=>$result->page_title));
		}
		return $virtual_links;
		
	}
	function redirects($virtual_path) {
		$sql = "SELECT * FROM commnetivity_redirects WHERE virtual_path = '$virtual_path'";
		$object = mysql_fetch_object((mysql_query($sql, $this->connection)));
		return $object;
	}
	function dynamics($divs_in_template) {
		$sql = "SELECT * FROM commnetivity_dynamics WHERE 1";
		$query = mysql_query($sql, $this->connection);
		$total = mysql_num_rows($query);
		for ($i=1; $i<=$total; $i++) {
			$pointer = mysql_fetch_object($query);
			if ( $pointer->target_div == in_array($pointer->target_div, $divs_in_template) ) {
				$array[$pointer->target_div] = $pointer->source_path;
			}
		}
		return $array;
	}
	function nav($params) {
		if ($params->use_navrules && $params->nav_field != "") {
			$sql = "SELECT `virtual_path` FROM `commnetivity_navigation` WHERE `".$params->nav_field."` = 'Y' ORDER BY 'weight' DESC";
			$virtual_links = array();
			
			
			$results = mysql_query($sql);
			while ( $result = mysql_fetch_object($results)) {
				$result->page_title = (strlen($result->nav_title) > 0) ? $result->nav_title : $result->page_title; 
				array_push($virtual_links, $result->virtual_path);
			}
			//ksort($virtual_links);
		}
		$query = mysql_query("SELECT `virtual_path`, `weight`, `page_title`, `nav_title` FROM `commnetivity_content` WHERE 1 ORDER BY 'weight' DESC");
		$nodes = array();
		$counts = array();
		$parents_only = array();
		$counter = 0;
		$parents = array();
		while ( $result = mysql_fetch_object($query) ) {
			$node = str_replace("//", "/", dirname($result->virtual_path));
			$count = count(explode('/', $node))-1;
			if ( !in_array($count, $counts) ) {
				$counts[] = $count;
			}
			
			$result->depth = count(explode('/', $node));
			$result->page_title = stripslashes($result->page_title);
			$result->nav_title = stripslashes($result->nav_title);
			
			$dir = str_replace("//","/",dirname($result->virtual_path));

			if (str_replace("$dir","",$result->virtual_path) ==  '/'.DIRECTORY_DEFAULT) { $result->is_parent = true; };

//			$test = str_replace("/", "", $result->virtual_path)."/".DIRECTORY_DEFAULT;
			
			//$result->parent = ( $test ) ? true : false;
			//$result->topdir = ( $result->parent && $result->count < 2 ) ? true : false;

			if ($params->use_navrules && in_array($result->virtual_path, $virtual_links)) {
				$result->in_navrules = true; //Make this dynamic
			} else {
				if (dirname($result->virtual_path) == dirname(VPATH)) {
						$result->start_here = true;
				}
			}
			$parents[$node][$increment++] = $result;
			$counter = 0;
		}
		$max =  max($counts);
		if ($params->root_first) {
			$index['/'] = $parents['/'];
			unset($parents['/']);
			$parents = array_merge($index, $parents);
		}
		if ($params->contact_last) {
			$index['/contact'] = $parents['/contact'];
			unset($parents['/contact']);
			$parents = array_merge($parents, $index);
		}
		return $parents;
	}
	
	function vpath($url) {
    	$path = (object) pathinfo($url);
        $url = (object) parse_url($url);
        

        
        
		if ($url->path == '/' && $path->dirname == '/' ) {
			header("HTTP/1.1 301 Moved Permanently");
            header("Location: http://" . HOSTNAME . "/" . DIRECTORY_DEFAULT);
			exit;
		}	
		if ($url->query != "" && $path->basename == "?") {
        	$basename = str_replace('?'.$url->query, "", $path->basename);
            $dirs = str_replace($basename, "", $url->path);
            $filename = str_replace($path->dirname, "", $dirs);
            $filename = ($filename == '/') ? DIRECTORY_DEFAULT : $filename;
            $dirs = str_replace("/". $filename, "", $dirs);
            //$joints = explode("/", $dirs);
			$vpath = $dirs . $filename;
		} elseif ($url->query != "") {
                $basename = str_replace($url->query, "", $path->basename);
                $dirs = str_replace($basename, "", $url->path);
                $filename = str_replace($path->dirname, "", $dirs);
                $filename = ($filename == '/') ? DIRECTORY_DEFAULT : $filename;
                $dirs = str_replace("/". $filename, "", $dirs);
                $vpath = $dirs;
				if ($vpath == "/".DIRECTORY_DEFAULT."/".DIRECTORY_DEFAULT) {
                	$vpath = "/".DIRECTORY_DEFAULT;
            	}
		} else {
                $basename = str_replace($url->query, "", $path->basename);
                $filename = str_replace($path->dirname."/", "", $url->path);
                $filename = str_replace("/", "", $filename);
                $directories = str_replace($filename, "", $url->path);
                $vpath = ($url->path == '/') ? '/' . DIRECTORY_DEFAULT : rtrim($url->path, '/');
                unset($basename); unset($filename); unset($directories);
                if ($vpath != $url->path ) { $vpath = $vpath . '/' . DIRECTORY_DEFAULT; }
                if ($vpath == "/".DIRECTORY_DEFAULT."/".DIRECTORY_DEFAULT) { $vpath = "/".DIRECTORY_DEFAULT; }
		}
            // If a javascript
            $info = pathinfo($vpath);
            
           // echo "$vpath";
           // exit;
            $sitelevel = $this->sitelevel($vpath);
            if ( $sitelevel == '/media' && $info['extension'] == "js") {
                // If the request was received...
//               $framework = new Framework;
//                $testing_vpath = str_replace(".js", ".html", "$vpath");
//                $testing_content = $framework->query($testing_vpath, "content");

				define('VPATH', $vpath);
				$framework = new Framework;
				$group = str_replace(".".$info['extension'], "", str_replace(dirname($vpath).'/', "", $vpath));
				$group = mysql_real_escape_string(urldecode($group));
				echo $framework->presentation($group);
				exit;
            } elseif ( $sitelevel == '/media' &&  $info['extension'] == "xml") {
				$audio_extentions = $this->get_extentions_from_mimetype("audio/%");
				$video_extentions = $this->get_extentions_from_mimetype("video/%");
        		$group = str_replace(".".$info['extension'], "", str_replace(dirname($vpath).'/', "", $vpath));

				$group = addslashes(urldecode($group));
				
				
				

        		$results = mysql_query("SELECT * FROM `commnetivity_media` WHERE `group` = '".$group."';");


				
        		if ( mysql_num_rows($results) > 0 ) {
        		    echo '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<playlist version="1">'."\n".'<trackList>'."\n";
        		    while ( $result = mysql_fetch_object($results) ) {
						//$filetype = $this->mediatype_discovery('/media/'.$result->real_filename);
						//if ( $filetype == "sound"||$filetype=="video" ) {
        	        		echo "".'<track>'."\n";
            	    		echo "".'<title>'.stripslashes($result->orig_filename).'</title>'."\n";
            	    		echo "".'<location>/media/'.$result->real_filename.'</location>'."\n";
                			echo "".'<image>'.$result->image.'</image>'."\n";
            	    		echo "".'<annotation>'.stripslashes($result->description).'</annotation>'."\n";
        	        		echo "".'<info></info>'."\n";
    	            		echo "".'</track>'."\n";
						//}
            		}
					echo '</trackList>'."\n".'</playlist>'."\n";	
				} else {
					echo "HELLO NO";
				}
				exit;
			} elseif($info['extension'] == "json") {
                $vpath = str_replace(".json", ".html", "$vpath");
                define("FORMAT", "json");
            } elseif($info['extension'] == "xml") {
                $vpath = str_replace(".xml", ".html", "$vpath");
                define("FORMAT", "xml");
            } else {
                define("FORMAT", "xhtml");
//				return $vpath;
                return str_replace("//", "/", "/".$vpath);
            }
            exit;
        }
		
		function get_mimetype_from_extention($extention) {
			$results =  mysql_query("SELECT mimetype FROM `commnetivity_mimetypes` WHERE `extention` = '".strtolower($extention)."' LIMIT 1;" );
			if (mysql_num_rows($results) == 1) {
				$result = mysql_fetch_object($results);
				return $result->mimetype;
			} else {
				return "";
			}
		}
		
		function get_extentions_from_mimetype($filter) {
			$results = mysql_query("SELECT DISTINCT extention FROM `commnetivity_mimetypes` WHERE `mimetype` LIKE '".strtolower($filter)."'");
			if (mysql_num_rows($results) > 0) {
				while ($extentions = mysql_fetch_object($results)) { $file_extentions[] = $extentions->extention; }
				return $file_extentions;
			} else {
				return array("");
			}
		}

function debug ($data) { 
    $xhtml = "<script>\r\n//<![CDATA[\r\nif(!console){var console={log:function(){}}}"; 
    $output    =    explode("\n", print_r($data, true)); 
    foreach ($output as $line) { 
        if (trim($line)) { 
            $line    =    addslashes($line); 
            $xhtml .= "console.log(\"{$line}\");"; 
        } 
    } 
    $xhtml .= "\r\n//]]>\r\n</script>"; 
	return $xhtml;
}

        function sitelevel($vpath) {
            return '/' . substr(substr($vpath,1), 0, strpos(substr($vpath,1), '/'));
        }
	function validateEmail($email) {
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			return false;
		}
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
				$local_array[$i])) { return false; }
			}
			if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false;
			}
	    	for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|↪([A-Za-z0-9]+))$",
					$domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
	}
	function sanitycheck($string, $type, $min_length, $max_length){
            $type = 'is_'.$type;
            if(!$type($string))	{
            return false;
            } elseif(empty($string)) {
		return false;
	    } elseif ( strlen($string) <= $min_length ) {
		return true;
            } elseif(strlen($string) >= $min_length && strlen($string) <= $max_length) {
		return false;
	    } else {
		return true;
            }
	}
	// array_values_recursive is a function added solely for the jeditables framework.
	function array_values_recursive($ary) {
		$lst = array();
		foreach( array_keys($ary) as $k ){
			$v = $ary[$k];
			if (is_scalar($v)) {
				$lst[] = $v;
			} elseif (is_array($v)) {
				$lst = array_merge( $lst, $this->array_values_recursive($v));
			}
		}
		return $lst;
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
					$names[] = mysql_field_name($query, $i);
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

	function logged_in() {
		return ( isset($_SESSION['username']) ) ? TRUE : FALSE;
	}
	function profile() {
		if ( isset($_SESSION['username']) ) {
			$sql = "SELECT * FROM commnetivity_users WHERE username = '" . $_SESSION['username'] . "' LIMIT 1;";
			$profile = mysql_fetch_object((mysql_query($sql, $this->connection)));
			if ( $profile->username ) {
				// Update last_seen
				$now = time();
				mysql_query("UPDATE `commnetivity_users` SET `last_seen`= 'NOW()' WHERE `username`=".$_SESSION['username'].";", $this->connection);
			}
			return $profile;
		} else {
			return 0;
		}
	}
	function error404 ($vpath) {
            //ob_start();
            //include(PATH."/dynamics/search.php");
           // $searched = ob_get_contents();
           // ob_end_clean();
           
    	header('HTTP/1.0 404 Not Found');

		$xhtml .= "<h1>404 Not Found</h1>";
		$xhtml .= "The page that you have requested could not be found.<br>";
		$xhtml .= $_SERVER['REFERRER'];

          // $xhtml = "<h2>No page found.</h2>";
          // $xhtml .= "<p>$searched</p>";
		  // $xhtml .= "<h2>Revisions?</h2>";
            
            
            return $xhtml;
        }

	function generatePassword($length=6,$level=2){
		list($usec, $sec) = explode(' ', microtime());
		srand((float) $sec + ((float) $usec * 100000));
		$validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
		$validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";
		$password  = "";
		$counter   = 0;
		while ($counter < $length) {
		$actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);
		// All character must be different
		if (!strstr($password, $actChar)) {
			$password .= $actChar;
			$counter++;
			}
		}
		return $password;
	}
	function register() {
		if ( $_POST['email'] && $_POST['username'] ) {
			$username = strtolower(mysql_real_escape_string($_POST['username']));
			ob_start();	include(PATH . COMMNETIVITY . "/templates/register.php"); $script_content = ob_get_contents(); ob_end_clean();
			$html = str_get_html("$script_content");
			$sql = "SELECT username FROM `commnetivity_users` WHERE username LIKE '" . $username . "' LIMIT 1;";
			$result = mysql_query("$sql") or die(mysql_error());
			if( mysql_num_rows($result) > 0) {
				$messege = "Username already taken. Please try another.";
				$html->find('div[id=messege]', 0)->innertext = "$messege";
				$html->find('input[name=username]', 0)->style = "border-color: #FF0000;";
				$html->find('input[name=username]', 0)->value = "$username";
			} else { // Username available.
				if ( $this->sanitycheck("$username", 'string', 4, 25 ) != FALSE ) {
					$username_validated = true;
					$messege = ""; // No messege needed yet.
					$html->find('input[name=username]', 0)->style = "border-color: #336633";
					$html->find('input[name=username]', 0)->value = "$username";
				} else {
					$messege = "The username must consist between 4 and 25 alphanumeric characters only.";
					$html->find('input[name=username]', 0)->style = "border-color: #FF0000";
					$html->find('input[name=username]', 0)->value = "$username";
				}
			}
			$email = strtolower(mysql_real_escape_string($_POST['email']));
			$sql = "SELECT email FROM `commnetivity_users` WHERE email LIKE '" . $email . "' LIMIT 1;";
			$result = mysql_query("$sql") or die(mysql_error());
			if( mysql_num_rows($result) > 0) {
				if ( $messege ) {
					$messege .= "Additionally the email address is already registered in our system.";
				} else {
					$messege = "The email address supplied is already registered in our system.";
				}
				$html->find('div[id=messege]', 0)->innertext = "$messege";
				$html->find('input[name=email]', 0)->style = "border-color: #FF0000;";
				$html->find('input[name=email]', 0)->value = "$email";
			} else { // Username available.
				if ( $this->validateEmail($email) != FALSE ) {
					$email_validated = true;
					$html->find('input[name=email]', 0)->style = "border-color: #336633";
					$html->find('input[name=email]', 0)->value = "$email";
				} else {
					if ( $messege ) {
						$messege .= "Additionally, the email address you supplied is incorrectly formatted.";
					} else {
						$messege = "The email address you supplied is incorrectly formatted.";
					}
					$html->find('div[id=messege]', 0)->innertext = "$messege";
					$html->find('input[name=email]', 0)->style = "border-color: #FF0000";
					$html->find('input[name=email]', 0)->value = "$email";
				}
			}
			if ( isset($username_validated) && isset($email_validated) ) {
				$password = md5($this->generatePassword(8, 2));
				$sql = "INSERT INTO `commnetivity_users` (`username`, `password`, `email`) VALUES ('".$username."', '".$password."', '".$email."');";
				mysql_query($sql);
				require_once(PATH . "/assets/3rdparty/PHPMailer/class.phpmailer.php");
				$script_content = '<h2>Account created.</h2> To activate, check your email for your temperary password and then <a href="/cgi-bin/login">sign in.</a>.';
				$mail = new PHPMailer();
				$mail->IsMail();
				$mail->From=EMAIL_FROM_ADDR;
				$mail->FromName=EMAIL_FROM_NAME;
				$mail->AddAddress("$email");
				$mail->Subject = "Account created. Please activate. " . HOSTNAME;
				$mail->Body = "Please visit " . HOSTNAME . " and login with the following information:";
				$mail->Body .= "          Username: $username.\n";
				$mail->Body .= "Temperary Password: $password.\n\n";
				$mail->Body .= "Please note: Once logged in, you may be required to provide same basic contact information to complete the registration process.\n\n";
				$mail->Body .= "If you did not initiate the creation of the account, please disregard the messege as we purge non-activated accounts in 1 hour.\n\n";
				$mail->Body .= "-- " . EMAIL_FROM_NAME . "\n" . HOSTNAME;
				$mail->Send();
			} else {
				$script_content = $html;
			}
			return "$script_content";
		} else {
			ob_start();
			include(PATH . COMMNETIVITY . "/templates/register.php");
			$script_content = ob_get_contents();
			ob_end_clean();
			$html = str_get_html("$script_content");
			$html->find('div[id=messege]', 0)->innertext = "To register for basic access, simply supply a desired username and your email address. We will send you a auto generated password to login with.";
			$script_content = $html;
			return (object) array("title"=>"Register", "content"=>"$script_content","template"=>"fullpage.dwt","targets"=>array("dashboard","Bullseye!"));
		}
	}
    function my_account () {
    	return (object) array("title"=>"My Account","content"=>"We have not yet enabled the custom control panel.");
    }

function count_files_in_group($group){
    $query = mysql_query("SELECT id FROM `commnetivity_media` WHERE `group` = '".$group."';");
    $count = mysql_num_rows($query);
    return $count;
}

function ifHasVideo($group) {
    $results = mysql_query("SELECT id FROM `commnetivity_media` WHERE `group` = '".$group."';");
    if ( mysql_num_rows($results) > 0 ) {
        while($result = mysql_fetch_object($results)) {
            if ( $result->extention == "mp4" OR $result->extention == "avi" OR $result->extention == "wmv" ) {
                return true;
            }
        }
    } else {
        return false;
    }
}

function cpuixhrrpc($script) {
	$params = (object) array("name"=>mysql_real_escape_string($_REQUEST['name']), "value"=>mysql_real_escape_string($_REQUEST['value']), "id"=>mysql_real_escape_string($_REQUEST['id']));
	$rail = RAIL;
	
	if (!file_exists($script)) {
		echo json_encode(array("display"=>"<p>No script at $script.</p>"));
		exit;
	} else {
		require($script);
	}
	
	$framework = new Framework;

	require_once(PATH.COMMNETIVITY.'/interface.class.php');
	$ui = new screen;
	
	if ($_SERVER['REQUEST_METHOD'] == "POST" ) { // POST
		if (function_exists($params->name)) {
			$panel = $params->name;
			$response = $panel($framework, $ui, $params);
			mysql_query("INSERT INTO `commnetivity_logfile` (`username`, `rail`, `params`) VALUES ('".$_SESSION['username']."', '".RAIL."', '".serialize($params)."');");
			return json_encode($response);
		}
	} else {
		if (function_exists(SCREEN)) {
			$screen = SCREEN;
			$response = $screen($framework, $ui, $params);
				$response['display'] .= '<style>' . file_get_contents(PATH.COMMNETIVITY."/all.css") . '</style>';
			echo json_encode($response);
		} else { // This was POST mode... meaning screens were drawn. However, There was no function in the $panel.
			if (function_exists('initialize')) {
	        	$initialized = initialize($framework, $ui, $params);

				$initialized['display'] .= '<style>' . file_get_contents(PATH.COMMNETIVITY."/all.css") . '</style>';
				return (is_array($initialized)) ? json_encode($initialized) : json_decode($initialized);
			} else {
				return "No screen function $screen in $script.";
			}
		}
	}
	exit;
}

function wordtruncate($string, $limit, $break=".", $pad="...") {
  // return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;
  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }
  return $string;
}

function play($field, $search) {
	$search = stripslashes(urldecode($search));
	$player_id = 'test';
	$results = mysql_query("SELECT * FROM `commnetivity_media` WHERE `".$field."` = '".$search."';");
	$video_extentions = $this->get_extentions_from_mimetype("video/%");
//	$obj_merged = (object) array_merge((array) $obj1, (array) $obj2);

	$presentation = (object) array("width"=>233,"height"=>24); // Default! This will be overwritten if defined in presentation.
	$presentation->autostart = true;
		$flash_vars = (object) array();
		$params = (object) array();
		$attributes = (object) array("id"=>$player_id,"name"=>$player_id);
	
	$flash_vars->id = $player_id;
	$has_video = false; // Set the default.
	if (mysql_num_rows($results)==1) {
		//$flash_vars = (object) unserialize($presentation->flash_vars);
		$record = mysql_fetch_object($results);
		$flash_vars->file = "/media/".$record->real_filename;
		// Does this file contain a video?
		$has_video = (in_array($record->extention, $video_extentions)) ? true : false;
	} elseif(mysql_num_rows($results)>0) {
		$flash_vars->playlistsize = '200';
		$flash_vars->playlist = 'bottom';
		$uses_playlist=true;
		while ($record = mysql_fetch_object($results)) {
			$has_video = (in_array($record->extention, $video_extentions)) ? true : $has_video;
		}
		$flash_vars->file = "/media/$search.xml";
	} else {
		// I need to create a custom audio introduction letting people know this software is powered by Commnetivity.
		$flash_vars->file = "/media/$search.xml";
	}
	
	// Check for player presentation
	$sql = "SELECT * FROM `commnetivity_presentation` WHERE `".$field."` = '".$search."';";
	$results = mysql_query($sql);
	if (mysql_num_rows($results)>=1) {
		$record = mysql_fetch_object($results);
		$params = (object) unserialize($player->params);
		// There is presentation data. Use it.
	} else {
		// Use defaults
		// Is video?
	}
	
	print_r($presentation);
	
	//if ( isset($_REQUEST['width']) ) { $presentation->width = $_REQUEST['width']; }
	//if ( isset($_REQUEST['height']) ) { $presentation->height = $_REQUEST['height']; }
	
	//$flash_vars->autostart = false;
	
	$params->type = ($has_video) ? "video" : "sound";
	$params->type = ($params->type == "video") ? "video" : "sound";

	if ($params->type=="video") {
		//$flash_vars->playlistsize = '200';
		//$flash_vars->playlist = 'bottom';
		//$flash_vars->allowresize = true;	
		//$flash_vars->stretching = "exactfit";
	} else {
		if ($uses_playlist) {
			$flash_vars->playlistsize = '200';
			$flash_vars->playlist = 'bottom';
		}
		//$presentation->height = 27;
		//$flash_vars->plugins = "spectrumvisualizer-1";
	}
	
	
	
	$flash_vars->autostart = ($has_video) ? false : true;
	$flash_vars->skin = "/assets/jwplayer/glow.zip";
	print_r($presentation);

	$javascript  = "var flash_vars = " . stripslashes(json_encode($flash_vars)) . ";\n";
    $javascript .= "var params = " . stripslashes(json_encode($params)) . ";\n";
    $javascript .= "var attributes = " . stripslashes(json_encode($attributes)) . ";\n";
	$javascript .= "swfobject.embedSWF('/assets/player.swf', '$player_id', '".$presentation->width."', '".$presentation->height."', '9.0.124', " . $presentation->autostart . ", flash_vars, params, attributes);"."\n\n";
	
	$extentions = $this->get_extentions_from_mimetype("video/%");
	return array("display"=>'<player id="'.$player_id.'"></player>', "javascript"=>$javascript);
}

function rgb2hex($r, $g=-1, $b=-1) {
    if (is_array($r) && sizeof($r) == 3)
        list($r, $g, $b) = $r;

    $r = intval($r); $g = intval($g);
    $b = intval($b);

    $r = dechex($r<0?0:($r>255?255:$r));
    $g = dechex($g<0?0:($g>255?255:$g));
    $b = dechex($b<0?0:($b>255?255:$b));

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return $color;
}

function presentation($group) {
                //$audio_defaults = (object) array(""=>"",""=>"",""=>"",""=>"");
                //$video_defaults = (object) array(""=>"",""=>"",""=>"",""=>"");
                $results = mysql_query("SELECT * FROM `commnetivity_media` WHERE `group` = '".$group."';");
                if ( mysql_num_rows($results) == 1 ) {
                    // There is 1 active record for this group....
                        $sql = "SELECT * FROM `commnetivity_media` WHERE `group` = '".$group."' LIMIT 1;";
                        $results = mysql_query($sql);
                        if ( mysql_num_rows($results) > 0 ) {
                            $result = mysql_fetch_object($results);
                            $filename = "/media/".$result->real_filename;
                            $extention = $result->extention;
                        }
                        $sql = "SELECT * FROM `commnetivity_presentation` WHERE `group` = '".$group."' LIMIT 1;";
                        $results = mysql_query($sql);
                        if ( mysql_num_rows($results) == 1 ) {
							$presentation = mysql_fetch_object($results);
							$flash_vars = (object) unserialize($presentation->flash_vars);
							$flash_vars->file = $filename;//changed 
							$flash_vars->id = $group;
							// unset($flash_vars->playlist);
//                           unset($flash_vars->playlistsize);
                            $params = unserialize($presentation->parameters);
                            $attributes = unserialize($presentation->attributes);
                            $attributes->id = $group;
                            $attributes->name = $group;
                            
                            //$flash_vars->backcolor = ( $flash_vars->backcolor != "" ) ? $flash_vars->backcolor : "7F8567";

                            if ($extention == "mp3" or $extention == "ogg" or $extention == "wav") {
                                $presentation->width = ( is_int($presentation->width) ) ? $presentation->width : 350;
                                $presentation->height = ( is_int($presentation->height) ) ? $presentation->height : 24;
                                $flash_vars->type = "sound";
                            } else {
                                $presentation->width = ( is_int($presentation->width) ) ? $presentation->width : 350;
                                $presentation->height = ( is_int($presentation->height) ) ? $presentation->height : 350;
                            }
                            $presentation->autostart = ( $presentation->autostart === true ) ? "true" : "false";
                            $video_js  = "var flash_vars = " . stripslashes(json_encode($flash_vars)) . ";\n";
                            $video_js .= "var params = " . stripslashes(json_encode($presentation->params)) . ";\n";
                            $video_js .= "var attributes = " . stripslashes(json_encode($attributes)) . ";\n";
                            echo $video_js;
                            return "swfobject.embedSWF('/assets/player.swf', '$group', '".$presentation->width."', '".$presentation->height."', '9.0.124', " . $presentation->autostart . ", flash_vars, params, attributes);"."\n\n";
                        } else {
                            return "There was no presentation available for the files in $group.";
                        }
                } elseif (mysql_num_rows($results) > 1 ) {
                    // There are many records for this group...
                    while ( $result = mysql_fetch_object($results) ) {
                        //echo "We have a possible presentation for this.... lookup presentation data...\n"
                        $results = mysql_query("SELECT * FROM `commnetivity_presentation` WHERE `group` = '".$group."' LIMIT 1;");
                        if ( mysql_num_rows($results) == 1 ) {
                            $presentation = mysql_fetch_object($results);
                            $flash_vars = (object) unserialize($presentation->flash_vars);
                            $flash_vars->file = "/media/xml/$group.xml";
                            $flash_vars->id = $group;
                            $params = unserialize($presentation->params);
                            $attributes = unserialize($presentation->attributes);
                            $attributes->id = $group;
                            $attributes->name = $group;
						//$presentation->width = ( is_int($presentation->width) ) ? $presentation->width : 300;
                        //$presentation->height = ( is_int($presentation->height) ) ? $presentation->height : 300;
						$presentation->autostart = ( $presentation->autostart === true ) ? "true" : "false";
						$video_js  = "var flash_vars = " . stripslashes(json_encode($flash_vars)) . ";\n";
						$video_js .= "var params = " . stripslashes(json_encode($params)) . ";\n";
						$video_js .= "var attributes = " . stripslashes(json_encode($attributes)) . ";\n";
						
					return "$video_js"."swfobject.embedSWF('/assets/player.swf', '$group', '".$presentation->width."', '".$presentation->height."', '9.0.124', " . $presentation->autostart . ", flash_vars, params, attributes);"."\n\n";
				} else {
                	return "/* There was no presentation available for the files in $group. */";
            	}
        	}
    	} else {
			return "// No records....";
	}
}


	function login(){
		$username = mysql_real_escape_string(trim($_REQUEST['username']));
		$password = md5(mysql_real_escape_string(trim($_REQUEST['password'])));
		$messege = "Please login";
		

		//return array("title"=>"Sign in","content"=>"Intercepted!!!".$_REQUEST['username']);
		//exit;
		if( isset($_REQUEST['username']) && isset($_REQUEST['password']) ) {
			$sql = "SELECT * FROM commnetivity_users WHERE username = '" . $username . "' LIMIT 1;";
			$profile = mysql_fetch_object((mysql_query($sql, $this->connection)));
			if( $username == $profile->username ) {
				if ( $password == $profile->password ) {
					$_SESSION['level'] = $profile->userlevel;
					$_SESSION['username'] = $profile->username;
					//echo " $profile->username";
					if ( isset($_SESSION['next_vpath']) ) {
						$next_vpath = $_SESSION['next_vpath'];
						unset($_SESSION['next_vpath']);
						header("Location: ".$next_vpath);
					} else {
						header("Location: /my_account/index.html");
					}

				} else {
					// Username correct, but password is wrong. Update last_attempt_ip and last_attempt_timestamp fields for $username used.
					$messege = "Incorrect password. Please try again.";
					ob_start();
					include(PATH.COMMNETIVITY."/templates/login.php");
					$script_content .= ob_get_contents();
					ob_end_clean();
				}
		} else {
			// Wrong username or Password. Show error here.
			if ( $_SESSION['auth_tries'] >= 3 ) {
				$lapse = time() - $_SESSION['last_seen'];
				if ( $lapse <= 30 ) {
					$script_content = "You must wait 30 seconds before submitting your request.";
				}
			} else {
				//$thetime = date(”H:i:s”, time()-7200);
				$_SESSION['auth_tries'] = 1;
			}
			if ( $_SESSION['auth_tries'] >= 1 && $_SESSION['last_seen'] ) {
				if ( $_SESSION['last_seen'] <= time() ) {

				}
				$_SESSION['auth_tries'] = $_SESSION['auth_tries'] + 1;
			} else {
				$_SESSION['auth_tries'] = 1;
			}

			ob_start();
			include(PATH . COMMNETIVITY . "/templates/login.php");
			$script_content = ob_get_contents();
			ob_end_clean();
			$html = str_get_html("$script_content");
			$html = $html->find('h1', 0)->innertext = "<h2>Incorrect. Please try again.</h2>";
			$script_content .= $html;
			if ( $username == $profile->username ) {
				// Username was good, add javascript to focus, modify body tag to have onLoad function and focus on password box.
				// add to javascripts(postition, value);
				//
				//$script_content .= $html->find('value', 0)->innertext = USER;
			}
		}
	} else {
		if(isset($_SESSION['messege'])) {
			$script_content = $_SESSION['messege'];
		} else {
			if( isset($_SESSION['username']) ){
				$script_content = "You are already signed in.";
			} else {
				$script_content = file_get_contents(PATH . COMMNETIVITY . "/templates/login.php");
			}
		}
	}
		return array("title"=>"Sign in","content"=>($script_content),"template"=>TEMPLATES."fullpage.dwt","targets"=>array("dashboard","Bullseye!"),"javascripts"=>array("/assets/test.js","/assets/test2.js"));
	}
	function edit ($table, $id, $name, $extra = "" ) {
		if (function_exists('init')) {
			init();
		} else {
			echo "Editable failed.";
			exit;
		}
		if ( EDITABLE ) {
			$framework = new Framework;
			$structure = (object) $framework->table_structure_by_id($table, $id, $name);
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
			return (object) array("js"=>"", "xhtml"=>"Editing $name was disabled. Check custom initialization routine.");	 
		}
	}
	function run_core_jeditable ($table, $id, $name, $structure, $value, $display_value=null) {
		mysql_query("UPDATE `".$table."` SET `".$name."`='".$value."' WHERE `id`=".$id." LIMIT 1;");
		//return $display_value;
		$display_value = ( $display_value ) ? $display_value : $value;
		return (mysql_affected_rows() >= 1) ? $display_value : $structure->current_value."<script>$(document).ready(function(){alert(\"SQL Error: The following query was submitted to $table\n\n$sql.\");});</script>";
	}
	function show_unix_time ($time, $format) {
    	return date($format,($time == 0) ? time() : $time);
	}
	function resizeImage($image_path, $type, $file,$scale="",$width="",$height="") {
		//Make sure that the vars (scale, width, height) are all numeric
		if((is_numeric($scale) === TRUE) || (is_numeric($width) === TRUE && is_numeric($height) === TRUE)) {
            // If they wish to scale the image.
            // You gotta make sure that it is set AND that it has a value.
            if (isset($scale) && $scale != "") {
                // Create our image object from the image.
                $fullImage = imagecreatefromjpeg($image_path.'/'.$file);
                // Get the image size, used in calculations later.
                $fullSize = getimagesize($image_path.'/'.$file);
                // If there is NOT a thumbnail for this image, make one.
                if (!file_exists("tn_".$file)) {
                    // Create our thumbnail size, so we can resize to this, and save it.
                    $tnImage = imagecreatetruecolor($fullSize[0]/$scale, $fullSize[1]/$scale);
                    // Resize the image.
                    imagecopyresampled($tnImage,$fullImage,0,0,0,0,$fullSize[0]/$scale,$fullSize[1]/$scale,$fullSize[0],$fullSize[1]);
                    // Create a new image thumbnail.
                    if ( $type == "jpg" ) { imagejpeg($tnImage, $image_path.'/'."tn_".$file); }
                    if ($type =="png") { imagepng($tnImage, $image_path.'/'."tn_".$file); }
                    // Clean Up.
                    imagedestroy($fullImage);
                    imagedestroy($tnImage);
                    // Return our new image.
                    return "tn_".$file;
                } else {// If there is a thumbnail file, lets just load it.
                    return "tn_".$file;
                }
            } elseif (isset($width) && isset($height)) {
                // If they want to force whatever size they want.
                return "tn_".$file;
            } else {
                return false;
            }
        } else {
            //Throw error:
            return $file;
        }
    }

	function navigation_by_reference($deep=0, $field_name="", $hide_pages=false) {
		
		//$group = "lvl";
		//for ($i = 1; $i <= ($deep+1); $i++) {
		//	$group{$i} = array();
		//}
		$framework = new Framework;
		if ($field_name!="") { $show_enabled_only = true; $navigation = $framework->navigation("$field_name"); }
		
		$sql = (VPATH == '/'.DIRECTORY_DEFAULT)
			? "SELECT weight, page_title, parent_id, virtual_path FROM commnetivity_content WHERE virtual_path LIKE '/%' ORDER BY INT('weight') LIMIT 25;"
			: "SELECT weight, page_title, parent_id, virtual_path FROM commnetivity_content WHERE virtual_path LIKE '".dirname(VPATH)."/%' ORDER BY INT('weight') LIMIT 25;";
		$sql = str_replace("//", "/", $sql);
		$results = mysql_query("$sql");
		$current_vpath_parts = explode("/", dirname(VPATH));
		$global_navigation = (object) array();
		if ( mysql_num_rows($results) >= 1 ) {
			$xhtml .= "<ul>\n";
			while ( $result = mysql_fetch_object($results) ) {
				if (VPATH == $result->virtual_path) {
					$visiting = ' class="visiting"';
				} else {
					$visiting = "";
				}
				if ( $result->virtual_path != dirname(VPATH).'/'.DIRECTORY_DEFAULT ) {
					$selected_vpath_parts = explode("/", str_replace(dirname(VPATH), "/", $result->virtual_path));
					$selected_vpath_parts_reversed = array_reverse($selected_vpath_parts);
					$first = array_shift($selected_vpath_parts_reversed);	
					$count = (count($selected_vpath_parts_reversed) - count($current_vpath_parts))+1;
					if ($count == 0 && $first == DIRECTORY_DEFAULT) {
						// This happens to be the landing page of the site. We dont want to show it.
					} else {
						if ( dirname(VPATH) == dirname($result->virtual_path)) {
							// We appear to be looking at current directory.
								

								
								$xhtml .= (VPATH == $result->virtual_path && in_array($result->virtual_path, $navigation)) // I love expressions.
								? 'd<li class="level'.$count.'"><a href="'. $result->virtual_path . ' class="visiting">'.stripslashes($result->page_title).'</a></li>'."\n"
								: '<li class="level'.$count.'"><a href="'. $result->virtual_path .$visiting. '">'.stripslashes($result->page_title).'</a></li>'."\n";
								
								
								
						} else {
							if ($show_enabled_only == true && in_array($result->virtual_path, $navigation)) {
								if ($first == DIRECTORY_DEFAULT && $result->virtual_path != dirname(VPATH).DIRECTORY_DEFAULT && $count < ($deep + 2)) {
									
										$xhtml .= (VPATH == $result->virtual_path)
										? '<li class="level'.$count.'"><a href="'. $result->virtual_path . ' class="visiting">'.stripslashes($result->page_title).'</a></li>'."\n"
										: '<li class="level'.$count.'"><a href="'. $result->virtual_path .$visiting. '">'.stripslashes($result->page_title).'</a></li>'."\n";									
								} else {
									if ($count < $deep) {
										$xhtml .= (VPATH == $result->virtual_path)
										? '<li class="level'.$count.'"><a href="'. $result->virtual_path . ' class="visiting">'.stripslashes($result->page_title).'</a></li>'."\n"
										: '<li class="level'.$count.'"><a href="'. $result->virtual_path .$visiting.'">'.stripslashes($result->page_title).'</a></li>'."\n";
									}
								}
							}
						}
					}
				}
			}
			$xhtml .= "</ul>\n";
		}
		return $xhtml;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function navigation_by_reference2($framework, $settings) {
		
		$setting = (object) $settings;
		
		$depth = ( $setting->depth && is_int($setting->depth) ) ? $setting->depth : 3;
		$limit = ( $setting->limit && is_int($setting->limit) ) ? $setting->limit : 100;
		
		$limit = ( $setting->limit && is_int($setting->limit) ) ? $setting->limit : 100;
		
		$group = "nesting";
		for ($i = 1; $i <= ($depth+1); $i++) {
			$group{$i} = array();
		}
		
		//if ($setting->select!="") { $show_enabled_only = true; $navigation = $framework->navigation($setting->select); }
		
		$sql = (VPATH == '/'.DIRECTORY_DEFAULT)
			? "SELECT page_title, parent_id, virtual_path FROM commnetivity_content WHERE parent_id LIKE '/%' ORDER BY `page_title`;"
			: "SELECT page_title, parent_id, virtual_path FROM commnetivity_content WHERE parent_id LIKE '".dirname(VPATH)."/%' ORDER BY `page_title`;";
		$sql = str_replace("//", "/", $sql);
		$results = mysql_query("$sql");
		$current_vpath_parts = explode("/", dirname(VPATH));
		$global_navigation = array();
		$determined = (object) array();
		if ( mysql_num_rows($results) >= 1 ) {
			while ( $result = mysql_fetch_object($results) ) {
				if ( $result->virtual_path != dirname(VPATH).'/'.DIRECTORY_DEFAULT ) {
					$selected_vpath_parts = explode("/", str_replace(dirname(VPATH), "/", $result->virtual_path));
					$selected_vpath_parts_reversed = array_reverse($selected_vpath_parts);
					$first = array_shift($selected_vpath_parts_reversed);	
					$count = (count($selected_vpath_parts_reversed) - count($current_vpath_parts))+1;
					$relationship = ($first == DIRECTORY_DEFAULT) ? "parent" : "child";	
					$determined->landing = false; // Preset to false, then check later.

					/* To list only the directories in the current directory, you must know the $count number.
						In this case, we will need to know how many parts are in the current directory.
					*/			
					$newcount =  count(explode("/", dirname(VPATH)));
					if ($relationship == "parent") {
						$result->newcount = $newcount;
						$parents[] = array("parent", $result);
					} else {
						
						if ( $setting->pages ) { $children[] = array("child", $result); }
					}
					//$result->newcount = $newcount;
					
					$dirnames[] = dirname($result->virtual_path);
					
					
					
					echo dirname($result->virtual_path)."<br>";
					
					
					
					if ($count == 0) {
						$determined->landing = ( $first == DIRECTORY_DEFAULT ) ? true : false;
					} else {
						
					}
				}
				$global_navigation[] = $determined;
				unset($determined);
			}
			//$xhtml .= "</ul>\n";
		}
		
		if ($determined->landing != true) {
			unset($determined->landing);
		}
		return array($$dirnames);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
} // end of class

?>