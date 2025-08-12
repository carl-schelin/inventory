<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Vendor: <select name="mod_vendor">
<?php
  $q_string  = "select ven_id,ven_name ";
  $q_string .= "from inv_vendors ";
  $q_string .= "order by ven_name";
  $q_inv_vendors = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_vendors = mysqli_fetch_array($q_inv_vendors)) {
    print "<option value=\"" . $a_inv_vendors['ven_id'] . "\">" . $a_inv_vendors['ven_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Model: <input type="text" name="mod_name" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Hardware Type: <select name="mod_type">
<?php
  $q_string  = "select part_id,part_name ";
  $q_string .= "from inv_parts ";
  $q_string .= "order by part_name";
  $q_inv_parts = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_parts = mysqli_fetch_array($q_inv_parts)) {
    print "<option value=\"" . $a_inv_parts['part_id'] . "\">" . $a_inv_parts['part_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Purchase: <input type="date" name="mod_eopur" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Shipping: <input type="date" name="mod_eoship" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Life: <input type="date" name="mod_eol" size="12"></td>
</tr>
</table>
