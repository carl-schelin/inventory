<?php
# Script: assume.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Admin);

  $package = "assume.php";

  logaccess($db, $formVars['uid'], $package, "Assuming a new identity.");

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Change User</title>

<link rel="stylesheet" href="<?php print $Loginroot; ?>/stylesheet.css" />

</head>
<body>

<div id="header">
    
<div id="title">

<h1>Change User</h1>

</div>

</div>

<div id="main">

<?php

if (!$_GET['uid'] && !isset($_POST['change_user'])) {

?>

<h2>Change User</h2>

<form action="" method="post">

<label>Enter Username or E-mail Address</label>

<input type="text" id="username" name="username" alt="Search Criteria" autocomplete="off" />
<input type="submit" class="suggest_button" value="Change User" name="change_user" />

</form>

<?php

}

if (isset($_POST['change_user'])) {

  $search_q = $_POST['username'];

  $q_string  = "select usr_id,usr_name,usr_email ";
  $q_string .= "from inv_users ";
  $q_string .= "where usr_id != 1 and usr_disabled = 0 and (usr_name = '" . $search_q . "' or usr_email = '" . $search_q . "')";
  $q_inv_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_users) == '1') {
    $a_inv_users = mysqli_fetch_array($q_inv_users);

    $q_string  = "select usr_id,usr_level,usr_disabled,usr_name,usr_first,usr_last,";
    $q_string .= "usr_group,usr_reset,theme_name ";
    $q_string .= "from inv_users ";
    $q_string .= "left join inv_themes on inv_themes.theme_id = inv_users.usr_theme ";
    $q_string .= "where usr_name = '" . $a_inv_users['usr_name'] . "' ";
    $q_inv_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_inv_users = mysqli_fetch_array($q_inv_users);

    $_SESSION['uid']         = $a_inv_users['usr_id'];
    $_SESSION['username']    = $a_inv_users['usr_name'];
    $_SESSION['name']        = $a_inv_users['usr_first'] . " " . $a_inv_users['usr_last'];
    $_SESSION['group']       = $a_inv_users['usr_group'];
    $_SESSION['theme']       = $a_inv_users['theme_name'];

    print "<p>You have assumed the identity of " . $_SESSION['username'] . ".</p>";

    logaccess($db, $formVars['uid'], $package, "Assumed identity.");
  }
}

?>

</div>

<div id="footer">

<a href="<?php print $Siteroot; ?>">Inventory Management</a>

</div>

</body>
</html>
