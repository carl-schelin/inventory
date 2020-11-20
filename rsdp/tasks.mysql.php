<?php
# Script: tasks.mysql.php
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
    $package = "tasks.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);
    $formVars['id']     = clean($_GET['id'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if (check_userlevel($AL_Edit)) {

      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Task Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('server-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"server-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Reset Task</strong> - Selecting this change the status of this task from Complete to Waiting. It can also remove a 'Skipped' tag ";
      $output .= "should requirements change (such as SAN, backups, or monitoring).</li>\n";
      $output .= "  <li><strong>Task</strong> - The name of the Task to be worked.</li>\n";
      $output .= "  <li><strong>Status</strong> - The status of the Task.\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Done</strong> - The Task has been completed.</li>\n";
      $output .= "    <li><strong>Skipped</strong> - No work needs to be done for this task.</li>\n";
      $output .= "    <li><strong>Waiting</strong> - Waiting on this group or person to work this task. As tasks are completed, emails and tickets are generated to ";
      $output .= "notify the next group or person that their task is ready to be worked. This is done because some Tasks require additional information that is ";
      $output .= "provided by another team.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "  <li><strong>Completed By</strong> - Who completed the work. While a group may be identified, the person completing the task will be identified here.</li>\n";
      $output .= "  <li><strong>Date Completed</strong> - The date and time the task was completed.</li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Reset</th>\n";
      $output .= "  <th class=\"ui-state-default\">Task</th>\n";
      $output .= "  <th class=\"ui-state-default\">Status</th>\n";
      $output .= "  <th class=\"ui-state-default\">Completed By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date Completed</th>\n";
      $output .= "</tr>\n";

      $virtual = rsdp_Virtual($formVars['id']);

      $tasks = array(
        1 => "Server Initialization",
        2 => "Server Provisioning",
        3 => "SAN Design",
        4 => "Network Configuration",
        5 => "Data Center",
        10 => "System Installation",
        11 => "SAN Provisioning",
        12 => "System Configuration",
        13 => "System Backups",
        14 => "Monitoring Configuration",
        15 => "Application Installed",
        16 => "Monitoring Complete",
        17 => "Application Configured",
        18 => "InfoSec Completed"
      );

      $waiting = array(
        1 => "rsdp_platformspoc",
        2 => "rsdp_platformspoc",
        3 => "rsdp_sanpoc",
        4 => "rsdp_networkpoc",
        5 => "rsdp_dcpoc",
        10 => "rsdp_platformspoc",
        11 => "rsdp_sanpoc",
        12 => "rsdp_platformspoc",
        13 => "rsdp_backuppoc",
        14 => "rsdp_monitorpoc",
        15 => "rsdp_apppoc",
        16 => "rsdp_monitorpoc",
        17 => "rsdp_apppoc",
        18 => "rsdp_platformspoc",
      );

      $group = array(
        1 => "Platforms",
        2 => "Platforms",
        3 => "SAN",
        4 => "Network Engineering",
        5 => "Data Center",
        10 => "Platforms",
        11 => "SAN",
        12 => "Platforms",
        13 => "Backups",
        14 => "Monitoring",
        15 => "Applications",
        16 => "Monitoring",
        17 => "Applications",
        18 => "Platforms",
      );

      $script = array(
        1 => "build/initial.php",
        2 => "build/build.php",
        3 => "san/designed.php",
        4 => "network/network.php",
        5 => "physical/physical.php",
        10 => "system/installed.php",
        11 => "san/provisioned.php",
        12 => "system/configured.php",
        13 => "backups/backups.php",
        14 => "monitoring/monitoring.php",
        15 => "application/installed.php",
        16 => "application/monitored.php",
        17 => "application/configured.php",
        18 => "infosec/scanned.php",
      );

      if ($virtual) {
        $tasks[5]   = "Virtualization";
        $waiting[5] = "rsdp_virtpoc";
        $group[5]   = "Virtualization";
        $script[5]  = "virtual/virtual.php";
      }

      for ($i = 1; $i < 19; $i++) {
# skip any steps that aren't in use any more
        if ($i < 6 || $i > 9) {

          $q_string  = "select st_id,st_completed,st_timestamp,st_user,usr_first,usr_last,st_step ";
          $q_string .= "from rsdp_status ";
          $q_string .= "left join users on users.usr_id = rsdp_status.st_user ";
          $q_string .= "where st_rsdp = " . $formVars['id'] . " and st_step = " . $i . " ";
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          $linkstart = "<a href=\"" . $RSDProot . "/" . $script[$i] . "?rsdp=" . $formVars['id'] . "\">";
          $linkend = "</a>";

          $output .= "<tr>\n";

          if (mysqli_num_rows($q_rsdp_status) > 0) {
            $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);
            $linkdel = "<a href=\"#\" onclick=\"javascript:clear_task('" . $RSDProot . "/tasks.del.php?id=" . $a_rsdp_status['st_id'] . "');\">";

            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "Reset Task" . $linkend . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $tasks[$i]   . $linkend . "</td>\n";

            $completed = "Waiting";
            $user      = '';
            $timestamp = '';
            if ($a_rsdp_status['st_completed'] == 1) {
              $completed = "Done";
              $user      = $a_rsdp_status['usr_first'] . " " . $a_rsdp_status['usr_last'];
              $timestamp = $a_rsdp_status['st_timestamp'];
            }
            if ($a_rsdp_status['st_completed'] == 2) {
              $completed = "Skipped";
              $user      = "--";
              $timestamp = "--";
            }
            $output .= "  <td class=\"ui-widget-content\">" . $completed . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $user      . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $timestamp . "</td>\n";
          } else {
            $output .= "  <td class=\"ui-widget-content delete\">--</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $linkstart . $tasks[$i] . $linkend . "</td>\n";

            $q_string  = "select " . $waiting[$i] . ",usr_first,usr_last ";
            $q_string .= "from rsdp_server ";
            $q_string .= "left join users on users.usr_id = rsdp_server." . $waiting[$i] . " ";
            $q_string .= "where rsdp_id = " . $formVars['id'];
            $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

            if ($a_rsdp_server[$waiting[$i]] == 0) {
              $output .= "  <td class=\"ui-widget-content\">Waiting on: " . $group[$i] . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">&nbsp;</td>\n";
              $output .= "  <td class=\"ui-widget-content\">&nbsp;</td>\n";
            } else {
              $output .= "  <td class=\"ui-widget-content\">Waiting on: " . $a_rsdp_server['usr_first'] . " " . $a_rsdp_server['usr_last'] . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">&nbsp;</td>\n";
              $output .= "  <td class=\"ui-widget-content\">&nbsp;</td>\n";
            }
          }
          $output .= "</tr>\n";
        }
      }

      $output .= "</table>\n";

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
