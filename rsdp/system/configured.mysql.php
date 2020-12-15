<?php
# Script: configured.mysql.php
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
    $package = "configured.mysql.php";
    $formVars['id']           = clean($_GET['id'],            10);
    $formVars['if_complete']  = clean($_GET['complete'],      10);
    $formVars['rsdp']         = clean($_GET['rsdp'],          10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['if_complete'] == '') {
      $formVars['if_complete'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
# save, submit, and save and exit
      if ($formVars['if_complete'] == 0 || $formVars['if_complete'] == 1 || $formVars['if_complete'] == 2) {
        $formVars['if_sanfs']     = clean($_GET['if_sanfs'],      10);
        $formVars['if_verified']  = clean($_GET['if_verified'],   10);
        $formVars['if_checklist'] = clean($_GET['if_checklist'],  10);
        $formVars['if_wiki']      = clean($_GET['if_wiki'],       10);
        $formVars['if_svrmgt']    = clean($_GET['if_svrmgt'],     10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['if_sanfs'] == 'true') {
          $formVars['if_sanfs'] = 1;
        } else {
          $formVars['if_sanfs'] = 0;
        }
        if ($formVars['if_verified'] == 'true') {
          $formVars['if_verified'] = 1;
        } else {
          $formVars['if_verified'] = 0;
        }
        if ($formVars['if_checklist'] == 'true') {
          $formVars['if_checklist'] = 1;
        } else {
          $formVars['if_checklist'] = 0;
        }
        if ($formVars['if_wiki'] == 'true') {
          $formVars['if_wiki'] = 1;
        } else {
          $formVars['if_wiki'] = 0;
        }
        if ($formVars['if_svrmgt'] == 'true') {
          $formVars['if_svrmgt'] = 1;
        } else {
          $formVars['if_svrmgt'] = 0;
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "if_rsdp      =   " . $formVars['rsdp']         . "," . 
          "if_checklist =   " . $formVars['if_checklist'] . "," . 
          "if_sanfs     =   " . $formVars['if_sanfs']     . "," . 
          "if_verified  =   " . $formVars['if_verified']  . "," . 
          "if_wiki      =   " . $formVars['if_wiki']      . "," . 
          "if_svrmgt    =   " . $formVars['if_svrmgt'];

        if ($formVars['id'] == 0) {
          $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
          $message = "Configuration added.";
        }
        if ($formVars['id'] > 0) {
          $query = "update rsdp_infrastructure set " . $q_string . " where if_id = " . $formVars['id'];
          $message = "Configuration updated.";
        }

        logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rsdp']);

        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

        print "alert('" . $message . "');\n";
      }


# three options here.
# -1 - Send a reminder to the platforms POC or group
# 0 - Save data
# 1 - Notify the backup team if backups are necessary
# 1 - Notify the monitoring team if monitoring has been selected
# 1 - Notify the applications team
# 2 - Save data and exit

# Send an e-mail to the platforms team to remind them of the awaiting task
      if ($formVars['if_complete'] == -1) {

# Check the status of the SAN mounts. Just making sure the e-mail is accurate.
        $q_string  = "select fs_id ";
        $q_string .= "from rsdp_filesystem ";
        $q_string .= "where fs_rsdp = " . $formVars['rsdp'];
        $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem);

# greater than zero, set the san variables, otherwise set the platforms team variables.
        if (mysqli_num_rows($q_rsdp_filesystem) > 0) {
          $body = "<p>Reminder: The SAN mounts have been provisioned and presented to the server.</p>";
          $subject = "RSDP Reminder: SAN mounts are ready";
        } else {
          $body = "<p>The Server hardware has been configured and documented, the Operating System installed. No SAN mounts are required.</p>";
          $subject = "RSDP: Infrastructure has been documented";
        }

        $q_string  = "select rsdp_platform ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        generateEmail(
          $formVars['rsdp'],
          $body,
          "<p>Click on <a href=\"" . $RSDProot . "/system/configured.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          $subject,
          "rsdp_platformspoc",
          $a_rsdp_server['rsdp_platform']
        );
        print "alert('Platforms Reminder Submitted');\n";
        print "window.location.href = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['if_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

# completed?
      if ($formVars['if_complete'] == 1) {
        $message = '';
# set task 12 as complete
        setstatus($formVars['rsdp'], 1, 12);

# Check to see if backups have been requested. If not, mark the backup section as completed with a status of 2 for skipped
        $q_string  = "select bu_retention ";
        $q_string .= "from rsdp_backups ";
        $q_string .= "where bu_rsdp = " . $formVars['rsdp'];
        $q_rsdp_backups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_backups = mysqli_fetch_array($q_rsdp_backups);

        if ($a_rsdp_backups['bu_retention'] == 0) {
          setstatus($formVars['rsdp'], 2, 13);
        } else {

# send e-mail to the backup team
          generateEmail(
            $formVars['rsdp'],
            "<p>The Server has been configured and is ready to have Backups configured.</p>", 
            "<p>Click on <a href=\"" . $RSDProot . "/backups/backups.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
            "RSDP: The Server is ready to configure Backups", 
            "rsdp_backuppoc",
            $GRP_Backups
          );
          $message .= "Backup task submitted.\\n";

# generate a Ticket
          $q_string  = "select tkt_backups ";
          $q_string .= "from rsdp_tickets ";
          $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
          $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
          if ($a_rsdp_tickets['tkt_backups']) {
            submit_Ticket(
              $formVars['rsdp'],
              $RSDProot . "/backups/backups.php",
              "rsdp_backuppoc",
              $GRP_Backups
            );
          }
        }

# Check to see if monitoring has been requested; check here only if backups were not requested so the os monitoring can also be marked as skipped
        $q_string  = "select rsdp_osmonitor ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        if ($a_rsdp_server['rsdp_osmonitor'] == 0) {
          setstatus($formVars['rsdp'], 2, 14);
        } else {

# send e-mail to the monitoring team
          generateEmail(
            $formVars['rsdp'],
            "<p>The Server has been configured and is ready to have Monitoring configured.</p>", 
            "<p>Click on <a href=\"" . $RSDProot . "/monitoring/monitoring.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
            "RSDP: The Server is ready to configure Monitoring", 
            "rsdp_monitorpoc",
            $GRP_Monitoring
          );
          $message .= "Monitoring task submitted.\\n";

# generate a Ticket
          $q_string  = "select tkt_monitor ";
          $q_string .= "from rsdp_tickets ";
          $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
          $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
          if ($a_rsdp_tickets['tkt_monitor']) {
            submit_Ticket(
              $formVars['rsdp'],
              $RSDProot . "/monitoring/monitoring.php",
              "rsdp_monitorpoc",
              $GRP_Monitoring
            );
          }
        }

# send e-mail to the Application folks regardless of backup and monitoring requests
        $q_string  = "select rsdp_application ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        generateEmail(
          $formVars['rsdp'],
          "<p>The Server has been configured and is ready for the installation of Applications.</p>", 
          "<p>Click on <a href=\"" . $RSDProot . "/application/installed.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
          "RSDP: The Server is ready for Application Installation", 
          "rsdp_apppoc",
          $a_rsdp_server['rsdp_application']
        );
        $message .= "Application task submitted.\\n";
        print "alert('" . $message . "');\n";

# generate a Ticket
        $q_string  = "select tkt_appins ";
        $q_string .= "from rsdp_tickets ";
        $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
        $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
        if ($a_rsdp_tickets['tkt_appins']) {
          submit_Ticket(
            $formVars['rsdp'],
            $RSDProot . "/application/installed.php",
            "rsdp_apppoc",
            $a_rsdp_server['rsdp_application']
          );
        }

        print "window.location.href = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['if_complete'] == 2) {
        print "alert('Data Saved.');\n";
        print "window.location.href = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
