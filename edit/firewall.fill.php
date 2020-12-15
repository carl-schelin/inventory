<?php
# Script: firewall.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Fill in the forms for editing

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "firewall.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from firewall");

      $q_string  = "select fw_source,fw_sourcezone,fw_destination,fw_destinationzone,";
      $q_string .= "fw_port,fw_protocol,fw_description,fw_timeout,fw_ticket,fw_portdesc ";
      $q_string .= "from firewall ";
      $q_string .= "where fw_id = " . $formVars['id'];
      $q_firewall = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_firewall = mysqli_fetch_array($q_firewall);
      mysqli_free_result($q_firewall);


      $q_string  = "select zone_id ";
      $q_string .= "from ip_zones ";
      $q_string .= "order by zone_name";
      $sourcezone = return_Index($db, $a_firewall['fw_sourcezone'], $q_string);

      $q_string  = "select zone_id ";
      $q_string .= "from ip_zones ";
      $q_string .= "order by zone_name";
      $destinationzone = return_Index($db, $a_firewall['fw_destinationzone'], $q_string);

      print "document.edit.fw_sourcezone['"      . $sourcezone      . "'].selected = true;\n";
      print "document.edit.fw_destinationzone['" . $destinationzone . "'].selected = true;\n";

      print "document.edit.fw_source.value = '"      . mysqli_real_escape_string($a_firewall['fw_source'])      . "';\n";
      print "document.edit.fw_destination.value = '" . mysqli_real_escape_string($a_firewall['fw_destination']) . "';\n";
      print "document.edit.fw_port.value = '"        . mysqli_real_escape_string($a_firewall['fw_port'])        . "';\n";
      print "document.edit.fw_protocol.value = '"    . mysqli_real_escape_string($a_firewall['fw_protocol'])    . "';\n";
      print "document.edit.fw_description.value = '" . mysqli_real_escape_string($a_firewall['fw_description']) . "';\n";
      print "document.edit.fw_timeout.value = '"     . mysqli_real_escape_string($a_firewall['fw_timeout'])     . "';\n";
      print "document.edit.fw_ticket.value = '"      . mysqli_real_escape_string($a_firewall['fw_ticket'])      . "';\n";
      print "document.edit.fw_portdesc.value = '"    . mysqli_real_escape_string($a_firewall['fw_portdesc'])    . "';\n";

      print "document.edit.fw_id.value = " . $formVars['id'] . ";\n";
      print "document.edit.fw_update.disabled = false;\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
