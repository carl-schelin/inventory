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
    $formVars['id']             = clean($_GET['id'],             10);
    $formVars['rsdp']           = clean($_GET['rsdp'],           10);
    $formVars['app_complete']   = clean($_GET['complete'],       10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['app_complete'] == 0 || $formVars['app_complete'] == 1 || $formVars['app_complete'] == 2) {
        $formVars['app_concheck']   = clean($_GET['app_concheck'],     10);
        $formVars['app_tested']     = clean($_GET['app_tested'],       10);
        $formVars['app_integrated'] = clean($_GET['app_integrated'],   10);
        $formVars['app_failover']   = clean($_GET['app_failover'],     10);

        if ($formVars['app_concheck'] == 'true') {
          $formVars['app_concheck'] = 1;
        } else {
          $formVars['app_concheck'] = 0;
        }
        if ($formVars['app_tested'] == 'true') {
          $formVars['app_tested'] = 1;
        } else {
          $formVars['app_tested'] = 0;
        }
        if ($formVars['app_integrated'] == 'true') {
          $formVars['app_integrated'] = 1;
        } else {
          $formVars['app_integrated'] = 0;
        }
        if ($formVars['app_failover'] == 'true') {
          $formVars['app_failover'] = 1;
        } else {
          $formVars['app_failover'] = 0;
        }

        logaccess($_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "app_rsdp       =   " . $formVars['rsdp']           . "," .
          "app_tested     =   " . $formVars['app_tested']     . "," .
          "app_integrated =   " . $formVars['app_integrated'] . "," . 
          "app_concheck   =   " . $formVars['app_concheck']   . "," . 
          "app_failover   =   " . $formVars['app_failover'];

        if ($formVars['id'] == 0) {
          $query = "insert into rsdp_applications set app_id = null," . $q_string;
          $message = "Configuration added.";
        }
        if ($formVars['id'] > 0) {
          $query = "update rsdp_applications set " . $q_string . " where app_id = " . $formVars['id'];
          $message = "Configuration updated.";
        }

        logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rsdp']);

        mysql_query($query) or die($query . ": " . mysql_error());

        print "alert('" . $message . "');\n";
      }


# four options here:
# -1 - Reminder to the Applications folks is necessary.
#   notify the applications folks.
# 0 - Data has been saved.
# 1 - task has been completed.
#   notify the platforms folks that an infosec scan can be started
# 2 - Data has been saved and exit to the task page.

# Send an e-mail to the applications team to remind them of the awaiting task
      if ($formVars['app_complete'] == -1) {
        $q_string  = "select rsdp_application ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

        generateEmail(
          $formVars['rsdp'],
          "<p>Reminder: Application monitoring has been completed and the new Server is ready to have the Application completed.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/application/configured.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: Application Monitoring has been Completed",
          "rsdp_apppoc",
          $a_rsdp_server['rsdp_application']
        );
        print "alert('Application Reminder E-Mail Sent');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['app_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

      if ($formVars['app_complete'] == 1) {
        setstatus($formVars['rsdp'], 1, 17);

        $q_string  = "select rsdp_platform ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

        generateEmail(
          $formVars['rsdp'],
          "<p>The Server is fully configured and ready to be scanned by InfoSec.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/infosec/scanned.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
          "RSDP: Server is ready to be scanned", 
          "rsdp_platformspoc",
          $a_rsdp_server['rsdp_platform']
        );

# generate a Ticket
        $q_string  = "select tkt_infosec ";
        $q_string .= "from rsdp_tickets ";
        $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
        $q_rsdp_tickets = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_rsdp_tickets = mysql_fetch_array($q_rsdp_tickets);
        if ($a_rsdp_tickets['tkt_infosec']) {
          submit_Ticket(
            $formVars['rsdp'],
            $RSDProot . "/infosec/scanned.php",
            "rsdp_platformspoc",
            $a_rsdp_server['rsdp_platform']
          );
        }

# also generate an InfoSec scan ticket
        $q_string  = "select tkt_sysscan ";
        $q_string .= "from rsdp_tickets ";
        $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
        $q_rsdp_tickets = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_rsdp_tickets = mysql_fetch_array($q_rsdp_tickets);
        if ($a_rsdp_tickets['tkt_sysscan']) {
          submit_Scan($formVars['rsdp']);
        }

        print "alert('Application Task Submitted');\n";
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
