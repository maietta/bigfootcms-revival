<?php

//header("Content-type: text/plain");

$permissions = (object) array(
	"cms"=>array(
		"content"	=>1
		,"users"	=>1
		,"system"	=>1
	)
	
	/* $blogging_abilities = (object) $permissions->blogs; */
	,"blogs"=>array(
	
		/* if ( $blogging_abilities->dart ) { ... } */
		"dart"=>array(
		
			/*  if ( $blogging_abilities->dart->can_edit ) { ... }  */
			 "can_view"=>true
			,"can_edit"=>true
		)
	)
);

echo serialize($permissions);

//exit;



$blogging_abilities = (object) $permissions->blogs; 



if ( $blogging_abilities->dart ) {
	echo "May access DART Blog";
	if ( $blogging_abilities->dart['can_edit'] ) {
		echo " and may edit DART Blog";
	}
} else {
	echo "No access to DART blog.";
}


//exit;
$controls = (object) $permissions->cms;
if ( $controls->content ) {
	echo "You may edit content.";
	
	
} else {
	echo "You may not control content. Sorry.";
}


/*

ob_clean();
$controls = (object) $permissions->cms;
if (!$controls->content) {   }

if ( $permissions->cms ) {
    $controls = (object) $permissions->cms;
	 if ($controls->content) {
		 $string = ''.$url.'';
$s = explode("/",$string);
$pieces = explode("/",$string);
$page = array_pop($pieces);
$locationarea = array_pop($pieces);
$dirtylocation = str_replace("_", " ", $page);
$location = str_replace(".html"," ",$dirtylocation);

	 $query = mysql_query("SELECT * FROM `commnetivity_content` WHERE `virtual_path` = '".$string."' ORDER BY `id` ASC");
			if (mysql_num_rows($query) > 0) {
			while($record = mysql_fetch_object($query)) {
						$pagelocation = $location;
						if ($string == $string){$mylocation = "<form>
																<label for=\"message\">Comment:</label>
		<textarea rows=\"5\" name=\"comment\" id=\"comment\" class=\"comment\"></textarea>
															   </form>.";}
						if ($string != $string){$mylocation = "<p>If you would like to comment on a page you must be a registered member for the ".ucfirst($locationarea)." section of this website, if you would like to register as a member of this wesite please <a href=\"#\">Click Here</a></p>";}
	 	echo 'Hello my record id is: '.$record->id.'</ br>';}
		echo 'My web path: '.$string.'</ br>';
		echo 'My web Page: '.$location.'</ br>';
		echo 'My result: '.$mylocation.'</ br>';
			}
	 } else echo 'Oops something went wrong';
}

*/

?>