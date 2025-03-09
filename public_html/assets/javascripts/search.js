function lookup(inputString) {
	if(inputString.length == 0) { $('#suggestions').hide(); } else { $.post("/dynamics/search.php", { queryString: ""+inputString+""}, function(data){ if(data.length >0) {$('#suggestions').show(); $('#suggested').html(data);}}); }
}
function fill(thisValue) {
	$('#inputString').val(thisValue);
	$('#suggestions').hide();
}