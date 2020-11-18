<?php
# Script: hardware.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "hardware.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Add New Hardware</title>

<style type='text/css' title='currentStyle' media='screen'>
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
  var answer = confirm("Delete this Equipment?")

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
  var af_form = document.hardware;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&mod_vendor="     + encode_URI(af_form.mod_vendor.value);
  af_url += "&mod_name="       + encode_URI(af_form.mod_name.value);
  af_url += "&mod_type="       + af_form.mod_type.value;
  af_url += "&mod_size="       + encode_URI(af_form.mod_size.value);
  af_url += "&mod_speed="      + encode_URI(af_form.mod_speed.value);
  af_url += "&mod_eopur="      + encode_URI(af_form.mod_eopur.value);
  af_url += "&mod_eoship="     + encode_URI(af_form.mod_eoship.value);
  af_url += "&mod_eol="        + encode_URI(af_form.mod_eol.value);
  af_url += "&mod_plugs="      + encode_URI(af_form.mod_plugs.value);
  af_url += "&mod_plugtype="   + af_form.mod_plugtype.value;
  af_url += "&mod_volts="      + af_form.mod_volts.value;
  af_url += "&mod_draw="       + encode_URI(af_form.mod_draw.value);
  af_url += "&mod_start="      + encode_URI(af_form.mod_start.value);
  af_url += "&mod_btu="        + encode_URI(af_form.mod_btu.value);
  af_url += "&mod_virtual="    + af_form.mod_virtual.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function show_btu() {
  var volts = 0;
  if (document.hardware.mod_volts[1].selected == true) {
    volts = 110;
  }
  if (document.hardware.mod_volts[2].selected == true) {
    volts = 220;
  }
  if (document.hardware.mod_volts[3].selected == true) {
    volts = 208;
  }
  if (document.hardware.mod_volts[4].selected == true) {
    volts = 48;
  }
  document.hardware.mod_btu.value = document.hardware.mod_draw.value * volts * 3.413;
}

function clear_fields() {
  show_file('hardware.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="hardware">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('hardware-hide');">Hardware Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('detail-help');">Help</a></th>
</tr>
</table>

<div id="detail-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Model</strong> - Save any changes to this form.</li>
    <li><strong>Add Model</strong> - Add a new Model to the system.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Hardware Form</strong>
  <ul>
    <li><strong>Vendor</strong> - The vendor of this equipment.</li>
    <li><strong>Model</strong> - The model information of this equipment.</li>
    <li><strong>Virtual</strong> - Is this equipment virtual. This flag is used in other places in the system to show or hide information.</li>
  </ul></li>
  <li><strong>End of Service Form</strong>
  <ul>
    <li><strong>End of Purchase</strong> - The last date you are able to purchase this equipment.</li>
    <li><strong>End of Ship</strong> - The last date the vendor will ship this equipment.</li>
    <li><strong>End of Life</strong> - When this equipment is not supported any more.</li>
  </ul></li>
  <li><strong>Power Form</strong>
  <ul>
    <li><strong>Plugs</strong> - List the number of plugs this system has.</li>
    <li><strong>Plugtype</strong> - Select the plug time from the menu.</li>
    <li><strong>Volts</strong> - Select the number of volts this equipment uses.</li>
    <li><strong>Start</strong> - Enter the startup amperage draw.</li>
    <li><strong>Draw</strong> - Enter the running amperage draw.</li>
  </ul></li>
</ul>

</div>

</div>


<div id="hardware-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" disabled="true" name="update" value="Update Model" onClick="javascript:attach_file('hardware.mysql.php', 1);hideDiv('hardware-hide');">
<input type="hidden" name="id" value="0">
<input type="button"                 name="addbtn" value="Add Model"    onClick="javascript:attach_file('hardware.mysql.php', 0);">
</td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="12">Hardware Form</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="4">Vendor: <input type="text" name="mod_vendor" size="30"></td>
  <td class="ui-widget-content" colspan="4">Model: <input type="text" name="mod_name" size="30"></td>
  <td class="ui-widget-content" colspan="4"><label>Virtual? <input type="checkbox" name="mod_virtual"></label></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3" title="Hardware type">Type <select name="mod_type">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select part_id,part_name ";
  $q_string .= "from parts ";
  $q_string .= "order by part_name";
  $q_parts = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_parts = mysqli_fetch_array($q_parts)) {
    print "<option value=\"" . $a_parts['part_id'] . "\">" . $a_parts['part_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content" colspan="3" title="Such as number of CPUs, Disk capacity, or Server Unit Size">Size: <input type="text" name="mod_size" size="20"></td>
  <td class="ui-widget-content" colspan="3" title="Such as MHz or GHz speed or Disk speed">Speed: <input type="text" name="mod_speed" size="20"></td>
  <td class="ui-widget-content" colspan="3" title="How much heat does the device produce">BTU: <input type="number" name="mod_btu" size="20"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">End of Service Form</th>
</tr>
<tr>
  <td class="ui-widget-content">End of Purchase: <input type="date" name="mod_eopur" size="12"></td>
  <td class="ui-widget-content">End of Ship: <input type="date" name="mod_eoship" size="12"></td>
  <td class="ui-widget-content">End of Life: <input type="date" name="mod_eol" size="12"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Power Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Plugs: <input type="number" name="mod_plugs" size="3"></td>
  <td class="ui-widget-content">Plugtype: <select name="mod_plugtype">
<option value="0">None</option>
<?php
  $q_string  = "select plug_id,plug_text ";
  $q_string .= "from int_plugtype ";
  $q_string .= "order by plug_id";
  $q_int_plugtype = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_int_plugtype = mysqli_fetch_array($q_int_plugtype)) {
    print "<option value=\"" . $a_int_plugtype['plug_id'] . "\">" . $a_int_plugtype['plug_text'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Volts: <select name="mod_volts" onchange="show_btu();">
<option value="0">None</option>
<?php
  $q_string  = "select volt_id,volt_text ";
  $q_string .= "from int_volts ";
  $q_string .= "order by volt_id";
  $q_int_volts = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_int_volts = mysqli_fetch_array($q_int_volts)) {
    print "<option value=\"" . $a_int_volts['volt_id'] . "\">" . $a_int_volts['volt_text'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Start: <input type="number" name="mod_start" size="8"></td>
  <td class="ui-widget-content">Draw: <input type="number" name="mod_draw" size="8" onchange="show_btu();"></td>
</tr>
</table>

</div>

</form>

<p></p>

<div id="tabs">

<ul>
  <li><a href="#servers">Servers</a></li>
  <li><a href="#disks">Hard Disks</a></li>
  <li><a href="#cpus">CPUs</a></li>
  <li><a href="#memory">Memory</a></li>
  <li><a href="#misc">Miscellaneous</a></li>
</ul>

<div id="servers">

<span id="server_mysql"></span>

</div>


<div id="disks">

<span id="disk_mysql"></span>

</div>


<div id="cpus">

<span id="cpu_mysql"></span>

</div>


<div id="memory">

<span id="memory_mysql"></span>

</div>


<div id="misc">

<span id="misc_mysql"></span>

</div>


</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
