<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Department: <select name="grp_department">
<?php
  $q_string  = "select dep_id,dep_name ";
  $q_string .= "from inv_department ";
  $q_string .= "order by dep_name ";
  $q_inv_department = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_department = mysqli_fetch_array($q_inv_department)) {
    print "<option value=\"" . $a_inv_department['dep_id'] . "\">" . $a_inv_department['dep_name'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Group Name: <input type="text" name="grp_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">E-Mail: <input type="text" name="grp_email" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Manager: <select name="grp_manager">
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from inv_users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
    print "<option value=\"" . $a_inv_users['usr_id'] . "\">" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Status <select name="grp_disabled">
<option value="0">Enabled</option>
<option value="1">Disabled</option>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Receive Check Status Report? <input type="checkbox" name="grp_status"></label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Receive Check Server Report? <input type="checkbox" name="grp_server"></label></td>
<tr>
</tr>
  <td class="ui-widget-content"><label>Import Server Data? <input type="checkbox" name="grp_import"></label></td>
</tr>
</table>
