<?php
# Script: license.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "license.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage License Keys</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">
<?php
  if (check_userlevel($db, $AL_Admin)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Delete this License?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}
<?php
  }
?>

function attach_file( p_script_url, update ) {
  var af_form = document.license;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&lic_vendor="    + encode_URI(af_form.lic_vendor.value);
  af_url += "&lic_product="   + encode_URI(af_form.lic_product.value);
  af_url += "&lic_date="      + encode_URI(af_form.lic_date.value);
  af_url += "&lic_quantity="  + encode_URI(af_form.lic_quantity.value);
  af_url += "&lic_vendorpo="  + encode_URI(af_form.lic_vendorpo.value);
  af_url += "&lic_po="        + encode_URI(af_form.lic_po.value);
  af_url += "&lic_project="   + encode_URI(af_form.lic_project.value);
  af_url += "&lic_key="       + encode_URI(af_form.lic_key.value);
  af_url += "&lic_serial="    + encode_URI(af_form.lic_serial.value);
  af_url += "&lic_domain="    + encode_URI(af_form.lic_domain.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('license.mysql.php?update=-1');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<form name="license">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('license-hide');">License Key Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('license-help');">Help</a></th>
</tr>
</table>

<div id="license-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update License</strong> - Save any changes to this form.</li>
    <li><strong>Add License</strong> - Add a new License.</li>
  </ul></li>
</ul>

</div>

</div>

<div id="license-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" disabled="true" name="update" value="Update License" onClick="javascript:attach_file('license.mysql.php', 1);hideDiv('license-hide');">
<input type="hidden" name="id" value="0">
<input type="button"                 name="addnew" value="Add License"    onClick="javascript:attach_file('license.mysql.php', 0);">
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="8">License Key Form</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Vendor: <input type="text" name="lic_vendor" size="15"></td>
  <td class="ui-widget-content" colspan="2">Product: <input type="text" name="lic_product" size="15"></td>
  <td class="ui-widget-content" colspan="2">Date Acquired: <input type="date" name="lic_date" size="15"></td>
  <td class="ui-widget-content" colspan="2">Quantity: <input type="number" name="lic_quantity" size="5"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Vendor PO Number: <input type="text" name="lic_vendorpo" size="10"></td>
  <td class="ui-widget-content" colspan="2">Company PO Number: <input type="text" name="lic_po" size="10"></td>
  <td class="ui-widget-content" colspan="4">Project: <select name="lic_project">
<option value="0">No Project</option>
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from products ";
  $q_string .= "order by prod_name";
  $q_products = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_products = mysqli_fetch_array($q_products)) {
    print "<option value=\"" . $a_products['prod_id'] . "\">" . $a_products['prod_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">License Key: <input type="text" name="lic_key" size="30"></td>
  <td class="ui-widget-content" colspan="2">Serial No: <input type="text" name="lic_serial" size="20"></td>
  <td class="ui-widget-content" colspan="4">Domain: <input type="text" name="lic_domain" size="32"></td>
</tr>
</table>

</div>

</form>

<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
