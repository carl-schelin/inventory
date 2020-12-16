<?php
# Script: timelines.hours.php
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
    $package = "timelines.hours.php";

    if (check_userlevel($db, $AL_Edit)) {
      $formVars['start'] = clean($_GET['start'], 15);
      $formVars['end']   = clean($_GET['end'],   15);
      $formVars['group'] = clean($_GET['group'], 10);

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">RSDP By Hours Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('hours-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"hours-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p>Cells showing a 0.00 value vs a 0 value indicate the time was greater than 0 but rounded down to \n";
      $output .= "0.00. Values with 0 either indicate a skipped step or that the server build hasn't been completed yet.</p>\n";

      $output .= "<p><span class=\"ui-state-highlight\">Highlighted</span> devices identify Virtual servers.</p>\n";

      $output .= "<p><span class=\"ui-state-error\">Highlighted</span> devices identify tasks that were completed by administratively closing out the task for the designated group.</p>\n";

      $output .= "<p>Clicking on an RSDP ID will bring up a single graph of that server based on number of hours between tasks.</p>\n";

      $output .= "<p>Click <a href=\"timelines.hours.csv.php?start=" . $formVars['start'] . "&end=" . $formVars['end'] . "&group=" . $formVars['group'] . "\" target=\"_blank\">here</a> to get an importable page of this data.</p>\n";

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
        if (rsdp_Virtual($db, "$a_rsdp_server['rsdp_id'])) {
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

# divide the seconds by 60 to get minutes, then divide the minutes to get hours, then divide the hours by 24 to get the number of days.
          $diff[$i] = $diff[$i] / 60 / 60;

        }

        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\"><a href=\"timelines.graph.php?rsdp=" . $a_rsdp_server['rsdp_id'] . "&type=hours\">" . $a_rsdp_server['rsdp_id'] . "</a></td>\n";
        for ($i = 1; $i < 19; $i++) {
          if ($diff[$i] > 0) {
            $output .= "<td class=\"" . $class . "\">" . number_format($diff[$i], 2, '.', ',') . "</td>\n";
          } else {
            $output .= "<td class=\"" . $class . "\">0</td>\n";
          }
        }
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";

      print "document.getElementById('hours_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }

?>
