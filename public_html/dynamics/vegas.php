<script src="/Templates/default/js/jquery.vegas.js"></script>
<script type="text/javascript">

	$.vegas('slideshow', {
		backgrounds:[
<?php
$query = mysql_query("SELECT * FROM `commnetivity_media` WHERE `group` = 'background' ORDER BY `orig_filename`;");
	if ( mysql_num_rows($query) > 0 ) {
		while($record = mysql_fetch_object($query)) {

echo "{ src:'/media/".$record->real_filename."', fade:1000 },";
}
								}									
?>
{ src:'/Templates/default/gfx/bodybg.jpg', fade:1000 }
		]
	});

</script>