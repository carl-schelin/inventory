<?php
# Script: pwreset.inc.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

# If the user is not logged in, send them to the login page
  if (!isset($_SESSION['username'])) {
    header( "Location: " . $Siteroot . "/index.php" );
    exit();
  }

# Where did they come from?
  if (isset($_SERVER['HTTP_REFERER'])) {
    $formVars['referer'] = $_SERVER['HTTP_REFERER'];
  } else {
    $formVars['referer'] = $Siteroot;
  }

# Has an error message been passed to login.php?
  if (isset($_GET['e'])) {
    $formVars['error'] = $_GET['e'];
  } else {
    $formVars['error'] = '';
  }

  if ($formVars['error'] == 1) {
    $formVars['error'] = '<div class="error_message">Attention! You must be logged in to view this page.</div>';
  }

# Only process if the password reset form has been submitted.

  if (isset($_POST['login'])) {
    if (isset($_POST['usr_name'])) {
      $formVars['usr_name']   = clean($_POST['usr_name'],    120);
    }
    if (isset($_POST['usr_passwd'])) {
      $formVars['usr_passwd'] = clean($_POST['usr_passwd'],   32);
    }
    if (isset($_POST['new_passwd'])) {
      $formVars['new_passwd'] = clean($_POST['new_passwd'],   32); 
    }
    if (isset($_POST['vfy_passwd'])) {
      $formVars['vfy_passwd'] = clean($_POST['vfy_passwd'],   32); 
    }

# Check that the user is calling the page from the password reset form and not accessing it directly and redirect back to the password reset form if necessary 
    if (!isset($formVars['usr_name']) || !isset($formVars['usr_passwd']) || !isset($formVars['new_passwd']) || !isset($formVars['vfy_passwd'])) { 
      header( "Location: " . $Siteroot . "/index.php" ); 
      exit();
    } else {

# Check that the form fields are not empty, and redirect back to the reset password page if they are 
      if (empty($formVars['usr_name']) || empty($formVars['usr_passwd']) || empty($formVars['new_passwd']) || empty($formVars['vfy_passwd'])) { 
        $formVars['error'] = '<div class="error_message">All fields must be filled in.</div>'; 
      } else { 

# Check that the old password isn't the same as the new password
        if ($formVars['usr_passwd'] == $formVars['new_passwd']) { 
          $formVars['error'] = '<div class="error_message">You cannot reuse passwords.</div>'; 
        } else { 

# Check that the new passwords match
          if ($formVars['new_passwd'] != $formVars['vfy_passwd']) { 
            $formVars['error'] = '<div class="error_message">New Passwords must match.</div>'; 
          } else { 

# Convert the field values to simple variables 
# Add slashes to the username and md5() the password 
          $usr_name    = addslashes($formVars['usr_name']); 
          $usr_passwd  = md5($formVars['usr_passwd']); 
          $new_passwd  = md5($formVars['new_passwd']); 

          $q_string  = "select usr_id ";
          $q_string .= "from users ";
          $q_string .= "where usr_name = '" . $usr_name . "' and usr_passwd = '" . $usr_passwd . "' ";
          $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# Check that at least one row was returned 
          if (mysqli_num_rows($q_users) > 0) { 

            $q_string  = "update ";
            $q_string .= "users ";
            $q_string .= "set usr_passwd = '" . $new_passwd . "',usr_reset = 0 ";
            $q_string .= "where usr_name = '" . $usr_name . "' and usr_passwd = '" . $usr_passwd . "'";
            $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

            logaccess($db, $_SESSION['uid'], "pwreset.inc.php", $_SESSION['username'] . " has reset their password.");

//  Successful login code will go here... 

            header( "Location: " . $formVars['referer'] ); 
            exit();
          } else { 

// If nothing is returned by the query, unsuccessful login code goes here... 

            $formVars['error'] = '<div class="error_message">Incorrect username or password.</div>'; 
          } 
        }
      }
    }
  }
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Password Reset</title>

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

print $formVars['error'];

?>

<h2>Password Reset Required</h2>

<form method="POST" action=""> 

<label>Username</label><input type="text" name="usr_name" size="20" autofocus> 
<br />
<label>Old Password</label><input type="password" name="usr_passwd" size="20"> 
<br />
<label>New Password</label><input type="password" name="new_passwd" size="20"> 
<br />
<label>Re-enter New Password</label><input type="password" name="vfy_passwd" size="20"> 
<br />
<input type="submit" value="Submit" name="login"> 

</form> 

</div>


<div id="footer">

<a href="<?php print $Siteroot; ?>">Inventory Management</a>

</div>

</body>
</html>
