<?php
# Script: license.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "license.fill.php";
    $formVars['id']    = clean($_GET['id'],    10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_licenses");

      $q_string  = "select lic_id,lic_parent,lic_vendor,lic_product,lic_customer,lic_date,lic_expire,lic_vendorpo,";
      $q_string .= "lic_po,lic_project,lic_quantity,lic_key,lic_serial,lic_domain,lic_status ";
      $q_string .= "from inv_licenses ";
      $q_string .= "where lic_id = " . $formVars['id'];
      $q_inv_licenses = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_inv_licenses = mysqli_fetch_array($q_inv_licenses);
      mysqli_free_result($q_inv_licenses);

      $licproject = return_Index($db, $a_inv_licenses['lic_project'], "select prod_id from inv_products order by prod_name");
      $licvendor  = return_Index($db, $a_inv_licenses['lic_vendor'], "select ven_id from inv_vendors order by ven_name");
      $licparent  = return_Index($db, $a_inv_licenses['lic_parent'], "select lic_id from inv_licenses where lic_parent = 0 order by lic_product");

      print "document.formUpdate.lic_product.value = '"  . mysqli_real_escape_string($db, $a_inv_licenses['lic_product'])  . "';\n";
      print "document.formUpdate.lic_date.value = '"     . mysqli_real_escape_string($db, $a_inv_licenses['lic_date'])     . "';\n";
      print "document.formUpdate.lic_expire.value = '"   . mysqli_real_escape_string($db, $a_inv_licenses['lic_expire'])   . "';\n";
      print "document.formUpdate.lic_vendorpo.value = '" . mysqli_real_escape_string($db, $a_inv_licenses['lic_vendorpo']) . "';\n";
      print "document.formUpdate.lic_po.value = '"       . mysqli_real_escape_string($db, $a_inv_licenses['lic_po'])       . "';\n";
      print "document.formUpdate.lic_customer.value = '" . mysqli_real_escape_string($db, $a_inv_licenses['lic_customer']) . "';\n";
      print "document.formUpdate.lic_quantity.value = '" . mysqli_real_escape_string($db, $a_inv_licenses['lic_quantity']) . "';\n";
      print "document.formUpdate.lic_key.value = '"      . mysqli_real_escape_string($db, $a_inv_licenses['lic_key'])      . "';\n";
      print "document.formUpdate.lic_serial.value = '"   . mysqli_real_escape_string($db, $a_inv_licenses['lic_serial'])   . "';\n";
      print "document.formUpdate.lic_domain.value = '"   . mysqli_real_escape_string($db, $a_inv_licenses['lic_domain'])   . "';\n";

      print "document.formUpdate.lic_vendor['"  . $licvendor  . "'].selected = true;\n";
      print "document.formUpdate.lic_project['" . $licproject . "'].selected = true;\n";
      if ($licparent == 0) {
        print "document.formUpdate.lic_parent['0'].selected = true;\n";
      } else {
        print "document.formUpdate.lic_parent['"  . $licparent + 1  . "'].selected = true;\n";
      }

      if ($a_inv_licenses['lic_status']) {
        print "document.formUpdate.lic_status.checked = true;\n";
      } else {
        print "document.formUpdate.lic_status.checked = false;\n";
      }

      print "document.formUpdate.id.value = '" . $formVars['id'] . "'\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
