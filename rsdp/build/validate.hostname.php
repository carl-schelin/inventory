<?php
# Script: validate.hostname.php
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
    $package = "validate.hostname.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }
    $formVars['os_sysname'] = 0;
    if (isset($_GET['os_sysname'])) {
      $formVars['os_sysname'] = clean($_GET['os_sysname'], 60);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting server " . $formVars['os_sysname'] . " from inventory");

      $q_string  = "select inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_name = \"" . $formVars['os_sysname'] . "\" and inv_status = 0 ";
      $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_inventory = mysql_fetch_array($q_inventory);

      $q_string  = "select os_sysname,os_rsdp ";
      $q_string .= "from rsdp_osteam ";
      $q_string .= "where os_rsdp != " . $formVars['rsdp'] . " and os_sysname = \"" . $formVars['os_sysname'] . "\"";
      $q_rsdp_osteam = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_rsdp_osteam = mysql_fetch_array($q_rsdp_osteam);

      $q_string  = "select st_completed ";
      $q_string .= "from rsdp_status ";
      $q_string .= "where st_rsdp = " . $formVars['rsdp'] . " and st_step = 2 ";
      $q_rsdp_status = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_status) > 0) {
        $a_rsdp_status = mysql_fetch_array($q_rsdp_status);
      } else {
        $a_rsdp_status['st_completed'] = 0;
      }

      if ($a_inventory['inv_name'] == $formVars['os_sysname'] || $a_rsdp_osteam['os_sysname'] == $formVars['os_sysname']) {
        if ($a_rsdp_status['st_completed'] == 0 && strlen($formVars['os_sysname']) > 0) {
          if ($a_inventory['inv_name'] == $formVars['os_sysname']) {
            print "alert('Server name " . $formVars['os_sysname'] . " already exists in the Inventory database.\\n\\nWhile you can assign the name to this server, you will not be able to populate\\nthe inventory database while this server name is in use.');\n";
          }
          if ($a_rsdp_osteam['os_sysname'] == $formVars['os_sysname']) {
            print "alert('Server name " . $formVars['os_sysname'] . " is already assigned to RSDP server " . $a_rsdp_osteam['os_rsdp'] . ".\\n\\nOnly one of the systems will be able to be saved in the inventory database with that name.\\n');\n";
          }
        }
        print "set_Class('os_sysname', 'ui-state-error');\n";
      } else {
        print "set_Class('os_sysname', 'ui-widget-content');\n";
      }

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>



