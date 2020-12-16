<?php
# Script: designed.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "designed.mysql.php";
    $formVars["id"]           = clean($_GET["id"],        10);
    $formVars["rsdp"]         = clean($_GET["rsdp"],      10);
    $formVars["san_complete"] = clean($_GET["complete"],  10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['san_complete'] == '') {
      $formVars['san_complete'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['san_complete'] == 0 || $formVars['san_complete'] == 1 || $formVars['san_complete'] == 2) {
        $formVars["san_checklist"]    = clean($_GET["san_checklist"], 10);

        if ($formVars['san_checklist'] == 'true') {
          $formVars['san_checklist'] = 1;
        } else {
          $formVars['san_checklist'] = 0;
        }
        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "san_rsdp      =   " . $formVars['rsdp']           . "," .
          "san_complete  =   " . $formVars['san_complete']   . "," . 
          "san_checklist =   " . $formVars['san_checklist'];

        if ($formVars['id'] == 0) {
          $query = "insert into rsdp_designed set san_id = null," . $q_string;
          $message = "Configuration added.";
        }
        if ($formVars['id'] > 0) {
          $query = "update rsdp_designed set " . $q_string . " where san_id = " . $formVars['id'];
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

# Sending a Reminder to the SAN team
      if ($formVars['san_complete'] == -1) {
        $q_string  = "select san_id ";
        $q_string .= "from rsdp_san ";
        $q_string .= "where san_rsdp = " . $formVars['rsdp'];
        $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_san = mysqli_fetch_array($q_rsdp_san);

# if there aren't any HBA ports configured, skip it
        if (mysqli_num_rows($q_rsdp_san) == 0) {
          setstatus($db, $formVars['rsdp'], 2, 3);
        } else {

          generateEmail(
            $db, 
            $formVars['rsdp'],
            "<p>Reminder: The new Server has been designed and the SAN switch ports need to be allocated.</p>",
            "<p>Click on <a href=\"" . $RSDProot . "/san/designed.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
            "RSDP Reminder: Server Designed Task completed",
            "rsdp_sanpoc",
            $GRP_SAN
          );
        }
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['san_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

# now set status as complete and send out e-mails.
      if ($formVars['san_complete'] == 1) {
        setstatus($db, $formVars['rsdp'], 1, 3);

# only send the e-mail if step 4 is also complete
        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $formVars['rsdp'] . " and st_step = 4";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

# if step 4 is complete
        if (mysqli_num_rows($q_rsdp_status) > 0) {

# special bit for systems that are virtual machines. Make sure the e-mail is properly sent
          $virtual = rsdp_Virtual($db, $formVars['rsdp']);

# now see if it is a Virtual Machine; if so, send the e-mail to the VM team and change the link to the 5vm link.
          if ($virtual) {
            generateEmail(
              $db, 
              $formVars['rsdp'],
              "<p>The LAN has been configured and IP addresses assigned.</p>", 
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
                $db, 
                $formVars['rsdp'],
                $RSDProot . "/virtual/virtual.php",
                "rsdp_virtpoc",
                $GRP_Virtualization
              );
            }
          } else {
# otherwise notification goes to the data center group
            generateEmail(
              $db, 
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
                $db, 
                $formVars['rsdp'],
                $RSDProot . "/physical/physical.php",
                "rsdp_dcpoc",
                $GRP_DataCenter
              );
            }
          }
        }
        print "alert('SAN Task Submitted');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['san_complete'] == 2) {
        print "alert('Data Saved.');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
