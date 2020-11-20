<?php
# Script: installed.mysql.php
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
    $package = "installed.mysql.php";
    $formVars['id']            = clean($_GET['id'],           10);
    $formVars['rsdp']          = clean($_GET['rsdp'],         10);
    $formVars['app_complete']  = clean($_GET['complete'],     10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['app_complete'] == '') {
      $formVars['app_complete'] = 0;
    }

    if (check_userlevel($AL_Edit)) {
# save, submit, and save and exit
      if ($formVars['app_complete'] == 0 || $formVars['app_complete'] == 1 || $formVars['app_complete'] == 2) {
        $formVars['app_inscheck']   = clean($_GET['app_inscheck'],     10);
        $formVars['app_installed']  = clean($_GET['app_installed'],    10);
        $formVars['app_configured'] = clean($_GET['app_configured'],   10);
        $formVars['app_mib']        = clean($_GET['app_mib'],          10);
        $formVars['app_process']    = clean($_GET['app_process'],      10);
        $formVars['app_logfile']    = clean($_GET['app_logfile'],      10);

        if ($formVars['app_inscheck'] == 'true') {
          $formVars['app_inscheck'] = 1;
        } else {
          $formVars['app_inscheck'] = 0;
        }
        if ($formVars['app_installed'] == 'true') {
          $formVars['app_installed'] = 1;
        } else {
          $formVars['app_installed'] = 0;
        }
        if ($formVars['app_configured'] == 'true') {
          $formVars['app_configured'] = 1;
        } else {
          $formVars['app_configured'] = 0;
        }
        if ($formVars['app_mib'] == 'true') {
          $formVars['app_mib'] = 1;
        } else {
          $formVars['app_mib'] = 0;
        }
        if ($formVars['app_process'] == 'true') {
          $formVars['app_process'] = 1;
        } else {
          $formVars['app_process'] = 0;
        }
        if ($formVars['app_logfile'] == 'true') {
          $formVars['app_logfile'] = 1;
        } else {
          $formVars['app_logfile'] = 0;
        }

        logaccess($_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "app_rsdp       =   " . $formVars['rsdp']            . "," . 
          "app_inscheck   =   " . $formVars['app_inscheck']    . "," . 
          "app_installed  =   " . $formVars['app_installed']   . "," . 
          "app_configured =   " . $formVars['app_configured']  . "," . 
          "app_mib        =   " . $formVars['app_mib']         . "," . 
          "app_process    =   " . $formVars['app_process']     . "," . 
          "app_logfile    =   " . $formVars['app_logfile'];

        if ($formVars['id'] == 0) {
          $query = "insert into rsdp_applications set app_id = null," . $q_string;
          $message = "Configuration added.";
        }
        if ($formVars['id'] > 0) {
          $query = "update rsdp_applications set " . $q_string . " where app_id = " . $formVars['id'];
          $message = "Configuration updated.";
        }

        logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rsdp']);

        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

        print "alert('" . $message . "');\n";
      }


# four options here
# -1 - send a reminder email
# 0 - save data
# 1 - notify san if physical and san mounts are requested
# 1 - or notify platforms folks
# 2 - save and exit

# Send an e-mail to the applications team to remind them of the awaiting task
      if ($formVars['app_complete'] == -1) {
        $q_string  = "select rsdp_application ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        generateEmail(
          $formVars['rsdp'],
          "<p>Reminder: The Server has been configured and is ready for the installation of Applications.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/application/installed.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: The Server is ready for Application Installation",
          "rsdp_apppoc",
          $a_rsdp_server['rsdp_application']
        );
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['app_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

      if ($formVars['app_complete'] == 1) {
        setstatus($formVars['rsdp'], 1, 15);

# this is the san part.
# if step 13 and step 14 is done (> 0), then
#   if monitoring is requested, send an e-mail to the monitoring team,
# otherwise
#   send to the applications team to complete the installation

# check the status of the backups task
        $backups = 0;
        $q_string  = "select st_completed ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $formVars['rsdp'] . " and st_step = 13";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

        if (mysqli_num_rows($q_rsdp_status) > 0) {
          $backups = 1;
        }

# check the status of the monitoring task
        $monitoring = 0;
        $q_string  = "select st_completed ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $formVars['rsdp'] . " and st_step = 14";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

        if (mysqli_num_rows($q_rsdp_status) > 0) {
          $monitoring = 1;
        }

# ok, if both have been completed, check the rsdp_appmonitor setting, otherwise no e-mail goes out.
        if ($backups == 1 && $monitoring == 1) {
# Check to see if monitoring has been requested; check here only if backups were not requested so the os monitoring can also be marked as skipped
          $q_string  = "select rsdp_appmonitor ";
          $q_string .= "from rsdp_server ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'];
          $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

          if ($a_rsdp_server['rsdp_appmonitor'] == 0) {
            setstatus($formVars['rsdp'], 2, 16);

# send e-mail to the Application folks since no app monitoring is required
            $q_string  = "select rsdp_application ";
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
          }
        }
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['app_complete'] == 2) {
        print "alert('Data Saved.');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
