<?php
# Script: license.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from licenses");

      $q_string  = "select lic_id,lic_vendor,lic_product,lic_date,lic_vendorpo,lic_po,lic_project,lic_quantity,lic_key,lic_serial,lic_domain ";
      $q_string .= "from licenses ";
      $q_string .= "where lic_id = " . $formVars['id'];
      $q_licenses = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_licenses = mysql_fetch_array($q_licenses);
      mysql_free_result($q_licenses);

      $licproject = return_Index($a_licenses['lic_project'], "select prod_id from products order by prod_name");

      print "document.license.lic_vendor.value = '"   . mysql_real_escape_string($a_licenses['lic_vendor'])   . "';\n";
      print "document.license.lic_product.value = '"  . mysql_real_escape_string($a_licenses['lic_product'])  . "';\n";
      print "document.license.lic_date.value = '"     . mysql_real_escape_string($a_licenses['lic_date'])     . "';\n";
      print "document.license.lic_vendorpo.value = '" . mysql_real_escape_string($a_licenses['lic_vendorpo']) . "';\n";
      print "document.license.lic_po.value = '"       . mysql_real_escape_string($a_licenses['lic_po'])       . "';\n";
      print "document.license.lic_quantity.value = '" . mysql_real_escape_string($a_licenses['lic_quantity']) . "';\n";
      print "document.license.lic_key.value = '"      . mysql_real_escape_string($a_licenses['lic_key'])      . "';\n";
      print "document.license.lic_serial.value = '"   . mysql_real_escape_string($a_licenses['lic_serial'])   . "';\n";
      print "document.license.lic_domain.value = '"   . mysql_real_escape_string($a_licenses['lic_domain'])   . "';\n";

      print "document.license.lic_project['" . $licproject . "'].selected = true;\n";

      print "document.license.id.value = '" . $formVars['id'] . "'\n";
      print "document.license.update.disabled = false;\n\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
