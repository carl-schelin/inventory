<?php
# Script: handoff.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login($AL_Edit);

  $package = "handoff.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing Handoffs");

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = $_SESSION['group'];
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Handoff Manager</title>

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
  var answer = confirm("Delete this entry?")

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
  var af_form = document.handoff;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&off_handoff="    + encode_URI(af_form.off_handoff.value);
  af_url += "&off_user="       + af_form.off_user.value;
  af_url += "&off_group="      + af_form.off_group.value;
  af_url += "&off_timestamp="  + encode_URI(af_form.off_timestamp.value);
  af_url += "&off_disabled="   + af_form.off_disabled.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('handoff.mysql.php?update=-1&off_group=<?php print $formVars['group']; ?>');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<form name="handoff">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('handoff-hide');">Handoff Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('handoff-help');">Help</a></th>
</tr>
</table>

<div id="handoff-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Handoff</strong> - Save any changes to the details of this Handoff.</li>
    <li><strong>Add Handoff</strong> - Create a new Handoff detail.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Handoff Form</strong>
  <ul>
    <li><strong>User</strong> Enter the Company Name.</li>
    <li><strong>Group</strong> Enter a Phone Number.</li>
    <li><strong>Date</strong> Enter Phone Tree options.</li>
    <li><strong>Handoff Detail</strong> Enter any information on how to contact the site if the main Contact fails.</li>
</ul>

</div>

</div>

<div id="handoff-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" disabled="true" name="update"   value="Update Handoff" onClick="javascript:attach_file('handoff.mysql.php', 1);hideDiv('handoff-hide');">
<input type="hidden" name="id" value="0">
<input type="button"                 name="vlandata" value="Add Handoff"    onClick="javascript:attach_file('handoff.mysql.php', 0);"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Handoff Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Select a User: <select name="off_user">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_users = mysqli_fetch_array($q_users)) {
    if ($_SESSION['uid'] == $a_users['usr_id']) {
      print "<option selected value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>";
    } else {
      print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>";
    }
  }
?>
</select></td>
  <td class="ui-widget-content">Select a Group: <select name="off_group">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    if ($_SESSION['group'] == $a_groups['grp_id']) {
      print "<option selected value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>";
    } else {
      print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>";
    }
  }
?>
</select></td>
  <td class="ui-widget-content" title="Leave Timestamp field set to Current Time to use current time, otherwise use YYYY-MM-DD HH:MM:SS.">Timestamp: <input type="text" name="off_timestamp" value="Current Time" size="23"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3"><textarea id="off_handoff" name="off_handoff" cols=130 rows=10
    onKeyDown="textCounter(document.handoff.off_handoff, document.handoff.remLen, 1024);"
    onKeyUp  ="textCounter(document.handoff.off_handoff, document.handoff.remLen, 1024);">
</textarea>
<br><input readonly type="text" name="remLen" size="5" maxlength="5" value="1024"> characters left</td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3"><label>Disable This Handoff? <input type="checkbox" name="off_disabled"></label></td>
</tr>
</table>

</div>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
