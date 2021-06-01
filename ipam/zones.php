<?php
# Script: zones.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "zones.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Network Zones</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

<?php
  if (check_userlevel($db, $AL_Admin)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Delete this Network Zone?")

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
  var af_form = document.zones;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&zone_name="       + encode_URI(af_form.zone_name.value);
  af_url += "&zone_desc="       + encode_URI(af_form.zone_desc.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('zones.mysql.php?update=-1');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<form name="zones">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('zone-hide');">Zone Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('zone-help');">Help</a></th>
</tr>
</table>

<div id="zone-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Zone</strong> - Save any changes to this form.</li>
    <li><strong>Add Zone</strong> - Add a new Network Zone.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Zone Form</strong>
  <ul>
    <li><strong>Network Zone</strong> - The short name of the Network Zone. This is used in drop down menus and displays in the inventory.</li>
    <li><strong>Description</strong> - A more detailed description of the Network Zone.</li>
  </ul></li>
</ul>

</div>

</div>


<div id="zone-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" disabled="true" name="update" value="Update Zone" onClick="javascript:attach_file('zones.mysql.php', 1);hideDiv('zone-hide');">
<input type="hidden" name="id" value="0">
<input type="button"                 name="addbtn" value="Add Zone"    onClick="javascript:attach_file('zones.mysql.php', 0);">
</td>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Zone Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Network Zone <input type="text" name="zone_name" size="30"></td>
  <td class="ui-widget-content">Description <input type="text" name="zone_desc" size="80"></td>
</tr>
</table>

</div>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
