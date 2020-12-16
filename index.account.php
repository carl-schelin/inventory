<?php
# Script: index.account.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "index.account.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the index.");

# if help has not been seen yet,
  if (show_Help($db, $Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Account Management</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<?php
  if (isset($_SESSION['username'])) {
?>
<div class="main ui-widget-content">

<ul>
  <li><a href="<?php print $Usersroot;?>/profile.php">Manage Your Account Profile</a></li>
  <li><a href="<?php print $Bugroot; ?>/bugs.php">Report a Bug</a></li>
  <li><a href="<?php print $Featureroot; ?>/features.php">Request An Enhancement</a></li>
  <li><a href="<?php print $FAQroot; ?>/whatsnew.php">What's New With Inventory 3.0?</a></li>
  <li><a href="<?php print $Loginroot; ?>/logout.php">Logout (<?php print $_SESSION['username']; ?>)</a></li>
</ul>

</div>

<?php
    if (check_userlevel($db, $AL_Admin)) {
?>
<div class="main ui-widget-content">

<ul>
  <li><a href="<?php print $Usersroot; ?>/users.php">User Management</a></li>
  <li><a href="<?php print $Usersroot; ?>/groups.php">Group Management</a></li>
  <li><a href="<?php print $Usersroot; ?>/levels.php">Access Level Management</a></li>
  <li><a href="<?php print $Loginroot; ?>/assume.php">Change Credentials</a> - Change your login information to become another user.</li>
  <li><a href="<?php print $Adminroot; ?>/rsdpdup.php">Review and Remove Duplicate RSDP Records</a></li>
  <li><a href="<?php print $Reportroot;  ?>/logs.php">View Last 7 Days of Logs</a></li>
  <li><a href="<?php print $Reportroot;  ?>/lastlogin.php">View User Logins</a></li>
</ul>

</div>

<div class="main ui-widget-content">

<ul>
  <li><a href="<?php print $FAQroot; ?>/infoexchange.php">InfoExchange</a></li>
  <li><a href="<?php print $Siteroot;  ?>/mailusers.php">Mail All Users</a></li>
  <li><a href="<?php print $Siteroot;  ?>/xcache/admin">XCache Statistics</a></li>
</ul>

</div>

<?php
  }
} else {
?>
<div class="main ui-widget-content">

<ul>
  <li><a href="<?php print $Loginroot; ?>/login.php">Login</a></li>
</ul>

</div>

<?php
}
?>
</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
