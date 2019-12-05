<?php
# Script: installed.fill.php
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
    $package = "installed.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_applications");

      $q_string  = "select rsdp_appmonitor ";
      $q_string .= "from rsdp_server ";
      $q_string .= "where rsdp_id = " . $formVars['rsdp'];
      $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

      $q_string  = "select app_id,app_installed,app_configured,app_mib,app_process,app_logfile,app_inscheck ";
      $q_string .= "from rsdp_applications ";
      $q_string .= "where app_rsdp = " . $formVars['rsdp'];
      $q_rsdp_applications = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_applications) > 0) {
        $a_rsdp_applications = mysql_fetch_array($q_rsdp_applications);

        if ($a_rsdp_applications['app_inscheck']) {
          print "document.rsdp.app_inscheck.checked = true;\n";
        } else {
          print "document.rsdp.app_inscheck.checked = false;\n";
        }
        if ($a_rsdp_applications['app_installed']) {
          print "document.rsdp.app_installed.checked = true;\n";
        } else {
          print "document.rsdp.app_installed.checked = false;\n";
        }
        if ($a_rsdp_applications['app_configured']) {
          print "document.rsdp.app_configured.checked = true;\n";
        } else {
          print "document.rsdp.app_configured.checked = false;\n";
        }

        if ($a_rsdp_server['rsdp_appmonitor'] == 1) {

          if ($a_rsdp_applications['app_mib']) {
            print "document.rsdp.app_mib.checked = true;\n";
          } else {
            print "document.rsdp.app_mib.checked = false;\n";
          }
          if ($a_rsdp_applications['app_process']) {
            print "document.rsdp.app_process.checked = true;\n";
          } else {
            print "document.rsdp.app_process.checked = false;\n";
          }
          if ($a_rsdp_applications['app_logfile']) {
            print "document.rsdp.app_logfile.checked = true;\n";
          } else {
            print "document.rsdp.app_logfile.checked = false;\n";
          }

        }

        print "document.rsdp.id.value = " . $a_rsdp_applications['app_id'] . ";\n";

      }

      mysql_free_result($q_rsdp_applications);

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
