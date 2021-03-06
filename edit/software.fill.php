<?php
# Script: software.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Fill in the table for editing.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "software.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from software");

      $q_string  = "select sw_id,sw_companyid,sw_vendor,sw_product,sw_software,sw_type,sw_group,sw_eol,sw_cert,";
      $q_string .= "sw_licenseid,sw_supportid,sw_department,sw_facing,sw_notification,sw_primary,sw_eolticket,sw_locked ";
      $q_string .= "from software ";
      $q_string .= "where sw_id = " . $formVars['id'];
      $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_software = mysqli_fetch_array($q_software);
      mysqli_free_result($q_software);

      $group         = return_Index($db, $a_software['sw_group'],       "select grp_id from a_groups where grp_disabled = 0 order by grp_name");
      $product       = return_Index($db, $a_software['sw_product'],     "select prod_id from products where prod_id != 0 order by prod_name");
      $cert          = return_Index($db, $a_software['sw_cert'],        "select cert_id from certs order by cert_url");
      $support       = return_Index($db, $a_software['sw_supportid'],   "select sup_id from support order by sup_company,sup_contract");
      $license       = return_Index($db, $a_software['sw_licenseid'],   "select lic_id from licenses left join products on products.prod_id = licenses.lic_project order by prod_name,lic_key,lic_id");
      $department    = return_Index($db, $a_software['sw_department'],  "select dep_id from department order by dep_unit,dep_name");

      print "document.edit.sw_vendor.value = '"       . mysqli_real_escape_string($db, $a_software['sw_vendor'])       . "';\n";
      print "document.edit.sw_software.value = '"     . mysqli_real_escape_string($db, $a_software['sw_software'])     . "';\n";
      print "document.edit.sw_type.value = '"         . mysqli_real_escape_string($db, $a_software['sw_type'])         . "';\n";
      print "document.edit.sw_notification.value = '" . mysqli_real_escape_string($db, $a_software['sw_notification']) . "';\n";
      print "document.edit.sw_eol.value = '"          . mysqli_real_escape_string($db, $a_software['sw_eol'])          . "';\n";
      print "document.edit.sw_eolticket.value = '"    . mysqli_real_escape_string($db, $a_software['sw_eolticket'])    . "';\n";

      if ($group > 0) {
        print "document.edit.sw_group['"      . $group      . "'].selected = true;\n";
      }
      if ($product > 0) {
        print "document.edit.sw_product['"    . $product    . "'].selected = true;\n";
      }
      if ($cert > 0) {
        print "document.edit.sw_cert['"       . $cert       . "'].selected = true;\n";
      }
      if ($support > 0) {
        print "document.edit.sw_supportid['"  . $support    . "'].selected = true;\n";
      }
      if ($license > 0) {
        print "document.edit.sw_licenseid['"  . $license    . "'].selected = true;\n";
      }
      if ($department > 0) {
        print "document.edit.sw_department['" . $department . "'].selected = true;\n";
      }

      if ($a_software['sw_facing']) {
        print "document.edit.sw_facing.checked = true;\n";
      } else {
        print "document.edit.sw_facing.checked = false;\n";
      }
      if ($a_software['sw_primary']) {
        print "document.edit.sw_primary.checked = true;\n";
      } else {
        print "document.edit.sw_primary.checked = false;\n";
      }
      if ($a_software['sw_locked']) {
        print "document.edit.sw_locked.checked = true;\n";
      } else {
        print "document.edit.sw_locked.checked = false;\n";
      }

      print "document.edit.sw_id.value = " . $formVars['id'] . ";\n";

      print "document.edit.sw_update.disabled = false;\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
