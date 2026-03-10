<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Add user <select name="gpl_user">
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
  <td class="ui-widget-content">To group <select name="gpl_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "left join inv_grouplist on inv_grouplist.gpl_group = inv_groups.grp_id ";
  if (check_userlevel($db, $AL_Admin) == 0) {
    $q_string .= "where gpl_user = " . $_SESSION['uid'] . " ";
  }
  $q_string .= "group by grp_name ";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="gpl_edit"> Let this user edit your assets?</label></td>
</tr>
</table>
