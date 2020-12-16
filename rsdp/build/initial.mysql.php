<?php
# Script: initial.mysql.php
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
    $package = "initial.mysql.php";
    $ticket = 'yes';
    $ticket = 'no';
    $formVars["rsdp"]              = clean($_GET["rsdp"],              10);
    $formVars["rsdp_complete"]     = clean($_GET["complete"],          10);
    $formVars["rsdp_requestor"]    = clean($_GET["rsdp_requestor"],    10);

    if (check_userlevel($db, $AL_Edit)) {
# save, submit, and save and exit
      if ($formVars['rsdp_complete'] == 0 || $formVars['rsdp_complete'] == 1 || $formVars['rsdp_complete'] == 2) {
        $formVars["usr_phone"]         = clean($_GET["usr_phone"],    15);
        $formVars["usr_email"]         = clean($_GET["usr_email"],   255);
        $formVars["usr_deptname"]      = clean($_GET["usr_deptname"], 10);

# update the user information, just to have up to date information without querying everyone
        $q_string = 
          "usr_phone    = \"" . $formVars['usr_phone']    . "\"," . 
          "usr_email    = \"" . $formVars['usr_email']    . "\"," . 
          "usr_deptname =   " . $formVars['usr_deptname'];

# it is assumed that the users already exist so only update the information
        $query  = "update users ";
        $query .= "set " . $q_string . " ";
        $query .= "where usr_id = \"" . $formVars['rsdp_requestor'] . "\"";
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));


        $formVars["rsdp_location"]     = clean($_GET["rsdp_location"],     10);
        $formVars["rsdp_product"]      = clean($_GET["rsdp_product"],      10);
        $formVars["rsdp_completion"]   = clean($_GET["rsdp_completion"],   12);
        $formVars["rsdp_project"]      = clean($_GET["rsdp_project"],      10);
        $formVars["rsdp_platformspoc"] = clean($_GET["rsdp_platformspoc"], 10);
        $formVars["rsdp_sanpoc"]       = clean($_GET["rsdp_sanpoc"],       10);
        $formVars["rsdp_networkpoc"]   = clean($_GET["rsdp_networkpoc"],   10);
        $formVars["rsdp_virtpoc"]      = clean($_GET["rsdp_virtpoc"],      10);
        $formVars["rsdp_dcpoc"]        = clean($_GET["rsdp_dcpoc"],        10);
        $formVars["rsdp_monitorpoc"]   = clean($_GET["rsdp_monitorpoc"],   10);
        $formVars["rsdp_apppoc"]       = clean($_GET["rsdp_apppoc"],       10);
        $formVars["rsdp_backuppoc"]    = clean($_GET["rsdp_backuppoc"],    10);
        $formVars["rsdp_platform"]     = clean($_GET["rsdp_platform"],     10);
        $formVars["rsdp_application"]  = clean($_GET["rsdp_application"],  10);
        $formVars["rsdp_service"]      = clean($_GET["rsdp_service"],      10);
        $formVars["rsdp_vendor"]       = clean($_GET["rsdp_vendor"],       10);
        $formVars["rsdp_function"]     = clean($_GET["rsdp_function"],     50);
        $formVars["rsdp_processors"]   = clean($_GET["rsdp_processors"],   10);
        $formVars["rsdp_memory"]       = clean($_GET["rsdp_memory"],       20);
        $formVars["rsdp_ossize"]       = clean($_GET["rsdp_ossize"],       20);
        $formVars["rsdp_osmonitor"]    = clean($_GET["rsdp_osmonitor"],    10);
        $formVars["rsdp_appmonitor"]   = clean($_GET["rsdp_appmonitor"],   10);
        $formVars["rsdp_datapalette"]  = clean($_GET["rsdp_datapalette"],  10);
        $formVars["rsdp_opnet"]        = clean($_GET["rsdp_opnet"],        10);
        $formVars["rsdp_newrelic"]     = clean($_GET["rsdp_newrelic"],     10);
        $formVars["rsdp_centrify"]     = clean($_GET["rsdp_centrify"],     10);
        $formVars["rsdp_backup"]       = clean($_GET["rsdp_backup"],       10);

        if ($formVars['rsdp_processors'] == '') {
          $formVars['rsdp_processors'] = 0;
        }
        if ($formVars['rsdp_ossize'] == '') {
          $formVars['rsdp_ossize'] = 0;
        }
        if ($formVars['rsdp_osmonitor'] == 'true') {
          $formVars['rsdp_osmonitor'] = 1;
        } else {
          $formVars['rsdp_osmonitor'] = 0;
        }
        if ($formVars['rsdp_appmonitor'] == 'true') {
          $formVars['rsdp_appmonitor'] = 1;
        } else {
          $formVars['rsdp_appmonitor'] = 0;
        }
        if ($formVars['rsdp_datapalette'] == 'true') {
          $formVars['rsdp_datapalette'] = 1;
        } else {
          $formVars['rsdp_datapalette'] = 0;
        }
        if ($formVars['rsdp_opnet'] == 'true') {
          $formVars['rsdp_opnet'] = 1;
        } else {
          $formVars['rsdp_opnet'] = 0;
        }
        if ($formVars['rsdp_newrelic'] == 'true') {
          $formVars['rsdp_newrelic'] = 1;
        } else {
          $formVars['rsdp_newrelic'] = 0;
        }
        if ($formVars['rsdp_centrify'] == 'true') {
          $formVars['rsdp_centrify'] = 1;
        } else {
          $formVars['rsdp_centrify'] = 0;
        }
        if ($formVars['rsdp_backup'] == 'true') {
          $formVars['rsdp_backup'] = 1;
        } else {
          $formVars['rsdp_backup'] = 0;
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# Now create or update the main server record
        $q_string = 
          "rsdp_requestor    =   " . $formVars['rsdp_requestor']    . "," .
          "rsdp_location     =   " . $formVars['rsdp_location']     . "," .
          "rsdp_product      =   " . $formVars['rsdp_product']      . "," .
          "rsdp_completion   = \"" . $formVars['rsdp_completion']   . "\"," .
          "rsdp_project      =   " . $formVars['rsdp_project']      . "," .
          "rsdp_platformspoc =   " . $formVars["rsdp_platformspoc"] . "," . 
          "rsdp_sanpoc       =   " . $formVars["rsdp_sanpoc"]       . "," . 
          "rsdp_networkpoc   =   " . $formVars["rsdp_networkpoc"]   . "," . 
          "rsdp_virtpoc      =   " . $formVars["rsdp_virtpoc"]      . "," . 
          "rsdp_dcpoc        =   " . $formVars["rsdp_dcpoc"]        . "," . 
          "rsdp_monitorpoc   =   " . $formVars["rsdp_monitorpoc"]   . "," . 
          "rsdp_apppoc       =   " . $formVars["rsdp_apppoc"]       . "," . 
          "rsdp_backuppoc    =   " . $formVars["rsdp_backuppoc"]    . "," . 
          "rsdp_platform     =   " . $formVars['rsdp_platform']     . "," .
          "rsdp_application  =   " . $formVars['rsdp_application']  . "," .
          "rsdp_service      =   " . $formVars['rsdp_service']      . "," .
          "rsdp_vendor       =   " . $formVars['rsdp_vendor']       . "," .
          "rsdp_function     = \"" . $formVars['rsdp_function']     . "\"," .
          "rsdp_processors   =   " . $formVars['rsdp_processors']   . "," .
          "rsdp_memory       = \"" . $formVars['rsdp_memory']       . "\"," .
          "rsdp_ossize       = \"" . $formVars['rsdp_ossize']       . "\"," .
          "rsdp_osmonitor    =   " . $formVars['rsdp_osmonitor']    . "," .
          "rsdp_appmonitor   =   " . $formVars['rsdp_appmonitor']   . "," .
          "rsdp_datapalette  =   " . $formVars['rsdp_datapalette']  . "," .
          "rsdp_opnet        =   " . $formVars['rsdp_opnet']        . "," .
          "rsdp_newrelic     =   " . $formVars['rsdp_newrelic']     . "," .
          "rsdp_centrify     =   " . $formVars['rsdp_centrify']     . "," .
          "rsdp_backup       =   " . $formVars['rsdp_backup']       . "," .
          "rsdp_complete     =   " . $formVars['rsdp_complete'];

        if ($formVars['rsdp'] > 0) {
          $query = "update rsdp_server set " . $q_string . " where rsdp_id = " . $formVars['rsdp'];
          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        }

        if ($formVars['rsdp'] == 0) {
          $query = "insert into rsdp_server set rsdp_id = null," . $q_string;
          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
          $formVars['rsdp'] = last_insert_id($db);
          print "document.rsdp.rsdp.value = "          . $formVars['rsdp'] . ";\n";
          print "document.comments.com_rsdp.value = "  . $formVars['rsdp'] . ";\n";
          print "document.filesystem.fs_rsdp.value = " . $formVars['rsdp'] . ";\n";
          print "document.rsdp.adddup.disabled = false;\n";
        }

        if ($ticket == 'yes') {
          $formVars['tkt_id']         = clean($_GET['tkt_id'],         10);
          $formVars['tkt_build']      = clean($_GET['tkt_build'],      10);
          $formVars['tkt_san']        = clean($_GET['tkt_san'],        10);
          $formVars['tkt_network']    = clean($_GET['tkt_network'],    10);
          $formVars['tkt_datacenter'] = clean($_GET['tkt_datacenter'], 10);
          $formVars['tkt_virtual']    = clean($_GET['tkt_virtual'],    10);
          $formVars['tkt_sysins']     = clean($_GET['tkt_sysins'],     10);
          $formVars['tkt_sysdns']     = clean($_GET['tkt_sysdns'],     10);
          $formVars['tkt_storage']    = clean($_GET['tkt_storage'],    10);
          $formVars['tkt_syscnf']     = clean($_GET['tkt_syscnf'],     10);
          $formVars['tkt_backups']    = clean($_GET['tkt_backups'],    10);
          $formVars['tkt_monitor']    = clean($_GET['tkt_monitor'],    10);
          $formVars['tkt_appins']     = clean($_GET['tkt_appins'],     10);
          $formVars['tkt_appmon']     = clean($_GET['tkt_appmon'],     10);
          $formVars['tkt_appcnf']     = clean($_GET['tkt_appcnf'],     10);
          $formVars['tkt_infosec']    = clean($_GET['tkt_infosec'],    10);
          $formVars['tkt_sysscan']    = clean($_GET['tkt_sysscan'],    10);

          $q_string =
            "tkt_rsdp        =   " . $formVars['rsdp']           . "," .
            "tkt_build       =   " . $formVars['tkt_build']      . "," .
            "tkt_san         =   " . $formVars['tkt_san']        . "," .
            "tkt_network     =   " . $formVars['tkt_network']    . "," .
            "tkt_datacenter  =   " . $formVars['tkt_datacenter'] . "," .
            "tkt_virtual     =   " . $formVars['tkt_virtual']    . "," .
            "tkt_sysins      =   " . $formVars['tkt_sysins']     . "," .
            "tkt_sysdns      =   " . $formVars['tkt_sysdns']     . "," .
            "tkt_storage     =   " . $formVars['tkt_storage']    . "," .
            "tkt_syscnf      =   " . $formVars['tkt_syscnf']     . "," .
            "tkt_backups     =   " . $formVars['tkt_backups']    . "," .
            "tkt_monitor     =   " . $formVars['tkt_monitor']    . "," .
            "tkt_appins      =   " . $formVars['tkt_appins']     . "," .
            "tkt_appmon      =   " . $formVars['tkt_appmon']     . "," .
            "tkt_appcnf      =   " . $formVars['tkt_appcnf']     . "," .
            "tkt_infosec     =   " . $formVars['tkt_infosec']    . "," .
            "tkt_sysscan     =   " . $formVars['tkt_sysscan'];

          if ($formVars['tkt_id'] == 0) {
            $query = "insert into rsdp_tickets set tkt_id = null," . $q_string;
            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

            $formVars['tkt_id'] = last_insert_id($db);
            print "document.rsdp.tkt_id.value = " . $formVars['tkt_id'] . ";\n";
          }
          if ($formVars['tkt_id'] > 0) {
            $query = "update rsdp_tickets set " . $q_string . " where tkt_id = " . $formVars['tkt_id'];
            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
          }
        }

        $formVars['bu_id']        = clean($_GET['bu_id'],        10);
        $formVars['bu_start']     = clean($_GET['bu_start'],     12);
        $formVars['bu_include']   = clean($_GET['bu_include'],   10);
        $formVars['bu_retention'] = clean($_GET['bu_retention'], 10);
        $formVars['bu_sunday']    = clean($_GET['bu_sunday'],    10);
        $formVars['bu_monday']    = clean($_GET['bu_monday'],    10);
        $formVars['bu_tuesday']   = clean($_GET['bu_tuesday'],   10);
        $formVars['bu_wednesday'] = clean($_GET['bu_wednesday'], 10);
        $formVars['bu_thursday']  = clean($_GET['bu_thursday'],  10);
        $formVars['bu_friday']    = clean($_GET['bu_friday'],    10);
        $formVars['bu_saturday']  = clean($_GET['bu_saturday'],  10);
        $formVars['bu_suntime']   = clean($_GET['bu_suntime'],   10);
        $formVars['bu_montime']   = clean($_GET['bu_montime'],   10);
        $formVars['bu_tuetime']   = clean($_GET['bu_tuetime'],   10);
        $formVars['bu_wedtime']   = clean($_GET['bu_wedtime'],   10);
        $formVars['bu_thutime']   = clean($_GET['bu_thutime'],   10);
        $formVars['bu_fritime']   = clean($_GET['bu_fritime'],   10);
        $formVars['bu_sattime']   = clean($_GET['bu_sattime'],   10);

        if ($formVars['bu_include'] == 'true') {
          $formVars['bu_include'] = 1;
        } else {
          $formVars['bu_include'] = 0;
        }

# see if there's a backup record already for this RSDP id (shouldn't be but best to be sure)
# this eliminates the possibility of duplicate records.
        $q_backup  = "select bu_id ";
        $q_backup .= "from rsdp_backups ";
        $q_backup .= "where bu_rsdp = " . $formVars['rsdp'] . " ";
        $q_rsdp_backups = mysqli_query($db, $q_backup) or die($q_backup . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_rsdp_backups) > 0) {
          $a_rsdp_backups = mysqli_fetch_array($q_rsdp_backups);
          $formVars['bu_id'] = $a_rsdp_backups['bu_id'];
        }

        $q_string = 
          "bu_rsdp      =   " . $formVars['rsdp']         . "," . 
          "bu_start     = \"" . $formVars['bu_start']     . "\"," .
          "bu_include   =   " . $formVars['bu_include']   . "," .
          "bu_retention =   " . $formVars['bu_retention'] . "," .
          "bu_sunday    =   " . $formVars['bu_sunday']    . "," .
          "bu_monday    =   " . $formVars['bu_monday']    . "," .
          "bu_tuesday   =   " . $formVars['bu_tuesday']   . "," .
          "bu_wednesday =   " . $formVars['bu_wednesday'] . "," .
          "bu_thursday  =   " . $formVars['bu_thursday']  . "," .
          "bu_friday    =   " . $formVars['bu_friday']    . "," .
          "bu_saturday  =   " . $formVars['bu_saturday']  . "," .
          "bu_suntime   = \"" . $formVars['bu_suntime']   . "\"," .
          "bu_montime   = \"" . $formVars['bu_montime']   . "\"," .
          "bu_tuetime   = \"" . $formVars['bu_tuetime']   . "\"," .
          "bu_wedtime   = \"" . $formVars['bu_wedtime']   . "\"," .
          "bu_thutime   = \"" . $formVars['bu_thutime']   . "\"," .
          "bu_fritime   = \"" . $formVars['bu_fritime']   . "\"," .
          "bu_sattime   = \"" . $formVars['bu_sattime']   . "\"";

        if ($formVars['bu_id'] == 0) {
          $query = "insert into rsdp_backups set bu_id = null," . $q_string;
          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          $formVars['bu_id'] = last_insert_id($db);
        }
        if ($formVars['bu_id'] > 0) {
          $query = "update rsdp_backups set " . $q_string . " where bu_id = " . $formVars['bu_id'];
          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        }
        print "document.rsdp.bu_id.value = " . $formVars['bu_id'] . ";\n";
      }

