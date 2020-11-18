<?php
# Script: inventory.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  $date = date('Y-m-d');

  if (isset($_SESSION['username'])) {
    $package = "inventory.mysql.php";
    $formVars['id']        = clean($_GET['id'],         10);
    $formVars['update']    = clean($_GET['update'],     10);
    $formVars["inv_name"]  = clean($_GET["inv_name"],   60);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['inv_name'] != 'Blank' && ($formVars['update'] == 0 || $formVars['update'] == 1)) {
        $formVars["inv_companyid"]   = clean($_GET["inv_companyid"],   10);
        $formVars["inv_clusterid"]   = clean($_GET["inv_clusterid"],   10);
        $formVars["inv_function"]    = clean($_GET["inv_function"],   255);
        $formVars["inv_callpath"]    = clean($_GET["inv_callpath"],    10);
        $formVars["inv_status"]      = clean($_GET["inv_status"],      10);
        $formVars["inv_document"]    = clean($_GET["inv_document"],   255);
        $formVars["inv_centrify"]    = clean($_GET["inv_centrify"],    11);
        $formVars["inv_adzone"]      = clean($_GET["inv_adzone"],      30);
        $formVars["inv_domain"]      = clean($_GET["inv_domain"],      50);
        $formVars["inv_ssh"]         = clean($_GET["inv_ssh"],         10);
        $formVars["inv_location"]    = clean($_GET["inv_location"],    15);
        $formVars["inv_rack"]        = clean($_GET["inv_rack"],        30);
        $formVars["inv_row"]         = clean($_GET["inv_row"],         30);
        $formVars["inv_unit"]        = clean($_GET["inv_unit"],        10);
        $formVars["inv_zone"]        = clean($_GET["inv_zone"],        10);
        $formVars["inv_front"]       = clean($_GET["inv_front"],      150);
        $formVars["inv_rear"]        = clean($_GET["inv_rear"],       150);
        $formVars["inv_manager"]     = clean($_GET["inv_manager"],     10);
        $formVars["inv_appadmin"]    = clean($_GET["inv_appadmin"],    10);
        $formVars["inv_class"]       = clean($_GET["inv_class"],       10);
        $formVars["inv_response"]    = clean($_GET["inv_response"],    10);
        $formVars["inv_product"]     = clean($_GET["inv_product"],     10);
        $formVars["inv_project"]     = clean($_GET["inv_project"],     10);
        $formVars["inv_department"]  = clean($_GET["inv_department"],  10);
        $formVars["inv_ansible"]     = clean($_GET["inv_ansible"],     10);
        $formVars["inv_notes"]       = clean($_GET["inv_notes"],      255);
        $formVars["inv_env"]         = clean($_GET["inv_env"],         10);
        $formVars["inv_appliance"]   = clean($_GET["inv_appliance"],   10);
        $formVars["inv_bigfix"]      = clean($_GET["inv_bigfix"],      10);
        $formVars["inv_ciscoamp"]    = clean($_GET["inv_ciscoamp"],    10);
        $formVars["inv_ticket"]      = clean($_GET["inv_ticket"],      30);
        $formVars["inv_maint"]       = clean($_GET["inv_maint"],       10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['inv_callpath'] == 'true' ) {
          $formVars['inv_callpath'] = 1;
        } else {
          $formVars['inv_callpath'] = 0;
        }
        if ($formVars['inv_ssh'] == 'true' ) {
          $formVars['inv_ssh'] = 1;
        } else {
          $formVars['inv_ssh'] = 0;
        }
        if ($formVars['inv_ansible'] == 'true' ) {
          $formVars['inv_ansible'] = 1;
        } else {
          $formVars['inv_ansible'] = 0;
        }
        if ($formVars['inv_unit'] == '') {
          $formVars['inv_unit'] = 0;
        }
        if ($formVars['inv_manager'] == 0) {
          $formVars['inv_manager'] = $_SESSION['group'];
        }
        if ($formVars['inv_appliance'] == 'true' ) {
          $formVars['inv_appliance'] = 1;
        } else {
          $formVars['inv_appliance'] = 0;
        }

        $newserver = $formVars['id'];

        if (strlen($formVars['inv_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "inv_name        = \"" . $formVars['inv_name']        . "\"," .
            "inv_companyid   =   " . $formVars['inv_companyid']   . "," .
            "inv_clusterid   =   " . $formVars['inv_clusterid']   . "," .
            "inv_function    = \"" . $formVars['inv_function']    . "\"," .
            "inv_callpath    =   " . $formVars['inv_callpath']    . "," .
            "inv_document    = \"" . $formVars['inv_document']    . "\"," .
            "inv_centrify    = \"" . $formVars['inv_centrify']    . "\"," .
            "inv_adzone      = \"" . $formVars['inv_adzone']      . "\"," .
            "inv_domain      = \"" . $formVars['inv_domain']      . "\"," .
            "inv_ssh         =   " . $formVars['inv_ssh']         . "," .
            "inv_ansible     =   " . $formVars['inv_ansible']     . "," .
            "inv_location    = \"" . $formVars['inv_location']    . "\"," .
            "inv_rack        = \"" . $formVars['inv_rack']        . "\"," .
            "inv_row         = \"" . $formVars['inv_row']         . "\"," .
            "inv_unit        =   " . $formVars['inv_unit']        . "," .
            "inv_zone        =   " . $formVars['inv_zone']        . "," .
            "inv_front       = \"" . $formVars['inv_front']       . "\"," .
            "inv_rear        = \"" . $formVars['inv_rear']        . "\", " . 
            "inv_manager     =   " . $formVars['inv_manager']     . "," .
            "inv_appadmin    =   " . $formVars['inv_appadmin']    . "," .
            "inv_class       = \"" . $formVars['inv_class']       . "\"," .
            "inv_response    = \"" . $formVars['inv_response']    . "\"," .
            "inv_product     =   " . $formVars['inv_product']     . "," .
            "inv_project     =   " . $formVars['inv_project']     . "," .
            "inv_department  =   " . $formVars['inv_department']  . "," . 
            "inv_env         =   " . $formVars['inv_env']         . "," . 
            "inv_appliance   =   " . $formVars['inv_appliance']   . "," . 
            "inv_bigfix      =   " . $formVars['inv_bigfix']      . "," . 
            "inv_ciscoamp    =   " . $formVars['inv_ciscoamp']    . "," . 
            "inv_maint       =   " . $formVars['inv_maint']       . "," . 
            "inv_ticket      = \"" . $formVars['inv_ticket']      . "\"," . 
            "inv_notes       = \"" . $formVars['inv_notes']       . "\"";

          if ($formVars['update'] == 1) {
# get the current owner of the system for comparison
# add the inv_name for the changelog function
            $q_group  = "select inv_name,inv_manager,inv_location,inv_status ";
            $q_group .= "from inventory ";
            $q_group .= "where inv_id = " . $formVars['id'] . " ";
            $q_inventory = mysqli_query($db, $q_group) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            $a_inventory = mysqli_fetch_array($q_inventory);

# make changes if the software platform owner changes. No need to do extra work if the same
            if ($a_inventory['inv_manager'] != $formVars['inv_manager']) {
# let'also make sure all software owned by the group is owned by the new group
              $q_software  = "update ";
              $q_software .= "software ";
              $q_software .= "set sw_group = " . $formVars['inv_manager'] . " ";
              $q_software .= "where sw_companyid = " . $formVars['id'] . " and sw_group = " . $a_inventory['inv_manager'] . " ";
              mysqli_query($db, $q_software) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            }

# making sure all the hardware is owned by the group and associated with the main product as there's no other way to change the owner or product
            $q_hwstring  = "select hw_id ";
            $q_hwstring .= "from hardware ";
            $q_hwstring .= "where hw_companyid = " . $formVars['id'];
            $q_hardware = mysqli_query($db, $q_hwstring) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_hardware = mysqli_fetch_array($q_hardware)) {
              $query = 
                "update hardware set " .
                "hw_group   = " . $formVars['inv_manager'] . "," . 
                "hw_product = " . $formVars['inv_product'] . " " . 
                "where hw_id = " . $a_hardware['hw_id'];

              $result = mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
            }

# update file system ownershps
            $query  = "update ";
            $query .= "filesystem ";
            $query .= "set ";
            $query .= "fs_group = " . $formVars['inv_manager'] . " ";
            $query .= "where fs_companyid = " . $formVars['id'] . " and fs_group = " . $a_inventory['inv_manager'] . " ";
            $result = mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

# for changelog requirements, compare old inv_name with new inv_name. If changed, save the old name before changing it
            if ($a_inventory['inv_name'] != $formVars['inv_name']) {
              changelog($formVars['id'], $a_inventory['inv_name'], "Asset Name Change", $_SESSION['uid'], "inventory", "inv_name", 0);
            }

# for changelog requirements, compare old inv_location with new inv_location. If changed, save the old location before changing it
            if ($a_inventory['inv_location'] != $formVars['inv_location']) {
              changelog($formVars['id'], $a_inventory['inv_location'], "Location Change", $_SESSION['uid'], "inventory", "inv_location", 0);
            }

# for changelog requirements, see if the status has changed
# slight difference in that 0 = in work, 1 = active, 2 is retired when passed from the main script.
# so 0 and 1 == ultimately active is inv_status = 0 and 2 == retired or inv_status = 1
            $status_check = 1;
            if ($formVars['inv_status'] == 0 || $formVars['inv_status'] == 1) {
              $status_check = 0;
            }
            if ($a_inventory['inv_status'] != $status_check) {
              changelog($formVars['id'], $a_inventory['inv_status'], "Status Change", $_SESSION['uid'], "inventory", "inv_status", 0);
            }

# now save any updated information
            logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['inv_name']);
            $query = "update inventory set " . $q_string . " where inv_id = " . $formVars['id'];
            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

            print "alert('System updated');\n";

          }

          if ($formVars['update'] == 0) {
# ensure folks don't click-click-click and add multiple systems with the same name.
            print "document.edit.addnew.disabled = true;\n";
            logaccess($_SESSION['uid'], $package, "Adding: " . $formVars['inv_name']);

            $query = "insert into inventory set inv_id = NULL, " . $q_string;
            $result = mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

# get the new inv_id
            $newserver = last_insert_id();

# for changelog requirements, new server was added
            changelog($newserver, $formVars['inv_name'], "New Server", $_SESSION['uid'], "inventory", "inv_name", 0);

#####
# Duplicate just the primary piece of hardware
#####
            if ($formVars['id'] > 0) {
              $q_string  = "select hw_id,hw_type,hw_serial,hw_asset,hw_vendorid,hw_speed,hw_size,hw_projectid,hw_product,";
              $q_string .= "hw_group,hw_poid,hw_built,hw_poid,hw_built,hw_active,hw_retired,hw_reused,hw_supportid,hw_primary ";
              $q_string .= "from hardware ";
              $q_string .= "where hw_primary = 1 and hw_companyid = " . $formVars['id'];
              $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_hardware) > 0) {
                while ($a_hardware = mysqli_fetch_array($q_hardware)) {

                  $q_string = "insert into hardware set " . 
                    "hw_id        =   " . "NULL"                      . "," . 
                    "hw_companyid =   " . $newserver                  . "," .
                    "hw_type      =   " . $a_hardware['hw_type']      . "," .
                    "hw_serial    = \"" . $a_hardware['hw_serial']    . "\"," .
                    "hw_asset     = \"" . $a_hardware['hw_asset']     . "\"," .
                    "hw_vendorid  =   " . $a_hardware['hw_vendorid']  . "," .
                    "hw_speed     = \"" . $a_hardware['hw_speed']     . "\"," .
                    "hw_size      = \"" . $a_hardware['hw_size']      . "\"," .
                    "hw_projectid =   " . $a_hardware['hw_projectid'] . "," .
                    "hw_product   =   " . $a_hardware['hw_product']   . "," .
                    "hw_group     =   " . $a_hardware['hw_group']     . "," .
                    "hw_poid      =   " . $a_hardware['hw_poid']      . "," .
                    "hw_built     = \"" . $date                       . "\"," .
                    "hw_active    = \"" . '0000-00-00'                . "\"," .
                    "hw_retired   = \"" . '0000-00-00'                . "\"," .
                    "hw_reused    = \"" . '0000-00-00'                . "\"," .
                    "hw_supportid =   " . $a_hardware['hw_supportid'] . "," .
                    "hw_primary   =   " . $a_hardware['hw_primary'];
  
                  $query = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
                }
              } else {
                $q_string = "insert into hardware set " . 
                  "hw_id        =   " . "NULL"                   . "," . 
                  "hw_companyid =   " . $newserver               . "," .
                  "hw_type      =   " . 15                       . "," .
                  "hw_vendorid  =   " . 45                       . "," .
                  "hw_product   =   " . $formVars['inv_product'] . "," .
                  "hw_group     =   " . $formVars['inv_manager'] . "," .
                  "hw_built     = \"" . $date                    . "\"," .
                  "hw_active    = \"" . '0000-00-00'             . "\"," .
                  "hw_retired   = \"" . '0000-00-00'             . "\"," .
                  "hw_reused    = \"" . '0000-00-00'             . "\"," .
                  "hw_primary   =   " . 1;

                $query = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              }
            } else {
              $q_string = "insert into hardware set " . 
                "hw_id        =   " . "NULL"                   . "," . 
                "hw_companyid =   " . $newserver               . "," .
                "hw_type      =   " . 15                       . "," .
                "hw_vendorid  =   " . 45                       . "," .
                "hw_product   =   " . $formVars['inv_product'] . "," .
                "hw_group     =   " . $formVars['inv_manager'] . "," .
                "hw_built     = \"" . $date                    . "\"," .
                "hw_active    = \"" . '0000-00-00'             . "\"," .
                "hw_retired   = \"" . '0000-00-00'             . "\"," .
                "hw_reused    = \"" . '0000-00-00'             . "\"," .
                "hw_primary   =   " . 1;

              $query = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            }

#####
# Duplicate just the operating system
#####
            if ($formVars['id'] > 0) {
              $q_string  = "select sw_id,sw_software,sw_notes,sw_vendor,sw_product,sw_licenseid,sw_type,sw_group,sw_verified ";
              $q_string .= "from software ";
              $q_string .= "where sw_type = 'OS' and sw_companyid = " . $formVars['id'];
              $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              while ($a_software = mysqli_fetch_array($q_software)) {

                $q_string = "insert into software set " . 
                  "sw_id        =   " . "NULL"                      . "," . 
                  "sw_companyid =   " . $newserver                  . "," .
                  "sw_software  = \"" . $a_software['sw_software']  . "\"," .
                  "sw_notes     = \"" . $a_software['sw_notes']     . "\"," .
                  "sw_vendor    = \"" . $a_software['sw_vendor']    . "\"," .
                  "sw_product   =   " . $a_software['sw_product']   . "," .
                  "sw_licenseid = \"" . $a_software['sw_licenseid'] . "\"," .
                  "sw_type      = \"" . $a_software['sw_type']      . "\"," .
                  "sw_group     =   " . $a_software['sw_group']     . "," .
                  "sw_verified  =   " . $a_software['sw_verified'];

                $query = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              }

#####
# Duplicate all the interfaces
#####
              $q_string  = "select int_id,int_server,int_face,int_ip6,int_addr,int_eth,int_mask,int_gate,int_verified,int_switch,int_primary,int_type ";
              $q_string .= "from interface ";
              $q_string .= "where int_companyid = " . $formVars['id'];
              $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_interface) > 0) {
                while ($a_interface = mysqli_fetch_array($q_interface)) {

                  $q_string = "insert into interface set " . 
                    "int_id        =   " . "NULL"                       . "," . 
                    "int_server    = \"" . $a_interface['int_server']   . "\"," .
                    "int_companyid =   " . $newserver                   . "," .
                    "int_face      = \"" . $a_interface['int_face']     . "\"," .
                    "int_ip6       = \"" . $a_interface['int_ip6']      . "\"," .
                    "int_addr      = \"" . $a_interface['int_addr']     . "\"," .
                    "int_eth       = \"" . $a_interface['int_eth']      . "\"," .
                    "int_mask      = \"" . $a_interface['int_mask']     . "\"," .
                    "int_gate      = \"" . $a_interface['int_gate']     . "\"," .
                    "int_verified  = \"" . $a_interface['int_verified'] . "\"," .
                    "int_switch    = \"" . $a_interface['int_switch']   . "\"," .
                    "int_primary   =   " . $a_interface['int_primary']  . "," .
                    "int_type      =   " . $a_interface['int_type'];

                  $query = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
                }
              }
            }

