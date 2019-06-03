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
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "filesystem.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);
    $formVars['rsdp']           = clean($_GET['rsdp'],           10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['fs_volume']      = clean($_GET['fs_volume'],      20);
        $formVars['fs_size']        = clean($_GET['fs_size'],        20);
        $formVars['fs_backup']      = clean($_GET['fs_backup'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['fs_backup'] == 'true') {
          $formVars['fs_backup'] = 1;
        } else {
          $formVars['fs_backup'] = 0;
        }

        if (strlen($formVars['fs_volume']) > 0) {

# if a new server is being entered, we'll need to create a server entry so the filesystem can have the correct association
          if ($formVars['rsdp'] == 0) {
            $q_string  = "insert ";
            $q_string .= "into rsdp_server ";
            $q_string .= "set rsdp_id = null,rsdp_requestor = " . $_SESSION['uid'];
            $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());

            $formVars['rsdp'] = last_insert_id();
          }

          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "fs_rsdp   =   " . $formVars['rsdp']       . "," .
            "fs_volume = \"" . $formVars['fs_volume'] . "\"," .
            "fs_size   = \"" . $formVars['fs_size']   . "\"," .
            "fs_backup =   " . $formVars['fs_backup'];

          if ($formVars['update'] == 0) {
            $query = "insert into rsdp_filesystem set fs_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update rsdp_filesystem set " . $q_string . " where fs_id = " . $formVars['id'];
          }
          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['fs_volume']);

          mysql_query($query) or die($query . ": " . mysql_error());

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Filesystem Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('filesystem-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"filesystem-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Filesystem Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Filesystem entry to edit the information.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Filesystem Management</strong> title bar to toggle the <strong>Filesystem Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      $output .= "  <th class=\"ui-state-default\">File System</th>\n";
      $output .= "  <th class=\"ui-state-default\">Partition Size</th>\n";
      $output .= "  <th class=\"ui-state-default\">Back up?</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select fs_id,fs_volume,fs_size,fs_backup ";
      $q_string .= "from rsdp_filesystem ";
      $q_string .= "where fs_rsdp = " . $formVars['rsdp'];
      $q_rsdp_filesystem = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_rsdp_filesystem = mysql_fetch_array($q_rsdp_filesystem)) {

        if ($a_rsdp_filesystem['fs_backup'] == 1) {
          $backup = "Yes";
        } else {
          $backup = "No";
        }
        $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('filesystem.fill.php?id="  . $a_rsdp_filesystem['fs_id'] . "');jQuery('#dialogFilesystem').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_line('filesystem.del.php?id=" . $a_rsdp_filesystem['fs_id'] . "');\">";
        $linkend   = "</a>";

        $output .= "<tr>\n";
        $output .=   "<td class=\"ui-widget-content delete\">" . $linkdel                                                . "</td>\n";
        $output .=   "<td class=\"ui-widget-content\">"        . $linkstart . $a_rsdp_filesystem['fs_volume'] . $linkend . "</td>\n";
        $output .=   "<td class=\"ui-widget-content\">"        . $linkstart . $a_rsdp_filesystem['fs_size']   . $linkend . "</td>\n";
        $output .=   "<td class=\"ui-widget-content\">"        . $linkstart . $backup                         . $linkend . "</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n";
      mysql_free_result($q_rsdp_filesystem);

      print "document.getElementById('filesystem_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.rsdp.rsdp.value = " . $formVars['rsdp'] . ";\n";

      print "document.filesystem.fs_volume.value = '';\n";
      print "document.filesystem.fs_size.value = '';\n";
      print "document.filesystem.fs_backup.checked = true;\n";
      print "document.filesystem.fs_rsdp.value = " . $formVars['rsdp'] . ";\n";
      print "document.rsdp.adddup.disabled = false;\n";

      print "document.comments.com_rsdp.value = " . $formVars['rsdp'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
