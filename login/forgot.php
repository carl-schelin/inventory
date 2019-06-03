<?php
# Script: forgot.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  session_start(); 
  include('settings.php');
  include($Loginpath . '/functions/dbconn.php');
  include($Loginpath . '/functions/functions.php');

function generatePassword ($length = 8) {

  $password = "";
  $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
  $maxlength = strlen($possible);

  if ($length > $maxlength) {
    $length = $maxlength;
  }

  $i = 0;
  while ($i < $length) {
    $char = substr($possible, mt_rand(0, $maxlength-1), 1);

    if (!strstr($password, $char)) {
      $password .= $char;
      $i++;
    }

  }
  return $password;
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Inventory Management</title>

<link rel="stylesheet" href="<?php print $Loginroot; ?>/stylesheet.css" />

</head>
<body>

<div id="header">
    
<div id="title">

<h1>Inventory Login</h1>

</div>

</div>

<div id="main">

<?php

if (!$_GET['uid'] && !isset($_POST['do_edit']) && !isset($_POST['mail_user'])) {

?>

<h2>Reset Password</h2>

<form action="" method="post">

<label>Enter Username or E-mail Address</label>

<input type="text" id="username" name="username" alt="Search Criteria" autocomplete="off" />
<input type="submit" class="suggest_button" value="E-Mail Password" name="mail_user" />

</form>

<?php

}

if (isset($_POST['mail_user'])) {

  $search_q = $_POST['username'];

  $q_string  = "select usr_id,usr_name,usr_email ";
  $q_string .= "from users ";
  $q_string .= "where usr_id != 1 and usr_disabled = 0 and (usr_name = '" . $search_q . "' or usr_email = '" . $search_q . "')";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_users) == '1') {
    $a_users = mysql_fetch_array($q_users);

    $user_email = $a_users['usr_email'];

    if (isEmail($user_email)) {

      $newpassword = generatePassword(8);

      $q_string  = "update ";
      $q_string .= "users ";
      $q_string .= "set usr_reset=1,usr_passwd = MD5('$newpassword') ";
      $q_string .= "where usr_id = " . $a_users['usr_id'];
      $q_newpw = mysql_query($q_string) or die(mysql_error());

      $headers  = "From: Inventory Password Reset <root@" . $Sitehttp . ">\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

      $body  = "<html>";

      $body .= "<p>A password reset request has been received. A new password has been created and is contained within this e-mail. ";
      $body .= "Please log in to the <a href=\"" . $Siteroot . "/index.php\">Inventory</a> site and reset your password.</p>\n\n";

      $body .= "<p>Account: " . $a_users['usr_name'] . "\n";
      $body .= "<br>Password: \"" . $newpassword . "\"\n\n";

      $body .= "<p>If you did not request this, after logging in and changing your password, please contact one of the site admins.</p>";

      $body .= "<p><strong>NOTE!</strong> If you try to copy/paste the password, you may get an extra character. If you are not able ";
      $body .= "to get in with the new password, try removing the last character of the pasted password and try again.</p>\n";

      $body .= "</html>";

      mail ($user_email, "Account Information", $body, $headers);

      print "<p>E-Mail sent. If you do not receive an e-mail within a short period of time, please check your spam folder. If it ";
      print "still doesn't show up, you may not have supplied a valid e-mail address when signing up. Please contact one of the site ";
      print "admins for assistance.</p>";

      print "<p>Click <a href=\"" . $Siteroot . "/index.php\">here</a> to return to the Inventory site.</p>";

    } else {
      print "<p>The user account you identified does not have an e-mail address associated with it. Please contact one of the site ";
      print "admins for assistance.</p><p>Click <a href=\"" . $Siteroot . "/index.php\">here</a> to return to the login screen.</p>";
    }

  } else {
    print "<p>The password request screen requires a unique user name or e-mail address before it will send out a new password. ";
    print "Please ensure you've entered a unique name or e-mail in the search box before clicking the <strong>E-Mail Password</strong> ";
    print "button.</p>";

    print "<p>Click <a href=\"" . $Loginroot . "/forgot.php\">here</a> to return to the forgotten password screen.</p>";
  }
}

?>

</div>

<div id="footer">

<a href="<?php print $Siteroot; ?>">Inventory Management</a>

</div>

</body>
</html>
