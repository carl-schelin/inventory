<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Source Port: <select name="con_sourceid">
<?php
  $q_string  = "select pat_id,pat_name,ast_name ";
  $q_string .= "from inv_patch ";
  $q_string .= "left join inv_assets on inv_assets.ast_id = inv_patch.pat_deviceid ";
  $q_string .= "order by ast_name,pat_name ";
  $q_inv_patch = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_patch = mysqli_fetch_array($q_inv_patch)) {
    print "<option value=\"" . $a_inv_patch['pat_id'] . "\">" . $a_inv_patch['pat_name'] . " (" . $a_inv_patch['ast_name'] . ")</option>\n";
  }
?>
</select> For cat5, this is the server port.</td>
</tr>
<tr>
  <td class="ui-widget-content">Destination Port: <select name="con_targetid">
<?php
  $q_string  = "select pat_id,pat_name,ast_name ";
  $q_string .= "from inv_patch ";
  $q_string .= "left join inv_assets on inv_assets.ast_id = inv_patch.pat_deviceid ";
  $q_string .= "order by pat_name,ast_name ";
  $q_inv_patch = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_patch = mysqli_fetch_array($q_inv_patch)) {
    print "<option value=\"" . $a_inv_patch['pat_id'] . "\">" . $a_inv_patch['pat_name'] . " (" . $a_inv_patch['ast_name'] . ")</option>\n";
  }
?>
</select> For cat5, this is the patch panel port.</td>
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
