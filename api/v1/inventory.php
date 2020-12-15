<?php
# Script: inventory.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . "/guest.php");

  $package = "inventory.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JSON API Index: Inventory</title>

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

<p><strong>GET /api/inventory.php</strong></p>

<pre style="text-align: left">curl -s -k https://incojs01.scc911.com/inventory/api/inventory.php?group=[groupname] | python -mjson.tool</pre>

<p><strong>Parameters</strong></p>

<ul>
  <li>[default] - Without a parameter, returns all servers in the inventory.</li>
  <li>group - Pass the name or partial name of a group to generate a listing for just the passed group.</li>
</ul>


<p><strong>Output</strong></p>

<pre style="text-align: left">
    "alde0euasnesa11": {
        "inventory_appowner": "Mobility Sys Admin",
        "inventory_satellite_uuid": "ffce24e7-e99d-49ff-92a9-24ffe83d3d39",
        "inventory_serverid": "11202",
        "inventory_servername": "alde0euasnesa11",
        "inventory_sysowner": "UNIX System Administration",
        "inventory_uuid": "42190C4D-2A58-9072-B57A-27DA0698857E"
    },
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
