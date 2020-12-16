<?php
# Script: build.mysql.php
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
    $package = "build.mysql.php";
    $formVars['complete']       = clean($_GET['complete'],      10);
    $formVars['rsdp']           = clean($_GET['rsdp'],          10);

    if ($formVars['complete'] == '') {
      $formVars['complete'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['complete'] == 0 || $formVars['complete'] == 1 || $formVars['complete'] == 2) {

############
### Operating System table
############
        $formVars["os_id"]        = clean($_GET["os_id"],       10);
        $formVars["os_sysname"]   = clean($_GET["os_sysname"],  60);
        $formVars["os_fqdn"]      = clean($_GET["os_fqdn"],     60);
        $formVars["os_software"]  = clean($_GET["os_software"], 10);
        $formVars["os_complete"]  = clean($_GET["complete"],    10);

        if (strlen($formVars['os_sysname']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# add info to the osteam table
          $q_osteam = 
            "os_rsdp     =   " . $formVars['rsdp']        . "," .
            "os_sysname  = \"" . $formVars['os_sysname']  . "\"," .
            "os_fqdn     = \"" . $formVars['os_fqdn']     . "\"," .
            "os_software =   " . $formVars['os_software'] . "," .
            "os_complete =   " . $formVars['os_complete'];

          if ($formVars['os_id'] == 0) {
            $query = "insert into rsdp_osteam set os_id = null," . $q_osteam;
          }
          if ($formVars['os_id'] > 0) {
            $query = "update rsdp_osteam set " . $q_osteam . " where os_id = " . $formVars['os_id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving OS Changes to: " . $formVars['os_sysname']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        }


############
### Platform table
############

        $formVars["pf_id"]        = clean($_GET["pf_id"],        10);
        $formVars["pf_model"]     = clean($_GET["pf_model"],     10);
        $formVars["pf_asset"]     = clean($_GET["pf_asset"],     20);
        $formVars["pf_serial"]    = clean($_GET["pf_serial"],   100);
        $formVars["pf_redundant"] = clean($_GET["pf_redundant"], 10);
        $formVars["pf_row"]       = clean($_GET["pf_row"],       20);
        $formVars["pf_rack"]      = clean($_GET["pf_rack"],      20);
        $formVars["pf_unit"]      = clean($_GET["pf_unit"],      10);
        $formVars["pf_special"]   = clean($_GET["pf_special"],  100);
        $formVars["pf_complete"]  = clean($_GET["complete"],     10);

        if ($formVars['pf_redundant'] == 'true') {
          $formVars['pf_redundant'] = 1;
        } else {
          $formVars['pf_redundant'] = 0;
        }

        if ($formVars['pf_unit'] == '') {
          $formVars['pf_unit'] = 0;
        }

        if ($formVars['pf_model'] > 0) {

# finally add info to the platforms table
          $q_platform =
            "pf_rsdp      =   " . $formVars['rsdp']         . "," .
            "pf_model     =   " . $formVars['pf_model']     . "," .
            "pf_asset     = \"" . $formVars['pf_asset']     . "\"," .
            "pf_serial    = \"" . $formVars['pf_serial']    . "\"," .
            "pf_redundant =   " . $formVars['pf_redundant'] . "," .
            "pf_row       = \"" . $formVars['pf_row']       . "\"," .
            "pf_rack      = \"" . $formVars['pf_rack']      . "\"," .
            "pf_unit      =   " . $formVars['pf_unit']      . "," .
            "pf_special   = \"" . $formVars['pf_special']   . "\"," .
            "pf_complete  =   " . $formVars['pf_complete'];

          if ($formVars['pf_id'] == 0) {
            $query = "insert into rsdp_platform set pf_id = null," . $q_platform;
          } else {
            $query = "update rsdp_platform set " . $q_platform . " where pf_id = " . $formVars['pf_id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Platform Changes to: " . $formVars['pf_model']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        }


############
### Update the Model table with any changes.
### Need the pf_model variable as it's the model table to be updated
############

        $formVars["mod_type"]     = clean($_GET["mod_type"],     10);
        $formVars["mod_size"]     = clean($_GET["mod_size"],    100);
        $formVars["mod_plugs"]    = clean($_GET["mod_plugs"],    10);
        $formVars["mod_plugtype"] = clean($_GET["mod_plugtype"], 10);
        $formVars["mod_volts"]    = clean($_GET["mod_volts"],    10);
        $formVars["mod_draw"]     = clean($_GET["mod_draw"],     20);
        $formVars["mod_start"]    = clean($_GET["mod_start"],    20);

        if ($formVars['mod_plugs'] == '') {
          $formVars['mod_plugs'] = 0;
        }

        if ($formVars['pf_model'] > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# update the model info regardless
          $q_string =
            "mod_type     =   " . $formVars['mod_type']     . "," .
            "mod_size     = \"" . $formVars['mod_size']     . "\"," .
            "mod_plugs    =   " . $formVars['mod_plugs']    . "," .
            "mod_plugtype =   " . $formVars['mod_plugtype'] . "," .
            "mod_volts    =   " . $formVars['mod_volts']    . "," .
            "mod_draw     = \"" . $formVars['mod_draw']     . "\"," .
            "mod_start    = \"" . $formVars['mod_start']    . "\"";

          logaccess($db, $_SESSION['uid'], $package, "Saving Model Changes to: " . $formVars['pf_model']);

          $query = "update models set " . $q_string . " where mod_id = " . $formVars['pf_model'];

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        }
      }


# four options here
# -1 - send a reminder email
# 0 - save data
# 1 - notify san if physical and san mounts are requested
# 1 - or notify platforms folks
# 2 - save and exit

# reminder for platforms team
      if ($formVars['complete'] == -1) {
        $q_string  = "select rsdp_platform ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        generateEmail(
          $db, 
          $formVars['rsdp'],
          "<p>Reminder: A new server request has been submitted through the Rapid Server Deployment Process.</p>",
          "<p>Click on <a href=\"" . $RSDProot . "/build/build.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>",
          "RSDP Reminder: New Server Request",
          "rsdp_platformspoc",
          $formVars['rsdp_platform']
        );

        print "alert('Platforms Reminder E-Mail Sent');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['complete'] == 0) {
        print "alert('Data Saved.');\n";
      }

      if ($formVars['complete'] == 1) {
        setstatus($db, "$formVars['rsdp'], 1, 2);

###############################################
#######   Virtual or Physical Machine   #######
###############################################

# special bit for systems that are virtual machines. Can skip all the datacenter bits and the SAN design bit
        $virtual = rsdp_Virtual($db, "$formVars['rsdp']);

# now see if it is a Virtual Machine; if so, send the e-mail to the VM team and change the link to the 5vm link.
        if ($virtual) {
# set the physical SAN task to skip if the system is a virtual machine. The SAN task is to assign physical ports to a physical system via fiber.
          setstatus($db, "$formVars['rsdp'], 2, 3);
        } else {

# now check to see if a SAN mount is requested. if physical, an HBA card will be installed so the SAN admin will need to configure that for the data center manager.
          $q_string  = "select san_id ";
          $q_string .= "from rsdp_san ";
          $q_string .= "where san_rsdp = " . $formVars['rsdp'];
          $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_san = mysqli_fetch_array($q_rsdp_san);

# if there aren't any HBA ports configured, skip it
          if (mysqli_num_rows($q_rsdp_san) == 0) {
            setstatus($db, "$formVars['rsdp'], 2, 3);
          } else {

            generateEmail(
              $db, 
              $formVars['rsdp'],
              "<p>The new Server has been designed and the SAN switch ports need to be allocated.</p>", 
              "<p>Click on <a href=\"" . $RSDProot . "/san/designed.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
              "RSDP: Server Designed Task completed", 
              "rsdp_sanpoc",
              $GRP_SAN
            );

# generate a Ticket
            $q_string  = "select tkt_san ";
            $q_string .= "from rsdp_tickets ";
            $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
            $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
            if ($a_rsdp_tickets['tkt_san']) {
              submit_Ticket(
                $db, 
                $formVars['rsdp'],
                $RSDProot . "/san/designed.php",
                "rsdp_sanpoc",
                $GRP_SAN
              );
            }
          }
        }

# finished. now let the network team know that interfaces need to be configured.
        generateEmail(
          $db, 
          $formVars['rsdp'],
          "<p>The new Server has been designed and the network interfaces need to be configured.</p>", 
          "<p>Click on <a href=\"" . $RSDProot . "/network/network.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
          "RSDP: Server Designed Task completed", 
          "rsdp_networkpoc",
          $GRP_Networking
        );

# generate a Ticket
        $q_string  = "select tkt_network ";
        $q_string .= "from rsdp_tickets ";
        $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
        $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
        if ($a_rsdp_tickets['tkt_network']) {
          submit_Ticket(
            $db, 
            $formVars['rsdp'],
            $RSDProot . "/network/network.php",
            'rsdp_networkpoc', 
            $GRP_Networking
          );
        }

        print "alert('Platforms Task Submitted');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

      if ($formVars['complete'] == 2) {
        print "alert('Data Saved.');\n";
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
