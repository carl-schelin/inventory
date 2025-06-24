<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content" colspan="2">Tag: <input type="text" name="tag_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Server: <select name="tag_companyid">
<?php
$q_string  = "select inv_id,inv_name ";
$q_string .= "from inv_inventory ";
$q_string .= "where inv_status = 0 ";
if ($_SESSION['p_group'] > 0) {
    $q_string .= "and inv_manager = " . $_SESSION['p_group'] . " ";
}
$q_string .= "order by inv_name ";
$q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
    print "<option value=\"" . $a_inv_inventory['inv_id'] . "\">" . $a_inv_inventory['inv_name'] . "</option>\n";
}
?>
</select> Select All Servers to create a Master Tag.</td>
</tr>
<tr>
  <td class="ui-widget-content">Owner: <select name="tag_owner">
<?php
$q_string  = "select usr_id,usr_last,usr_first ";
$q_string .= "from inv_users ";
$q_string .= "where usr_disabled = 0 ";
$q_string .= "order by usr_last,usr_first ";
$q_inv_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
    print "<option value=\"" . $a_inv_users['usr_id'] . "\">" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "</option>\n";
}
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Group: <select name="tag_group">
<?php
$q_string  = "select grp_id,grp_name ";
$q_string .= "from inv_groups ";
$q_string .= "where grp_disabled = 0 ";
$q_string .= "order by grp_name ";
$q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
}
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="applytoall"> Add this Tag definition to all servers in this listing?</label></td>
</tr>
</table>
