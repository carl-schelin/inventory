<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Vendor: <select name="lic_vendor">
<?php
  $q_string  = "select ven_id,ven_name ";
  $q_string .= "from inv_vendors ";
  $q_string .= "order by ven_name ";
  $q_inv_vendors = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_inv_vendors = mysqli_fetch_array($q_inv_vendors)) {
    print "<option value=\"" . $a_inv_vendors['ven_id'] . "\">" . $a_inv_vendors['ven_name'] . "</option>\n";
  }
?>
</select></td>
</tr>

<tr>
  <td class="ui-widget-content">License File: <select name="lic_parent">
<?php
  $q_string  = "select lic_id,lic_product ";
  $q_string .= "from inv_licenses ";
  $q_string .= "where lic_parent = 0 ";
  $q_string .= "order by lic_product ";
  $q_inv_licenses = mysqli_query($db, $q_string) or die(mysqli_error($db));
  print "<option value=\"0\">Parent</option>\n";
  while ($a_inv_licenses = mysqli_fetch_array($q_inv_licenses)) {
    print "<option value=\"" . $a_inv_licenses['lic_id'] . "\">" . $a_inv_licenses['lic_product'] . "</option>\n";
  }
?>
</tr>
<tr>
  <td class="ui-widget-content">Product: <input type="text" name="lic_product" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Company ID: <input type="text" name="lic_customer" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Date Acquired: <input type="date" name="lic_date" size="15"></td>
</tr>
<tr>
  <td class="ui-widget-content">Date Expires: <input type="date" name="lic_expire" size="15"></td>
</tr>
<tr>
  <td class="ui-widget-content">Quantity: <input type="number" name="lic_quantity" size="5"></td>
</tr>
<tr>
  <td class="ui-widget-content">Vendor PO Number: <input type="text" name="lic_vendorpo" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Company PO Number: <input type="text" name="lic_po" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Project: <select name="lic_project">
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from inv_products ";
  $q_string .= "order by prod_name";
  $q_inv_products = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_inv_products = mysqli_fetch_array($q_inv_products)) {
    print "<option value=\"" . $a_inv_products['prod_id'] . "\">" . $a_inv_products['prod_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">License Key: <input type="text" name="lic_key" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Serial No: <input type="text" name="lic_serial" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Domain: <input type="text" name="lic_domain" size="32"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Disable Notifications: <input type="checkbox" name="lic_status"></label></td>
</tr>
</table>
