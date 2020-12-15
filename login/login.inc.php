<?php
  include('settings.php');
  include('functions/dbconn.php');
  include('functions/functions.php');

  if (!isset($_SESSION['username'])) {
    session_start(); 
  }

  include($Sitepath . '/function.php');

  if (isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
  } else {
    $ref = $Siteroot;
  }

if (isset($_SESSION['username'])) {

#  session_start(); 
  include('settings.php');

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

<h1>Inventory Login</h1>

</div>

</div>

<div id="main">

<div class="error_message">Attention! You are already logged in.</div>

<h2>What to do now?</h2>

Go <a href='javascript:history.go(-1)'>back</a> to the page you were viewing before this.</li>

</div>

<div id="footer"><a href="<?php print $Siteroot; ?>">Inventory Management</a></div>

</body>
</html>
<?php
  exit();
}

// Has an error message been passed to login.php?
$error = '';
if (isset($_GET['e'])) {
  $error = $_GET['e'];
}

if ($error == 1) {
  $error = '<div class="error_message">Attention! You must be logged in to view this page.</div>';
}

// Only process if the login form has been submitted.

if (isset($_POST['login'])) {

  $username = $_POST['username']; 
  $password = $_POST['password']; 
  $ipaddr = $_SERVER['REMOTE_ADDR'];
  $checkin = date('Y-m-d H:i:s');

// Check that the user is calling the page from the login form and not accessing it directly 
// and redirect back to the login form if necessary 

  if (!isset($username) || !isset($password)) { 
    header( "Location: " . $Siteroot . "/index.php" ); 
    exit();
  } else {

// Check that the form fields are not empty, and redirect back to the login page if they are 
    if (empty($username) || empty($password)) { 
      header( "Location: " . $Siteroot . "/index.php" );
      exit();
    } else { 

// Convert the field values to simple variables 

// Add slashes to the username and md5() the password 
      $user = addslashes($_POST['username']); 
      $pass = md5($_POST['password']); 

      $q_string  = "select usr_id,usr_first,usr_last,usr_group,usr_deptname,usr_email,usr_disposition ";
      $q_string .= "from users ";
      $q_string .= "where usr_name='$user' and usr_passwd='$pass'"; 
      $q_users = mysqli_query($db, $q_string);

// Check that at least one row was returned 
      $c_users = mysqli_num_rows($q_users); 

      if ($c_users > 0) { 
        while ($a_users = mysqli_fetch_array($q_users)) { 

          $q_string  = "update users ";
          $q_string .= "set ";
          $q_string .= "usr_checkin = '" . $checkin . "',";
          $q_string .= "usr_ipaddr = '" . $ipaddr . "' ";
          $q_string .= "where usr_id = " . $a_users['usr_id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

// Start the session and register a variable 
          session_start(); 

//session_register('username'); // session_register() has been depreciated in PHP5
          $_SESSION["uid"]         = $a_users['usr_id'];
          $_SESSION["name"]        = $a_users['usr_first'] . " " . $a_users['usr_last'];
          $_SESSION["username"]    = $user;
          $_SESSION['group']       = $a_users['usr_group'];
          $_SESSION['dept']        = $a_users['usr_deptname'];
          $_SESSION['email']       = $a_users['usr_email'];
          $_SESSION['rand']        = rand(5,1000);
          $_SESSION['disposition'] = $a_users['usr_disposition'];
          logaccess($db, $_SESSION['uid'], "login.inc.php", $_SESSION['name'] . " has logged in.");

//  Successful login code will go here... 

          header( "Location: ".$ref); 
          exit();
        } 
      } else { 

// If nothing is returned by the query, unsuccessful login code goes here... 

        $error = '<div class="error_message">Incorrect username or password.</div>'; 
      } 
    }
  }
}

session_start(); 
include('settings.php');
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

<h1>Inventory Login</h1>

</div>

</div>

<div id="main">

<?php

echo $error;

?>

<h2>Login</h2>

<form method="POST" action="">

<label>Username</label><input type="text" name="username" size="20" autofocus> 
<br />
<label>Password</label><input type="password" name="password" size="20"> 
<br />
<input type="submit" value="Submit" name="login"> 

</form>

</div>


<div id="main">

<p>Log in to the Inventory or select an option below.</p>

<center>

<input title="Create a new account in the Inventory" type="submit" onclick="location.href='<?php print $Loginroot; ?>/sign_up.php';" value="Create Account" name="register">&nbsp;&nbsp;&nbsp;&nbsp;
<input title="E-Mail a new password to your registered email address" type="submit" onclick="location.href='<?php print $Loginroot; ?>/forgot.php';" value="Forgot Password" name="forgot">&nbsp;&nbsp;&nbsp;&nbsp;
<?php
  if ("inwork" == "completed") {
?>
<input title="E-Mail a link to your registered email address to auto-login to the Inventory" disabled="yes" type="submit" onclick="location.href='<?php print $Loginroot; ?>/magic.php';" value="Magic Link" name="magic">
<?php
  }
?>

</center>

</div>

<div id="footer"><a href="<?php print $Siteroot; ?>">Inventory Management</a></div>

</body>
</html>
