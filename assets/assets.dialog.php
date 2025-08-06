<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Asset Name or Label: <input type="text" name="ast_name" size="50"></td>
</tr>
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
#  $q_string .= "where mod_primary = 1 ";
  $q_string .= "order by ven_name,mod_name ";
  $q_inv_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_models = mysqli_fetch_array($q_inv_models)) {
    print "<option value=\"" . $a_inv_models['mod_id'] . "\">" . $a_inv_models['ven_name'] . " " . $a_inv_models['mod_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Starting Unit: <input type="text" name="ast_unit" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Parent: <select name="ast_parentid">
<?php
  $q_string  = "select ast_id,ast_name,ast_serial,ast_asset,mod_name,ven_name ";
  $q_string .= "from inv_assets ";
  $q_string .= "left join inv_models on inv_models.mod_id = inv_assets.ast_modelid ";
  $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
  $q_string .= "where ast_name != \"\" ";
  $q_string .= "order by mod_name ";
  $q_inv_assets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  print "<option value=\"0\">None</option>\n";
  while ($a_inv_assets = mysqli_fetch_array($q_inv_assets)) {
    print "<option value=\"" . $a_inv_assets['ast_id'] . "\">" . $a_inv_assets['ast_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Recorded in Company Asset Management System: <input type="checkbox" name="ast_managed"></label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Recorded at Vendor Site: <input type="checkbox" name="ast_vendor"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Support End Date: <input type="date" name="ast_endsupport"></td>
</tr>
</table>
