<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Product <select name="prj_product">
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from inv_products ";
  $q_string .= "order by prod_name";
  $q_inv_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_products = mysqli_fetch_array($q_inv_products)) {
    print "<option value=\"" . $a_inv_products['prod_id'] . "\">" . $a_inv_products['prod_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="prj_name" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Project Code: <input type="text" name="prj_code" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Task: <input type="text" name="prj_task" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="prj_desc" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Terraform: <input type="text" name="prj_directory" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Group Ownership: <select name="prj_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "order by grp_name ";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>