#####
# Get the backup schedule for the current server
# all servers should have a backup entry even if it's "no backups"
#####
            if ($formVars['id'] > 0) {
              $q_string = "select bu_id,bu_companyid,bu_start,bu_include,bu_retention,bu_sunday,bu_monday,"
                        . "bu_tuesday,bu_wednesday,bu_thursday,bu_friday,bu_saturday,bu_suntime,bu_montime,"
                        . "bu_tuetime,bu_wedtime,bu_thutime,bu_fritime,bu_sattime,bu_changedby "
                        . "from backups "
                        . "where bu_companyid = " . $formVars['id'];
              $q_backups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_backups) > 0) {
                while ($a_backups = mysqli_fetch_array($q_backups)) {

                  $q_string = "insert into backups set " .
                    "bu_id        = \"" . "NULL"                     . "\"," .
                    "bu_companyid =   " . $newserver                 . "," .
                    "bu_start     = \"" . $a_backups['bu_start']     . "\"," .
                    "bu_include   =   " . $a_backups['bu_include']   . "," .
                    "bu_retention =   " . $a_backups['bu_retention'] . "," .
                    "bu_sunday    =   " . $a_backups['bu_sunday']    . "," .
                    "bu_monday    =   " . $a_backups['bu_monday']    . "," .
                    "bu_tuesday   =   " . $a_backups['bu_tuesday']   . "," .
                    "bu_wednesday =   " . $a_backups['bu_wednesday'] . "," .
                    "bu_thursday  =   " . $a_backups['bu_thursday']  . "," .
                    "bu_friday    =   " . $a_backups['bu_friday']    . "," .
                    "bu_saturday  =   " . $a_backups['bu_saturday']  . "," .
                    "bu_suntime   = \"" . $a_backups['bu_suntime']   . "\"," .
                    "bu_montime   = \"" . $a_backups['bu_montime']   . "\"," .
                    "bu_tuetime   = \"" . $a_backups['bu_tuetime']   . "\"," .
                    "bu_wedtime   = \"" . $a_backups['bu_wedtime']   . "\"," .
                    "bu_thutime   = \"" . $a_backups['bu_thutime']   . "\"," .
                    "bu_fritime   = \"" . $a_backups['bu_fritime']   . "\"," .
                    "bu_sattime   = \"" . $a_backups['bu_sattime']   . "\"," .
                    "bu_changedby =   " . $a_backups['bu_changedby'];

                  $query = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
                }
              } else {
                $q_string = "insert into backups set " .
                  "bu_id        = \"" . "NULL"                     . "\"," .
                  "bu_companyid =   " . $newserver                 . "," .
                  "bu_start     = \"" . $date                      . "\"," .
                  "bu_include   =   " . 1                          . "," .
                  "bu_retention =   " . 4                          . "," .
                  "bu_saturday  =   " . 1                          . "," .
                  "bu_changedby =   " . $_SESSION['uid'];

                $query = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              }
            } else {
              $q_string = "insert into backups set " .
                "bu_id        = \"" . "NULL"                     . "\"," .
                "bu_companyid =   " . $newserver                 . "," .
                "bu_start     = \"" . $date                      . "\"," .
                "bu_include   =   " . 1                          . "," .
                "bu_retention =   " . 4                          . "," .
                "bu_saturday  =   " . 1                          . "," .
                "bu_changedby =   " . $_SESSION['uid'];

              $query = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            }

