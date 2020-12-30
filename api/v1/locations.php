<?php
# Script: locations.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . "/guest.php");

  $package = "locations.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JSON API Index: Locations</title>

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

<p><strong>GET /api/locations.php</strong></p>

<pre style="text-align: left">curl -s -k <?php print $Siteroot; ?>/api/locations.php?site=[site name]&type=[site type] | python -mjson.tool</pre>

<p><strong>Parameters</strong></p>

<ul>
  <li>[default] - Returns all locations in the inventory.</li>
  <li>site - Wildcard search on the location name.</li>
  <li>type - Location type; Data Center, PSAP, NOC, Customer. For 'Data Center', use '%20' in place of the space.</li>
</ul>

<p><strong>Output</strong></p>

<pre style="text-align: left">
$ curl -s -k <?php print $Siteroot; ?>/api/locations.php?site=nederland | python -mjson.tool
{
    "678": {
        "location_address1": "9999 Ridge Road",
        "location_address2": "",
        "location_city": "Nederland",
        "location_clli": "NDLD",
        "location_country": "United States",
        "location_designation": "NED01",
        "location_environment": "",
        "location_instance": "1",
        "location_name": "Nederland",
        "location_state": "Colorado",
        "location_suite": "",
        "location_type": "Data Center",
        "location_zipcode": "80555"
    }
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
