<?php
# Script: index.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "index.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the articles.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Did You Know?</title>

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

<div id="main">

<div class="main-help ui-widget-content">

<p>The following articles are provided to give you some insight in how things work in the Inventory and to connect the dots on the various reports that are available.</p>

<ul>
  <li><a href="changelog.php">Changelog, how does it work?!</a></li>
  <li><a href="emailquery.php">E-Mail Query, how does it work?!</a></li>
  <li><a href="users.php">User Management, how does it work?!</a></li>
  <li><a href="tags.php">Tag Management (and Ansible), how does it work?!</a></li>
</ul>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
