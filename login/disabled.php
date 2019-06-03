<?php
  session_start(); 
  include('settings.php');
  include('functions/functions.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Inventory Management</title>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<META NAME="robots" content="index,follow">

<link rel="stylesheet" href="<?php print $Loginroot; ?>/stylesheet.css" />

</head>
<body>

<div id="header">
    
<div id="title">

<h1>Account Disabled</h1>

</div>

</div>

<div id="main">

<?php

$error    = '';
$name     = ''; 
$email    = ''; 
$subject  = ''; 
$comments = ''; 

if (isset($_POST['contactus'])) {

  $name     = $_POST['name'];
  $email    = $_POST['email'];
  $subject  = $_POST['subject'];
  $comments = $_POST['comments'];

  if (trim($name) == '') {
    $error = '<div class="error_message">Attention! You must enter your name.</div>';
  } else if (trim($email) == '') {
    $error = '<div class="error_message">Attention! Please enter a valid email address.</div>';
  } else if (!isEmail($email)) {
    $error = '<div class="error_message">Attention! You have entered an invalid e-mail address, try again.</div>';
  }

  if (trim($subject) == '') {
    $error = '<div class="error_message">Attention! Please enter a subject.</div>';
  } else if (trim($comments) == '') {
    $error = '<div class="error_message">Attention! Please enter your message.</div>';
  }

  if ($error == '') {

    if (get_magic_quotes_gpc()) {
      $comments = stripslashes($comments);
    }

    $address = $EmergencyContact;

    $e_subject = 'You\'ve been contacted by ' . $name . '.';

    $e_body = "You have been contacted by $name with regards to $subject, their additional message is as follows.\r\n\n";
    $e_content = "\"$comments\"\r\n\n";
    $e_reply = "You can contact $name via email, $email";

    $msg = $e_body . $e_content . $e_reply;

    mail($address, $e_subject, $msg, "From: $email\r\nReply-To: $email\r\nReturn-Path: $email\r\n");

    echo "<div id='succsess_page'>";
    echo "<h1>Email Sent Successfully.</h1>";
    echo "<p>Thank you <strong>$name</strong>, your message has been submitted.</p>";
    echo "</div>";
  }
}

if (!isset($_POST['contactus']) || $error != '') { // Do not edit.

  echo $error;

  echo "<h1 style='margin: 0; padding: 0; font-size: 20px;'>Oops, Access Denied</h1>\n\n";
  echo "<h3>Sorry, your username or user group has been disabled!</h3>\n\n";
  echo "<p>We have detected that your username or user group has been disabled, you cannot view internal pages.</p><br><br>\n";

?>            
<fieldset>

<legend>Fill out this form if you feel this is in error.</legend>

<form  method="post" action="">

<label for=name accesskey=U>Your Name <span class="required">*</span></label>
<input name="name" type="text" id="name" size="20" style="width: 430px;" value="<?php print $name; ?>" />

<br />
<label for=email accesskey=E>Email <span class="required">*</span></label>
<input name="email" type="text" id="email" size="20" style="width: 430px;" value="<?php print $email; ?>" />

<br />
<label for=subject accesskey=S>Subject</label>
<select name="subject" type="text" id="subject">
<option selected value="User / Group Disabled">Disabled Message</option>
<option value="a Bug fix">Report a bug</option>
</select>

<br />
<label for=comments accesskey=C>Your comments <span class="required">*</span></label>
<textarea name="comments" cols="20" rows="3" style="width: 430px;" id="comments"><?php print $comments; ?></textarea>
            
<input name="contactus" type="submit" class="submit" id="contactus" value="Submit" />

</form>
            
</fieldset>

<?php
  }
?>
</div>

<?php include('footer.php'); ?>

</body>
</html>
