<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Department Name: <input type="text" name="dep_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Business: <select name="dep_business">
<?php
  $q_string  = "select bus_id,bus_name ";
  $q_string .= "from inv_business ";
  $q_string .= "order by bus_name ";
  $q_inv_business = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_business)) {
    while ($a_inv_business = mysqli_fetch_array($q_inv_business)) {
      print "<option value=\"" . $a_inv_business['bus_id'] . "\">" . $a_inv_business['bus_name'] . "</option>\n";
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
