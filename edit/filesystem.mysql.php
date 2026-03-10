<?php
# Script: filesystem.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['fs_id']        = clean($_GET['fs_id'],        10);
        $formVars['fs_backup']    = clean($_GET['fs_backup'],    10);
        $formVars['fs_device']    = clean($_GET['fs_device'],   255);
        $formVars['fs_mount']     = clean($_GET['fs_mount'],    255);
        $formVars['fs_group']     = clean($_GET['fs_group'],     10);
        $formVars['fs_size']      = clean($_GET['fs_size'],      10);
        $formVars['fs_used']      = clean($_GET['fs_used'],      10);
        $formVars['fs_avail']     = clean($_GET['fs_avail'],     10);
        $formVars['fs_percent']   = clean($_GET['fs_percent'],   10);
        $formVars['fs_wwid']      = clean($_GET['fs_wwid'],     100);
        $formVars['fs_subsystem'] = clean($_GET['fs_subsystem'], 30);
        $formVars['fs_volume']    = clean($_GET['fs_volume'],   100);
        $formVars['fs_lun']       = clean($_GET['fs_lun'],       10);
        $formVars['fs_volid']     = clean($_GET['fs_volid'],     30);
        $formVars['fs_path']      = clean($_GET['fs_path'],      30);
        $formVars['fs_switch']    = clean($_GET['fs_switch'],    50);
        $formVars['fs_port']      = clean($_GET['fs_port'],      50);
        $formVars['fs_sysport']   = clean($_GET['fs_sysport'],   50);

        if ($formVars['fs_id'] == '') {
          $formVars['fs_id'] = 0;
        }
        if ($formVars['fs_backup'] == 'true') {
          $formVars['fs_backup'] = 1;
        } else {
          $formVars['fs_backup'] = 0;
        }
        if ($formVars['fs_size'] == '') {
          $formVars['fs_size'] = 0;
        }
        if ($formVars['fs_used'] == '') {
          $formVars['fs_used'] = 0;
        }
        if ($formVars['fs_avail'] == '') {
          $formVars['fs_avail'] = 0;
	}
	if ($formVars['fs_percent'] == 0) {
	  $formVars['fs_percent'] = 0;
	}
        if ($formVars['fs_lun'] == '') {
          $formVars['fs_lun'] = 0;
        }
        if ($formVars['fs_group'] == '') {
          $formVars['fs_group'] = 0;
        }

        if ($formVars['fs_companyid'] > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string = 
            "fs_companyid =   " . $formVars['fs_companyid'] . "," .
            "fs_backup    =   " . $formVars['fs_backup']    . "," .
            "fs_device    = \"" . $formVars['fs_device']    . "\"," .
            "fs_mount     = \"" . $formVars['fs_mount']     . "\"," .
            "fs_group     =   " . $formVars['fs_group']     . "," .
            "fs_size      =   " . $formVars['fs_size']      . "," .
            "fs_used      =   " . $formVars['fs_used']      . "," .
            "fs_avail     =   " . $formVars['fs_avail']     . "," .
            "fs_percent   = \"" . $formVars['fs_percent']   . "\"," .
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
            $q_string = "insert into inv_filesystem set fs_id = NULL," . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_filesystem set " . $q_string . " where fs_id = " . $formVars['fs_id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['fs_companyid']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\" width=\"160\">Delete Filesystem</th>\n";
      $output .=   "<th class=\"ui-state-default\">Managed By</th>\n";
      $output .=   "<th class=\"ui-state-default\">Device</th>\n";
      $output .=   "<th class=\"ui-state-default\">Size</th>\n";
      $output .=   "<th class=\"ui-state-default\">Used</th>\n";
      $output .=   "<th class=\"ui-state-default\">Available</th>\n";
      $output .=   "<th class=\"ui-state-default\">% Used</th>\n";
      $output .=   "<th class=\"ui-state-default\">Mount</th>\n";
      $output .=   "<th class=\"ui-state-default\">Volume Name</th>\n";
      $output .=   "<th class=\"ui-state-default\">WWNN</th>\n";
      $output .=   "<th class=\"ui-state-default\">Updated</th>\n";
      $output .= "</tr>\n";

# if all filesystems are checked in the backup form, then we don't need checkboxes here
      $q_string  = "select bu_include ";
      $q_string .= "from inv_backups ";
      $q_string .= "where bu_companyid = " . $formVars['fs_companyid'] . " ";
      $q_inv_backups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_backups) > 0) {
        $a_inv_backups = mysqli_fetch_array($q_inv_backups);
      } else {
        $a_inv_backups['bu_include'] = 0;
      }

      $q_string  = "select fs_id,fs_backup,fs_wwid,fs_volume,fs_device,fs_size,fs_mount,fs_verified,fs_update,grp_name,fs_used,fs_avail,fs_percent ";
      $q_string .= "from inv_filesystem ";
      $q_string .= "left join inv_inventory on inv_inventory.inv_id = inv_filesystem.fs_companyid ";
      $q_string .= "left join inv_groups    on inv_groups.grp_id    = inv_filesystem.fs_group ";
      $q_string .= "where fs_companyid = " . $formVars['fs_companyid'] . " ";
      $q_string .= "order by fs_device";
      $q_inv_filesystem = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_filesystem) > 0) {
        while ($a_inv_filesystem = mysqli_fetch_array($q_inv_filesystem)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('filesystem.fill.php?id=" . $a_inv_filesystem['fs_id'] . "');jQuery('#dialogFilesystemUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_filesystem('filesystem.del.php?id=" . $a_inv_filesystem['fs_id'] . "');\">";
          $linkend   = "</a>";

          $class = "ui-widget-content";
# if all filesystems are not being backed up
          if ($a_inv_backups['bu_include'] == 0) {
            $class = "ui-state-highlight";
# and if this specific filesystem is not being backed up.
            if ($a_inv_filesystem['fs_backup'] == 0) {
              $class = "ui-state-error";
            }
          }

          $checked = "";
          if ($a_inv_filesystem['fs_verified']) {
            $checked = "&#x2713;";
          }

          $output .= "<tr>\n";
          $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                                                          . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_filesystem['grp_name']                             . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_filesystem['fs_device']                            . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . " button\">" . $linkstart . number_format($a_inv_filesystem['fs_size'], 0, '.', ',')  . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . " button\">" . $linkstart . number_format($a_inv_filesystem['fs_used'], 0, '.', ',')  . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . " button\">" . $linkstart . number_format($a_inv_filesystem['fs_avail'], 0, '.', ',') . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_inv_filesystem['fs_percent']                           . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_filesystem['fs_mount']                             . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_filesystem['fs_volume']                            . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_filesystem['fs_wwid']                              . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_filesystem['fs_update'] . $checked                 . $linkend . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"11\">No Filesystems defined.</td>\n";
          $output .= "</tr>\n";
      }

      mysqli_free_result($q_inv_filesystem);

      $output .= "</table>\n";

      print "document.getElementById('filesystem_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
