<?php
# Script: index.manage.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "index.manage.php";

  logaccess($formVars['uid'], $package, "Checking out the index.");

# if help has not been seen yet,
  if (show_Help($Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Table Management</title>

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

<div class="main ui-widget-content">

<h4>Database Table Management</h4>

<p>This section contains all the supporting tables that are used to populate other areas in the database.<p>

<ul>
  <li><a href="<?php print $Adminroot; ?>/device.php">Manage Server Naming Standard Device Type table</a></li>
  <li><a href="<?php print $Adminroot; ?>/product.php">Manage the Product and Service Listing table</a></li>
  <li><a href="<?php print $Adminroot; ?>/service.php">Manage Service Class Descriptions table</a></li>
  <li><a href="<?php print $Adminroot; ?>/support.php">Manage Support Contract Information table</a></li>
  <li><a href="<?php print $Usersroot; ?>/business.php">Manage Business Unit Names table</a></li>
  <li><a href="<?php print $Usersroot; ?>/department.php">Manage Department Names table</a></li>
  <li><a href="<?php print $Usersroot; ?>/organization.php">Manage Organizations table</a></li>
  <li><a href="<?php print $Usersroot; ?>/roles.php">Manage Roles table</a></li>
</ul>

<ul>
  <li><a href="<?php print $Licenseroot; ?>/license.php">Manage Software Licenses table</a></li>
  <li><a href="<?php print $Adminroot; ?>/timezones.php">Manage System Timezones table</a></li>
</ul>

<ul>
  <li><a href="<?php print $Hardwareroot; ?>/hardware.php">Manage Hardware Model Data table</a></li>
  <li><a href="<?php print $Hardwareroot; ?>/parts.php">Manage Part Descriptions table</a></li>
  <li><a href="<?php print $Hardwareroot; ?>/type.php">Manage Interface Names table</a></li>
  <li><a href="<?php print $Hardwareroot; ?>/speed.php">Manage Interface Speeds table</a></li>
  <li><a href="<?php print $Hardwareroot; ?>/vlans.php">Manage Network VLANs table</a></li>
  <li><a href="<?php print $Hardwareroot; ?>/zones.php">Manage Network Zones table</a></li>
</ul>

<ul>
  <li><a href="<?php print $Securityroot; ?>/severity.php">Manage Severity Levels</a></li>
  <li><a href="<?php print $Securityroot; ?>/family.php">Manage Families</a></li>
  <li><a href="<?php print $Securityroot; ?>/security.php">Manage Security Listing</a></li>
</ul>

</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
