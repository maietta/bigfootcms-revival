<style>
#contactform li{
	list-style:none;
	margin:2px 0px 2px 0px;
}
#contactform label { 
	min-width:75px;
	text-align:right;
	valign:top;
	display:inline-block;
}
#contactform input {
	display:inline-block;
	margin-left:10px;
	background-color:#CCC;
	border:1px solid #666;
}
#contactform textarea {
	min-width: 350px;
	margin:0px 0px 2px 0px;
	border:1px solid #666;
}
#gmap {
	margin:auto;
	text-align:center;
}
#corp_contact {
	font-size:12px;
	margin:5px 0px 5px 0px;
}
</style>
<?php
$string = ''.$url.'';
$pieces = explode("/",$string);
$page = array_pop($pieces);
$admincat = array_pop($pieces);
//$dirtypage = str_replace("-", " ", $page);
$cleanpage = str_replace(".html"," ",$page);
$nameparts = split("-", $page);
$fname = $nameparts[0];
$dirtylname = $nameparts[1];
$lname = str_replace(".html", "",$dirtylname);
$membername = ''.$fname.' '.$lname.'';
//echo '<title="Contact '.$fname.' '.$lname.'">';
$query = mysql_query("SELECT *  FROM contact_dir WHERE `name` = '$name'");
	if (mysql_num_rows($query) > 0) {
		while($record = mysql_fetch_object($query)) {
		 $memberemail = $record->email;		
		 //$membername = $record->name;
//If the form is submitted
if(isset($_POST['submit'])) {

	//Check to make sure that the name field is not empty
	if(trim($_POST['contactname']) == '') {
		$hasError = true;
	} else {
		$name = trim($_POST['contactname']);
	}

	//Check to make sure that the subject field is not empty
	if(trim($_POST['subject']) == '') {
		$hasError = true;
	} else {
		$subject = trim($_POST['subject']);
	}

	//Check to make sure sure that a valid email address is submitted
	if(trim($_POST['email']) == '')  {
		$hasError = true;
	} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
		$hasError = true;
	} else {
		$email = trim($_POST['email']);
	}

	//Check to make sure comments were entered
	if(trim($_POST['message']) == '') {
		$hasError = true;
	} else {
		$comments = (function_exists('stripslashes')) ? stripslashes(trim($_POST['message'])) : trim($_POST['message']);		
	}

	//If there is no error, send the email
	if(!isset($hasError)) {
		$emailTo = "connect@bicoastalmedia.com";  //Put your own email address here
		$body = "Name: $name \n\nEmail: $email \n\nSubject: $subject \n\nComments:\n $comments";
		$headers = 'From: '.$email.'' . "\r\n" . 'Reply-To: ' . $email;

		mail($emailTo, $subject, $body, $headers);
		$emailSent = true;
	}
}
	}
	}
?>

<script src="/assets/javascripts/jquery.validate.js" type="text/javascript"></script>
<script method="sticky" type="text/javascript">
$(document).ready(function(){
	$("#contactform").validate();
});
</script>


	<?php if(isset($hasError)) { //If errors are found ?>
		<p class="error">Please check if you've filled all the fields with valid information. Thank you.</p>
	<?php } ?>

	<?php if(isset($emailSent) && $emailSent == true) { //If email is sent ?>
		<p><strong>Email Successfully Sent!</strong></p>
		<p>Thank you <strong><?php echo $name;?></strong> for using my contact form! Your email was successfully sent and I will be in touch with you soon.</p>
	<?php } ?>
	<div id="contactform">
	<form method="post" action="<?php echo $vpath; ?>" id="contactform">
    <fieldset style="text-align: left;">
	<legend>Send <?php echo $membername ?> an email</legend>
	<li><label for="name">Name:</label><input type="text" name="contactname" id="contactname" value="" class="required" /></li>
	<li><label for="email">Email:</label><input type="text" name="email" id="email" value="" class="required" /></li>
	<li><label for="subject">Subject:</label><input name="subject" id="subject" class="required" /></li>
	<li><label for="message">Message:</label></li>
		<textarea rows="5" name="message" id="message" class="required"></textarea>
	<li><input type="submit" value="Send Message" name="submit" /></li>
        </fieldset>
		</form>
        </div>
        <?php //echo SITELEVEL;?>