<?php
# Script: monitoring.mysql.php
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
    $package = "monitoring.mysql.php";
    $formVars['id']          = clean($_GET['id'],          10);
    $formVars['rsdp']        = clean($_GET['rsdp'],        10);
    $formVars['if_complete'] = clean($_GET['complete'],    10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['if_complete'] == '') {
      $formVars['if_complete'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
# save, submit, and save and exit
      if ($formVars['if_complete'] == 0 || $formVars['if_complete'] == 1 || $formVars['if_complete'] == 2) {
        $formVars['if_monitor']      = clean($_GET['if_monitor'],       10);
        $formVars['if_monverified']  = clean($_GET['if_monverified'],   10);
        $formVars['if_moncheck']     = clean($_GET['if_moncheck'],      10);

        if ($formVars['if_monitor'] == 'true') {
          $formVars['if_monitor'] = 1;
        } else {
          $formVars['if_monitor'] = 0;
        }
        if ($formVars['if_monverified'] == 'true') {
          $formVars['if_monverified'] = 1;
        } else {
          $formVars['if_monverified'] = 0;
        }
        if ($formVars['if_moncheck'] == 'true') {
          $formVars['if_moncheck'] = 1;
        } else {
          $formVars['if_moncheck'] = 0;
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "if_rsdp         =   " . $formVars['rsdp']             . "," . 
          "if_moncheck     =   " . $formVars['if_moncheck']      . "," . 
          "if_monitor      =   " . $formVars['if_monitor']       . "," . 
          "if_monverified  =   " . $formVars['if_monverified'];

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


# four options here
# -1 - send a reminder email
# 0 - save data
# 1 - notify san if physical and san mounts are requested
# 1 - or notify platforms folks
# 2 - save and exit

# Send an e-mail to the monitoring team to remind them of the awaiting task
      if ($formVars['if_complete'] == -1) {
        generateEmail(
          $formVars['rsdp'],
          "<p>Reminder: The Server has been configured and is ready to have Monitoring configured.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/monitoring/monitoring.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: The Server is ready to configure Monitoring",
          "rsdp_monitorpoc",
          $GRP_Monitoring
        );
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['if_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

      if ($formVars['if_complete'] == 1) {
        setstatus($formVars['rsdp'], 1, 14);

# this is the san part.
# if step 13 and step 15 is done (> 0), then
#   if monitoring is requested, send an e-mail to the monitoring team,
# otherwise
#   send to the applications team to complete the installation

# check the status of the backup task
        $backups = 0;
        $q_string  = "select st_completed ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $formVars['rsdp'] . " and st_step = 13";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

        if (mysqli_num_rows($q_rsdp_status) > 0) {
          $backups = 1;
        }

# check the status of the app install task
        $application = 0;
        $q_string = "select st_completed ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $formVars['rsdp'] . " and st_step = 15";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

        if (mysqli_num_rows($q_rsdp_status) > 0) {
          $application = 1;
        }

# ok, if both have been completed, check the rsdp_appmonitor setting, otherwise no e-mail goes out.
        if ($backups == 1 && $application == 1) {
# Check to see if monitoring has been requested
          $q_string = "select rsdp_appmonitor ";
          $q_string .= "from rsdp_server ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'];
          $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

          if ($a_rsdp_server['rsdp_appmonitor'] == 0) {
            setstatus($formVars['rsdp'], 2, 16);

# send e-mail to the Application folks since no app monitoring is required
            $q_string = "select rsdp_application ";
            $q_string .= "from rsdp_server ";
            $q_string .= "where rsdp_id = " . $formVars['rsdp'];
            $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

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
            $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
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
# send e-mail to the monitoring folks to configure monitoring
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
            $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
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
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
