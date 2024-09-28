<?php
	$result = mysql_query("SELECT * FROM `commnetivity_media` WHERE `group` = 'vendorslider';");
	if (mysql_num_rows($result) > 0) {
		echo '<ul data-orbit data-options="timer_speed:3500; bullets:false;">';
    		while($record = mysql_fetch_object($result)) { 
			echo '<li>';
			echo '<a href="'.$record->description.'"><img src="/media/'.$record->real_filename.'" width="320" height="240" /></a>';
			//echo '<div class="orbit-caption">'.$record->description.'</div>';
			echo '</li>';
		}
		echo '</ul>';
	} else {
		echo 'We have nothing for this '.$location.'';
	}
?>
<style>
.orbit-slide-number {
		display: none;
		position: absolute;
}
.orbit-timer {
	display:none;
}
.orbit-prev {display:none;}
.orbit-next {display:none;}
</style>