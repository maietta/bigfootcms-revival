<style>
#adminareas {
	position:fixed;
	top:50%;
	right:0px;
	padding:10px 0px 10px 5px;
	z-index:900000;
	display:block;
	background-color:#fff;
}
#adminnav {
	margin:5px;
	text-align:right;
}
#adminnav a {
	 background-color:rgba(51,102,51,.9);
}
</style>
<?php

global $permissions;

if ( isset($permissions->cms) ) {
    $controls = (object) $permissions->cms;
	 if ( isset($controls->content)) { 
$adminarea  = <<<"EOF"
	<div id="adminareas" class="leftround">
		<div id="adminnav">
			<a href="/projects/admin.html" class="button">Projects</a>
		</div>
	 </div>
EOF;
echo $adminarea;


	}
} else {
	echo "";
}