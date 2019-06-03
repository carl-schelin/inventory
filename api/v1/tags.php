<?php
# Script: tags.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . "/guest.php");

  $package = "tags.php";

  logaccess($formVars['uid'], $package, "Accessing the script.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JSON API Index: Tags</title>

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

<p><strong>GET /api/tags.php</strong></p>

<pre style="text-align: left">curl -s -k https://incojs01.scc911.com/inventory/api/west.php?tags=[tags]&group=[groupid]&location=[location]&service=[service class]&product=[product]&zone=[zone]&admin=[app admin]&manager=[system manager] | python -mjson.tool</pre>

<p><strong>Parameters</strong></p>

<ul>
  <li>[default] - Without a parameter, returns all Unix System Administrator servers in the inventory.</li>
  <li>tags - comma delimited list of tags.</li>
  <li>group - group id that manages servers.</li>
  <li>location - all servers in the West designated location (DEN03, MIA04, etc).</li>
  <li>service - all servers designed by the service class (LMCS, BCS, BES, UBS, LABetc).</li>
  <li>product - all servers for a specified product.</li>
  <li>zone - all servers in a specific zone (CORP, DMZ, E911).</li>
  <li>admin - all servers where the application is managed by the listed group. This is a wildcard search so passing 'web' will give servers managed by Web Applications.</li>
  <li>manager - all servers where the server is managed by the listed group. This is a wildcard search so passing 'unix' will give servers managed by Unix Admins.</li>
</ul>

<p><strong>NOTE:</strong> admin and manager are mutually exclusive. Use one or the other, not both.</p>

<p><strong>Output</strong></p>

<pre style="text-align:left;">
$ curl -s -k 'https://incojs01.scc911.com/inventory/api/tags.php?location=mia04&product=nead/neam&zone=dmz&admin=web' | python -mjson.tool
{
    "miam1duasnenx11": {
        "servername": "miam1duasnenx11"
    },
    "miam1duasnenx21": {
        "servername": "miam1duasnenx21"
    },
    "miam1duasnenx31": {
        "servername": "miam1duasnenx31"
    }
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
