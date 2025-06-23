<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Company: <input type="text" name="sup_company" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Hardware Response: <select name="sup_hwresponse">
<?php
  $q_string  = "select slv_id,slv_value ";
  $q_string .= "from inv_supportlevel ";
  $q_string .= "order by slv_value";
  $q_inv_supportlevel = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_supportlevel = mysqli_fetch_array($q_inv_supportlevel)) {
    print "<option value=\"" . $a_inv_supportlevel['slv_id'] . "\">" . htmlspecialchars($a_inv_supportlevel['slv_value']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Software Response: <select name="sup_swresponse">
<?php
  $q_string  = "select slv_id,slv_value ";
  $q_string .= "from inv_supportlevel ";
  $q_string .= "order by slv_value";
  $q_inv_supportlevel = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_supportlevel = mysqli_fetch_array($q_inv_supportlevel)) {
    print "<option value=\"" . $a_inv_supportlevel['slv_id'] . "\">" . htmlspecialchars($a_inv_supportlevel['slv_value']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Phone: <input type="text" name="sup_phone" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">E-Mail: <input type="email" name="sup_email" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Contract #: <input type="text" name="sup_contract" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Company Web Site: <input type="text" name="sup_web" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Wiki Site: <input type="text" name="sup_wiki" size="60"></td>
</tr>
</table>
