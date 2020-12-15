<?php
# Script: type.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "type.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing the Interface Type table");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Interface Types</title>

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
  var answer = confirm("Delete this Interface Type?")

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
  var af_form = document.interfacetype;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&itp_name="          + encode_URI(af_form.itp_name.value);
  af_url += "&itp_acronym="       + encode_URI(af_form.itp_acronym.value);
  af_url += "&itp_description="   + encode_URI(af_form.itp_description.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('type.mysql.php?update=-1');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<form name="interfacetype">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('type-hide');">Interface Type Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('type-help');">Help</a></th>
</tr>
</table>

<div id="type-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Type</strong> - Save any changes to this form.</li>
    <li><strong>Add Type</strong> - Add a new Interface Type.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Interface Type Form</strong>
  <ul>
    <li><strong>Acronym</strong> - The acronym used to describe the interface.</li>
    <li><strong>Name</strong> - The full descriptive name of the interface type.</li>
    <li><strong>Description</strong> - Details about the interface type.</li>
  </ul></li>
</ul>

</div>

</div>


<div id="type-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" disabled="true" name="update" value="Update Type" onClick="javascript:attach_file('type.mysql.php', 1);hideDiv('type-hide');">
<input type="hidden" name="id" value="0">
<input type="button"                 name="addbtn" value="Add Type"    onClick="javascript:attach_file('type.mysql.php', 0);">
</td>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Interface Type Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Acronym <input type="text" name="itp_acronym" size="10"></td>
  <td class="ui-widget-content">Name <input type="text" name="itp_name" size="30"></td>
  <td class="ui-widget-content">Description <input type="text" name="itp_description" size="80"></td>
</tr>
</table>

</div>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
