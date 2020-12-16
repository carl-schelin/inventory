<?php
# Script: installed.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "installed.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_infrastructure");

      $q_string  = "select if_id,if_config,if_built,if_network,if_dns,if_inscheck ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'];
      $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
        $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

        if ($a_rsdp_infrastructure['if_inscheck']) {
          print "document.rsdp.if_inscheck.checked = true;\n";
        } else {
          print "document.rsdp.if_inscheck.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_config']) {
          print "document.rsdp.if_config.checked = true;\n";
        } else {
          print "document.rsdp.if_config.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_built']) {
          print "document.rsdp.if_built.checked = true;\n";
        } else {
          print "document.rsdp.if_built.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_network']) {
          print "document.rsdp.if_network.checked = true;\n";
        } else {
          print "document.rsdp.if_network.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_dns']) {
          print "document.rsdp.if_dns.checked = true;\n";
        } else {
          print "document.rsdp.if_dns.checked = false;\n";
        }

        print "document.rsdp.id.value = " . $a_rsdp_infrastructure['if_id'] . ";\n";

      }

      mysqli_free_result($q_rsdp_infrastructure);

      print "validate_Form();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
