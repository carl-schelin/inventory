<?php
# Script: retirement.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "retirement.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);
    $formVars['ret_companyid']  = clean($_GET['ret_companyid'],  10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['ret_companyid'] == '') {
      $formVars['ret_companyid'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']                = clean($_GET['id'],                10);
        $formVars['inv_monitoring']    = clean($_GET['inv_monitoring'],    10);
        $formVars['inv_backups']       = clean($_GET['inv_backups'],       10);
        $formVars['inv_storage']       = clean($_GET['inv_storage'],       10);
        $formVars['inv_physical']      = clean($_GET['inv_physical'],      10);
        $formVars['inv_vmware']        = clean($_GET['inv_vmware'],        10);
        $formVars['inv_dns']           = clean($_GET['inv_dns'],           10);
        $formVars['inv_networking']    = clean($_GET['inv_networking'],    10);
        $formVars['inv_infosec']       = clean($_GET['inv_infosec'],       10);
        $formVars['inv_contracts']     = clean($_GET['inv_contracts'],     10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['rec_companyid'] > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string = 
            "inv_monitoring    = \"" . $formVars['inv_monitoring']    . "\"," .
            "inv_backups       = \"" . $formVars['inv_backups']       . "\"," .
            "inv_storage       = \"" . $formVars['inv_storage']       . "\"," .
            "inv_physical      = \"" . $formVars['inv_physical']      . "\"," .
            "inv_virtual       = \"" . $formVars['inv_virtual']       . "\"," .
            "inv_dns           = \"" . $formVars['inv_dns']           . "\"," .
            "inv_networking    = \"" . $formVars['inv_networking']    . "\"," .
            "inv_infosec       = \"" . $formVars['inv_infosec']       . "\"," .
            "inv_contracts     = \"" . $formVars['inv_contracts']     . "\"";

          if ($formVars['update'] == 1) {
            $query = "update inventory set " . $q_string . " where inv_id = " . $formVars['id'];
            $message = "Tickets updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['fs_companyid']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      if ($formVars['update'] == -2) {
        $formVars['copyfrom']     = clean($_GET['copyfrom'],     10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select fs_backup,fs_device,fs_mount,fs_size,fs_wwid,fs_subsystem,fs_volume,fs_lun,fs_volid,fs_path,fs_switch,fs_port,fs_sysport ";
          $q_string .= "from filesystem ";
          $q_string .= "where fs_companyid = " . $formVars['copyfrom'];
          $q_filesystem = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_filesystem = mysqli_fetch_array($q_filesystem)) {

            $q_string = 
              "fs_companyid =   " . $formVars['fs_companyid']     . "," .
              "fs_backup    =   " . $a_filesystem['fs_backup']    . "," .
              "fs_device    = \"" . $a_filesystem['fs_device']    . "\"," .
              "fs_mount     = \"" . $a_filesystem['fs_mount']     . "\"," .
              "fs_size      =   " . $a_filesystem['fs_size']      . "," .
              "fs_wwid      = \"" . $a_filesystem['fs_wwid']      . "\"," .
              "fs_subsystem = \"" . $a_filesystem['fs_subsystem'] . "\"," .
              "fs_volume    = \"" . $a_filesystem['fs_volume']    . "\"," .
              "fs_lun       =   " . $a_filesystem['fs_lun']       . "," .
              "fs_volid     = \"" . $a_filesystem['fs_volid']     . "\"," .
              "fs_path      = \"" . $a_filesystem['fs_path']      . "\"," .
              "fs_switch    = \"" . $a_filesystem['fs_switch']    . "\"," .
              "fs_port      = \"" . $a_filesystem['fs_port']      . "\"," .
              "fs_sysport   = \"" . $a_filesystem['fs_sysport']   . "\"";

            $query = "insert into filesystem set fs_id = NULL, " . $q_string;
            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
          }
        }
      }


      if ($formVars['update'] == -3) {
        logaccess($db, $_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"addret\" id=\"clickRetirement\" value=\"Update Tickets\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"copyitem\"  value=\"Add System:\" onClick=\"javascript:attach_filesystem('retirement.mysql.php', -2);\">\n";
        $output .= "<select name=\"fs_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name";
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . htmlspecialchars($a_inventory['inv_name']) . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('retirement_form').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Filesystem Listing</th>\n";
      $output .=   "<th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('filesystem-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<div id=\"filesystem-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Filesystem Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Highlighted</strong> - Filesystems that are <span class=\"ui-state-highlight\">highlighted</span> are <strong>not</strong> being backed up if the Backup Form \"Include all filesystems\" checkbox is not checked.</li>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Clicking the <strong>x</strong> will delete this filesystem from this server.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a filesystem to toggle the form for editing.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Rows marked with a checkmark in the Updated column have been automatically captured where possible.</li>\n";
      $output .= "    <li>Click the <strong>Filesystem Management</strong> title bar to toggle the <strong>Filesystem Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Monitoring</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\">The following servers are being retired. Please remove all monitoring from the listed servers. The list following each server are all the names and IPs associated with the servers to ensure all monitoring is stopped:<br><br>";
      $q_string  = "select inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['ret_companyid'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inventory = mysqli_fetch_array($q_inventory);
      $output .= "Server Name: " . $a_inventory['inv_name'] . "<br>\n";
      $output .= "----------<br>\n";
      $q_string  = "select int_server,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $formVars['ret_companyid'] . " ";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_interface = mysqli_fetch_array($q_interface)) {
        if ($a_interface['int_addr'] != '' && $a_interface['int_server'] != 'localhost') {
          $output .= $a_interface['int_server'] . " - " . $a_interface['int_addr'] . "<br>\n";
        }
      }
      $output .= "</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";
     

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Backups</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\">The following servers are being retired. Please disable any backups for the following systems:<br><br>";
      $q_string  = "select inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['ret_companyid'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inventory = mysqli_fetch_array($q_inventory);
      $output .= "Server Name: " . $a_inventory['inv_name'] . "<br>\n";
      $output .= "----------<br>\n";
      $q_string  = "select int_server,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $formVars['ret_companyid'] . " ";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_interface = mysqli_fetch_array($q_interface)) {
        if ($a_interface['int_addr'] != '' && $a_interface['int_server'] != 'localhost') {
          $output .= $a_interface['int_server'] . " - " . $a_interface['int_addr'] . "<br>\n";
        }
      }
      $output .= "</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Network Engineering</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\">The following servers are being retired. Please recover the following IPs and make them available for reuse:<br><br>";
      $q_string  = "select int_server,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $formVars['ret_companyid'] . " ";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_interface = mysqli_fetch_array($q_interface)) {
        if ($a_interface['int_addr'] != '' && $a_interface['int_server'] != 'localhost') {
          $output .= $a_interface['int_server'] . " - " . $a_interface['int_addr'] . "<br>\n";
        }
      }
      $output .= "</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";
     

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Information Security</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\">The following servers are being retired. Please remove any and all of the following objects from your firewall rules:<br><br>";
      $q_string  = "select int_server,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $formVars['ret_companyid'] . " ";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_interface = mysqli_fetch_array($q_interface)) {
        if ($a_interface['int_addr'] != '' && $a_interface['int_server'] != 'localhost') {
          $output .= $a_interface['int_server'] . " - " . $a_interface['int_addr'] . "<br>\n";
        }
      }
      $output .= "</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";
     

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Network Engineering</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\">The following servers are being retired. Please clear the following hostnames and IPs, forward and reverse, from DNS:<br><br>";
      $q_string  = "select int_server,int_domain,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
      $q_string .= "where int_companyid = " . $formVars['ret_companyid'] . " ";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_interface = mysqli_fetch_array($q_interface)) {
        if ($a_interface['int_addr'] != '' && $a_interface['int_server'] != 'localhost') {
          $output .= $a_interface['int_server'] . "." . $a_interface['int_domain'] . " - " . $a_interface['int_addr'] . "<br>\n";
        }
      }
      $output .= "</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">IT System Admins</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\">The following servers are being retired. Please clear the following hostnames and IPs, forward and reverse, from DNS.<br><br>";
      $q_string  = "select int_server,int_domain,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
      $q_string .= "where int_companyid = " . $formVars['ret_companyid'] . " ";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_interface = mysqli_fetch_array($q_interface)) {
        if ($a_interface['int_addr'] != '' && $a_interface['int_server'] != 'localhost') {
          $output .= $a_interface['int_server'] . "." . $a_interface['int_domain'] . " - " . $a_interface['int_addr'] . "<br>\n";
        }
      }
      $output .= "</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Data Center</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\">The following servers are being retired. Please uncable and remove the following systems and deliver them to my desk at 1K3005 for disposal:<br><br>";

      $q_string  = "select inv_name,hw_asset,hw_serial,inv_rack,inv_row,inv_unit,mod_vendor,mod_name,loc_identity ";
      $q_string .= "from inventory ";
      $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
      $q_string .= "left join models   on models.mod_id         = hardware.hw_vendorid ";
      $q_string .= "left join locations   on locations.loc_id         = inventory.inv_location ";
      $q_string .= "where inv_id = " . $formVars['ret_companyid'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inventory = mysqli_fetch_array($q_inventory);



      $output .= "Label: " . $a_inventory['inv_name'] . ", Vendor: " . $a_inventory['mod_vendor'] . ", Model: " . $a_inventory['mod_name'] . ", Asset Tag: " . $a_inventory['hw_asset'] . ", Serial/Service: " . $a_inventory['hw_serial'] . ", Location: " . $a_inventory['loc_identity'] . " " . $a_inventory['inv_row'] . "-" . $a_inventory['inv_rack'] . " U" . $a_inventory['inv_unit'] . "</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Virtualization</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\">The following servers are being retired. The systems have been powered off. Please delete the following Virtual Machines:<br><br>";
      $q_string  = "select inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['ret_companyid'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inventory = mysqli_fetch_array($q_inventory);
      $output .= $a_inventory['inv_name'] . "<br>\n";
      $output .= "</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Storage</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\">The following systerms are being retired. Please recover any SAN storage:<br><br>";
      $q_string  = "select inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['ret_companyid'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inventory = mysqli_fetch_array($q_inventory);
      $output .= $a_inventory['inv_name'] . "<br>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      print "document.getElementById('retirement_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
