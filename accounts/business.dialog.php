<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Business Name: <input type="text" name="bus_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Organization: <select name="bus_organization">
<?php
  $q_string  = "select org_id,org_name ";
  $q_string .= "from inv_organizations ";
  $q_string .= "order by org_name ";
  $q_inv_organizations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_organizations)) {
    while ($a_inv_organizations = mysqli_fetch_array($q_inv_organizations)) {
      print "<option value=\"" . $a_inv_organizations['org_id'] . "\">" . $a_inv_organizations['org_name'] . "</option>\n";
    }
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Manager: <select name="bus_manager">
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
</table>
