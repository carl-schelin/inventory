<?php
# Script: network.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');
  check_login('4');

  $package = "network.php";

  logaccess($_SESSION['uid'], $package, "Viewing Network Settings for the project");

  if (isset($_GET['myrsdp'])) {
    $formVars['myrsdp'] = clean($_GET['myrsdp'], 10);
  } else {
    $formVars['myrsdp'] = 'no';
  }
  if ($formVars['myrsdp'] == '') {
    $formVars['myrsdp'] = 'yes';
  }

  if (isset($_GET['projectid'])) {
    $formVars['projectid'] = clean($_GET['projectid'], 10);
  } else {
    $formVars['projectid'] = 0;
  }

  if (isset($_GET['productid'])) {
    $formVars['productid'] = clean($_GET['productid'], 10);
  } else {
    $formVars['productid'] = 0;
  }

  if (isset($_GET['filter'])) {
    $formVars['filter'] = clean($_GET['filter'], 255);
  } else {
    $formVars['filter'] = '';
  }

# if help has not been seen yet,
  if (show_Help('rsdp_network')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSDP: Networks</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function clear_fields() {
  show_file('network.mysql.php?projectid=<?php print $formVars['projectid']; ?>&productid=<?php print $formVars['productid']; ?>&filter=<?php print $formVars['filter']; ?>');
}

function check_Monitored( p_id ) {
  show_file('network.checked.php?id=' + p_id);
}

function systems_Group( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=1');
}

function select_Systems( p_id ) {
  var ss_value = document.getElementById(p_id + '_groups').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=1&select=' + ss_value);
}

function applications_Group( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=2');
}

function select_Applications( p_id ) {
  var sa_value = document.getElementById(p_id + '_groups').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=2&select=' + sa_value);
}

function applications_Admin( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=3');
}

function select_Appadmin( p_id ) {
  var sa_value = document.getElementById(p_id + '_appadmin').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=3&select=' + sa_value);
}

function service_Class( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=4');
}

function select_Service( p_id ) {
  var ss_value = document.getElementById(p_id + '_service').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=4&select=' + ss_value);
}

function data_Center( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=5');
}

function select_Location( p_id ) {
  var sl_value = document.getElementById(p_id + '_location').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=5&select=' + sl_value);
}


function server_Function( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=6');
}

function select_Function( p_id ) {
  var sf_value = encode_URI(document.getElementById(p_id + '_function').value);

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=6&select=' + sf_value);
}

function operating_System( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=7');
}

function select_Platform( p_id ) {
  var sp_value = document.getElementById(p_id + '_system').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=7&select=' + sp_value);
}

function central_Processor( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=8');
}

function select_Processor( p_id ) {
  var sp_value = document.getElementById(p_id + '_cpu').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=8&select=' + sp_value);
}

function system_Memory( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=9');
}

function select_Memory( p_id ) {
  var sm_value = document.getElementById(p_id + '_memory').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=9&select=' + sm_value);
}

function system_Size( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=10');
}

function select_Size( p_id ) {
  var ss_value = document.getElementById(p_id + '_size').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=10&select=' + ss_value);
}

function file_Systems( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=11');
}

function select_filesystem( p_id ) {
  var sf_value = document.getElementById(p_id + '_filesystem').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=11&select=' + sf_value);
}

function vendor_Name( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=12');
}

function select_vendor( p_id ) {
  var sv_value = document.getElementById(p_id + '_vendor').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=12&select=' + sv_value);
}

function model_Name( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=13');
}

function select_Model( p_id ) {
  var sm_value = document.getElementById(p_id + '_model').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=13&select=' + sm_value);
}


function interface_Name( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=14');
}

function select_Name( p_id ) {
  var sn_value = document.getElementById(p_id + '_name').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=14&select=' + sn_value);
}

function interface_Acronym( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=15');
}

function select_Acronym( p_id ) {
  var sa_value = document.getElementById(p_id + '_acronym').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=15&select=' + sa_value);
}

function interface_Description( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=16');
}

function select_Description( p_id ) {
  var sd_value = document.getElementById(p_id + '_description').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=16&select=' + sd_value);
}

function interface_Address( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=18');
}

function select_Address( p_id ) {
  var sa_value = document.getElementById(p_id + '_address').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=18&select=' + sa_value);
}

function interface_Zone( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=19');
}

function select_Zone( p_id ) {
  var sz_value = document.getElementById(p_id + '_zone').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=19&select=' + sz_value);
}

function interface_Gateway( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=20');
}

function select_Gateway( p_id ) {
  var sg_value = document.getElementById(p_id + '_gateway').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=20&select=' + sg_value);
}

function interface_Device( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=21');
}

function select_Device( p_id ) {
  var sd_value = document.getElementById(p_id + '_device').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=21&select=' + sd_value);
}

function interface_Media( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=22');
}

function select_Media( p_id ) {
  var sm_value = document.getElementById(p_id + '_media').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=22&select=' + sm_value);
}

function interface_Switch( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=23');
}

function select_Switch( p_id ) {
  var ss_value = document.getElementById(p_id + '_switch').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=23&select=' + ss_value);
}

function interface_Port( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=24');
}

function select_Port( p_id ) {
  var sp_value = document.getElementById(p_id + '_port').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=24&select=' + sp_value);
}

function interface_Netmask( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=25');
}

function select_Netmask( p_id ) {
  var sn_value = document.getElementById(p_id + '_netmask').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=25&select=' + sn_value);
}

function interface_VLAN( p_id ) {
  show_file('network.interface.php?id=' + p_id + '&status=1&type=26');
}

function select_VLAN( p_id ) {
  var sv_value = document.getElementById(p_id + '_vlan').value;

  show_file('network.interface.php?id=' + p_id + '&status=0&type=26&select=' + sv_value);
}


function platforms_Admin( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=14');
}

function select_PlatformsAdmin( p_id ) {
  var sp_value = document.getElementById(p_id + '_platformsadmin').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=14&select=' + sp_value);
}

function SAN_Admin( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=15');
}

function select_SANAdmin( p_id ) {
  var ss_value = document.getElementById(p_id + '_sanadmin').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=15&select=' + ss_value);
}

function network_Admin( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=16');
}

function select_NetworkAdmin( p_id ) {
  var sn_value = document.getElementById(p_id + '_networkadmin').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=16&select=' + sn_value);
}

function virtualization_Admin( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=17');
}

function select_VirtualizationAdmin( p_id ) {
  var sv_value = document.getElementById(p_id + '_virtualizationadmin').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=17&select=' + sv_value);
}

function datacenter_Admin( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=18');
}

function select_DataCenterAdmin( p_id ) {
  var sd_value = document.getElementById(p_id + '_datacenteradmin').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=18&select=' + sd_value);
}

function monitoring_Admin( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=19');
}

function select_MonitoringAdmin( p_id ) {
  var sm_value = document.getElementById(p_id + '_monitoringadmin').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=19&select=' + sm_value);
}

function backup_Admin( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=20');
}

function select_BackupAdmin( p_id ) {
  var sb_value = document.getElementById(p_id + '_backupadmin').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=20&select=' + sb_value);
}


function volt_Text( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=21');
}

function select_voltText( p_id ) {
  var sv_value = document.getElementById(p_id + '_volttext').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=21&select=' + sv_value);
}

function power_Draw( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=22');
}

function select_powerDraw( p_id ) {
  var sp_value = document.getElementById(p_id + '_powerdraw').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=22&select=' + sp_value);
}

function power_Start( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=23');
}

function select_powerStart( p_id ) {
  var sp_value = document.getElementById(p_id + '_powerstart').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=23&select=' + sp_value);
}

function power_Plugs( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=24');
}

function select_powerPlugs( p_id ) {
  var sp_value = document.getElementById(p_id + '_powerplugs').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=24&select=' + sp_value);
}

function power_Redundant( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=25');
}

function select_powerRedundant( p_id ) {
  var sp_value = document.getElementById(p_id + '_powerredundant').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=25&select=' + sp_value);
}

function plug_Text( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=26');
}

function select_plugText( p_id ) {
  var sp_value = document.getElementById(p_id + '_plugtext').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=26&select=' + sp_value);
}

function start_Row( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=27');
}

function select_startRow( p_id ) {
  var ss_value = document.getElementById(p_id + '_startrow').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=27&select=' + ss_value);
}

function start_Rack( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=28');
}

function select_startRack( p_id ) {
  var ss_value = document.getElementById(p_id + '_startrack').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=28&select=' + ss_value);
}

function start_Unit( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=29');
}

function select_startUnit( p_id ) {
  var ss_value = document.getElementById(p_id + '_startunit').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=29&select=' + ss_value);
}

function number_Units( p_id ) {
  show_file('network.inputs.php?id=' + p_id + '&status=1&type=30');
}

function select_numberUnits( p_id ) {
  var sn_value = document.getElementById(p_id + '_numberunits').value;

  show_file('network.inputs.php?id=' + p_id + '&status=0&type=30&select=' + sn_value);
}


function set_Filter( p_rsdp ) {
  var sf_filter = document.rsdp.filter.value;
  var sf_checked = document.getElementById('filter_' + p_rsdp).checked;

  show_file('network.filter.php?rsdp=' + p_rsdp+ '&filter=' + sf_filter + '&status=' + sf_checked);
}

function attach_filter( ) {
  var af_form = document.rsdp;
  var af_url = 'network.mysql.php';

  af_url += '?projectid='     + <?php print $formVars['projectid']; ?>;
  af_url += '&productid='     + <?php print $formVars['productid']; ?>;
  af_url += '&filter='        + af_form.filter.value;

  script = document.createElement('script');
  script.src = af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_Filter( p_rsdp ) {
  document.rsdp.filter.value = '';

  clear_fields();
}


$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<form name="rsdp">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Project Management</th>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" name="filterbutton" value="Filter" onClick="javascript:attach_filter();"><input type="hidden" name="filter" value=""></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Project Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>The Purpose of this page is to give you an overall view of all servers in your project. While it can help with smaller lists of servers, the intention is to assist when the project has 10 or more servers that need to be reviewed.</p>

<p><u><strong>NOTE</strong> - Data on this page assumes you've filled out the necessary information in the first two tasks of a server build. This is an <strong>EDIT</strong> page. You cannot add or delete lines here.</u></p>

<p>The Project Information tab lists the server name along with relevant details for each server. When scanning the list, you can ensure the proper group and group member have been assigned to the server. Plus you can validate the Service Class and the ultimate location of the server. Clicking on the Server Name will take you to the RSDP Task page for this server. From there you can select and manage any of the tasks. If you need to make an on-the-fly change such as changing any of the listed columns, click on the table cell and you can make a change while on this page.</p>

<p>The System Information tab lists out more details about the server. You can click on the server name in the leftmost column to go to the RSDP Task page or perform an on-the-fly edit of Function, OS, CPU, RAM, and OS Disk fields for that server. The remaining fields need to be managed through the RSDP Task page for this server.</p>

<p>The Interface Information tab lists every defined interface for this server. You can click on the server name in the leftmost column to go to the RSDP Task page or perform an on-the-fly edit of the rest of the columns.</p>

<p>The Unix Kickstart tab lists the necessary details for each server which can then be copy/pasted into the techops.stub script which is used to configure a server after Virtualization has provisioned it.</p>

<p>The DNS Listing tab lists the IP addresses and the FQDN and server name for every server in the project. This is suitable for copy/pasting into a ticket for a DNS update and/or copy/pasting it into /etc/hosts on the Jumpstart servers.</p>

</div>

</div>

</form>

<div id="tabs">

<ul>
  <li><a href="#system">System Information</a></li>
  <li><a href="#project">Project Information</a></li>
  <li><a href="#hardware">Hardware Details</a></li>
  <li><a href="#interface">Interface Information</a></li>
  <li><a href="#kickstart">Unix Kickstart</a></li>
  <li><a href="#dns">DNS Listing</a></li>
  <li><a href="#hosts">/etc/hosts Listing</a></li>
  <li><a href="#vulnerability">Vulnerability Listing</a></li>
  <li><a href="#virtualization">Virtualization</a></li>
  <li><a href="#monitoring">Monitoring</a></li>
</a></li>
</ul>

<div id="system">

<span id="system_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="project">

<span id="project_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="interface">

<span id="interface_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="hardware">

<span id="hardware_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="kickstart">

<span id="kickstart_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="dns">

<span id="dns_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="hosts">

<span id="hosts_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="vulnerability">

<span id="vulnerability_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="virtualization">

<span id="virtualization_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="monitoring">

<span id="monitoring_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
