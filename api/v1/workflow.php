<?php
# Script: workflow.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . "/guest.php");

  $package = "workflow.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JSON API Index: Workflow</title>

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

<p><strong>GET /api/workflow.php</strong></p>

<pre style="text-align: left">curl -s -k https://incojs01.scc911.com/inventory/api/workflow.php?server=[servername]&group=[groupid] | python -mjson.tool</pre>

<p><strong>Parameters</strong></p>

<ul>
  <li>[default] - Without a parameter, returns all servers in the inventory.</li>
  <li>server - Pass the name of the server to generate a listing.</li>
  <li>group - Pass a group id to restrict the listing to servers just managed by that group.</li>
</ul>

<p><strong>Output</strong></p>

<pre style="text-align: left;">
{
    "alde0euasnesa11": {
        "config_agent": "",
        "config_appadmin": "mobadmin",
        "config_appsudo": "mobadmin",
        "config_cpus": "2",
        "config_disk": {
            "disk_1202": {
                "disk_mount": "/",
                "disk_size": "80"
            },
            "disk_736": {
                "disk_mount": "/opt",
                "disk_size": "160"
            }
        },
        "config_memory": "8",
        "config_network": {
            "interface_2354": {
                "interface_address": "10.39.22.7",
                "interface_ethernet": "ens192",
                "interface_gateway": "10.39.22.254",
                "interface_monitored": "No"
            },
            "interface_2394": {
                "interface_address": "10.39.19.7",
                "interface_ethernet": "ens224",
                "interface_gateway": "10.39.19.254",
                "interface_monitored": "Yes"
            }
        },
        "config_sysadmin": "sysadmins",
        "config_syssudo": "sysadmins",
        "config_workflowid": "1202"
    }
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
