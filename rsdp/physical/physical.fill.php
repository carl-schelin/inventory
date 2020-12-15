<?php
# Script: physical.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "physical.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_infrastructure");

      $q_string  = "select pf_id,pf_row,pf_rack,pf_unit,pf_circuita,pf_circuitb ";
      $q_string .= "from rsdp_platform ";
      $q_string .= "where pf_rsdp = " . $formVars['rsdp'];
      $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_platform) > 0) {
        $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);

        print "document.rsdp.pf_row.value = '"      . mysqli_real_escape_string($a_rsdp_platform['pf_row'])      . "';\n";
        print "document.rsdp.pf_rack.value = '"     . mysqli_real_escape_string($a_rsdp_platform['pf_rack'])     . "';\n";
        print "document.rsdp.pf_unit.value = '"     . mysqli_real_escape_string($a_rsdp_platform['pf_unit'])     . "';\n";
        print "document.rsdp.pf_circuita.value = '" . mysqli_real_escape_string($a_rsdp_platform['pf_circuita']) . "';\n";
        print "document.rsdp.pf_circuitb.value = '" . mysqli_real_escape_string($a_rsdp_platform['pf_circuitb']) . "';\n";

        print "document.rsdp.pf_id.value = " . $a_rsdp_platform['pf_id'] . ";\n";

      }

      mysqli_free_result($q_rsdp_platform);


      $q_string  = "select if_id,if_dcrack,if_dccabled ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'];
      $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
        $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

        if ($a_rsdp_infrastructure['if_dcrack']) {
          print "document.rsdp.if_dcrack.checked = true;\n";
        } else {
          print "document.rsdp.if_dcrack.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_dccabled']) {
          print "document.rsdp.if_dccabled.checked = true;\n";
        } else {
          print "document.rsdp.if_dccabled.checked = false;\n";
        }

        print "document.rsdp.if_id.value = " . $a_rsdp_infrastructure['if_id'] . ";\n";

      }

      mysqli_free_result($q_rsdp_infrastructure);


      $q_string  = "select dc_id,dc_power,dc_cables,dc_infra,dc_received,dc_installed,dc_checklist,dc_path ";
      $q_string .= "from rsdp_datacenter ";
      $q_string .= "where dc_rsdp = " . $formVars['rsdp'];
      $q_rsdp_datacenter = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_datacenter) > 0) {
        $a_rsdp_datacenter = mysqli_fetch_array($q_rsdp_datacenter);

        if ($a_rsdp_datacenter['dc_power']) {
          print "document.rsdp.dc_power.checked = true;\n";
        } else {
          print "document.rsdp.dc_power.checked = false;\n";
        }
        if ($a_rsdp_datacenter['dc_cables']) {
          print "document.rsdp.dc_cables.checked = true;\n";
        } else {
          print "document.rsdp.dc_cables.checked = false;\n";
        }
        if ($a_rsdp_datacenter['dc_infra']) {
          print "document.rsdp.dc_infra.checked = true;\n";
        } else {
          print "document.rsdp.dc_infra.checked = false;\n";
        }
        if ($a_rsdp_datacenter['dc_received']) {
          print "document.rsdp.dc_received.checked = true;\n";
        } else {
          print "document.rsdp.dc_received.checked = false;\n";
        }
        if ($a_rsdp_datacenter['dc_installed']) {
          print "document.rsdp.dc_installed.checked = true;\n";
        } else {
          print "document.rsdp.dc_installed.checked = false;\n";
        }
        if ($a_rsdp_datacenter['dc_checklist']) {
          print "document.rsdp.dc_checklist.checked = true;\n";
        } else {
          print "document.rsdp.dc_checklist.checked = false;\n";
        }

        print "document.rsdp.dc_path.value = '" . mysqli_real_escape_string($a_rsdp_datacenter['dc_path']) . "';\n";

        print "document.rsdp.dc_id.value = " . $a_rsdp_datacenter['dc_id'] . ";\n";

      }

      mysqli_free_result($q_rsdp_datacenter);


      print "validate_Form();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
