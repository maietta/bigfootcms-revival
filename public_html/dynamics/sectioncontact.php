<?php
$string = ''.$url.'';
$s = explode("/",$string);
$pieces = explode("/",$string);
$page = array_pop($pieces);
$locationarea = array_pop($pieces);
$dirtylocation = str_replace("-", " ", $page);
$location = str_replace(".html"," ",$dirtylocation);

//echo 'we have no contacts yet associated to the section '.$locationarea.' in this website';

?>