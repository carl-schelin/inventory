<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Device: <select name="port_deviceid">
<?php
  $q_string  = "select ast_id,ast_name ";
  $q_string .= "from inv_assets ";
  $q_string .= "where ast_name != \"\" ";
  $q_string .= "order by ast_name ";
  $q_inv_assets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_assets = mysqli_fetch_array($q_inv_assets)) {
    print "<option value=\"" . $a_inv_assets['ast_id'] . "\">" . $a_inv_assets['ast_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="port_name" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Port Type: <select name="port_type">
<?php
  $q_string  = "select plug_id,plug_text ";
  $q_string .= "from inv_int_plugtype ";
  $q_string .= "order by plug_text ";
  $q_inv_int_plugtype = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_int_plugtype = mysqli_fetch_array($q_inv_int_plugtype)) {
    print "<option value=\"" . $a_inv_int_plugtype['plug_id'] . "\">" . $a_inv_int_plugtype['plug_text'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Is the Port Actively in use? <input type="checkbox" name="port_active"></label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Is the Port on the Front of the unit? <input type="checkbox" name="port_facing"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="port_desc" size="50"></td>
</tr>
</table>
