<?php
# Script: network.mysql.php
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
    $package = "network.mysql.php";
    $formVars['id']           = clean($_GET['id'],           10);
    $formVars["rsdp"]         = clean($_GET["rsdp"],         10);
    $formVars["if_complete"]  = clean($_GET["complete"],     10);
    $formVars["if_netcheck"]  = clean($_GET["if_netcheck"],  10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if ($formVars['if_netcheck'] == 'true') {
      $formVars['if_netcheck'] = 1;
    } else {
      $formVars['if_netcheck'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['if_complete'] == 0 || $formVars['if_complete'] == 1 || $formVars['if_complete'] == 2) {
        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "if_rsdp       =   " . $formVars['rsdp']         . "," .
          "if_netcheck   =   " . $formVars['if_netcheck'];

        if ($formVars['id'] == 0) {
          $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
          $message = "Network added.";
        }
        if ($formVars['id'] > 0) {
          $query = "update rsdp_infrastructure set " . $q_string . " where if_id = " . $formVars['id'];
          $message = "Network updated.";
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

# Send an e-mail to the networking folks to remind them of the awaiting task
      if ($formVars['if_complete'] == -1) {
        generateEmail(
          $formVars['rsdp'],
          "<p>Reminder: The new Server has been designed and the network interfaces need to be configured.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/network/network.php?rsdp=" . $formVars['rsdp'] . "#tabs-4\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: Server Designed Task completed",
          "rsdp_networkpoc",
          $GRP_Networking
        );
        print "alert('Network Engineering Reminder E-Mail Sent');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['if_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

# now set status as complete and send out e-mails.
      if ($formVars['if_complete'] == 1) {
        setstatus($formVars['rsdp'], 1, 4);

# only send the e-mail if step 3 is also complete
        $q_string  = "select st_id,st_completed ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $formVars['rsdp'] . " and st_step = 3";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# if step 3 is complete
        if (mysqli_num_rows($q_rsdp_status) > 0) {

# special bit for systems that are virtual machines. Make sure the e-mail is properly sent
          $virtual = rsdp_Virtual($formVars['rsdp']);

# now see if it is a Virtual Machine; if so, send the e-mail to the VM team and change the link to the 5vm link.
          if ($virtual == 1) {
            generateEmail(
              $formVars['rsdp'],
              "<p>The SAN and LAN ports have been identified and IP addresses assigned.</p>", 
              "<p>Click on <a href=\"" . $RSDProot . "/virtual/virtual.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
              "RSDP: SAN and Network Design Completed", 
              "rsdp_virtpoc",
              $GRP_Virtualization
            );

# generate a Ticket
            $q_string  = "select tkt_virtual ";
            $q_string .= "from rsdp_tickets ";
            $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
            $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
            if ($a_rsdp_tickets['tkt_virtual']) {
              submit_Ticket(
                $formVars['rsdp'],
                $RSDProot . "/virtual/virtual.php",
                "rsdp_virtpoc",
                $GRP_Virtualization
              );
            }

            $alertmsg = "Network Engineering Task Submitted to Virtualization";
          } else {
# send email to the data center folks.
            generateEmail(
              $formVars['rsdp'],
              "<p>The SAN and LAN ports have been identified and IP addresses assigned.</p>", 
              "<p>Click on <a href=\"" . $RSDProot . "/physical/physical.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
              "RSDP: SAN and Network Design Completed", 
              "rsdp_dcpoc",
              $GRP_DataCenter
            );

# generate a Ticket
            $q_string  = "select tkt_datacenter ";
            $q_string .= "from rsdp_tickets ";
            $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
            $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
            if ($a_rsdp_tickets['tkt_datacenter']) {
              submit_Ticket(
                $formVars['rsdp'],
                $RSDProot . "/physical/physical.php",
                "rsdp_dcpoc",
                $GRP_DataCenter
              );
            }

            $alertmsg = "Network Engineering Task Submitted to Data Center";
          }
        }
        print "alert('" . $alertmsg . "');\n";
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
