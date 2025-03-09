
<?php
$query  = "SELECT * FROM `alerts_dir` WHERE `active` = 'ac'";
 		$result = mysql_query($query);
  			if (mysql_num_rows($result) > 0)
   				{
    			while($row = mysql_fetch_array($result))
   				{ 
				$alertname = $row['name'];
				$alerttype = $row['type'];
				$alerttext = $row['advisory'];
				echo "<div class=\"alert-box error alert\">Name: $alertname  Type: $alerttype Message: $alerttext</div>";
				}
				}else echo '';


?>
