<form id="start" action="/cgi-bin/register" method="post">
	<h1></h1>
    	<div id="messege"></div>
	<p> 
		<label for="username">Desired Username</label> 
		<input id="username" name="username" type="text" value=""/> @<?php echo str_replace("www.", "", HOSTNAME); ?>
	</p> 
	<p> 
		<label for="email">Email address</label> 
		<input id="email" name="email" type="text" value=""/> 
	</p>
    <p> 
		<input type="submit" value="Register" /> or <a href="/cgi-bin/login">Sign in</a> 
	</p>
</form> 