<?php
# Script: west.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . "/guest.php");

  $package = "west.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JSON API Index: West</title>

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

<p><strong>GET /api/west.php</strong></p>

<pre style="text-align: left">curl -s -k https://incojs01.scc911.com/inventory/api/west.php?server=[servername] | python -mjson.tool</pre>

<p><strong>Description</strong></p>

<p>This was created for a specific request by West to extract patching data and provide it to the central database group for insertion and reporting.</p>

<p><strong>Parameters</strong></p>

<ul>
  <li>[default] - Without a parameter, returns all servers in the inventory.</li>
  <li>server - Pass the name of the server to generate a listing.</li>
</ul>

<p><strong>Output</strong></p>

<pre style="text-align: left;">
{
    "alde0euasnesa11": {
        "inventory_domain": "",
        "inventory_environment": "Pre-Production",
        "inventory_fqdn": "scc911.com",
        "inventory_function": "Standalone Server",
        "inventory_location": "DEN03",
        "inventory_name": "alde0euasnesa11",
        "inventory_network": {
            "interface_0": {
                "interface_address": "10.39.22.7",
                "interface_ethernet": "00:50:56:99:3c:c7",
                "interface_scanned": "No"
            },
            "interface_1": {
                "interface_address": "10.39.19.7",
                "interface_ethernet": "00:50:56:99:ba:e7",
                "interface_scanned": "No"
            },
            "interface_2": {
                "interface_address": "127.0.0.1",
                "interface_ethernet": "",
                "interface_scanned": "No"
            }
        },
        "inventory_operating_system": "Red Hat Enterprise Linux Server release 7.2 (Maipo)",
        "inventory_patched": "2017-04-27",
        "inventory_satellite_uuid": "ffce24e7-e99d-49ff-92a9-24ffe83d3d39"
    }
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
