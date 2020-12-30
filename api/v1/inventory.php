<?php
# Script: inventory.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

<pre style="text-align: left">curl -s -k <?php print $Siteroot; ?>/api/inventory.php?group=[groupname] | python -mjson.tool</pre>

<p><strong>Parameters</strong></p>

<ul>
  <li>[default] - Without a parameter, returns all servers in the inventory.</li>
  <li>group - Pass the name or partial name of a group to generate a listing for just the passed group.</li>
</ul>


<p><strong>Output</strong></p>

<pre style="text-align: left">
$ curl -s -k <?php print $Siteroot; ?>/api/inventory.php?group=unix | python -mjson.tool
{
    "bldr0cuomaws1": {
        "inventory_appowner": "UNIX System Administration",
        "inventory_satellite_uuid": "",
        "inventory_serverid": "12939",
        "inventory_servername": "bldr0cuomaws1",
        "inventory_sysowner": "UNIX System Administration",
        "inventory_uuid": "4233632f-d70b-ec25-c7fb-2c288bd8fc70"
    },
    "bldr0cuomdev1": {
        "inventory_appowner": "DBA Admin",
        "inventory_satellite_uuid": "",
        "inventory_serverid": "12849",
        "inventory_servername": "bldr0cuomdev1",
        "inventory_sysowner": "UNIX System Administration",
        "inventory_uuid": "564d0d58-dd09-c932-837f-70e5e80362ba"
    },
...
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
