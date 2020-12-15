<?php
# Script: monitored.mysql.php
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
    $package = "monitored.mysql.php";
    $formVars['id']           = clean($_GET['id'],          10);
    $formVars['rsdp']         = clean($_GET['rsdp'],        10);
    $formVars['app_complete'] = clean($_GET['complete'],    10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['app_complete'] == '') {
      $formVars['app_complete'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
# save, submit, and save and exit
      if ($formVars['app_complete'] == 0 || $formVars['app_complete'] == 1 || $formVars['app_complete'] == 2) {
        $formVars['app_moncheck']  = clean($_GET['app_moncheck'],   10);
        $formVars['app_monitor']   = clean($_GET['app_monitor'],    10);
        $formVars['app_verified']  = clean($_GET['app_verified'],   10);

        if ($formVars['app_moncheck'] == 'true') {
          $formVars['app_moncheck'] = 1;
        } else {
          $formVars['app_moncheck'] = 0;
        }
        if ($formVars['app_monitor'] == 'true') {
          $formVars['app_monitor'] = 1;
        } else {
          $formVars['app_monitor'] = 0;
        }
        if ($formVars['app_verified'] == 'true') {
          $formVars['app_verified'] = 1;
        } else {
          $formVars['app_verified'] = 0;
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "app_rsdp      =   " . $formVars['rsdp']          . "," . 
          "app_moncheck  =   " . $formVars['app_moncheck']  . "," . 
          "app_monitor   =   " . $formVars['app_monitor']   . "," . 
          "app_verified  =   " . $formVars['app_verified'];

        if ($formVars['id'] == 0) {
          $query = "insert into rsdp_applications set app_id = null," . $q_string;
          $message = "Configuration added.";
        }
        if ($formVars['id'] > 0) {
          $query = "update rsdp_applications set " . $q_string . " where app_id = " . $formVars['id'];
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

# Send an e-mail to the monitoring folks to remind them of the awaiting task
      if ($formVars['app_complete'] == -1) {
        generateEmail(
          $db, 
          $formVars['rsdp'],
          "<p>Reminder: The Server is ready for Application level monitoring.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/applciation/monitored.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: Server ready for Application level monitoring",
          "rsdp_monitorpoc",
          $GRP_Monitoring
        );
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['app_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

      if ($formVars['app_complete'] == 1) {
        setstatus($db, "$formVars['rsdp'], 1, 16);

        $q_string  = "select rsdp_application ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        generateEmail(
          $db, 
          $formVars['rsdp'],
          "<p>Application monitoring has been completed and the new Server is ready to have the Application completed.</p>", 
          "<p>Click on <a href=\"" . $RSDProot . "/application/configured.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
          "RSDP: Application Monitoring has been Completed", 
          "rsdp_apppoc",
          $a_rsdp_server['rsdp_application']
        );

# generate a Ticket
        $q_string  = "select tkt_appcnf ";
        $q_string .= "from rsdp_tickets ";
        $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
        $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
        if ($a_rsdp_tickets['tkt_appcnf']) {
          submit_Ticket(
            $db,
            $formVars['rsdp'],
            $RSDProot . "/application/configured.php",
            "rsdp_apppoc",
            $a_rsdp_server['rsdp_application']
          );
        }
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['app_complete'] == 2) {
        print "alert('Data Saved.');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
