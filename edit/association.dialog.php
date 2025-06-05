<div id="dialogAssociationCreate" title="Edit Association">

<form name="formAssociationCreate">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Association Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Associate With: <select name="clu_association">
<option value="0">None</option>
<?php
  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inv_inventory ";
  $q_string .= "where inv_status = 0 ";
  $q_string .= "order by inv_name ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
    print "<option value=\"" . $a_inv_inventory['inv_id'] . "\">" . $a_inv_inventory['inv_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Association Type: <select name="clu_type">
<option value="0">None</option>
<option value="1">NFS</option>
<option value="2">Samba</option>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Source Mount Point <input type="text" name="clu_source" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Target Mount Point <input type="text" name="clu_target" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">NFS Options <input type="text" name="clu_options" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Notes <input type="text" name="clu_notes" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Port <input type="text" name="clu_port" size="10">/Protocol <input type="text" name="clu_protocol" size="10"></td>
</tr>
</table>

</form>

</div>


<div id="dialogAssociationUpdate" title="Edit Association">

<form name="formAssociationUpdate">

<input type="hidden" name="clu_id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Association Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Associate With: <select name="clu_association">
<option value="0">None</option>
<?php
  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inv_inventory ";
  $q_string .= "where inv_status = 0 ";
  $q_string .= "order by inv_name ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
    print "<option value=\"" . $a_inv_inventory['inv_id'] . "\">" . $a_inv_inventory['inv_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Association Type: <select name="clu_type">
<option value="0">None</option>
<option value="1">NFS</option>
<option value="2">Samba</option>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Source Mount Point <input type="text" name="clu_source" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Target Mount Point <input type="text" name="clu_target" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">NFS Options <input type="text" name="clu_options" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Notes <input type="text" name="clu_notes" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Port <input type="text" name="clu_port" size="10">/Protocol <input type="text" name="clu_protocol" size="10"></td>
</tr>
</table>

</form>

</div>
