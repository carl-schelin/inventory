<?php
# Script: product.fill.php
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
    $package = "product.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from products");

      $q_string  = "select prod_name,prod_code,prod_oldcode,prod_desc,prod_group,prod_type,prod_citype,prod_tier1,prod_tier2,prod_tier3,prod_remedy,prod_unit,prod_service ";
      $q_string .= "from products ";
      $q_string .= "where prod_id = " . $formVars['id'];
      $q_products = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_products = mysql_fetch_array($q_products);
      mysql_free_result($q_products);

      $group   = return_Index($a_products['prod_group'],   "select grp_id from groups where grp_disabled = 0 order by grp_name");
      $unit    = return_Index($a_products['prod_unit'],    "select bus_id from business_unit order by bus_name");
      $service = return_Index($a_products['prod_service'], "select svc_id from service order by svc_id");

      print "document.products.prod_id.value = '"      . mysql_real_escape_string($formVars['id'])             . "';\n";
      print "document.products.prod_name.value = '"    . mysql_real_escape_string($a_products['prod_name'])    . "';\n";
      print "document.products.prod_code.value = '"    . mysql_real_escape_string($a_products['prod_code'])    . "';\n";
      print "document.products.prod_oldcode.value = '" . mysql_real_escape_string($a_products['prod_oldcode']) . "';\n";
      print "document.products.prod_desc.value = '"    . mysql_real_escape_string($a_products['prod_desc'])    . "';\n";

      print "document.products.prod_group['"   . $group   . "'].selected = true;\n";
      print "document.products.prod_unit['"    . $unit    . "'].selected = true;\n";
      print "document.products.prod_service['" . $service . "'].selected = true;\n";

      print "document.products.prod_type.value = '"   . mysql_real_escape_string($a_products['prod_type'])   . "';\n";
      print "document.products.prod_citype.value = '" . mysql_real_escape_string($a_products['prod_citype']) . "';\n";
      print "document.products.prod_tier1.value = '"  . mysql_real_escape_string($a_products['prod_tier1'])  . "';\n";
      print "document.products.prod_tier2.value = '"  . mysql_real_escape_string($a_products['prod_tier2'])  . "';\n";
      print "document.products.prod_tier3.value = '"  . mysql_real_escape_string($a_products['prod_tier3'])  . "';\n";

      if ($a_products['prod_remedy']) {
        print "document.products.prod_remedy.checked = true;\n";
      } else {
        print "document.products.prod_remedy.checked = false;\n";
      }

      print "document.products.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
