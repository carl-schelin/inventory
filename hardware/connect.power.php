<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Source Port: <select name="con_sourceid">
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
</select> For power, this is the server power supply.</td>
</tr>
<tr>
  <td class="ui-widget-content">Destination Port: <select name="con_targetid">
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
</select> For power, this is an outlet on a UPS or wall.</td>
</tr>
<tr>
  <td class="ui-widget-content">Connection Type: <select name="con_type">
<?php
  $q_string  = "select pt_id,pt_name ";
  $q_string .= "from inv_powertype ";
  $q_string .= "order by pt_name ";
  $q_inv_powertype = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_powertype = mysqli_fetch_array($q_inv_powertype)) {
    print "<option value=\"" . $a_inv_powertype['pt_id'] . "\">" . $a_inv_powertype['pt_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>
