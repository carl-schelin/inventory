<?php
# Script: security.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "security.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Add New Security</title>

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
  var answer = confirm("Delete this Security?")

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
  var af_form = document.security;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&sec_name="     + encode_URI(af_form.sec_name.value);
  af_url += "&sec_family="   + af_form.sec_family.value;
  af_url += "&sec_severity=" + af_form.sec_severity.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('security.mysql.php?update=-1');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="security">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('security-hide');">Security Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('security-help');">Help</a></th>
</tr>
</table>

<div id="security-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Model</strong> - Save any changes to this form.</li>
    <li><strong>Add Model</strong> - Add a new Model to the system.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Family Form</strong>
  <ul>
    <li><strong>Family</strong> - Enter the Family from SecurityCenter.</li>
  </ul></li>
</ul>

</div>

</div>


<div id="security-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" disabled="true" name="update" value="Update Security" onClick="javascript:attach_file('security.mysql.php', 1);hideDiv('security-hide');">
<input type="hidden" name="id" value="0">
<input type="button"                 name="addbtn" value="Add Security"    onClick="javascript:attach_file('security.mysql.php', 0);">
</td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Security Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Family: <select name="sec_family">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select fam_id,fam_name ";
  $q_string .= "from family ";
  $q_string .= "order by fam_name ";
  $q_family = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_family = mysqli_fetch_array($q_family)) {
    print "<option value=\"" . $a_family['fam_id'] . "\">" . $a_family['fam_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Severity: <select name="sec_severity">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select sev_id,sev_name ";
  $q_string .= "from severity ";
  $q_string .= "order by sev_name ";
  $q_severity = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_severity = mysqli_fetch_array($q_severity)) {
    print "<option value=\"" . $a_severity['sev_id'] . "\">" . $a_severity['sev_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Security: <input type="text" name="sec_name" size="80"></td>
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
