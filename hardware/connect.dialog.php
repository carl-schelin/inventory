<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Source Power Supply (PS): <select name="con_sourceid">
<?php
  $q_string  = "select port_id,port_name,ast_name ";
  $q_string .= "from inv_ports ";
  $q_string .= "left join inv_assets on inv_assets.ast_id = inv_ports.port_deviceid ";
  $q_string .= "order by ast_name,port_name ";
  $q_inv_ports = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_ports = mysqli_fetch_array($q_inv_ports)) {
    print "<option value=\"" . $a_inv_ports['port_id'] . "\">" . $a_inv_ports['port_name'] . " (" . $a_inv_ports['ast_name'] . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Target Power Outlet: <select name="con_targetid">
<?php
  $q_string  = "select out_id,out_name,ast_name ";
  $q_string .= "from inv_outlets ";
  $q_string .= "left join inv_assets on inv_assets.ast_id = inv_outlets.out_deviceid ";
  $q_string .= "order by ast_name,out_name ";
  $q_inv_outlets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_outlets = mysqli_fetch_array($q_inv_outlets)) {
    print "<option value=\"" . $a_inv_outlets['out_id'] . "\">" . $a_inv_outlets['out_name'] . " (" . $a_inv_outlets['ast_name'] . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Connection Type: <select name="con_type">
  <option value="1">Network Interface</option>
  <option value="2">Fiber</option>
  <option value="3">Power</option>
</select></td>
</tr>
</table>
