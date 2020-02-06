<?php
# Script: backup.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "backup.mysql.php";

  logaccess($formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string  = "select int_server,inv_manager ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "where inv_id = " . $formVars['id'] . " and int_management = 1 ";
  $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_interface = mysql_fetch_array($q_interface);

  $retention[0] = "None";
  $retention[1] = "Less than 6 Months (Details Required)";
  $retention[2] = "6 Months";
  $retention[3] = "1 Year<";
  $retention[4] = "3 Years (Standard)";
  $retention[5] = "7 Years";

# get the backup information

  $output  = "<p></p>";
  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($AL_Edit)) {
    if (check_grouplevel($a_interface['inv_manager'])) {
      $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#backups\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
    }
  }
  $output .= "Backup Information";
  if (check_userlevel($AL_Edit)) {
    if (check_grouplevel($a_interface['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('backup-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"backup-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<ul>";
  $output .= "  <li><strong>Device</strong> - The system device.</li>";
  $output .= "  <li><strong>Mount</strong> - Where the file system is mounted.</li>";
  $output .= "  <li><strong>Size</strong> - The size of the file system.</li>";
  $output .= "  <li><strong>Volume</strong> - In a multipathed SAN environment, the volume this path is connected to.</li>";
  $output .= "  <li><strong>WWID</strong> - The World Wide Identifier.</li>";
  $output .= "  <li><strong>Updated</strong> - When this device was last updated. A checkmark indicates the device was updated automatically.</li>";
  $output .= "</ul>";

  $output .= "</div>";

  $output .= "</div>";

  $q_string  = "select bu_start,bu_include,bu_retention,bu_sunday,bu_monday,bu_tuesday,bu_wednesday,";
  $q_string .= "bu_thursday,bu_friday,bu_saturday,bu_suntime,bu_montime,bu_tuetime,bu_wedtime,";
  $q_string .= "bu_thutime,bu_fritime,bu_sattime,bu_notes ";
  $q_string .= "from backups ";
  $q_string .= "where bu_companyid = " . $formVars['id'] . " ";
  $q_backups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_backups) > 0) {
    $a_backups = mysql_fetch_array($q_backups);

    $output .= "<table class=\"ui-styled-table\">";
    $output .= "<tr>";
    $output .= "  <th class=\"ui-state-default\" colspan=\"7\">Backup Details</th>";
    $output .= "</tr>";
    $output .= "<tr>";
    $output .= "  <td class=\"ui-widget-content\">Start: " . $a_backups['bu_start'] . "</td>";
    $output .= "  <td class=\"ui-widget-content\">Include: " . $a_backups['bu_include'] . "</td>";
    $output .= "  <td class=\"ui-widget-content\">Retention: " . $retention[$a_backups['bu_retention']] . "</td>";
    $output .= "  <td class=\"ui-widget-content\">Notes: " . $a_backups['bu_notes'] . "</td>";
    $output .= "</tr>";
    $output .= "</table>\n";

    $output .= "<table class=\"ui-styled-table\">";
    $output .= "<tr>";
    $output .= "  <th class=\"ui-state-default\" colspan=\"7\">Backup Schedule</th>";
    $output .= "</tr>";

    $output .= "<tr>";
    if ($a_backups['bu_sunday']) {
      $type = 'Incremental';
    } else {
      $type = 'Full';
    }
    $output .= "<td class=\"ui-widget-content\">Sunday: "    . $type . " at " . $a_backups['bu_suntime'] . "</td>\n";
    if ($a_backups['bu_monday']) {
      $type = 'Incremental';
    } else {
      $type = 'Full';
    }
    $output .= "<td class=\"ui-widget-content\">Monday: "    . $type . " at " . $a_backups['bu_montime'] . "</td>\n";
    if ($a_backups['bu_tuesday']) {
      $type = 'Incremental';
    } else {
      $type = 'Full';
    }
    $output .= "<td class=\"ui-widget-content\">Tuesday: "   . $type . " at " . $a_backups['bu_tuetime'] . "</td>\n";
    if ($a_backups['bu_wednesday']) {
      $type = 'Incremental';
    } else {
      $type = 'Full';
    }
    $output .= "<td class=\"ui-widget-content\">Wednesday: " . $type . " at " . $a_backups['bu_wedtime'] . "</td>\n";
    if ($a_backups['bu_thursday']) {
      $type = 'Incremental';
    } else {
      $type = 'Full';
    }
    $output .= "<td class=\"ui-widget-content\">Thursday: "  . $type . " at " . $a_backups['bu_thutime'] . "</td>\n";
    if ($a_backups['bu_friday']) {
      $type = 'Incremental';
    } else {
      $type = 'Full';
    }
    $output .= "<td class=\"ui-widget-content\">Friday: "    . $type . " at " . $a_backups['bu_fritime'] . "</td>\n";
    if ($a_backups['bu_saturday']) {
      $type = 'Incremental';
    } else {
      $type = 'Full';
    }
    $output .= "<td class=\"ui-widget-content\">Saturday: "  . $type . " at " . $a_backups['bu_sattime'] . "</td>\n";
    $output .= "</tr>";

    $output .= "</table>";

    $output .= "<table class=\"ui-styled-table\">";
    $output .= "<tr>";
    $output .= "  <th class=\"ui-state-default\" colspan=\"7\">Filesystem Backups</th>";
    $output .= "</tr>";

    $output .= "</table>";

  } else {
    $output .= "<table class=\"ui-styled-table\">";
    $output .= "<tr>";
    $output .= "  <th class=\"ui-widget-content\">No Backups Scheduled</th>";
    $output .= "</tr>";
    $output .= "</table>";
  }

?>

document.getElementById('backup_mysql').innerHTML = '<?php print mysql_real_escape_string($output); ?>';


<?php

  $output  = "<p></p>";
  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($AL_Edit)) {
    if (check_grouplevel($a_interface['inv_manager'])) {
      $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#backup\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
    }
  }
  $output .= "Backup Log Information";
  if (check_userlevel($AL_Edit)) {
    if (check_grouplevel($a_interface['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('backuplog-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"backuplog-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<ul>";
  $output .= "  <li><strong>Device</strong> - The system device.</li>";
  $output .= "  <li><strong>Mount</strong> - Where the file system is mounted.</li>";
  $output .= "  <li><strong>Size</strong> - The size of the file system.</li>";
  $output .= "  <li><strong>Volume</strong> - In a multipathed SAN environment, the volume this path is connected to.</li>";
  $output .= "  <li><strong>WWID</strong> - The World Wide Identifier.</li>";
  $output .= "  <li><strong>Updated</strong> - When this device was last updated. A checkmark indicates the device was updated automatically.</li>";
  $output .= "</ul>";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<div class=\"main-help ui-widget-content\">\n";

  if (file_exists($Sitedir . "/servers/" . $a_interface['int_server'] . "/backups.output")) {
    $row = 1;
    if (($handle = fopen($Sitedir . "/servers/" . $a_interface['int_server'] . "/backups.output", "r")) !== FALSE) {
      $output .= "<pre>";
      while (($data = fgets($handle, 1000)) !== FALSE) {

        $output .= $data;

      }

      fclose($handle);
      $output .= "</pre>";
    }
  } else {
    $output .= "<p>FILE NOT FOUND (" . $Sitedir . "/servers/" . $a_interface['int_server'] . "/backups.output" . "): Unable to open backup output file.</p>\n";
  }

  $output .= "</div>";

?>

document.getElementById('backuplog_mysql').innerHTML = '<?php print mysql_real_escape_string($output); ?>';

