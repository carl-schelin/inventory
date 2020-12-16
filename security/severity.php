<?php
# Script: severity.php
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

  $package = "severity.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Add A New Severity</title>

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
  var answer = confirm("Delete this Severity?")

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
  var af_form = document.severity;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&sev_name="     + encode_URI(af_form.sev_name.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('severity.mysql.php?update=-1');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="severity">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('severity-hide');">Severity Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('severity-help');">Help</a></th>
</tr>
</table>

<div id="severity-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Model</strong> - Save any changes to this form.</li>
    <li><strong>Add Model</strong> - Add a new Model to the system.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Severity Form</strong>
  <ul>
    <li><strong>Severity</strong> - Enter the Severity from SecurityCenter.</li>
  </ul></li>
</ul>

</div>

</div>


<div id="severity-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" disabled="true" name="update" value="Update Severity" onClick="javascript:attach_file('severity.mysql.php', 1);hideDiv('severity-hide');">
<input type="hidden" name="id" value="0">
<input type="button"                 name="addbtn" value="Add Severity"    onClick="javascript:attach_file('severity.mysql.php', 0);">
</td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Severity Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Severity: <input type="text" name="sev_name" size="60"></td>
</tr>
</table>

</div>

</form>

<p></p>

<span id="table_mysql"></span>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
