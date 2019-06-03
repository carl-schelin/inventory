<?php
# Script: physical.mysql.php
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
    $package = "physical.mysql.php";
    $formVars['pf_id']       = clean($_GET['pf_id'],       10);
    $formVars['dc_id']       = clean($_GET['dc_id'],       10);
    $formVars['if_id']       = clean($_GET['if_id'],       10);
    $formVars['rsdp']        = clean($_GET['rsdp'],        10);
    $formVars['pf_complete'] = clean($_GET['complete'],    10);

    if ($formVars['pf_id'] == '') {
      $formVars['pf_id'] = 0;
    }
    if ($formVars['dc_id'] == '') {
      $formVars['dc_id'] = 0;
    }
    if ($formVars['if_id'] == '') {
      $formVars['if_id'] = 0;
    }
    if ($formVars['pf_complete'] == '') {
      $formVars['pf_complete'] = 0;
    }

    if (check_userlevel(2)) {
# save, submit, and save and exit
      if ($formVars['pf_complete'] == 0 || $formVars['pf_complete'] == 1 || $formVars['pf_complete'] == 2) {
        $formVars["pf_row"]      = clean($_GET["pf_row"],            20);
        $formVars["pf_rack"]     = clean($_GET["pf_rack"],           20);
        $formVars["pf_unit"]     = clean($_GET["pf_unit"],           10);
        $formVars["pf_circuita"] = clean($_GET["pf_circuita"],       20);
        $formVars["pf_circuitb"] = clean($_GET["pf_circuitb"],       20);

        if ($formVars['pf_unit'] == '') {
          $formVars['pf_unit'] = 0;
        }

        logaccess($_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "pf_rsdp         =   " . $formVars['rsdp']        . "," . 
          "pf_row          = \"" . $formVars['pf_row']      . "\"," . 
          "pf_rack         = \"" . $formVars['pf_rack']     . "\"," . 
          "pf_unit         =   " . $formVars['pf_unit']     . "," . 
          "pf_circuita     = \"" . $formVars['pf_circuita'] . "\"," . 
          "pf_circuitb     = \"" . $formVars['pf_circuitb'] . "\"";

        if ($formVars['pf_id'] == 0) {
          $query = "insert into rsdp_platform set pf_id = null," . $q_string;
        }
        if ($formVars['pf_id'] > 0) {
          $query = "update rsdp_platform set " . $q_string . " where pf_id = " . $formVars['pf_id'];
        }

        logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['pf_id']);

        mysql_query($query) or die($query . ": " . mysql_error());


# Data Center Checklist
        $formVars['dc_power']      = clean($_GET['dc_power'],        10);
        $formVars['dc_cables']     = clean($_GET['dc_cables'],       10);
        $formVars['dc_infra']      = clean($_GET['dc_infra'],        10);
        $formVars['dc_received']   = clean($_GET['dc_received'],     10);
        $formVars['dc_installed']  = clean($_GET['dc_installed'],    10);
        $formVars['dc_checklist']  = clean($_GET['dc_checklist'],    10);
        $formVars['dc_path']       = clean($_GET['dc_path'],        255);

        if ($formVars['dc_power'] == 'true') {
          $formVars['dc_power'] = 1;
        } else {
          $formVars['dc_power'] = 0;
        }
        if ($formVars['dc_cables'] == 'true') {
          $formVars['dc_cables'] = 1;
        } else {
          $formVars['dc_cables'] = 0;
        }
        if ($formVars['dc_infra'] == 'true') {
          $formVars['dc_infra'] = 1;
        } else {
          $formVars['dc_infra'] = 0;
        }
        if ($formVars['dc_received'] == 'true') {
          $formVars['dc_received'] = 1;
        } else {
          $formVars['dc_received'] = 0;
        }
        if ($formVars['dc_installed'] == 'true') {
          $formVars['dc_installed'] = 1;
        } else {
          $formVars['dc_installed'] = 0;
        }
        if ($formVars['dc_checklist'] == 'true') {
          $formVars['dc_checklist'] = 1;
        } else {
          $formVars['dc_checklist'] = 0;
        }

        $q_string =
          "dc_power        =   " . $formVars['dc_power']        . "," .
          "dc_cables       =   " . $formVars['dc_cables']       . "," .
          "dc_infra        =   " . $formVars['dc_infra']        . "," .
          "dc_received     =   " . $formVars['dc_received']     . "," .
          "dc_installed    =   " . $formVars['dc_installed']    . "," .
          "dc_checklist    =   " . $formVars['dc_checklist']    . "," .
          "dc_path         = \"" . $formVars['dc_path']         . "\"";

        if ($formVars['dc_id'] == 0) {
          $query = "insert into rsdp_datacenter set dc_id = null," . $q_string;
        }
        if ($formVars['dc_id'] > 0) {
          $query = "update rsdp_datacenter set " . $q_string . " where dc_id = " . $formVars['dc_id'];
        }

        mysql_query($query) or die($query . ": " . mysql_error());


# Infrastructure Checklist
        $formVars['if_dcrack']    = clean($_GET['if_dcrack'],      10);
        $formVars['if_dccabled']  = clean($_GET['if_dccabled'],    10);

        if ($formVars['if_dcrack'] == 'true') {
          $formVars['if_dcrack'] = 1;
        } else {
          $formVars['if_dcrack'] = 0;
        }
        if ($formVars['if_dccabled'] == 'true') {
          $formVars['if_dccabled'] = 1;
        } else {
          $formVars['if_dccabled'] = 0;
        }

        $q_string =
          "if_dcrack       =   " . $formVars['if_dcrack']       . "," .
          "if_dccabled     =   " . $formVars['if_dccabled'];

        if ($formVars['if_id'] == 0) {
          $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        }
        if ($formVars['if_id'] > 0) {
          $query = "update rsdp_infrastructure set " . $q_string . " where if_id = " . $formVars['if_id'];
        }

        logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rsdp']);

        mysql_query($query) or die($query . ": " . mysql_error());

        logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['if_id']);
      }


# four options here
# -1 - send a reminder email
# 0 - save data
# 1 - notify san if physical and san mounts are requested
# 1 - or notify platforms folks
# 2 - save and exit

# Send an e-mail to the data center folks to remind them of the awaiting task
      if ($formVars['pf_complete'] == -1) {
        generateEmail(
          $formVars['rsdp'],
          "<p>Reminder: The SAN and LAN ports have been identified and IP addresses assigned.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/physical/physical.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: SAN and Network Design Completed",
          "rsdp_dcpoc",
          $GRP_DataCenter
        );
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['pf_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

      if ($formVars['pf_complete'] == 1) {
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
          "RSDP: Physical System has been Racked and Cabled", 
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

      if ($formVars['pf_complete'] == 2) {
        print "alert('Data Saved.');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
