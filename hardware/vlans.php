<?php
# Script: vlans.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "vlans.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>VLAN Management</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

<?php
  if (check_userlevel($AL_Admin)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Delete this VLan entry?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}
<?php
  }
?>

function attach_file( p_script_url, update ) {
  var af_form = document.vlans;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&vlan_vlan="        + encode_URI(af_form.vlan_vlan.value);
  af_url += "&vlan_zone="        + encode_URI(af_form.vlan_zone.value);
  af_url += "&vlan_name="        + encode_URI(af_form.vlan_name.value);
  af_url += "&vlan_description=" + encode_URI(af_form.vlan_description.value);
  af_url += "&vlan_range="       + encode_URI(af_form.vlan_range.value);
  af_url += "&vlan_gateway="     + encode_URI(af_form.vlan_gateway.value);
  af_url += "&vlan_netmask="     + encode_URI(af_form.vlan_netmask.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('vlans.mysql.php?update=-1');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<form name="vlans">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('vlan-hide');">VLan Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('vlan-help');">Help</a></th>
</tr>
</table>

<div id="vlan-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update VLAN</strong> - Save any changes to this form.</li>
    <li><strong>Add VLAN</strong> - Add a new VLan to the listing.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>VLan Form</strong>
  <ul>
    <li><strong>VLAN</strong> - The VLan Identifier used in drop down menus.</li>
    <li><strong>Network Zone</strong> - What network zone does this VLAN reside in.</li>
    <li><strong>Name</strong> - The Name of the VLan.</li>
    <li><strong>Description</strong> - A longer description of the VLan.</li>
    <li><strong>IP Range</strong> - The IP Range for this VLan.</li>
    <li><strong>Network Mask</strong> - The Netmask for IPs in this VLan.</li>
  </ul></li>
</ul>

</div>

</div>

<div id="vlan-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" disabled="true" name="update"   value="Update VLAN" onClick="javascript:attach_file('vlans.mysql.php', 1);hideDiv('vlan-hide');">
<input type="hidden" name="id" value="0">
<input type="button"                 name="vlandata" value="Add VLAN"    onClick="javascript:attach_file('vlans.mysql.php', 0);"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">VLan Form</th>
</tr>
<tr>
  <td class="ui-widget-content">VLAN: <input type="text" name="vlan_vlan" size="10"></td>
  <td class="ui-widget-content">Zone: <input type="text" name="vlan_zone" size="10"></td>
  <td class="ui-widget-content">IP: <input type="text" name="vlan_range" size="40"></td>
  <td class="ui-widget-content">Network Mask: <input type="text" name="vlan_netmask" size="5"></td>
  <td class="ui-widget-content">Gateway: <input type="text" name="vlan_gateway" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Name: <input type="text" name="vlan_name" size="40"></td>
  <td class="ui-widget-content" colspan="3">Description: <input type="text" name="vlan_description" size="40"></td>
</tr>
</table>

</div>

</form>

<span id="table_mysql"></span>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
