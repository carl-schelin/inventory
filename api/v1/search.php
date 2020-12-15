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

<p><strong>GET /api/server.php</strong></p>

<pre style="text-align: left">curl -s -k https://incojs01.scc911.com/inventory/api/search.php?ip=[ip address]&mac=[mac address]&asset=[asset tag]&serial=[serial number/service tag] | python -mjson.tool</pre>

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
$ curl -s -k https://incojs01.scc911.com/inventory/api/search.php?ip=10.100.78.143 | python -mjson.tool
{
    "incojs01": {
        "inventory_appadmins": "UNIX System Administration",
        "inventory_documentation": "",
        "inventory_fqdn": "incojs01.scc911.com",
        "inventory_function": "Unix Jumpstart Server",
        "inventory_hardware": "Sun Fire X4200 M2",
        "inventory_location": "DEN03",
        "inventory_name": "incojs01",
        "inventory_network": {
            "interface_0": {
                "interface_address": "127.0.0.1",
                "interface_default": "",
                "interface_ethernet": "",
                "interface_gateway": "127.0.0.1",
                "interface_groupname": "",
                "interface_label": "lo0",
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
            "interface_1": {
                "interface_address": "10.100.78.143",
                "interface_default": "Default Route",
                "interface_ethernet": "",
                "interface_gateway": "10.100.78.253",
                "interface_groupname": "",
                "interface_label": "e1000g0:1",
                "interface_name": "incojs01",
                "interface_netmask": "24",
                "interface_netzone": "CORP_APP",
                "interface_redundant": "IPMP (Solaris)",
                "interface_role": null,
                "interface_scanned": "Yes",
                "interface_type": "Management",
                "interface_virtual": "Yes",
                "interface_vlan": "",
                "inventory_network": {
                    "child_0": {
                        "interface_address": "10.100.78.144",
                        "interface_default": "",
                        "interface_ethernet": "0:15:17:7b:8b:35",
                        "interface_gateway": "10.100.78.253",
                        "interface_groupname": "js1net",
                        "interface_label": "e1000g0",
                        "interface_name": "incojs01",
                        "interface_netmask": "24",
                        "interface_netzone": "CORP_APP",
                        "interface_redundant": "Child",
                        "interface_role": null,
                        "interface_scanned": "Yes",
                        "interface_type": "Signaling",
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
                        "physical_switch": "lnmtcoccswa",
                        "physical_switch_port": "Gi6/30"
                    },
                    "child_1": {
                        "interface_address": "10.100.78.33",
                        "interface_default": "",
                        "interface_ethernet": "0:21:28:10:53:46",
                        "interface_gateway": "10.100.78.253",
                        "interface_groupname": "js1net",
                        "interface_label": "nge0",
                        "interface_name": "incojs01",
                        "interface_netmask": "24",
                        "interface_netzone": "CORP_APP",
                        "interface_redundant": "Child",
                        "interface_role": null,
                        "interface_scanned": "Yes",
                        "interface_type": "Signaling",
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
                        "physical_switch": "sr11",
                        "physical_switch_port": "30"
                    }
                },
                "monitor_cfg2html": "Monitored",
                "monitor_ftp": "No",
                "monitor_hours": "Business Hours",
                "monitor_http": "No",
                "monitor_nagios": "Monitored",
                "monitor_notify": "None",
                "monitor_openview": "Monitored",
                "monitor_ping": "Monitored",
                "monitor_smtp": "No",
                "monitor_ssh": "Monitored",
                "physical_duplex": null,
                "physical_media": null,
                "physical_port": "",
                "physical_speed": null,
                "physical_switch": "",
                "physical_switch_port": ""
            },
            "interface_2": {
                "interface_address": "10.100.78.32",
                "interface_default": "",
                "interface_ethernet": "00:21:28:10:18:3A",
                "interface_gateway": "10.100.78.253",
                "interface_groupname": "",
                "interface_label": "netmgt",
                "interface_name": "incojs01-sp",
                "interface_netmask": "24",
                "interface_netzone": "CORP_APP",
                "interface_redundant": "None",
                "interface_role": null,
                "interface_scanned": "Yes",
                "interface_type": "LOM",
                "interface_virtual": "No",
                "interface_vlan": "",
                "monitor_cfg2html": "Ignored",
                "monitor_ftp": "No",
                "monitor_hours": "Business Hours",
                "monitor_http": "No",
                "monitor_nagios": "Monitored",
                "monitor_notify": "None",
                "monitor_openview": "No",
                "monitor_ping": "Monitored",
                "monitor_smtp": "No",
                "monitor_ssh": "No",
                "physical_duplex": null,
                "physical_media": null,
                "physical_port": "",
                "physical_speed": null,
                "physical_switch": "sr2",
                "physical_switch_port": "4"
            },
            "interface_3": {
                "interface_address": "10.100.9.23",
                "interface_default": "",
                "interface_ethernet": "",
                "interface_gateway": "10.100.9.254",
                "interface_groupname": "",
                "interface_label": "e1000g1:1",
                "interface_name": "incojs01",
                "interface_netmask": "24",
                "interface_netzone": "CORP_APP",
                "interface_redundant": "IPMP (Solaris)",
                "interface_role": null,
                "interface_scanned": "Yes",
                "interface_type": "Application",
                "interface_virtual": "Yes",
                "interface_vlan": "",
                "inventory_network": {
                    "child_0": {
                        "interface_address": "10.100.9.22",
                        "interface_default": "",
                        "interface_ethernet": "0:21:28:10:53:47",
                        "interface_gateway": "10.100.9.254",
                        "interface_groupname": "mgtnet",
                        "interface_label": "nge1",
                        "interface_name": "incojs06-int",
                        "interface_netmask": "24",
                        "interface_netzone": "CORP_APP",
                        "interface_redundant": "Child",
                        "interface_role": null,
                        "interface_scanned": "Yes",
                        "interface_type": "Signaling",
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
                    "child_1": {
                        "interface_address": "10.100.9.21",
                        "interface_default": "",
                        "interface_ethernet": "0:15:17:7b:8b:34",
                        "interface_gateway": "10.100.9.254",
                        "interface_groupname": "mgtnet",
                        "interface_label": "e1000g1",
                        "interface_name": "incojs01-int",
                        "interface_netmask": "24",
                        "interface_netzone": null,
                        "interface_redundant": "Child",
                        "interface_role": null,
                        "interface_scanned": "Yes",
                        "interface_type": "Signaling",
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
        "inventory_maintenanc_window": "Prod Grp A (Mon 2300 - Tue 0500 CT)",
        "inventory_sysadmins": "UNIX System Administration",
        "inventory_timezone": "MST/MDT",
        "inventory_uuid": "ff200008-ffff-ffff-ffff-465310282100"
    }
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
