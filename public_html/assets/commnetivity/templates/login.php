<form id="start" action="/login" method="post">
	<h1><?php echo "$messege"; ?></h1>
    	<div id="messege"></div>
	<p> 
		<label for="username">Username</label> 
		<input id="username" name="username" type="text" />
	</p> 
	<p> 
		<label for="password">Password</label> 
		<input id="password" name="password" type="password" /> 
	</p>
    <p> 
		<input type="submit" value="Login" /> or <a href="/register">register</a>. 
	</p>
</form> 