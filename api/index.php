<?php
# Script: index.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . "/guest.php");

  $package = "index.php";

  logaccess($formVars['uid'], $package, "Accessing the script.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JSON API Index</title>

<?php include($Sitepath . "/head.php"); ?>

<script language="javascript">

$(document).ready( function() {
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main ui-widget-content">

<p><strong>Resources</strong></p>
<ul>
  <li><a href="v1/inventory.php">GET /api/inventory.php</a> Generates an Inventory listing of servers, owners, and UUIDs</li>
  <li><a href="v1/locations.php">GET /api/locations.php</a> Generates a listing of all inventory locations</li>
  <li><a href="v1/search.php">GET /api/search.php</a> Search for a system based on IP address, MAC address, Asset Tag, or Serial Number/Dell Service Tag</li>
  <li><a href="v1/server.php">GET /api/server.php</a> Generates a Server listing</li>
  <li><a href="v1/tags.php">GET /api/tag.php</a> Generates a server listing based on passed tags</li>
  <li><a href="v1/west.php">GET /api/west.php</a> West Report</a></li>
  <li><a href="v1/workflow.php">GET /api/workflow.php</a> Workflow Listing</a></li>
</ul>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
