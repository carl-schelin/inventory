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
    $formVars['id']          = clean($_GET['id'],           10);
    $formVars['if_complete'] = clean($_GET['complete'],     10);
    $formVars['rsdp']        = clean($_GET['rsdp'],         10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['if_complete'] == '') {
      $formVars['if_complete'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['if_complete'] == 0 || $formVars['if_complete'] == 1 || $formVars['if_complete'] == 2) {
        $formVars['if_inscheck']   = clean($_GET['if_inscheck'],  10);
        $formVars['if_config']     = clean($_GET['if_config'],    10);
        $formVars['if_built']      = clean($_GET['if_built'],     10);
        $formVars['if_network']    = clean($_GET['if_network'],   10);
        $formVars['if_dns']        = clean($_GET['if_dns'],       10);

        if ($formVars['if_inscheck'] == 'true') {
          $formVars['if_inscheck'] = 1;
        } else {
          $formVars['if_inscheck'] = 0;
        }
        if ($formVars['if_config'] == 'true') {
          $formVars['if_config'] = 1;
        } else {
          $formVars['if_config'] = 0;
        }
        if ($formVars['if_built'] == 'true') {
          $formVars['if_built'] = 1;
        } else {
          $formVars['if_built'] = 0;
        }
        if ($formVars['if_network'] == 'true') {
          $formVars['if_network'] = 1;
        } else {
          $formVars['if_network'] = 0;
        }
        if ($formVars['if_dns'] == 'true') {
          $formVars['if_dns'] = 1;
        } else {
          $formVars['if_dns'] = 0;
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "if_rsdp      = " . $formVars['rsdp']         . "," . 
          "if_config    = " . $formVars['if_config']    . "," . 
          "if_built     = " . $formVars['if_built']     . "," . 
          "if_network   = " . $formVars['if_network']   . "," . 
          "if_dns       = " . $formVars['if_dns']       . "," . 
          "if_inscheck  = " . $formVars['if_inscheck'];

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


# three options here
# -1 - send a reminder email
# 0 - save data
# 1 - notify san if physical and san mounts are requested
# 1 - or notify platforms folks
# 2 - save data and exit

# Send an e-mail to the platforms folks to remind them of the awaiting task
      if ($formVars['if_complete'] == -1) {
        $q_string  = "select rsdp_platform ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        generateEmail(
          $db, 
          $formVars['rsdp'],
          "<p>Reminder: The Virtual Machine has been provisioned and is ready to install.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/system/installed.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: Virtual Machine has been Provisioned",
          "rsdp_platformspoc",
          $a_rsdp_server['rsdp_platform']
        );
        print "alert('Platforms Reminder Submitted');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['if_complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

      if ($formVars['if_complete'] == 1) {
        setstatus($db, "$formVars['rsdp'], 1, 10);

        $virtual = rsdp_Virtual($db, "$formVars['rsdp']);

        $q_string  = "select fs_id ";
        $q_string .= "from rsdp_filesystem ";
        $q_string .= "where fs_rsdp = " . $formVars['rsdp'];
        $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem);

# greater than zero and a physical system, tell the SAN guys they're up.
        if (mysqli_num_rows($q_rsdp_filesystem) > 0 && $virtual == 0) {
          generateEmail(
            $db, 
            $formVars['rsdp'],
            "<p>The Server hardware has been configured and documented, the Operating System installed. The SAN mounts need to be provisioned and presented.</p>", 
            "<p>Click on <a href=\"" . $RSDProot . "/san/provisioned.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
            "RSDP: Infrastructure has been documented", 
            "rsdp_sanpoc",
            $GRP_SAN
          );

# generate a Ticket
          $q_string  = "select tkt_storage ";
          $q_string .= "from rsdp_tickets ";
          $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
          $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
          if ($a_rsdp_tickets['tkt_storage']) {
            submit_Ticket(
              $db, 
              $formVars['rsdp'],
              $RSDProot . "/san/provisioned.php",
              "rsdp_sanpoc",
              $GRP_SAN
            );
          }
          print "alert('SAN task submitted');\n";
        } else {
# skip step 11 as there are no extra file systems to be provisioned. E-mail the platforms group; redundant but best to follow the steps.
          setstatus($db, "$formVars['rsdp'], 2, 11);

          $q_string  = "select rsdp_platform ";
          $q_string .= "from rsdp_server ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'];
          $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

          generateEmail(
            $db, 
            $formVars['rsdp'],
            "<p>The Server hardware has been configured and documented, the Operating System installed. No SAN mounts are required.</p>", 
            "<p>Click on <a href=\"" . $RSDProot . "/system/configured.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
            "RSDP: Infrastructure has been documented", 
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
              $db, 
              $formVars['rsdp'],
              $RSDProot . "/system/configured.php",
              "rsdp_platformspoc",
              $a_rsdp_server['rsdp_platform']
            );
          }
          print "alert('Platforms task submitted');\n";
        }

# generate a DNS ticket
        $q_string  = "select tkt_sysdns ";
        $q_string .= "from rsdp_tickets ";
        $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
        $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
        if ($a_rsdp_tickets['tkt_sysdns']) {
          submit_DNS($db, $formVars['rsdp']);
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
