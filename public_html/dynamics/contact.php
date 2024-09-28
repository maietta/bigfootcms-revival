
<?php
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
		$emailTo = "";  //Put your own email address here
		$body = "Name: $name \n\nEmail: $email \n\nSubject: $subject \n\nComments:\n $comments";
		$headers = 'From: '.HOSTNAME.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;

		mail($emailTo, $subject, $body, $headers);
		$emailSent = true;
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
		<p>Thank you <strong><?php echo $name;?></strong> for using my contact form! Your email was successfully sent and we will be in touch with you soon.</p>
	<?php } ?>
	<div id="contactform">
	<form method="post" action="<?php echo $vpath; ?>" id="contactform">
    <fieldset style="text-align: left;" class="greenborder round">
	<legend class="round greenborder">Send us an email</legend>
    <div class="row">
	<label for="name">Name:</label><input type="text" name="contactname" id="contactname" value="" class="required" />
    </div>
    <div class="row">
	<label for="email">Email:</label><input type="text" name="email" id="email" value="" class="required" />
    </div>
    <div class="row">
	<label for="subject">Subject:</label><input type="text" name="subject" id="subject" value="" class="required" />
    </div>
    <div class="row">
	<label for="message">Message:</label>
	<textarea rows="5" name="message" id="message" class="required greenborder"></textarea>
    </div>
	<input class="small button radius" type="submit" value="Send Message" name="submit" />
        </fieldset>
		</form>
        </div>
	