<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Device: <select name="fib_deviceid">
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
  <td class="ui-widget-content">Name: <input type="text" name="fib_name" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Drop Type: <select name="fib_type">
<?php
  $q_string  = "select ft_id,ft_name ";
  $q_string .= "from inv_fibertype ";
  $q_string .= "order by ft_name ";
  $q_inv_fibertype = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_fibertype = mysqli_fetch_array($q_inv_fibertype)) {
    print "<option value=\"" . $a_inv_fibertype['ft_id'] . "\">" . $a_inv_fibertype['ft_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Is the Drop Actively in use? <input type="checkbox" name="fib_active"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Office: <input type="text" name="fib_office" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="fib_desc" size="50"></td>
</tr>
</table>
