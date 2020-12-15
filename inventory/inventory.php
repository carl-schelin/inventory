<?php
# Script: inventory.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login($AL_Guest);

  $package = "inventory.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing Inventory Settings");

  if (isset($_GET['filter'])) {
    $formVars['filter'] = clean($_GET['filter'], 255);
  } else {
    $formVars['filter'] = '';
  }

  if (isset($_GET['product'])) {
    $formVars['productid'] = clean($_GET['product'], 255);
  } else {
    $formVars['productid'] = 0;
  }

  if (isset($_GET['project'])) {
    $formVars['projectid'] = clean($_GET['project'], 255);
  } else {
    $formVars['projectid'] = 0;
  }

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 255);
  } else {
    $formVars['group'] = 0;
  }

  if (isset($_GET['location'])) {
    $formVars['location'] = clean($_GET['location'], 10);
  } else {
    $formVars['location'] = 0;
  }

  if (isset($_GET['csv'])) {
    $formVars['csv'] = clean($_GET['csv'], 10);
  }

# if help has not been seen yet,
  if (show_Help($db, 'tabbed_inventory')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Inventory Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function clear_fields() {
<?php
  print "  show_file('inventory.mysql.php";
  print "?group="     . $formVars['group'];
  print "&projectid=" . $formVars['projectid'];
  print "&productid=" . $formVars['productid'];
  print "&filter="    . $formVars['filter'];
  print "&location="  . $formVars['location'];
  print "&csv="       . $formVars['csv'];
  print "');";
?>
}

function check_Monitored( p_id ) {
  show_file('inventory.checked.php?id=' + p_id);
}

function edit_Detail( p_id, p_function) {
  show_file('inventory.detail.php?id=' + p_id + '&function=' + p_function + '&status=1');
}

function detail_Completed( p_id, p_function) {
  var edit_data = document.getElementById('edit_data').value;

  show_file('inventory.detail.php?id=' + p_id + '&function=' + p_function + '&status=0' + "&select=" + edit_data);
}

function edit_Hardware( p_id, p_function) {
  show_file('inventory.hardware.php?id=' + p_id + '&function=' + p_function + '&status=1');
}

function hardware_Completed( p_id, p_function) {
  var edit_data = document.getElementById('edit_data').value;

  show_file('inventory.hardware.php?id=' + p_id + '&function=' + p_function + '&status=0' + "&select=" + edit_data);
}

function edit_Interface( p_id, p_function) {
  show_file('inventory.interface.php?id=' + p_id + '&function=' + p_function + '&status=1');
}

function interface_Completed( p_id, p_function) {
  var edit_data = document.getElementById('edit_data').value;

  show_file('inventory.interface.php?id=' + p_id + '&function=' + p_function + '&status=0' + "&select=" + edit_data);
}


function set_Filter( p_rsdp ) {
  var sf_filter = document.rsdp.filter.value;
  var sf_checked = document.getElementById('filter_' + p_rsdp).checked;

  show_file('inventory.filter.php?rsdp=' + p_rsdp+ '&filter=' + sf_filter + '&status=' + sf_checked);
}

function attach_filter( ) {
  var af_form = document.rsdp;
  var af_url = 'inventory.mysql.php';

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
  <th class="ui-state-default">Server Management</th>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" name="filterbutton" value="Filter" onClick="javascript:attach_filter();"><input type="hidden" name="filter" value=""></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Server Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This page gives you a filtered listing of all servers and devices. Filters will give you a shorter list of servers to view which 
might also make it a bit easier to make the changes.</p>

<p><u><strong>NOTE</strong> - Changing the data on this page <strong>actually changes the live data in the inventory WITH NO 
CONFIRMATION DIALOG BOX.</strong>. When you change it, it's changed.</u> It is restricted to just the team that can manage the server.</p>

<p>In many cases, if the server is managed by the Unix System Administration team, the server information is automatically 
retrieved from each server and the inventory is updated. Regarding those fields, making changes here will be temporary in such cases.</p>

<p>The System Tree tab shows all the parent servers and chassis and under that are the children (blades for example) for that main device.</p>

<p>The System Information tab shows the main detail record for the server or device. This information isn't automatically captured 
from the system.</p>

<p>The Hardware Details tab shows the hardware associated with this server in a parent/child relationship. The primary device and all 
unassociated devices will be at the top of each server's line with child devices listed under the primary device. Child devices are very 
likely being automatically captured from the system so making changes here will be overwritten.</p>

<p>The Interface Information tab shows all the interfaces associated with this server in a parent/child relationship. The IPMI, Bonded, 
or APA virtual interfaces are displayed and any child interfaces are displayed under the appropriate interface. Several checkboxes are 
provided.</p>

<ul>
  <li>Management (Mgt) - All servers must have an interface identified as a Management interface. This is where all backups, monitoring, system level logins, and such are allowed.</li>
  <li>Secure Shell (SSH) - If the Management interface is not used for ssh access, this checkbox identifies the alternate interface used for logging in via ssh.</li>
  <li>Backups (Bkup) - If the Management interface is not used for backups, this checkbox identifies the alternate interface used for backups. Note that the backup tab identifies which systems are backed up.</li>
  <li>Monitoring (OMI) - This checkbox identifies the interface used to officially monitor the system.</li>
  <li>Nagios (Nag) - This is a special case used by the Unix team to monitor servers that aren't Production or otherwise monitored by the monitoring team.</li>
</ul>

<p>Note: The server names are clickable links to the server detail pages. Fields that are <u>underscored</u> are editable.</p>

</div>

</div>

</form>

<div id="tabs">

<ul>
  <li><a href="#tree">System Tree</a></li>
  <li><a href="#detail">System Information</a></li>
  <li><a href="#hardware">Hardware Details</a></li>
  <li><a href="#interface">Interface Information</a></li>
</ul>

<div id="tree">

<span id="tree_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="detail">

<span id="detail_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="hardware">

<span id="hardware_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="interface">

<span id="interface_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
