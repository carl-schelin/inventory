<?php
# Script: server.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . "/guest.php");

  $package = "server.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JSON API Index: Server</title>

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

<p><strong>GET /api/server.php</strong></p>

<pre style="text-align: left">curl -s -k <?php print $Siteroot; ?>/api/server.php?server=[servername]&location=[yes|NO]&service=[yes|NO]&interfaces=[yes|NO]&hardware=[yes|NO] | python -mjson.tool</pre>

<p><strong>Parameters</strong></p>

<ul>
  <li>[default] - Without a parameter, returns all servers in the inventory.</li>
  <li>server - Pass the name of the server to generate a listing.</li>
  <li>location - Generate location information.</li>
  <li>service - Generate service class information.</li>
  <li>interfaces - Generate interface information.</li>
  <li>hardware - Generate hardware information.</li>
</ul>

<p><strong>Output</strong></p>


<pre style="text-align: left;">
$ curl -s -k <?php print $Siteroot; ?>/api/server.php?server=lnmt1cuomtool11 | python -mjson.tool
{
    "lnmt1cuomtool11": {
        "inventory_appadmins": "UNIX System Administration",
        "inventory_documentation": "",
        "inventory_function": "Tool Server",
        "inventory_hardware": "Virtual Machine",
        "inventory_location": "LMT01",
        "inventory_maintenance_window": "Unassigned",
        "inventory_name": "lnmt1cuomtool11",
        "inventory_network": "Corporate Zone",
        "inventory_product": "Infrastructure",
        "inventory_project": "Unknown",
        "inventory_satellite_uuid": "",
        "inventory_service_class": "Business Support Services",
        "inventory_sysadmins": "UNIX System Administration",
        "inventory_timezone": "UTC",
        "inventory_uuid": "564dbde2-50bd-d23e-7a3a-b4a048a80538"
    }
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
