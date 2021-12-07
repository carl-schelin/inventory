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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from licenses");

      $q_string  = "select lic_id,lic_vendor,lic_product,lic_date,lic_vendorpo,lic_po,lic_project,lic_quantity,lic_key,lic_serial,lic_domain ";
      $q_string .= "from licenses ";
      $q_string .= "where lic_id = " . $formVars['id'];
      $q_licenses = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_licenses = mysqli_fetch_array($q_licenses);
      mysqli_free_result($q_licenses);

      $licproject = return_Index($db, $a_licenses['lic_project'], "select prod_id from products order by prod_name");

      print "document.formUpdate.lic_vendor.value = '"   . mysqli_real_escape_string($db, $a_licenses['lic_vendor'])   . "';\n";
      print "document.formUpdate.lic_product.value = '"  . mysqli_real_escape_string($db, $a_licenses['lic_product'])  . "';\n";
      print "document.formUpdate.lic_date.value = '"     . mysqli_real_escape_string($db, $a_licenses['lic_date'])     . "';\n";
      print "document.formUpdate.lic_vendorpo.value = '" . mysqli_real_escape_string($db, $a_licenses['lic_vendorpo']) . "';\n";
      print "document.formUpdate.lic_po.value = '"       . mysqli_real_escape_string($db, $a_licenses['lic_po'])       . "';\n";
      print "document.formUpdate.lic_quantity.value = '" . mysqli_real_escape_string($db, $a_licenses['lic_quantity']) . "';\n";
      print "document.formUpdate.lic_key.value = '"      . mysqli_real_escape_string($db, $a_licenses['lic_key'])      . "';\n";
      print "document.formUpdate.lic_serial.value = '"   . mysqli_real_escape_string($db, $a_licenses['lic_serial'])   . "';\n";
      print "document.formUpdate.lic_domain.value = '"   . mysqli_real_escape_string($db, $a_licenses['lic_domain'])   . "';\n";

      print "document.formUpdate.lic_project['" . $licproject . "'].selected = true;\n";

      print "document.formUpdate.id.value = '" . $formVars['id'] . "'\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
