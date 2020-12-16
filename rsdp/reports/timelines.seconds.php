<?php
# Script: timelines.seconds.php
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
    $package = "timelines.seconds.php";

    if (check_userlevel($db, $AL_Edit)) {
      $formVars['start'] = clean($_GET['start'], 15);
      $formVars['end']   = clean($_GET['end'],   15);
      $formVars['group'] = clean($_GET['group'], 10);

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">RSDP By Seconds Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('rsdp-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"rsdp-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p>This page shows the number of seconds it takes from the previous task's completion to the current tasks's completion.</p>\n";

      $output .= "<p><span class=\"ui-state-highlight\">Highlighted</span> devices identify Virtual servers.</p>\n";

      $output .= "<p><span class=\"ui-state-error\">Highlighted</span> devices identify tasks that were completed by administratively closing out the task for the designated group.</p>\n";

      $output .= "<p>Click <a href=\"timelines.seconds.csv.php?start=" . $formVars['start'] . "&end=" . $formVars['end'] . "&group=" . $formVars['group'] . "\" target=\"_blank\">here</a> to get an importable page of this data.</p>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      settype($task[0], "float");
      settype($task[1], "float");
      settype($task[2], "float");
      settype($task[3], "float");
      settype($task[4], "float");
      settype($task[5], "float");
      settype($task[6], "float");
      settype($task[7], "float");
      settype($task[8], "float");
      settype($task[9], "float");
      settype($task[10], "float");
      settype($task[11], "float");
      settype($task[12], "float");
      settype($task[13], "float");
      settype($task[14], "float");
      settype($task[15], "float");
      settype($task[16], "float");
      settype($task[17], "float");

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">RSDP</th>";
      $output .= "  <th class=\"ui-state-default\">Start</th>";
      $output .= "  <th class=\"ui-state-default\">Build</th>";
      $output .= "  <th class=\"ui-state-default\">SAN</th>";
      $output .= "  <th class=\"ui-state-default\">Network</th>";
      $output .= "  <th class=\"ui-state-default\">V/DC</th>";
      $output .= "  <th class=\"ui-state-default\">DC</th>";
      $output .= "  <th class=\"ui-state-default\">DC</th>";
      $output .= "  <th class=\"ui-state-default\">SR</th>";
      $output .= "  <th class=\"ui-state-default\">DC</th>";
      $output .= "  <th class=\"ui-state-default\">System</th>";
      $output .= "  <th class=\"ui-state-default\">SAN</th>";
      $output .= "  <th class=\"ui-state-default\">System</th>";
      $output .= "  <th class=\"ui-state-default\">Backup</th>";
      $output .= "  <th class=\"ui-state-default\">Monitor</th>";
      $output .= "  <th class=\"ui-state-default\">App</th>";
      $output .= "  <th class=\"ui-state-default\">Mon</th>";
      $output .= "  <th class=\"ui-state-default\">App</th>";
      $output .= "  <th class=\"ui-state-default\">Infosec</th>";
      $output .= "</tr>";

      $q_string  = "select rsdp_id,rsdp_created ";
      $q_string .= "from rsdp_server ";
      $q_string .= "where rsdp_created >= '" . $formVars['start'] . "' and rsdp_created <= '" . $formVars['end'] . "' ";
      if ($formVars['group'] > 0) {
        $q_string .= "and grp_id = " . $formVars['group'] . " ";
      }
      $q_string .= "order by rsdp_id ";
      $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {

        $class = "ui-widget-content";
        if (rsdp_Virtual($db, $a_rsdp_server['rsdp_id'])) {
          $class = "ui-state-highlight";
        }

        for ($i = 0; $i < 19; $i++) {
          $task[$i] = 0;
        }

        for ($i = 0; $i < 19; $i++) {
          $diff[$i] = 0;
        }

        $baseline = 0;
        for ($i = 1; $i < 19; $i++) {

          $q_string  = "select st_completed,st_timestamp,st_user ";
          $q_string .= "from rsdp_status ";
          $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and st_step = " . $i . " ";
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_rsdp_status) > 0) {
            $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

# if the task was completed; overcome by events, identify them.
            if ($a_rsdp_status['st_user'] == 1) {
              $class = "ui-state-error";
            }

            $task[$i] = strtotime($a_rsdp_status['st_timestamp']);

            if ($i == 1) {
              $baseline = $task[$i];
            }
          } else {
            $task[$i] = $task[$i-1];
          }

          $task[$i] = $task[$i] - $baseline;
          if ($task[$i] < 0) {
            $task[$i] = $task[$i-1];
          }

          if ($task[$i] > 0) {
            $diff[$i] = $task[$i] - $task[$i - 1];
          }
          if ($diff[$i] < 0) {
            $diff[$i] = 0;
          }

        }

        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\">" . $a_rsdp_server['rsdp_id'] . "</td>\n";
        for ($i = 1; $i < 19; $i++) {
          $output .= "<td class=\"" . $class . "\">" . $diff[$i] . "</td>\n";
        }
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";

      print "document.getElementById('seconds_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }

?>
