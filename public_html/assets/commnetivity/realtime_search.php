<?php

echo <<<EOF
<style>
#search {
	float: right;
	text-align: right;
	margin-right: 11px;
	position:relative;
	z-index:auto;
}
#suggestions {
	position:absolute;
	left:auto;
	right:auto;
	z-index:999;
}
#suggested {
	text-align:left;
	background-color:#fff;
	border-collapse:collapse;
	border-left:1px solid #000;
	border-bottom:0px solid #000;
	border-right:1px solid #000;
	position:relative;
	z-index:999;
}
.searchbox {
	width:200px;
	height:14px;
	margin-bottom:2px;
	border-top:1px solid #993333;
	border-left:1px solid #993333;
	border-bottom:1px solid #993366;
	border-right:1px solid #993366;
	position:relative;
/*	z-index:999; */
}
.suggestionList {
	color:#000;
	width:auto;
	margin-left:2px;
	margin-right:2px;
	position:relative;
	list-style:none;
	z-index:999;
	cursor:default;
	font-size:13px;
}
.suggestionList:hover {
    background-color:#0033CC;asdf
    color:#fff;
}
.searchsubmit {
	height:22px;
	width:60px;
	position:relative;
}
.searchsubmit:hover {
	color:#993366;

}
</style>
<script>
function goto(inputString) {
    $(window.location).attr('href', inputString);
}

function lookup(inputString) {
    if(inputString.length == 0) {
        $('#suggestions').hide();
    } else {
        $.post("/rpc/search", { queryString: ""+inputString+""}, function(data) {
        if(data.length >0) {
            $('#suggestions').show();
                $('#suggested').html(data);
            }
        });
    }
}

function fill(thisValue) {
    $('#inputString').val(thisValue);
    $('#suggestions').hide();
}
</script>
<form name="smartSearchForm" method="get" value="" autocomplete="off"> 
		<input size="30" id="inputString" class="searchbox" onkeyup="lookup(this.value);" type="text"> 
		<input name="search" type="submit" class="searchsubmit" id="searchSubmit" value="Search"> 
		<div id="suggestions"><div id="suggested"></div></div> 
	</form>

EOF;
	
?>