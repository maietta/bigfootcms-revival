<?php
 function ellipsis($text, $max=100, $append='&hellip;')
{
    if (strlen($text) <= $max) return $text;
    $out = substr($text,0,$max);
    if (strpos($text,' ') === FALSE) return $out.$append;
    return preg_replace('/\w+$/','',$out).$append;
}
?>
<?php
echo '<style>
		a.sitemap {
		display:block;
		}
		</style>';

$query  = "SELECT * FROM `commnetivity_content` WHERE parent_id='/dart/index.html' AND virtual_path != '/dart/blog.html' ";
 $result = mysql_query($query);
  if (mysql_num_rows($result) > 0)
   {
    while($row = mysql_fetch_array($result))
   {
    $page_title = stripslashes($row['page_title']);
	$page_id = $row['id'];
    $vpath = $row['virtual_path'];
	$texttrim=695;
	$text = base64_decode($row['encoded_content']);
	$blogpost = substr($text, 0, $texttrim);
				
     //echo'<a href="'.$vpath.'" class="sitemap">'.stripslashes($page_title).'</a>';
	 echo  ''.stripslashes($blogpost).'....<a href="'.$vpath.'">Read More</a><hr />';
} 
    }else echo '<p>We have no posts to list at this time please come back soon.</p>'; 
	
    
 ?>