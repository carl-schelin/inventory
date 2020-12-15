<?php
# Script: server.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

<pre style="text-align: left">curl -s -k https://incojs01.scc911.com/inventory/api/server.php?server=[servername]&location=[yes|NO]&service=[yes|NO]&interfaces=[yes|NO]&hardware=[yes|NO] | python -mjson.tool</pre>

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
$ curl -s -k https://incojs01.scc911.com/inventory/api/server.php?server=alde0duasneap11 | python -mjson.tool
{
    "alde0duasneap11": {
        "inventory_appadmins": "Mobility Sys Admin",
        "inventory_documentation": "",
        "inventory_fqdn": "alde0duasneap11.scc911.com",
        "inventory_function": "Application Server",
        "inventory_hardware": "Virtual Machine",
        "inventory_location": "DEN03",
        "inventory_name": "alde0duasneap11",
        "inventory_network": "DMZ",
        "inventory_product": "NEAD/NEAM",
        "inventory_project": "NEAD/NEAM",
        "inventory_satellite_uuid": "a395bb48-c3a7-42f0-a3fe-4ffa40fb5a7d",
        "inventory_service_class": "Lab Services",
        "inventory_maintenance_window": "Prod Grp A (Mon 2300 - Tue 0500 CT)",
        "inventory_sysadmins": "UNIX System Administration",
        "inventory_timezone": "UTC",
        "inventory_uuid": "42196A91-746E-3C34-728E-B174B6A236D9"
    }
}
</pre>

