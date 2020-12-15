<?php
# Script: psaps.fill.php
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
    $package = "psaps.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from psaps");

      $q_string  = "select psap_customerid,psap_parentid,psap_ali_id,psap_companyid,psap_psap_id,psap_description,";
      $q_string .= "psap_lport,psap_circuit_id,psap_pseudo_cid,psap_lec,psap_texas,psap_updated,psap_delete ";
      $q_string .= "from psaps ";
      $q_string .= "where psap_id = " . $formVars['id'];
      $q_psaps = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_psaps = mysqli_fetch_array($q_psaps);
      mysqli_free_result($q_psaps);

      print "document.psaps.psap_customerid.value = '"    . mysqli_real_escape_string($a_psaps['psap_customerid'])    . "';\n";
      print "document.psaps.psap_parentid.value = '"      . mysqli_real_escape_string($a_psaps['psap_parentid'])      . "';\n";
      print "document.psaps.psap_ali_id.value = '"        . mysqli_real_escape_string($a_psaps['psap_ali_id'])        . "';\n";
      print "document.psaps.psap_companyid.value = '"     . mysqli_real_escape_string($a_psaps['psap_companyid'])     . "';\n";
      print "document.psaps.psap_psap_id.value = '"       . mysqli_real_escape_string($a_psaps['psap_psap_id'])       . "';\n";
      print "document.psaps.psap_description.value = '"   . mysqli_real_escape_string($a_psaps['psap_description'])   . "';\n";
      print "document.psaps.psap_lport.value = '"         . mysqli_real_escape_string($a_psaps['psap_lport'])         . "';\n";
      print "document.psaps.psap_circuit_id.value = '"    . mysqli_real_escape_string($a_psaps['psap_circuit_id'])    . "';\n";
      print "document.psaps.psap_pseudo_cid.value = '"    . mysqli_real_escape_string($a_psaps['psap_pseudo_cid'])    . "';\n";
      print "document.psaps.psap_lec.value = '"           . mysqli_real_escape_string($a_psaps['psap_lec'])           . "';\n";
      print "document.psaps.psap_texas.value = '"         . mysqli_real_escape_string($a_psaps['psap_texas'])         . "';\n";
      print "document.psaps.psap_updated.value = '"       . mysqli_real_escape_string($a_psaps['psap_updated'])       . "';\n";
      print "document.psaps.psap_delete.value = '"        . mysqli_real_escape_string($a_psaps['psap_delete'])        . "';\n";

      print "document.psaps.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
