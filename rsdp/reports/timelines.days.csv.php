<?php
# Script: timelines.days.csv.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "timelines.days.csv.php";

    if (check_userlevel($db, $AL_Edit)) {
      $formVars['start'] = clean($_GET['start'], 15);
      $formVars['end']   = clean($_GET['end'],   15);
      $formVars['group'] = clean($_GET['group'], 10);

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

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

      $output  = "<html>";
      $output .= "<head>";
      $output .= "</head>";
      $output .= "<body>";

      $output .= "<p>RSDP,";
      $output .= "Start,";
      $output .= "Build";
      $output .= "SAN,";
      $output .= "Network";
      $output .= "V/DC,";
      $output .= "DC";
      $output .= "DC,";
      $output .= "SR,";
      $output .= "DC,";
      $output .= "System,";
      $output .= "SAN,";
      $output .= "System,";
      $output .= "Backup,";
      $output .= "Monitor,";
      $output .= "App,";
      $output .= "Mon,";
      $output .= "App,";
      $output .= "Infosec";

      $q_string  = "select rsdp_id,rsdp_created ";
      $q_string .= "from rsdp_server ";
      $q_string .= "where rsdp_created >= '" . $formVars['start'] . "' and rsdp_created <= '" . $formVars['end'] . "' ";
      if ($formVars['group'] > 0) {
        $q_string .= "and grp_id = " . $formVars['group'] . " ";
      }
      $q_string .= "order by rsdp_id ";
      $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {

        for ($i = 0; $i < 19; $i++) {
          $task[$i] = 0;
        }

        for ($i = 0; $i < 19; $i++) {
          $diff[$i] = 0;
        }

        $baseline = 0;
        for ($i = 1; $i < 19; $i++) {

          $q_string  = "select st_completed,st_timestamp ";
          $q_string .= "from rsdp_status ";
          $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and st_step = " . $i . " ";
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_rsdp_status) > 0) {
            $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

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
          if ($diff[$i] > 0) {
            $diff[$i] = $diff[$i] / 60 / 60 / 24;
          }

        }

        $comma = "";
        $output .= "<br>";
        $output .= $a_rsdp_server['rsdp_id'] . ",";
        for ($i = 1; $i < 19; $i++) {
          if ($diff[$i] > 0) {
            $output .= $comma . number_format($diff[$i], 2, '.', ',');
          } else {
            $output .= $comma . "0";
          }
          $comma = ",";
        }

      }

      $output .= "</body>";
      $output .= "</html>";

      print mysqli_real_escape_string($output);

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }

?>