# okay, go to new server
print "alert('All Done!');\n";
            print "window.location.href = 'inventory.php?server=" . $newserver . "';\n";

          }
        }

# Changing the status of a server section. The above is a bit messier so I wanted to have a clean section
# for just updating the hardware information to change the server status.
# if here regardless of new or update
# get server info ; inv_status
# get hardware info ; hw_active and hw_retired/hw_reused
# current status is $formVars['inv_status'] = 0 - in work, 1 = in production, 2 = retired.

        $q_string  = "select inv_status ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_id = " . $newserver;
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_inventory = mysqli_fetch_array($q_inventory);

        $q_string  = "select hw_id,hw_active,hw_retired,hw_reused ";
        $q_string .= "from hardware ";
        $q_string .= "where hw_companyid = " . $newserver . " and hw_primary = 1 and hw_deleted = 0 ";
        $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_hardware = mysqli_fetch_array($q_hardware);

        if ($a_inventory['inv_status'] == 0) {
          if ($a_hardware['hw_active'] == '0000-00-00') {
            $current = "work";
          } else {
            $current = "production";
          }
        } else {
          $current = "retired";
        }

        $r_hardware = '';
        $r_inventory = '';
        if ($current == 'work') {
          if ($formVars['inv_status'] == 1) {
            $r_hardware = "update hardware set hw_active = '" . date('Y-m-d') . "' where hw_id = " . $a_hardware['hw_id'] . " ";
          }
          if ($formVars['inv_status'] == 2) {
            $r_hardware = "update hardware set hw_retired = '" . date('Y-m-d') . "' where hw_id = " . $a_hardware['hw_id'] . " ";
            $r_inventory = "update inventory set inv_status = 1, inv_ssh = 0 where inv_id = " . $newserver . " ";
          }
        }

        if ($current == 'production') {
          if ($formVars['inv_status'] == 0) {
            $r_hardware = "update hardware set hw_active = '0000-00-00' where hw_id = " . $a_hardware['hw_id'] . " ";
          }
          if ($formVars['inv_status'] == 2) {
            $r_hardware = "update hardware set hw_retired = '" . date('Y-m-d') . "' where hw_id = " . $a_hardware['hw_id'] . " ";
            $r_inventory = "update inventory set inv_status = 1, inv_ssh = 0 where inv_id = " . $newserver . " ";
          }
        }

        if ($current == 'retired') {
          if ($formVars['inv_status'] == 0) {
            $r_hardware = "update hardware set hw_retired = '0000-00-00' where hw_id = " . $a_hardware['hw_id'] . " ";
            $r_inventory = "update inventory set inv_status = 0 where inv_id = " . $newserver . " ";
          }
          if ($formVars['inv_status'] == 1) {
            $r_hardware = "update hardware set hw_active = '" . date('Y-m-d') . "', hw_retired = '0000-00-00' where hw_id = " . $a_hardware['hw_id'] . " ";
            $r_inventory = "update inventory set inv_status = 0 where inv_id = " . $newserver . " ";
          }
        }

        if (strlen($r_hardware) > 0) {
          $result = mysqli_query($db, $r_hardware) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $r_hardware . "&mysql=" . mysqli_error($db)));
        }
        if (strlen($r_inventory) > 0) {
          $result = mysqli_query($db, $r_inventory) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $r_inventory . "&mysql=" . mysqli_error($db)));
        }

      } else {
        if ($formVars['inv_name'] == 'Blank') {
          print "alert('You must change the server name before saving changes.');\n";
#        } else {
#          print "alert('You must input data before saving changes.');\n";
        }
      }

      if ($formVars['update'] == -2) {
        $formVars['copyfrom'] = clean($_GET['copyfrom'], 10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select inv_function,inv_callpath,inv_document,inv_centrify,inv_adzone,inv_domain,inv_ssh,inv_location,";
          $q_string .= "inv_rack,inv_row,inv_unit,inv_zone,inv_front,inv_rear,inv_manager,inv_appadmin,inv_class,inv_response,inv_mstart,";
          $q_string .= "inv_mend,inv_mdow,inv_minterval,inv_product,inv_project,inv_department,inv_ansible,inv_notes ";
          $q_string .= "from inventory ";
          $q_string .= "where inv_id = " . $formVars['copyfrom'];
          $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_inventory = mysqli_fetch_array($q_inventory)) {

            $q_string =
              "inv_function       = \"" . $a_inventory['inv_function']       . "\"," .
              "inv_callpath       = \"" . $a_inventory['inv_callpath']       . "\"," .
              "inv_document       = \"" . $a_inventory['inv_document']       . "\"," .
              "inv_centrify       = \"" . $a_inventory['inv_centrify']       . "\"," .
              "inv_adzone         = \"" . $a_inventory['inv_adzone']         . "\"," .
              "inv_domain         = \"" . $a_inventory['inv_domain']         . "\"," .
              "inv_ssh            = \"" . $a_inventory['inv_ssh']            . "\"," .
              "inv_location       = \"" . $a_inventory['inv_location']       . "\"," .
              "inv_rack           = \"" . $a_inventory['inv_rack']           . "\"," .
              "inv_row            = \"" . $a_inventory['inv_row']            . "\"," .
              "inv_unit           = \"" . $a_inventory['inv_unit']           . "\"," .
              "inv_zone           = \"" . $a_inventory['inv_zone']           . "\"," .
              "inv_front          = \"" . $a_inventory['inv_front']          . "\"," .
              "inv_rear           = \"" . $a_inventory['inv_rear']           . "\"," .
              "inv_manager        = \"" . $a_inventory['inv_manager']        . "\"," .
              "inv_appadmin       = \"" . $a_inventory['inv_appadmin']       . "\"," .
              "inv_class          = \"" . $a_inventory['inv_class']          . "\"," .
              "inv_response       = \"" . $a_inventory['inv_response']       . "\"," .
              "inv_mstart         = \"" . $a_inventory['inv_mstart']         . "\"," .
              "inv_mend           = \"" . $a_inventory['inv_mend']           . "\"," .
              "inv_mdow           = \"" . $a_inventory['inv_mdow']           . "\"," .
              "inv_minterval      = \"" . $a_inventory['inv_minterval']      . "\"," .
              "inv_product        = \"" . $a_inventory['inv_product']        . "\"," .
              "inv_project        = \"" . $a_inventory['inv_project']        . "\"," .
              "inv_department     = \"" . $a_inventory['inv_department']     . "\"," .
              "inv_ansible        = \"" . $a_inventory['inv_ansible']        . "\"," .
              "inv_env            = \"" . $a_inventory['inv_env']            . "\"," .
              "inv_appliance      = \"" . $a_inventory['inv_appliance']      . "\"," .
              "inv_bigfix         = \"" . $a_inventory['inv_bigfix']         . "\"," .
              "inv_ciscoamp       = \"" . $a_inventory['inv_ciscoamp']       . "\"," .
              "inv_notes          = \"" . $a_inventory['inv_notes']          . "\"";

            $query = "update inventory set " . $q_string . " where inv_id = " . $formVars['id'];;
            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
          }

          print "window.location.href = 'inventory.php?server=" . $formVars['id'] . "';\n";

        }
      }

    }
  }
?>
