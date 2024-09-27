<script type="text/javascript"> 
$(document).ready(function () {
$("#form input[username]").change(function () {
	var name = $(this).attr("name");
	var value = $(this).attr("value");
	$.ajax({
		url: "/rpc/validate",
		global: false,
		type: "POST",
		data: ({name : name, value : value}),
		dataType: "html",
		success: function(msg){
			doHandler(msg);
	    }
	});
	$('<div id="processInput">Processing...</div>')
		.insertAfter( $(this) )
		.fadeIn('slow')
		.animate({opacity: 1.0}, 1000)
		.fadeOut('slow', function() {
			$(this).remove();
	});
	function doHandler(msg) {
		$(msg)
			.insertAfter( $('body') )
			.fadeIn('slow')
			.animate({opacity: 1.0}, 2500)
			.fadeOut('slow', function(){});	
	}
	});
});
</script>

<form class="register" action="/cgi-bin/register" method="post">
  <h1>Register</h1>
  <div id="messege"></div>
  <p>
    <label for="username">Desired Username</label>
    <label for="email">Email address</label>
  </p>
  <p>
    <input id="username" name="username" type="text" value=""> 
    <input id="email" name="email" type="text" value="">
    <input name="action" type="hidden" value="register">
    <input type="submit" value="Register">
  </p>
</form>
