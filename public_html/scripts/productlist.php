<div class="row">
<?php
$result = mysql_query("SELECT * FROM `store` ORDER BY `Dept_ID` ASC");
	if (mysql_num_rows($result) > 0) {
    		while($record = mysql_fetch_object($result)) { 
			
			echo '<div class="large-2 columns small-3"><img src="http://placehold.it/80x80&text='.$record-itemNum.'" /></div>
					<div class="large-10 columns">
						<strong>Name:</strong>'.$record->ItemName.' - <strong>'.$result-Vendor_Part_Num.'</strong> - <strong>Price:<strong>'.$result->Price.'
					</div>
					<ul class="inline-list">
            			<li><a href="">Add To Cart</a></li>
            			<li><a href="">Buy Now</a></li>
          			</ul>
				  </div>	
			';
			}
	}
?>
</div>
