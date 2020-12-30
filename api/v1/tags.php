<?php
# Script: tags.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . "/guest.php");

  $package = "tags.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

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

<pre style="text-align: left">curl -s -k <?php print $Siteroot; ?>/api/west.php?tags=[tags]&group=[groupid]&location=[location]&service=[service class]&product=[product]&zone=[zone]&admin=[app admin]&manager=[system manager] | python -mjson.tool</pre>


<p><strong>Description</strong></p>

<p>The purpose of this api call is to provide a list of just server names to Ansible based on the passed piece of data. Locations, tags, groups, etc. In this way, a separate hosts file doesn't need to be maintained.</p>

<p><strong>Parameters</strong></p>

<ul>
  <li>[default] - Without a parameter, returns all Unix System Administrator servers in the inventory.</li>
  <li>tags - comma delimited list of tags.</li>
  <li>group - group id that manages servers.</li>
  <li>location - all servers in the short designated location (BLD01, CAB01, etc).</li>
  <li>service - all servers designed by the service class (LMCS, BCS, BES, UBS, LABetc).</li>
  <li>product - all servers for a specified product.</li>
  <li>zone - all servers in a specific zone (CORP, DMZ).</li>
  <li>admin - all servers where the application is managed by the listed group. This is a wildcard search so passing 'dba' will give servers managed by the DBA Group.</li>
  <li>manager - all servers where the server is managed by the listed group. This is a wildcard search so passing 'unix' will give servers managed by Unix Admins.</li>
</ul>

<p><strong>NOTE:</strong> admin and manager are mutually exclusive. Use one or the other, not both.</p>

<p><strong>Output</strong></p>

<pre style="text-align:left;">
$ curl -s -k '<?php print $Siteroot; ?>/api/tags.php?location=bld01&product=kubernetes&admin=unix' | python -mjson.tool
{
    "bldr0cuomknode1": {
        "servername": "bldr0cuomknode1"
    },
    "bldr0cuomknode2": {
        "servername": "bldr0cuomknode2"
    },
    "bldr0cuomknode3": {
        "servername": "bldr0cuomknode3"
    },
    "bldr0cuomkube1": {
        "servername": "bldr0cuomkube1"
    },
    "bldr0cuomkube2": {
        "servername": "bldr0cuomkube2"
    },
    "bldr0cuomkube3": {
        "servername": "bldr0cuomkube3"
    }
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
