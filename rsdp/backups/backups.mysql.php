<?php
# Script: backups.mysql.php
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
    $package = "backups.mysql.php";
    $formVars['id']          = clean($_GET['id'],         10);
    $formVars['rsdp']        = clean($_GET['rsdp'],       10);
    $formVars['if_complete'] = clean($_GET['complete'],   10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['if_complete'] == '') {
      $formVars['if_complete'] = 0;
    }

    if (check_userlevel(2)) {
# save, submit, and save and exit
      if ($formVars['if_complete'] == 0 || $formVars['if_complete'] == 1 || $formVars['if_complete'] == 2) {
        $formVars['if_bucheck']     = clean($_GET['if_bucheck'],      10);
        $formVars['if_backups']     = clean($_GET['if_backups'],      10);
        $formVars['if_buverified']  = clean($_GET['if_buverified'],   10);

        if ($formVars['if_bucheck'] == 'true') {
          $formVars['if_bucheck'] = 1;
        } else {
          $formVars['if_bucheck'] = 0;
        }
        if ($formVars['if_backups'] == 'true') {
          $formVars['if_backups'] = 1;
        } else {
          $formVars['if_backups'] = 0;
        }
        if ($formVars['if_buverified'] == 'true') {
          $formVars['if_buverified'] = 1;
        } else {
          $formVars['if_buverified'] = 0;
        }

        logaccess($_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "if_rsdp       =   " . $formVars['rsdp']           . "," . 
          "if_bucheck    =   " . $formVars['if_bucheck']     . "," . 
          "if_backups    =   " . $formVars['if_backups']     . "," . 
          "if_buverified =   " . $formVars['if_buverified'];

        if ($formVars['id'] == 0) {
          $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
          $message = "Configuration added.";
        }
        if ($formVars['id'] > 0) {
          $query = "update rsdp_infrastructure set " . $q_string . " where if_id = " . $formVars['id'];
          $message = "Configuration updated.";
        }

        logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rsdp']);

        mysql_query($query) or die($query . ": " . mysql_error());

        print "alert('" . $message . "');\n";
      }


# four options here
# -1 - send a reminder email
# 0 - save data
# 1 - notify san if physical and san mounts are requested
# 1 - or notify platforms folks
# 2 - save and exit

# Send an e-mail to the backup team to remind them of the awaiting task
      if ($formVars['if_complete'] == -1) {
        generateEmail(
          $formVars['rsdp'],
          "<p>Reminder: The Server has been configured and is ready to have Backups configured.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/backups/backups.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: The Server is ready to configure Backups",
          "rsdp_backuppoc",
          $GRP_Backups
        );
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['if_complete'] == 1) {
        setstatus($formVars['rsdp'], 1, 13);
# if completed
#  if monitoring and application installation is complete (or skipped)
#    send email/ticket to monitoring team for application monitoring if not skipped
#    send email/ticket to application team for application configuration if no monitoring
#    

# check the status of the monitoring task
        $monitoring = 0;
        $q_string  = "select st_completed ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $formVars['rsdp'] . " and st_step = 14";
        $q_rsdp_status = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_rsdp_status = mysql_fetch_array($q_rsdp_status);

        if (mysql_num_rows($q_rsdp_status) > 0) {
          $monitoring = 1;
        }

# check the status of the app install task
        $application = 0;
        $q_string  = "select st_completed ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $formVars['rsdp'] . " and st_step = 15";
        $q_rsdp_status = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_rsdp_status = mysql_fetch_array($q_rsdp_status);

        if (mysql_num_rows($q_rsdp_status) > 0) {
          $application = 1;
        }

# ok, if both have been completed, check the rsdp_appmonitor setting, otherwise no e-mail goes out.
        if ($monitoring == 1 && $application == 1) {
# Check to see if monitoring has been requested; check here only if backups were not requested so the os monitoring can also be marked as skipped
          $q_string  = "select rsdp_appmonitor ";
          $q_string .= "from rsdp_server ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'];
          $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

          if ($a_rsdp_server['rsdp_appmonitor'] == 0) {
            setstatus($formVars['rsdp'], 2, 16);

# send e-mail to the Application folks since no app monitoring is required
            $q_string  = "select rsdp_application ";
            $q_string .= "from rsdp_server ";
            $q_string .= "where rsdp_id = " . $formVars['rsdp'];
            $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
            $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

            generateEmail(
              $formVars['rsdp'],
              "<p>The Server is ready for the final Application configuration.</p>", 
              "<p>Click on <a href=\"" . $RSDProot . "/application/configured.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
              "RSDP: Server ready to finish Application configuration", 
              "rsdp_apppoc",
              $a_rsdp_server['rsdp_application']
            );

# generate a Ticket
            $q_string  = "select tkt_appcnf ";
            $q_string .= "from rsdp_tickets ";
            $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
            $q_rsdp_tickets = mysql_query($q_string) or die($q_string . ": " . mysql_error());
            $a_rsdp_tickets = mysql_fetch_array($q_rsdp_tickets);
            if ($a_rsdp_tickets['tkt_appcnf']) {
              submit_Ticket(
                $formVars['rsdp'],
                $RSDProot . "/application/configured.php",
                "rsdp_apppoc",
                $a_rsdp_server['rsdp_application']
              );
            }
            print "alert('Applications task submitted');\n";
          } else {
# send e-mail to the monitoring folks to configure application monitoring
            generateEmail(
              $formVars['rsdp'],
              "<p>The Server is ready for Application level monitoring.</p>", 
              "<p>Click on <a href=\"" . $RSDProot . "/application/monitored.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
              "RSDP: Server ready for Application level monitoring", 
              "rsdp_monitorpoc",
              $GRP_Monitoring
            );

# generate a Ticket
            $q_string  = "select tkt_appmon ";
            $q_string .= "from rsdp_tickets ";
            $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
            $q_rsdp_tickets = mysql_query($q_string) or die($q_string . ": " . mysql_error());
            $a_rsdp_tickets = mysql_fetch_array($q_rsdp_tickets);
            if ($a_rsdp_tickets['tkt_appmon']) {
              submit_Ticket(
                $formVars['rsdp'],
                $RSDProot . "/application/monitored.php",
                "rsdp_monitorpoc",
                $GRP_Monitoring
              );
            }
            print "alert('Monitoring task submitted');\n";
          }
        }
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['if_complete'] == 2) {
        print "alert('Data Saved.');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
