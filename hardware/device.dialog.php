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
  <td class="ui-widget-content">Model: <input type="text" name="mod_name" size="50"> <label>Virtual Machine? <input type="checkbox" name="mod_virtual"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Device Type <select name="mod_type">
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
  <td class="ui-widget-content">Brief Description: <input type="text" name="mod_desc" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Height in Units: <input type="text" name="mod_height" size="10">U</td>
</tr>
<tr>
  <td class="ui-widget-content">Weight of Device: <input type="text" name="mod_weight" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Full Rack Depth: <input type="checkbox" name="mod_depth"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Front Image: <select name="mod_front">
<option value="0">None</option>
<?php
  $q_string  = "select img_id,img_title ";
  $q_string .= "from inv_images ";
  $q_string .= "where img_facing = 1 ";
  $q_string .= "order by img_title ";
  $q_inv_images = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_images = mysqli_fetch_array($q_inv_images)) {
    print "<option value=\"" . $a_inv_images['img_id'] . "\">" . $a_inv_images['img_title'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Rear Image: <select name="mod_rear">
<option value="0">None</option>
<?php
  $q_string  = "select img_id,img_title ";
  $q_string .= "from inv_images ";
  $q_string .= "where img_facing = 0 ";
  $q_string .= "order by img_title ";
  $q_inv_images = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_images = mysqli_fetch_array($q_inv_images)) {
    print "<option value=\"" . $a_inv_images['img_id'] . "\">" . $a_inv_images['img_title'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <th class="ui-state-default">Power Management</th>
</tr>
<tr>
  <td class="ui-widget-content">Number of Power Supplies: <input type="text" name="mod_plugs" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Type of Power Connection: <select name="mod_plugtype">
<option value="0">None</option>
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
  <td class="ui-widget-content">Voltage: <select name="mod_volts">
<option value="0">None</option>
<?php
  $q_string  = "select volt_id,volt_text ";
  $q_string .= "from inv_int_volts ";
  $q_string .= "order by volt_text ";
  $q_inv_int_volts = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_int_volts = mysqli_fetch_array($q_inv_int_volts)) {
    print "<option value=\"" . $a_inv_int_volts['volt_id'] . "\">" . $a_inv_int_volts['volt_text'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Power During Normal Operation (Draw): <input type="text" name="mod_draw" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Power When The Device Starts: <input type="text" name="mod_start" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Heat Generated in BTUs: <input type="text" name="mod_btu" size="10"></td>
</tr>
<tr>
  <th class="ui-state-default">Device Life Cycle</th>
</tr>
<tr>
  <td class="ui-widget-content">Last Date device can be purchased: <input type="date" name="mod_eopur" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Last Date when device can be shipped: <input type="date" name="mod_eoship" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Official End of Life Date: <input type="date" name="mod_eol" size="12"></td>
</tr>
</table>
