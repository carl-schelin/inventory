<?php
# Script: build.system.php
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
    $package = "build.system.php";

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Populating system field with derived and selected values");

      $formVars['name_location']   = clean($_GET['name_location'],     4);
      $formVars['name_instance']   = clean($_GET['name_instance'],     1);
      $formVars['name_zone']       = clean($_GET['name_zone'],         1);
      $formVars['name_device']     = clean($_GET['name_device'],       3);
      $formVars['name_service']    = clean($_GET['name_service'],      2);
      $formVars['name_freeform']   = clean($_GET['name_freeform'],     4);

      if ($formVars['name_zone'] == '1') {
        $formVars['name_zone'] = 'c';
      }
      if ($formVars['name_zone'] == '2') {
        $formVars['name_zone'] = 'e';
      }
      if ($formVars['name_zone'] == '3') {
        $formVars['name_zone'] = 'd';
      }

      $q_string  = "select dev_type ";
      $q_string .= "from device ";
      $q_string .= "where dev_id = " . $formVars['name_device'] . " ";
      $q_device = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_device = mysql_fetch_array($q_device);

      print "document.rsdp.os_sysname.value = '" . strtolower($formVars['name_location'] . $formVars['name_instance'] . $formVars['name_zone'] . $a_device['dev_type'] . $formVars['name_service'] . $formVars['name_freeform']) . "';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>



