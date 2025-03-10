<style>
#generalboardmember { }
#boardmember h3 { }
#generalboardmember li {
	list-style:none;
	width:300px;
	display:block;
	/*float:left;*/
	margin:2px;
	border:1px solid #ccc;
	background-image:url(/Templates/default/gfx/boardboxbg.png);
}
#boardbox {
	width:110px;
	display:block;
	float:left;
	border:1px solid #ccc;
	margin:2px;
	background-image:url(/Templates/default/gfx/boardboxbg.png);
}
#memberposition { text-align:center; }
#memberphoto {
	width:98px;
	height:100px;
	margin:2px;
	overflow:hidden;
}
#boardbox li {
	list-style:none;
	text-align:center;
}
#boardmember a {
	text-decoration:none;
	color:#00C;
}
#generalmember { }
#generalmember h3 {
}
#generalmember li {
	list-style:none;
	width:300px;
	display:block;
	float:left;
	margin:2px;
	border:1px solid #ccc;
	background-image:url(/Templates/default/gfx/boardboxbg.png);
}
/* added by nick */
#members_column li a { text-decoration: none; color: #e9e9e9; }
</style>

<?php
function partition( $list, $p ) {
    $listlen = count( $list );
    $partlen = floor( $listlen / $p );
    $partrem = $listlen % $p;
    $partition = array();
    $mark = 0;
    for ($px = 0; $px < $p; $px++) {
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
        $partition[$px] = array_slice( $list, $mark, $incr );
        $mark += $incr;
    }
    return $partition;
}?>
<?php
$string = ''.$url.'';
$pieces = explode("/",$string);
$page = array_pop($pieces);
$admincat = array_pop($pieces);
$dirtypage = str_replace("_", " ", $page);
$cleanpage = str_replace(".html"," ",$dirtypage);

echo '<h3>Staff Members:</h3>';
$generalmembers = array();
echo '<div id="generalboardmember">';
$query = mysql_query("SELECT * FROM contact_dir WHERE `active` = 'ac' ORDER BY `name` ASC");
if (mysql_num_rows($query) > 0) {
	while($record = mysql_fetch_object($query)) {
					
		
		$name = $record->name;				
		$membercontact = str_replace(" ","-",$name);	
		$position = stripslashes(ucfirst($record->position));
		
		
				$emailcontact = '<a href="/contact/'.$membercontact.'.html">'.$record->name.'</a>: '.$position.'<br />';
				
			
		array_push($generalmembers, $emailcontact);
		} // End of WHILE (record2) statement.
} // End if statement for mysql_num_rows($query2)
echo '<hr />';
$columns = partition( $generalmembers, 1 );
foreach ( $columns as $members_in_column ) {
	echo '<ul class="members_column" style="float: left; display: inline-block; padding: 0px; margin: 0px;">';
	foreach($members_in_column as $generalmember) {
		echo '<li>'.$generalmember.'</li>';
	}
	echo '</ul>';
}

?>
