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

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from psaps");

      $q_string  = "select psap_ali_id,psap_companyid,psap_psap_id,psap_description,";
      $q_string .= "psap_lport,psap_circuit_id,psap_texas,psap_updated,psap_delete ";
      $q_string .= "from psaps ";
      $q_string .= "left join inventory on inventory.inv_id = psaps.psap_companyid ";
      $q_string .= "where psap_id = " . $formVars['id'];
      $q_psaps = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_psaps = mysqli_fetch_array($q_psaps);
      mysqli_free_result($q_psaps);

      $q_string  = "select inv_id,inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "where prod_name = \"ALIM\" and inv_manager = 1 and inv_appadmin != 1 and inv_status = 0 ";
      $q_string .= "order by inv_name ";

      $companyid = return_Index($a_psaps['psap_companyid'], $q_string);

      print "document.psaps.psap_companyid[" . $companyid . "].selected = true;\n";

      print "document.psaps.psap_ali_id.value = '"        . mysqli_real_escape_string($a_psaps['psap_ali_id'])        . "';\n";
      print "document.psaps.psap_psap_id.value = '"       . mysqli_real_escape_string($a_psaps['psap_psap_id'])       . "';\n";
      print "document.psaps.psap_description.value = '"   . mysqli_real_escape_string($a_psaps['psap_description'])   . "';\n";
      print "document.psaps.psap_lport.value = '"         . mysqli_real_escape_string($a_psaps['psap_lport'])         . "';\n";
      print "document.psaps.psap_circuit_id.value = '"    . mysqli_real_escape_string($a_psaps['psap_circuit_id'])    . "';\n";
      print "document.psaps.psap_updated.value = '"       . mysqli_real_escape_string($a_psaps['psap_updated'])       . "';\n";

      if ($a_psaps['psap_texas']) {
        print "document.psaps.psap_texas.checked = true;\n";
      } else {
        print "document.psaps.psap_texas.checked = false;\n";
      }

      if ($a_psaps['psap_delete']) {
        print "document.psaps.psap_delete.checked = true;\n";
      } else {
        print "document.psaps.psap_delete.checked = false;\n";
      }

      print "document.psaps.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
