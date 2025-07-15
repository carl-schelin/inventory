<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Asset Tag: <input type="text" name="ast_asset" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Serial Number: <input type="text" name="ast_serial" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Model: <select name="ast_modelid">
<?php
  $q_string  = "select mod_id,mod_name,ven_name ";
  $q_string .= "from inv_models ";
  $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
  $q_string .= "where mod_primary = 1 ";
  $q_string .= "order by mod_name ";
  $q_inv_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_models = mysqli_fetch_array($q_inv_models)) {
    print "<option value=\"" . $a_inv_models['mod_id'] . "\">" . $a_inv_models['ven_name'] . " " . $a_inv_models['mod_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Device: <input type="text" name="ast_device" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Parent ID: <input type="text" name="ast_parentid" size="12"></td>
</tr>
</table>
