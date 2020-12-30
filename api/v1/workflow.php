<?php
# Script: workflow.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

<pre style="text-align: left">curl -s -k <?php print $Siteroot; ?>/api/workflow.php?server=[servername]&group=[groupid] | python -mjson.tool</pre>

<p><strong>Description</strong></p>

<p>IN WORK: The intention here is to facilitate automation by returning the necessary information for provisioning a server. Passing along ownerships, system specifications, etc. A server upon being provisioned would kick off a query to the Inventory api, retrieve its configuration information, and self configure.</p>

<p><strong>Parameters</strong></p>

<ul>
  <li>[default] - Without a parameter, returns all servers in the inventory.</li>
  <li>server - Pass the name of the server to generate a listing.</li>
  <li>group - Pass a group id to restrict the listing to servers just managed by that group.</li>
</ul>

<p><strong>Output</strong></p>

<pre style="text-align: left;">
$ curl -s -k <?php print $Siteroot; ?>/api/workflow.php?server=lnmt1cuomknode1 | python -mjson.tool
{
    "lnmt1cuomknode1": {
        "config_agent": [],
        "config_appadmin": "",
        "config_appsudo": "",
        "config_cpus": "8",
        "config_disk": {
            "/": {
                "disk_mount": "/",
                "disk_size": "2"
            },
            "/home": {
                "disk_mount": "/home",
                "disk_size": "8"
            },
            "/opt": {
                "disk_mount": "/opt",
                "disk_size": "4"
            },
            "/opt/docker": {
                "disk_mount": "/opt/docker",
                "disk_size": "200"
            },
            "/tmp": {
                "disk_mount": "/tmp",
                "disk_size": "4"
            },
            "/usr": {
                "disk_mount": "/usr",
                "disk_size": "8"
            },
            "/var": {
                "disk_mount": "/var",
                "disk_size": "4"
            },
            "swap": {
                "disk_mount": "swap",
                "disk_size": "2"
            }
        },
        "config_memory": "16",
        "config_network": {
            "ens192": {
                "interface_address": "192.168.104.50",
                "interface_ethernet": "ens192",
                "interface_gateway": "192.168.104.254",
                "interface_monitored": "Yes"
            }
        },
        "config_sysadmin": "sysadmins",
        "config_syssudo": "sysadmins",
        "config_workflowid": "1484"
    }
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
