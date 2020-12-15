<?php
# Script: provisioned.mysql.php
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
    $package = "provisioned.mysql.php";
    $formVars['id']           = clean($_GET['id'],          10);
    $formVars['rsdp']         = clean($_GET['rsdp'],        10);
    $formVars['if_complete']  = clean($_GET['complete'],    10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['if_complete'] == '') {
      $formVars['if_complete'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
# save, submit, and save and exit
      if ($formVars['if_complete'] == 0 || $formVars['if_complete'] == 1 || $formVars['if_complete'] == 2) {
        $formVars['if_procheck']    = clean($_GET['if_procheck'],    10);
        $formVars['if_sanconf']     = clean($_GET['if_sanconf'],     10);
        $formVars['if_provisioned'] = clean($_GET['if_provisioned'], 10);

        if ($formVars['if_procheck'] == 'true') {
          $formVars['if_procheck'] = 1;
        } else {
          $formVars['if_procheck'] = 0;
        }
        if ($formVars['if_sanconf'] == 'true') {
          $formVars['if_sanconf'] = 1;
        } else {
          $formVars['if_sanconf'] = 0;
        }
        if ($formVars['if_provisioned'] == 'true') {
          $formVars['if_provisioned'] = 1;
        } else {
          $formVars['if_provisioned'] = 0;
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "if_rsdp        =   " . $formVars['rsdp']           . "," . 
          "if_procheck    =   " . $formVars['if_procheck']    . "," . 
          "if_sanconf     =   " . $formVars['if_sanconf']     . "," . 
          "if_provisioned =   " . $formVars['if_provisioned'];

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

# Send an e-mail to the SAN team to remind them of the awaiting task
      if ($formVars['if_complete'] == -1) {
        generateEmail(
          $formVars['rsdp'],
          "<p>Reminder: The Server hardware has been configured and documented, the Operating System installed. The SAN mounts need to be provisioned and presented.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/san/provisioned.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: Infrastructure has been documented",
          "rsdp_sanpoc",
          $GRP_SAN
        );
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['if_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

      if ($formVars['if_complete'] == 1) {
        setstatus($formVars['rsdp'], 1, 11);

        $q_string  = "select rsdp_platform ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        generateEmail(
          $formVars['rsdp'],
          "<p>The SAN mounts have been provisioned and presented to the server.</p>", 
          "<p>Click on <a href=\"" . $RSDProot . "/system/configured.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
          "RSDP: SAN mounts are ready",
          "rsdp_platformspoc",
          $a_rsdp_server['rsdp_platform']
        );

# generate a Ticket
        $q_string  = "select tkt_syscnf ";
        $q_string .= "from rsdp_tickets ";
        $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
        $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
        if ($a_rsdp_tickets['tkt_syscnf']) {
          submit_Ticket(
            $formVars['rsdp'],
            $RSDProot . "/system/configured.php",
            "rsdp_platformspoc",
            $a_rsdp_server['rsdp_platform']
          );
        }
        print "alert('Platforms task submitted');\n";
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
