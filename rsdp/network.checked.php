<?php
# Script: network.checked.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "network.checked.php";
    $formVars['id'] = clean($_GET['id'], 10);

    if (check_userlevel($db, $AL_Edit)) {

      $q_string  = "select if_monitored ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_id = " . $formVars['id'] . " ";
      $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface);

      if ($a_rsdp_interface['if_monitored']) {
        $a_rsdp_interface['if_monitored'] = 0;
      } else {
        $a_rsdp_interface['if_monitored'] = 1;
      }

      $q_string  = "update ";
      $q_string .= "rsdp_interface ";
      $q_string .= "set ";
      $q_string .= "if_monitored = " . $a_rsdp_interface['if_monitored'] . " ";
      $q_string .= "where if_id = " . $formVars['id'] . " ";
      $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      if ($a_rsdp_interface['if_monitored']) {
        print "document.getElementById('if_mon_" . $formVars['id'] . "').checked = true;\n";
      } else {
        print "document.getElementById('if_mon_" . $formVars['id'] . "').checked = false;\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
