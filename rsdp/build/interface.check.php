<?php
# Script: interface.check.php
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
    $package = "interface.check.php";
    $formVars['ask'] = 0;
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Checking interface " . $formVars['id']);

# checking for the existance of a requirement to get an IP or switch 
# configuration and then if there's any data in those fields.
      $q_string  = "select if_ip,if_ipcheck,if_switch,if_swcheck ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_id = " . $formVars['id'];
      $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface);

# if check and ip or switch is configured, tell the main page to ask
      if ($a_rsdp_interface['if_ipcheck'] && strlen($a_rsdp_interface['if_ip']) > 0) {
# if ip is set, then flip to 1
        $formVars['ask'] = 1;
      }
      if ($a_rsdp_interface['if_swcheck'] && strlen($a_rsdp_interface['if_switch']) > 0) {
# if switch is set, the flip to 2 but if ip is already set, it adds 1 to make it 3; both are set
        $formVars['ask'] += 2;
      }

      print "document.rsdp.ipokay.value = " . $formVars['ask'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
