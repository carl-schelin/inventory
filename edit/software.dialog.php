
<div id="dialogSoftwareCreate" title="Add Software">

<form name="formSoftwareCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Group: <select name="svr_groupid">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    if ($_SESSION['group'] == $a_inv_groups['grp_id']) {
      print "<option selected value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
    } else {
      print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
    }
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Software: <select name="svr_softwareid">
<?php
  $q_string  = "select sw_id,sw_software,prod_name ";
  $q_string .= "from software ";
  $q_string .= "left join inv_products on inv_products.prod_id = software.sw_product ";
  $q_string .= "order by sw_software,prod_name";
  $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_software = mysqli_fetch_array($q_software)) {
    print "<option value=\"" . $a_software['sw_id'] . "\">" . $a_software['sw_software'] . " (" . $a_software['prod_name'] . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">RHEL Yum Versionlocked? <input type="checkbox" name="svr_locked"></td>
</tr>
<tr>
  <td class="ui-widget-content">SSL Certificate <select name="svr_certid">
<?php
  $q_string  = "select cert_id,cert_desc ";
  $q_string .= "from inv_certs ";
  $q_string .= "where cert_ca = 0 ";
  $q_string .= "order by cert_desc";
  $q_inv_certs = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_certs = mysqli_fetch_array($q_inv_certs)) {
    print "<option value=\"" . $a_inv_certs['cert_id'] . "\">" . $a_inv_certs['cert_desc'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="svr_primary"> Software Defines The System?</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="svr_facing"> Software is Customer Facing?</label></td>
</table>

</form>

</div>


<div id="dialogSoftwareUpdate" title="Edit Software">

<form name="formSoftwareUpdate">

<input type="hidden" name="svr_id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Group: <select name="svr_groupid">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    if ($_SESSION['group'] == $a_inv_groups['grp_id']) {
      print "<option selected value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
    } else {
      print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
    }
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Software: <select name="svr_softwareid">
<?php
  $q_string  = "select sw_id,sw_software,prod_name ";
  $q_string .= "from software ";
  $q_string .= "left join inv_products on inv_products.prod_id = software.sw_product ";
  $q_string .= "order by sw_software,prod_name";
  $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_software = mysqli_fetch_array($q_software)) {
    print "<option value=\"" . $a_software['sw_id'] . "\">" . $a_software['sw_software'] . " (" . $a_software['prod_name'] . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">RHEL Yum Versionlocked? <input type="checkbox" name="svr_locked"></td>
</tr>
<tr>
  <td class="ui-widget-content">SSL Certificate <select name="svr_certid">
<?php
  $q_string  = "select cert_id,cert_desc ";
  $q_string .= "from inv_certs ";
  $q_string .= "where cert_ca = 0 ";
  $q_string .= "order by cert_desc";
  $q_inv_certs = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_certs = mysqli_fetch_array($q_inv_certs)) {
    print "<option value=\"" . $a_inv_certs['cert_id'] . "\">" . $a_inv_certs['cert_desc'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="svr_primary"> Software Defines The System?</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="svr_facing"> Software is Customer Facing?</label></td>
</table>

</form>

</div>

