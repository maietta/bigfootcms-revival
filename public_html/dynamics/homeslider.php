<?php
	$result = mysql_query("SELECT * FROM `commnetivity_media` WHERE `group` = 'homeslider';");
	if (mysql_num_rows($result) > 0) {
		echo '<ul data-orbit data-options="timer_speed:3500; bullets:false;">';
    		while($record = mysql_fetch_object($result)) { 
			echo '<li>';
			echo '<a href="'.$record->description.'"><img src="/media/'.$record->real_filename.'" /></a>';
			//echo '<div class="orbit-caption">'.$record->description.'</div>';
			echo '</li>';
		}
		echo '</ul>';
	} else {
		echo "Orbit feature has no images to work with.";
	}
?>
<style>
.orbit-slide-number {
		display: none;
		position: absolute;
}
.orbit-wrapper .timer { visibility: hidden; }
.orbit-wrapper { visibility: hidden; }
.orbit-timer {display:none;}
</style>