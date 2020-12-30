<?php
# Script: search.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . "/guest.php");

  $package = "search.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JSON API Index: Search</title>

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

<p><strong>GET /api/search.php</strong></p>

<pre style="text-align: left">curl -s -k <?php print $Siteroot; ?>/api/search.php?ip=[ip address]&mac=[mac address]&asset=[asset tag]&serial=[serial number/service tag] | python -mjson.tool</pre>

<p><strong>Parameters</strong></p>

<p>Note that these are exclusive searches and in the order listed below.</p>

<ul>
  <li>IP Address - Search for an IP address and return a server listing.</li>
  <li>MAC Address - Search for a MAC address and return a server listing.</li>
  <li>Asset Tag - Search for an asset tag and return a server listing.</li>
  <li>Serial Number/Dell Service Tag - Search for a serial number or Dell service tag and return a server listing.</li>
</ul>

<p><strong>Output</strong></p>


<pre style="text-align: left;">
$ curl -s -k <?php print $Siteroot; ?>/api/search.php?ip=192.168.104.57 | python -mjson.tool
{
    "lnmt1cuomtool11": {
        "inventory_appadmins": "UNIX System Administration",
        "inventory_documentation": "",
        "inventory_fqdn": null,
        "inventory_function": "Tool Server",
        "inventory_hardware": "Virtual Machine",
        "inventory_location": "LMT01",
        "inventory_maintenance_window": "Unassigned",
        "inventory_name": "lnmt1cuomtool11",
        "inventory_network": {
            "interface_0": {
                "interface_address": "fe80::20c:29ff:fea8:538",
                "interface_backup": "No",
                "interface_default": "",
                "interface_ethernet": "00:0c:29:a8:05:38",
                "interface_fqdn": "lnmt1cuomtool11.internal.pri",
                "interface_gateway": "",
                "interface_groupname": "",
                "interface_label": "noprefixroute",
                "interface_management": "No",
                "interface_name": "lnmt1cuomtool11",
                "interface_netmask": "64",
                "interface_netzone": null,
                "interface_redundant": "None",
                "interface_role": null,
                "interface_scanned": "No",
                "interface_type": null,
                "interface_virtual": "No",
                "interface_vlan": "",
                "monitor_cfg2html": "Ignored",
                "monitor_ftp": "No",
                "monitor_hours": "Business Hours",
                "monitor_http": "No",
                "monitor_nagios": "No",
                "monitor_notify": "None",
                "monitor_openview": "No",
                "monitor_ping": "No",
                "monitor_smtp": "No",
                "monitor_ssh": "No",
                "physical_duplex": null,
                "physical_media": null,
                "physical_port": "",
                "physical_speed": null,
                "physical_switch": "",
                "physical_switch_port": ""
            },
            "interface_1": {
                "interface_address": "192.168.104.57",
                "interface_backup": "No",
                "interface_default": "Default Route",
                "interface_ethernet": "00:0c:29:a8:05:38",
                "interface_fqdn": "lnmt1cuomtool11.internal.pri",
                "interface_gateway": "192.168.104.254",
                "interface_groupname": "",
                "interface_label": "ens192",
                "interface_management": "Yes",
                "interface_name": "lnmt1cuomtool11",
                "interface_netmask": "24",
                "interface_netzone": "CORP_MGT",
                "interface_redundant": "None",
                "interface_role": null,
                "interface_scanned": "No",
                "interface_type": "Management",
                "interface_virtual": "No",
                "interface_vlan": "",
                "monitor_cfg2html": "Ignored",
                "monitor_ftp": "No",
                "monitor_hours": "Business Hours",
                "monitor_http": "No",
                "monitor_nagios": "Monitored",
                "monitor_notify": "None",
                "monitor_openview": "No",
                "monitor_ping": "No",
                "monitor_smtp": "No",
                "monitor_ssh": "No",
                "physical_duplex": null,
                "physical_media": null,
                "physical_port": "",
                "physical_speed": null,
                "physical_switch": "",
                "physical_switch_port": ""
            },
            "interface_2": {
                "interface_address": "fe80::20c:29ff:fea8:538",
                "interface_backup": "No",
                "interface_default": "",
                "interface_ethernet": "00:0c:29:a8:05:38",
                "interface_fqdn": "lnmt1cuomtool11.internal.pri",
                "interface_gateway": "",
                "interface_groupname": "",
                "interface_label": "ens192",
                "interface_management": "No",
                "interface_name": "lnmt1cuomtool11",
                "interface_netmask": "64",
                "interface_netzone": "CORP_MGT",
                "interface_redundant": "None",
                "interface_role": null,
                "interface_scanned": "No",
                "interface_type": "Management",
                "interface_virtual": "No",
                "interface_vlan": "",
                "monitor_cfg2html": "Ignored",
                "monitor_ftp": "No",
                "monitor_hours": "Business Hours",
                "monitor_http": "No",
                "monitor_nagios": "No",
                "monitor_notify": "None",
                "monitor_openview": "No",
                "monitor_ping": "No",
                "monitor_smtp": "No",
                "monitor_ssh": "No",
                "physical_duplex": null,
                "physical_media": null,
                "physical_port": "",
                "physical_speed": null,
                "physical_switch": "",
                "physical_switch_port": ""
            },
            "interface_3": {
                "interface_address": "127.0.0.1",
                "interface_backup": "No",
                "interface_default": "",
                "interface_ethernet": "",
                "interface_fqdn": "localhost",
                "interface_gateway": "",
                "interface_groupname": "",
                "interface_label": "lo",
                "interface_management": "No",
                "interface_name": "localhost",
                "interface_netmask": "8",
                "interface_netzone": null,
                "interface_redundant": "None",
                "interface_role": null,
                "interface_scanned": "No",
                "interface_type": "Loopback",
                "interface_virtual": "No",
                "interface_vlan": "",
                "monitor_cfg2html": "Ignored",
                "monitor_ftp": "No",
                "monitor_hours": "Business Hours",
                "monitor_http": "No",
                "monitor_nagios": "No",
                "monitor_notify": "None",
                "monitor_openview": "No",
                "monitor_ping": "No",
                "monitor_smtp": "No",
                "monitor_ssh": "No",
                "physical_duplex": null,
                "physical_media": null,
                "physical_port": "",
                "physical_speed": null,
                "physical_switch": "",
                "physical_switch_port": ""
            },
            "interface_4": {
                "interface_address": "::1",
                "interface_backup": "No",
                "interface_default": "",
                "interface_ethernet": "",
                "interface_fqdn": "localhost",
                "interface_gateway": "",
                "interface_groupname": "",
                "interface_label": "lo",
                "interface_management": "No",
                "interface_name": "localhost",
                "interface_netmask": "128",
                "interface_netzone": null,
                "interface_redundant": "None",
                "interface_role": null,
                "interface_scanned": "No",
                "interface_type": "Loopback",
                "interface_virtual": "No",
                "interface_vlan": "",
                "monitor_cfg2html": "Ignored",
                "monitor_ftp": "No",
                "monitor_hours": "Business Hours",
                "monitor_http": "No",
                "monitor_nagios": "No",
                "monitor_notify": "None",
                "monitor_openview": "No",
                "monitor_ping": "No",
                "monitor_smtp": "No",
                "monitor_ssh": "No",
                "physical_duplex": null,
                "physical_media": null,
                "physical_port": "",
                "physical_speed": null,
                "physical_switch": "",
                "physical_switch_port": ""
            }
        },
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