<pre style="text-align: left;">
{
    "alde0euasnesa11": {
        "inventory_appadmins": "Mobility Sys Admins",
        "inventory_documentation": "",
        "inventory_fqdn": "alde0euasnesa11.scc911.com",
        "inventory_function": "Standalone Server",
        "inventory_hardware": {
            "hardware_0": {
                "hardware_active": "0000-00-00",
                "hardware_admins": "UNIX System Administration",
                "hardware_asset_tag": "",
                "hardware_built": "2017-02-08",
                "hardware_eol": "0000-00-00",
                "hardware_model": "Virtual Machine",
                "hardware_model_type": "Virtual Machine",
                "hardware_product": "NEAD/NEAM",
                "hardware_project": null,
                "hardware_purchased": "2017-02-08",
                "hardware_serial_number": "",
                "hardware_service": "",
                "hardware_size": "1",
                "hardware_speed": "",
                "hardware_type": "Server",
                "hardware_vendor": "VMWare",
                "inventory_hardware": {
                    "child_0": {
                        "hardware_active": "0000-00-00",
                        "hardware_admins": "UNIX System Administration",
                        "hardware_asset_tag": "",
                        "hardware_built": "0000-00-00",
                        "hardware_eol": "0000-00-00",
                        "hardware_model": "Intel(R) Xeon(R) CPU E5-2699 v3 @ 2.30GHz",
                        "hardware_model_type": "CPU",
                        "hardware_product": "Unassigned",
                        "hardware_project": null,
                        "hardware_purchased": "0000-00-00",
                        "hardware_serial_number": "",
                        "hardware_service": "",
                        "hardware_size": "2 Cores",
                        "hardware_speed": "2.30 GHz",
                        "hardware_type": "CPU",
                        "hardware_vendor": "Intel"
                    },
                    "child_1": {
                        "hardware_active": "0000-00-00",
                        "hardware_admins": "UNIX System Administration",
                        "hardware_asset_tag": "",
                        "hardware_built": "0000-00-00",
                        "hardware_eol": "0000-00-00",
                        "hardware_model": null,
                        "hardware_model_type": "Unknown",
                        "hardware_product": "Unassigned",
                        "hardware_project": null,
                        "hardware_purchased": "0000-00-00",
                        "hardware_serial_number": "",
                        "hardware_service": "",
                        "hardware_size": null,
                        "hardware_speed": null,
                        "hardware_type": "Memory",
                        "hardware_vendor": null
                    },
                    "child_2": {
                        "hardware_active": "0000-00-00",
                        "hardware_admins": "UNIX System Administration",
                        "hardware_asset_tag": "",
                        "hardware_built": "0000-00-00",
                        "hardware_eol": "0000-00-00",
                        "hardware_model": null,
                        "hardware_model_type": "Unknown",
                        "hardware_product": "Unassigned",
                        "hardware_project": null,
                        "hardware_purchased": "0000-00-00",
                        "hardware_serial_number": "",
                        "hardware_service": "",
                        "hardware_size": null,
                        "hardware_speed": null,
                        "hardware_type": "Hard Disk",
                        "hardware_vendor": null
                    },
                    "child_3": {
                        "hardware_active": "0000-00-00",
                        "hardware_admins": "UNIX System Administration",
                        "hardware_asset_tag": "",
                        "hardware_built": "0000-00-00",
                        "hardware_eol": "0000-00-00",
                        "hardware_model": null,
                        "hardware_model_type": "Unknown",
                        "hardware_product": "Unassigned",
                        "hardware_project": null,
                        "hardware_purchased": "0000-00-00",
                        "hardware_serial_number": "",
                        "hardware_service": "",
                        "hardware_size": null,
                        "hardware_speed": null,
                        "hardware_type": "Hard Disk",
                        "hardware_vendor": null
                    }
                }
            }
        },
        "inventory_location": {
            "location_address1": "1601 Dry Creek Drive",
            "location_address2": "",
            "location_city": "Alderaan",
            "location_clli": "ALDE",
            "location_country": "Star Wars",
            "location_datacenter": "-/U0",
            "location_designation": "DEN03",
            "location_environment": "Pre-Production",
            "location_instance": "0",
            "location_name": "Intrado CIL Data Center - Miami Site",
            "location_state": "Alderaan",
            "location_suite": "",
            "location_type": "Data Center",
            "location_zipcode": "80503"
        },
        "inventory_name": "alde0euasnesa11",
        "inventory_network": {
            "interface_0": {
                "interface_address": "10.39.22.7",
                "interface_default": "Default Route",
                "interface_ethernet": "00:50:56:99:3c:c7",
                "interface_gateway": "10.39.22.254",
                "interface_groupname": "",
                "interface_label": "ens192",
                "interface_name": "alde0euasnesa10",
                "interface_netmask": "24",
                "interface_netzone": "CIL_E911_APP",
                "interface_redundant": "None",
                "interface_role": null,
                "interface_scanned": "No",
                "interface_type": "Application",
                "interface_virtual": "No",
                "interface_vlan": "3222",
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
                "interface_address": "10.39.19.7",
                "interface_default": "",
                "interface_ethernet": "00:50:56:99:ba:e7",
                "interface_gateway": "10.39.19.254",
                "interface_groupname": "",
                "interface_label": "ens224",
                "interface_name": "alde0euasnesa11",
                "interface_netmask": "24",
                "interface_netzone": "CIL_E911_MGT",
                "interface_redundant": "None",
                "interface_role": null,
                "interface_scanned": "No",
                "interface_type": "Management",
                "interface_virtual": "No",
                "interface_vlan": "3219",
                "monitor_cfg2html": "Ignored",
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
                "interface_address": "127.0.0.1",
                "interface_default": "",
                "interface_ethernet": "",
                "interface_gateway": "",
                "interface_groupname": "",
                "interface_label": "lo",
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
            }
        },
        "inventory_product": "NEAD/NEAM",
        "inventory_project": "NEAD/NEAM",
        "inventory_satellite_uuid": "ffce24e7-e99d-49ff-92a9-24ffe83d3d39",
        "inventory_service_class": {
            "service_acronym": "LAB",
            "service_availability": "9-5/M-F",
            "service_downtime": "Unspecified",
            "service_mtbf": "Unspecified",
            "service_mttr": "Unspecified",
            "service_name": "Lab Services",
            "service_redundant": "No",
            "service_restore": "None",
            "service_sharing": "No"
        },
        "inventory_maintenance_window": "Prod Grp A (Mon 2300 - Tue 0500 CT)",
        "inventory_sysadmins": "UNIX System Administration",
        "inventory_timezone": "UTC",
        "inventory_uuid": "42190C4D-2A58-9072-B57A-27DA0698857E"
    }
}
</pre>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
