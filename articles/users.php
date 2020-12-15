<?php
# Script: users.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "users.php";

  logaccess($db, $formVars['uid'], $package, "How does Changelog work?");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>User Management, how does it work?</title>

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

<div class="main-help ui-widget-content">

<p><strong><u>User Management</u></strong></p>

<p>This page describes the process and standards used to manage users on Unix servers. It describes the documentation and reporting process plus any automation in place.</p>


<p><strong><u>System Accounts</u></strong></p>

<p>There are basically three types of accounts on Unix servers.</p>

<ul>
  <li><strong>System</strong> - These are your standard system and application accounts such as root, nobody, ntp, apache, docker, etc.</li>
  <li><strong>Service Accounts</strong> - These are created and used for applications. They should have a group owner such as Web Applications, and a valid user or group email</li>
  <li><strong>User Accounts</strong> - Standard login accounts for users.</li>
</ul>

<p>User accounts have a standardized GECOS field which consists of the user's full name, a comma, and the user's valid email address.</p>


<p><strong><u>Email Listing</u></strong></p>

<p>Several years back, by request, a script was created which extracts all valid emails from Exchange in order to provide a list of employees. This list is retrieved and 
copied to all servers for a validation check. Any account where the email address doesn't exist, or the listed email address isn't found in the generated email listing is 
reported to the Unix team for review.</p>


<p><strong><u>Exceptions</u></strong></p>

<p>Since System and Service Accounts aren't User Accounts, an exception file was created. This file lists any accounts that can be ignored since they aren't actual users.</p>


<p><strong><u>Validating Email</u></strong></p>

<p>With the transition to West's email system, email accounts started switching from intrado.com to west.com or even regmail.west.com. The new email accounts were part of the 
email extraction and folks with changed email addresses were being reported as departing the company. A new data file was created which would updated user's GECOS fields with 
the corrected email address.</p>


<p><strong><u>Locking Users</u></strong></p>

<p>With consistent information in the user's GECOS field, we can now automatically lock users on systems after they leave. A data file that has the user's login and their 
email address for verification is used to lock any user that has left.</p>


<p><strong><u>Data File Management</u></strong></p>

<p>The files were originally manually updated. Admins needed to know where the files were in order to update them. Now updating the files are done through a module in the 
inventory and scripts automatically recreate the files each night.</p>


<p><strong><u>Finally</u></strong></p>

<p>This page was created to try and provide an overall view of the User Management process and available reports. Let me know if you have questions or need clarification.</p>


</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
