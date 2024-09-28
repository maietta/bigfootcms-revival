<?php
$list = glob("/home/oesdigit/public_html/docs/*.pdf");
	// Open a known directory, and proceed to read its contents 
	foreach($list as $value) {
		$dirtydocs =  str_replace($_SERVER['DOCUMENT_ROOT'], "", $value);
		
		$parts = (object) pathinfo($value);
		//$module_num = str_replace("_", ":", $parts);
		$filename = $parts->basename; 
		//$module_num = str_replace("_", ":", $filename);
		$nhndoc = explode("_",$filename);
		$nhndirtyname = str_replace(".pdf","",$nhndoc[1]);
		$nhndocname = str_replace("-"," ",$nhndirtyname);
	echo '<li><a href="'.$dirtydocs.'" target="_blank">Module '.$nhndoc[0].': '.ucwords($nhndocname).'</a></li>';
	}
?>