######
# Now let's check and create "dummy" records in rsdp_osteam, rsdp_platform, and rsdp_interface for the Provisioning Task to prevent duplicate records from being created.
######

      $q_string  = "select os_id ";
      $q_string .= "from rsdp_osteam ";
      $q_string .= "where os_rsdp = " . $formVars['rsdp'] . " ";
      $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
# if a system record does not exist for this server, make one.
      if (mysqli_num_rows($q_rsdp_osteam) == 0) {
        $q_string  = "insert ";
        $q_string .= "into rsdp_osteam ";
        $q_string .= "set ";
        $q_string .= "os_id = null,";
        $q_string .= "os_rsdp = " . $formVars['rsdp'] . ",";
        $q_string .= "os_sysname = \"Unnamed-" . $formVars['rsdp'] . "\"";

        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      }

      $q_string  = "select pf_id ";
      $q_string .= "from rsdp_platform ";
      $q_string .= "where pf_rsdp = " . $formVars['rsdp'] . " ";
      $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
# if a platform record does not exist for this server, make one.
      if (mysqli_num_rows($q_rsdp_platform) == 0) {
        $q_string  = "insert ";
        $q_string .= "into rsdp_platform ";
        $q_string .= "set ";
        $q_string .= "pf_id = null,";
        $q_string .= "pf_rsdp = " . $formVars['rsdp'] . ",";
        $q_string .= "pf_model = 45";

        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      }


      $q_string  = "select if_id ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
