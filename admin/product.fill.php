<?php
# Script: product.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from products");

      $q_string  = "select prod_name,prod_code,prod_desc,prod_unit,prod_service ";
      $q_string .= "from products ";
      $q_string .= "where prod_id = " . $formVars['id'];
      $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_products = mysqli_fetch_array($q_products);
      mysqli_free_result($q_products);

      $unit    = return_Index($db, $a_products['prod_unit'],    "select bus_id from business_unit order by bus_name");
      $service = return_Index($db, $a_products['prod_service'], "select svc_id from service order by svc_id");

      print "document.formUpdate.prod_name.value = '"    . mysqli_real_escape_string($db, $a_products['prod_name'])    . "';\n";
      print "document.formUpdate.prod_code.value = '"    . mysqli_real_escape_string($db, $a_products['prod_code'])    . "';\n";
      print "document.formUpdate.prod_desc.value = '"    . mysqli_real_escape_string($db, $a_products['prod_desc'])    . "';\n";

      if ($unit > 0) {
        print "document.formUpdate.prod_unit['"    . $unit    . "'].selected = true;\n";
      }
      if ($service > 0) {
        print "document.formUpdate.prod_service['" . $service . "'].selected = true;\n";
      }

      $prod_tags = '';
      $space = '';
      $q_string  = "select tag_name ";
      $q_string .= "from tags ";
      $q_string .= "where tag_companyid = " . $formVars['id'] . " and tag_type = 3 ";
      $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_tags) > 0) {
        while ($a_tags = mysqli_fetch_array($q_tags)) {
          $prod_tags .= $space . $a_tags['tag_name'];
          $space = " ";
        }
      }
      print "document.formUpdate.prod_tags.value = '" . mysqli_real_escape_string($db, $prod_tags) . "';\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
