<?php
# Script: virtual.mysql.php
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
    $package = "virtual.mysql.php";
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
        $formVars['if_vmcheck']  = clean($_GET['if_vmcheck'],   10);
        $formVars['if_netprov']  = clean($_GET['if_netprov'],   10);
        $formVars['if_sanprov']  = clean($_GET['if_sanprov'],   10);
        $formVars['if_vmprov']   = clean($_GET['if_vmprov'],    10);
        $formVars['if_vmnote']   = clean($_GET['if_vmnote'],   255);

        if ($formVars['if_vmcheck'] == 'true') {
          $formVars['if_vmcheck'] = 1;
        } else {
          $formVars['if_vmcheck'] = 0;
        }
        if ($formVars['if_netprov'] == 'true') {
          $formVars['if_netprov'] = 1;
        } else {
          $formVars['if_netprov'] = 0;
        }
        if ($formVars['if_sanprov'] == 'true') {
          $formVars['if_sanprov'] = 1;
        } else {
          $formVars['if_sanprov'] = 0;
        }
        if ($formVars['if_vmprov'] == 'true') {
          $formVars['if_vmprov'] = 1;
        } else {
          $formVars['if_vmprov'] = 0;
        }

        logaccess($_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "if_rsdp    =   " . $formVars['rsdp']        . "," . 
          "if_vmcheck =   " . $formVars['if_vmcheck']  . "," . 
          "if_netprov =   " . $formVars['if_netprov']  . "," . 
          "if_sanprov =   " . $formVars['if_sanprov']  . "," . 
          "if_vmprov  =   " . $formVars['if_vmprov']   . "," . 
          "if_vmnote  = \"" . $formVars['if_vmnote']   . "\"";

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

# Send an e-mail to the virtualization folks to remind them of the awaiting task
      if ($formVars['if_complete'] == -1) {
        generateEmail(
          $formVars['rsdp'],
          "<p>Reminder: The SAN and LAN ports have been identified and IP addresses assigned.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/virtual/virtual.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: SAN and Network Design Completed",
          "rsdp_virtpoc",
          $GRP_Virtualization
        );
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['if_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

      if ($formVars['if_complete'] == 1) {
# Set the status for all the physical steps
        $arr = array(5, 6, 7, 8, 9);
        foreach ($arr as &$step) {
          setstatus($formVars['rsdp'], 1, $step);
        }

        $q_string  = "select rsdp_platform ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

        generateEmail(
          $formVars['rsdp'],
          "<p>The Virtual Machine has been provisioned and is ready to install.</p>", 
          "<p>Click on <a href=\"" . $RSDProot . "/system/installed.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
          "RSDP: Virtual Machine has been Provisioned", 
          "rsdp_platformspoc",
          $a_rsdp_server['rsdp_platform']
        );

# generate a Ticket
        $q_string  = "select tkt_sysins ";
        $q_string .= "from rsdp_tickets ";
        $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
        $q_rsdp_tickets = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_rsdp_tickets = mysql_fetch_array($q_rsdp_tickets);
        if ($a_rsdp_tickets['tkt_sysins']) {
          submit_Ticket(
            $formVars['rsdp'],
            $RSDProot . "/system/installed.php",
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
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
