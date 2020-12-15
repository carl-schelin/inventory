<?php
# Script: locations.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

<pre style="text-align: left">curl -s -k https://incojs01.scc911.com/inventory/api/locations.php?site=[site name]&type=[site type] | python -mjson.tool</pre>

<p><strong>Parameters</strong></p>

<ul>
  <li>[default] - Returns all locations in the inventory.</li>
  <li>site - Wildcard search on the location name.</li>
  <li>type - Location type; Data Center, PSAP, NOC, Customer. For 'Data Center', use '%20' in place of the space.</li>
</ul>

<p><strong>Output</strong></p>

<pre style="text-align: left">
$ curl -s -k https://incojs01.scc911.com/inventory/api/locations.php?site=nap | python -mjson.tool
{
    "26": {
        "location_address1": "18155 Technology Drive",
        "location_address2": "Data Center A",
        "location_city": "Culpeper",
        "location_clli": "CLPP",
        "location_country": "United States",
        "location_designation": "WDC09",
        "location_environment": "Production",
        "location_instance": "1",
        "location_name": "NAP of the Capital Region - Culpeper",
        "location_state": "Virginia",
        "location_suite": "cage 71",
        "location_type": "Data Center",
        "location_zipcode": "22701"
    },
    "4": {
        "location_address1": "50 N.E. 9th Street",
        "location_address2": "8th Street Entrance",
        "location_city": "Miami",
        "location_clli": "MIAM",
        "location_country": "United States",
        "location_designation": "MIA04",
        "location_environment": "Production",
        "location_instance": "1",
        "location_name": "Nap of The Americas - Miami",
        "location_state": "Florida",
        "location_suite": "",
        "location_type": "Data Center",
        "location_zipcode": "33132"
    }
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