# if an interface record does not exist for this server, make one.
      if (mysqli_num_rows($q_rsdp_interface) == 0) {
        $q_string  = "insert ";
        $q_string .= "into rsdp_interface ";
        $q_string .= "set ";
        $q_string .= "if_id = null,";
        $q_string .= "if_rsdp = " . $formVars['rsdp'] . ",";
        $q_string .= "if_name = \"Unnamed-" . $formVars['rsdp'] . "\"";

        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      }


###
# now set the task as complete
      if ($formVars['rsdp_complete'] == 1) {
        setstatus($db, $formVars['rsdp'], 1, 1);

        $q_string  = "select rsdp_platform ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'];
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        generateEmail(
          $db, 
          $formVars['rsdp'], 
          "<p>A new server request has been submitted through the Rapid Server Deployment Process.</p>", 
          "<p>Click on <a href=\"" . $RSDProot . "/build/build.php?rsdp=" . $formVars['rsdp'] . "\">this link</a> to work on your assigned task</p>", 
          "RSDP: New Server Request", 
          "rsdp_platformspoc", 
          $formVars['rsdp_platform']
        );

# generate a Ticket
        $q_string  = "select tkt_build ";
        $q_string .= "from rsdp_tickets ";
        $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
        $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);
        if ($a_rsdp_tickets['tkt_build']) {
          submit_Ticket(
            $db, 
            $formVars['rsdp'],
            $RSDProot . "/build/build.php",
            "rsdp_platformspoc",
            $a_rsdp_server['rsdp_platform']
          );
        }
        print "window.location = '" . $RSDProot . "/tasks.php?id=" . $formVars['rsdp'] . "';\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
