<?php
# Script: filesystem.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "filesystem.mysql.php";
    $formVars['update']       = clean($_GET['update'],       10);
    $formVars['fs_companyid'] = clean($_GET['fs_companyid'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['fs_companyid'] == '') {
      $formVars['fs_companyid'] = 0;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],           10);
        $formVars['fs_backup']    = clean($_GET['fs_backup'],    10);
        $formVars['fs_device']    = clean($_GET['fs_device'],    60);
        $formVars['fs_mount']     = clean($_GET['fs_mount'],     60);
        $formVars['fs_group']     = clean($_GET['fs_group'],     10);
        $formVars['fs_size']      = clean($_GET['fs_size'],      10);
        $formVars['fs_wwid']      = clean($_GET['fs_wwid'],     100);
        $formVars['fs_subsystem'] = clean($_GET['fs_subsystem'], 30);
        $formVars['fs_volume']    = clean($_GET['fs_volume'],   100);
        $formVars['fs_lun']       = clean($_GET['fs_lun'],       10);
        $formVars['fs_volid']     = clean($_GET['fs_volid'],     30);
        $formVars['fs_path']      = clean($_GET['fs_path'],      30);
        $formVars['fs_switch']    = clean($_GET['fs_switch'],    50);
        $formVars['fs_port']      = clean($_GET['fs_port'],      50);
        $formVars['fs_sysport']   = clean($_GET['fs_sysport'],   50);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['fs_backup'] == 'true') {
          $formVars['fs_backup'] = 1;
        } else {
          $formVars['fs_backup'] = 0;
        }
        if ($formVars['fs_size'] == '') {
          $formVars['fs_size'] = 0;
        }
        if ($formVars['fs_lun'] == '') {
          $formVars['fs_lun'] = 0;
        }

        if ($formVars['fs_companyid'] > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string = 
            "fs_companyid =   " . $formVars['fs_companyid'] . "," .
            "fs_backup    =   " . $formVars['fs_backup']    . "," .
            "fs_device    = \"" . $formVars['fs_device']    . "\"," .
            "fs_mount     = \"" . $formVars['fs_mount']     . "\"," .
            "fs_group     =   " . $formVars['fs_group']     . "," .
            "fs_size      =   " . $formVars['fs_size']      . "," .
            "fs_wwid      = \"" . $formVars['fs_wwid']      . "\"," .
            "fs_subsystem = \"" . $formVars['fs_subsystem'] . "\"," .
            "fs_volume    = \"" . $formVars['fs_volume']    . "\"," .
            "fs_lun       =   " . $formVars['fs_lun']       . "," .
            "fs_volid     = \"" . $formVars['fs_volid']     . "\"," .
            "fs_path      = \"" . $formVars['fs_path']      . "\"," . 
            "fs_switch    = \"" . $formVars['fs_switch']    . "\"," . 
            "fs_port      = \"" . $formVars['fs_port']      . "\"," . 
            "fs_sysport   = \"" . $formVars['fs_sysport']   . "\"," . 
            "fs_verified  =   " . "0"                       . "," . 
            "fs_user      =   " . $_SESSION['uid']          . "," . 
            "fs_update    = \"" . date('Y-m-d')             . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into filesystem set fs_id = NULL," . $q_string;
            $message = "Filesystem added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update filesystem set " . $q_string . " where fs_id = " . $formVars['id'];
            $message = "Filesystem updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['fs_companyid']);

          mysql_query($query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysql_error()));

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
          $q_filesystem = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          while ($a_filesystem = mysql_fetch_array($q_filesystem)) {

            $q_string = 
              "fs_companyid =   " . $formVars['fs_companyid']     . "," .
              "fs_backup    =   " . $a_filesystem['fs_backup']    . "," .
              "fs_device    = \"" . $a_filesystem['fs_device']    . "\"," .
              "fs_mount     = \"" . $a_filesystem['fs_mount']     . "\"," .
              "fs_group     =   " . $a_filesystem['fs_group']     . "," .
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
            mysql_query($query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysql_error()));
          }
        }
      }


      if ($formVars['update'] == -3) {
        logaccess($_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"fs_refresh\" value=\"Refresh Filesystem Listing\" onClick=\"javascript:attach_filesystem('filesystem.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"fs_update\"  value=\"Update Filesystem\" onClick=\"javascript:attach_filesystem('filesystem.mysql.php', 1);hideDiv('filesystem-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"fs_id\" value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"fs_addbtn\"  value=\"Add Filesystem\"    onClick=\"javascript:attach_filesystem('filesystem.mysql.php', 0);\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"copyitem\"  value=\"Copy Filesystem Table From:\" onClick=\"javascript:attach_filesystem('filesystem.mysql.php', -2);\">\n";
        $output .= "<select name=\"fs_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name";
        $q_inventory = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
        while ($a_inventory = mysql_fetch_array($q_inventory)) {
          $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . htmlspecialchars($a_inventory['inv_name']) . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"4\">Filesystem Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\"><label>Back up? <input type=\"checkbox\" name=\"fs_backup\"></label></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Device:* <input type=\"text\" name=\"fs_device\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Mount Point:* <input type=\"text\" name=\"fs_mount\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Size:* <input type=\"text\" name=\"fs_size\" size=\"10\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">Managed by: <select name=\"fs_group\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select grp_id,grp_name ";
        $q_string .= "from groups ";
        $q_string .= "where grp_disabled = 0 ";
        $q_string .= "order by grp_name ";
        $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
        while ($a_groups = mysql_fetch_array($q_groups)) {
          $output .= "<option value=\"" . $a_groups['grp_id'] . "\">" . htmlspecialchars($a_groups['grp_name']) . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"3\">SAN Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">WWID: <input type=\"text\" name=\"fs_wwid\" size=\"30\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Subsystem: <input type=\"text\" name=\"fs_subsystem\" size=\"30\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">LUN: <input type=\"text\" name=\"fs_lun\" size=\"10\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Volume: <input type=\"text\" name=\"fs_volume\" size=\"30\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">VolID: <input type=\"text\" name=\"fs_volid\" size=\"30\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Path: <input type=\"text\" name=\"fs_path\" size=\"10\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Switch: <input type=\"text\" name=\"fs_switch\" size=\"30\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Port: <input type=\"text\" name=\"fs_port\" size=\"10\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Server Port: <input type=\"text\" name=\"fs_sysport\" size=\"30\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('filesystem_form').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

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


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Del</th>\n";
      $output .=   "<th class=\"ui-state-default\">Device</th>\n";
      $output .=   "<th class=\"ui-state-default\">Mount</th>\n";
      $output .=   "<th class=\"ui-state-default\">Managed By</th>\n";
      $output .=   "<th class=\"ui-state-default\">Size</th>\n";
      $output .=   "<th class=\"ui-state-default\">Volume Name</th>\n";
      $output .=   "<th class=\"ui-state-default\">WWNN</th>\n";
      $output .=   "<th class=\"ui-state-default\">Updated</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select bu_include ";
      $q_string .= "from backups ";
      $q_string .= "where bu_companyid = " . $formVars['fs_companyid'] . " ";
      $q_backups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_backups) > 0) {
        $a_backups = mysql_fetch_array($q_backups);
      } else {
        $a_backups['bu_include'] = 0;
      }

      $q_string  = "select fs_id,fs_backup,fs_wwid,fs_volume,fs_device,fs_size,fs_mount,fs_verified,fs_update,grp_name ";
      $q_string .= "from filesystem ";
      $q_string .= "left join inventory on inventory.inv_id = filesystem.fs_companyid ";
      $q_string .= "left join groups on groups.grp_id = filesystem.fs_group ";
      $q_string .= "where fs_companyid = " . $formVars['fs_companyid'] . " ";
      $q_string .= "order by fs_device";
      $q_filesystem = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_filesystem) > 0) {
        while ($a_filesystem = mysql_fetch_array($q_filesystem)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('filesystem.fill.php?id=" . $a_filesystem['fs_id'] . "');showDiv('filesystem-hide');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_filesystem('filesystem.del.php?id=" . $a_filesystem['fs_id'] . "');\">";
          $linkend   = "</a>";

          $class = "ui-widget-content";
          if ($a_backups['bu_include'] == 0) {
            $class = "ui-state-highlight";
            if ($a_filesystem['fs_backup']) {
              $class = "ui-widget-content";
            }
          }

          $checked = "";
          if ($a_filesystem['fs_verified']) {
            $checked = "&#x2713;";
          }

          $output .= "<tr>\n";
          $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                                      . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_filesystem['fs_device']            . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_filesystem['fs_mount']             . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_filesystem['grp_name']             . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_filesystem['fs_size']              . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_filesystem['fs_volume']            . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_filesystem['fs_wwid']              . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_filesystem['fs_update'] . $checked . $linkend . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"8\">No Filesystems defined.</td>\n";
          $output .= "</tr>\n";
      }

      mysql_free_result($q_filesystem);

      $output .= "</table>\n";

      print "document.getElementById('filesystem_table').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.edit.fs_update.disabled = true;\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
