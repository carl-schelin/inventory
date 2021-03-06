<?php
# Script: support.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "support.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from support");

      $q_string  = "select sup_company,sup_phone,sup_email,sup_web,sup_contract,sup_wiki,sup_hwresponse,sup_swresponse ";
      $q_string .= "from support ";
      $q_string .= "where sup_id = " . $formVars['id'];
      $q_support = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_support = mysqli_fetch_array($q_support);
      mysqli_free_result($q_support);

      $hwresponse   = return_Index($db, $a_support['sup_hwresponse'],  "select slv_id from supportlevel order by slv_value");
      $swresponse   = return_Index($db, $a_support['sup_swresponse'],  "select slv_id from supportlevel order by slv_value");

      print "document.support.sup_hwresponse['" . $hwresponse . "'].selected = true;\n";
      print "document.support.sup_swresponse['" . $swresponse . "'].selected = true;\n";

      print "document.support.sup_company.value = '"  . $a_support['sup_company']  . "';\n";
      print "document.support.sup_phone.value = '"    . $a_support['sup_phone']    . "';\n";
      print "document.support.sup_email.value = '"    . $a_support['sup_email']    . "';\n";
      print "document.support.sup_web.value = '"      . $a_support['sup_web']      . "';\n";
      print "document.support.sup_contract.value = '" . $a_support['sup_contract'] . "';\n";
      print "document.support.sup_wiki.value = '"     . $a_support['sup_wiki']     . "';\n";

      print "document.support.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
