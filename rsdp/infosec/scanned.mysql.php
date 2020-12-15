<?php
# Script: scanned.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "scanned.mysql.php";
    $formVars['id']           = clean($_GET['id'],           10);
    $formVars['rsdp']         = clean($_GET['rsdp'],         10);
    $formVars['is_complete']  = clean($_GET['complete'],     10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['is_complete'] == 0 || $formVars['is_complete'] == 1 || $formVars['is_complete'] == 2) {
        $formVars['is_ticket']    = clean($_GET['is_ticket'],    30);
        $formVars['is_scan']      = clean($_GET['is_scan'],      10);
        $formVars['is_verified']  = clean($_GET['is_verified'],  10);
        $formVars['is_checklist'] = clean($_GET['is_checklist'], 10);

        if ($formVars['is_scan'] == 'true') {
          $formVars['is_scan'] = 1;
        } else {
          $formVars['is_scan'] = 0;
        }
        if ($formVars['is_verified'] == 'true') {
          $formVars['is_verified'] = 1;
        } else {
          $formVars['is_verified'] = 0;
        }
        if ($formVars['is_checklist'] == 'true') {
          $formVars['is_checklist'] = 1;
        } else {
          $formVars['is_checklist'] = 0;
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "is_rsdp      =   " . $formVars['rsdp']             . "," . 
          "is_ticket    = \"" . $formVars['is_ticket']        . "\"," . 
          "is_scan      =   " . $formVars['is_scan']          . "," . 
          "is_checklist =   " . $formVars['is_checklist']     . "," . 
          "is_verified  =   " . $formVars['is_verified'];

        if ($formVars['id'] == 0) {
          $query = "insert into rsdp_infosec set is_id = null," . $q_string;
          $message = "Configuration added.";
        }
        if ($formVars['id'] > 0) {
          $query = "update rsdp_infosec set " . $q_string . " where is_id = " . $formVars['id'];
          $message = "Configuration updated.";
        }

        logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rsdp']);

        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

        print "alert('" . $message . "');\n";
      }


# four options here
# -1 - send a reminder email
# 0 - save data
# 1 - notify san if physical and san mounts are requested
# 1 - or notify platforms folks
# 2 - save and exit

# Send an e-mail to the platforms team to remind them of the awaiting task
      if ($formVars['is_complete'] == -1) {
        $q_string  = "select rsdp_platform ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        generateEmail(
          $db, 
          $formVars['rsdp'],
          "<p>Reminder: The Server is fully configured and ready to be scanned by InfoSec.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/infosec/scanned.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: Server is ready to be scanned",
          "rsdp_platformspoc",
          $a_rsdp_server['rsdp_platform']
        );
        print "alert('Platforms Reminder E-Mail Sent');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['is_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

      if ($formVars['is_complete'] == 1) {
        setstatus($db, "$formVars['rsdp'], 1, 18);

        $q_string  = "select rsdp_platform ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        generateEmail(
          $db, 
          $formVars['rsdp'],
          "<p>The InfoSec scans have been completed and resolved. The Server is ready for use.</p>", 
          "", 
          "RSDP: InfoSec Scans Completed", 
          "rsdp_platformspoc",
          $a_rsdp_server['rsdp_platform']
        );
        print "alert('Platforms Task Submitted');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['is_complete'] == 2) {
        print "alert('Data Saved.');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
