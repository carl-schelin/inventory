<?php
# Script: morning.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "morning.mysql.php";
    $formVars['update']     = clean($_GET['update'],   10);
    $formVars['id']         = clean($_GET['id'],       10);
    $formVars['server']     = clean($_GET['server'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['server'] == '') {
      $formVars['server'] = 0;
    }

    if (check_userlevel($AL_Edit)) {

# update the issue
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['morn_id']        = clean($_GET['morn_id'],          10);
        $formVars['morn_text']      = clean($_GET['morn_text'],       255);
        $formVars['morn_timestamp'] = clean($_GET['morn_timestamp'],   20);
        $formVars['morn_user']      = clean($_GET['morn_user'],        10);
        $formVars['morn_status']    = clean($_GET['morn_status'],      10);

        if ($formVars['morn_timestamp'] == "YYYY-MM-DD HH:MM:SS" || $formVars['morn_timestamp'] == '' || $formVars['morn_timestamp'] == 'Current Time') {
          $formVars['morn_timestamp'] = date("Y-m-d H:i:s");
        }

        if (strlen($formVars['morn_text']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "morn_issue      =   " . $formVars['id']             . "," .
            "morn_text       = \"" . $formVars['morn_text']      . "\"," . 
            "morn_timestamp  = \"" . $formVars['morn_timestamp'] . "\"," .
            "morn_status     =   " . $formVars['morn_status']    . "," .
            "morn_user       =   " . $formVars['morn_user'];

          if ($formVars['update'] == 0) {
            $query = "insert into issue_morning set morn_id = NULL, " . $q_string;
            $message = "Morning Report added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update issue_morning set " . $q_string . " where morn_id = " . $formVars['morn_id'];
            $message = "Morning Report updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['morn_id']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

# publish the status; update the morning report
      if ($formVars['update'] == 2) {

        $output = '';
        $q_string  = "select inv_name,inv_function ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_id = " . $formVars['server'] . " ";
        $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_inventory = mysqli_fetch_array($q_inventory);

        $initial = "<strong>" . $a_inventory['inv_name'] . "</strong> - " . $a_inventory['inv_function'] . " - ";

        $q_string  = "select morn_text,morn_user,morn_timestamp,morn_status ";
        $q_string .= "from issue_morning ";
        $q_string .= "where morn_issue = " . $formVars['id'] . " ";
        $q_string .= "order by morn_timestamp ";
        $q_issue_morning = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_issue_morning = mysqli_fetch_array($q_issue_morning)) {

          $output .= $initial . $a_issue_morning['morn_text'];

          $initial = "<br><strong>Update</strong> - ";

# build final string for the Morning Report
          $q_morning_report = 
            "rep_user        =   " . $a_issue_morning['morn_user']      . "," . 
            "rep_issue       =   " . $formVars['id']                    . "," . 
            "rep_timestamp   = \"" . $a_issue_morning['morn_timestamp'] . "\"," . 
            "rep_group       =   " . $_SESSION['group']                 . "," . 
            "rep_date        = \"" . date('Y-m-d')                      . "\"," . 
            "rep_status      =   " . $a_issue_morning['morn_status']    . "," . 
            "rep_task        = \"" . $output                            . "\"";
        }

# see if there is an existing morning report tied to this issue.
        $q_string  = "select rep_id ";
        $q_string .= "from report ";
        $q_string .= "where rep_issue = " . $formVars['id'] . " ";
        $q_string .= "order by rep_date ";
        $q_string .= "desc ";
        $q_string .= "limit 1 ";
        $q_report = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_report) > 0) {
          $a_report = mysqli_fetch_array($q_report);
          $query = "update report set " . $q_morning_report . " where rep_id = " . $a_report['rep_id'];
        } else {
          $query = "insert into report set rep_id = null," . $q_morning_report;
        }

        $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

        print "alert('Morning Report published.');\n";
      }

# display the current morning report.
# server - function - status
# update - status

      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";

# box with the actual report as it'll be displayed
      $q_string  = "select inv_name,inv_function ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['server'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_inventory = mysqli_fetch_array($q_inventory);

      $initial = "<p><strong>" . $a_inventory['inv_name'] . "</strong> - " . $a_inventory['inv_function'] . " - ";

      $output .= "<div class=\"main-help\">\n";

      $q_string  = "select morn_text ";
      $q_string .= "from issue_morning ";
      $q_string .= "where morn_issue = " . $formVars['id'] . " ";
      $q_string .= "order by morn_timestamp ";
      $q_issue_morning = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_issue_morning = mysqli_fetch_array($q_issue_morning)) {

        $output .= $initial . $a_issue_morning['morn_text'];

        $initial = "<br><strong>Update</strong> - ";
      }

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Morning Report Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('morning-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"morning-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Morning Report Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Click here to Delete this entry.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a line to load the data which lets you make changes.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Morning Report Management</strong> title bar to toggle the <strong>Morning Report Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

# display the table for editing
      $status[0] = 'No Status';
      $status[1] = 'Resolved';
      $status[2] = 'Warning';
      $status[3] = 'Major';

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">Timestamp</th>";
      $output .= "  <th class=\"ui-state-default\">User</th>";
      $output .= "  <th class=\"ui-state-default\">Description</th>";
      $output .= "  <th class=\"ui-state-default\">Status</th>";
      $output .= "</tr>";

      $q_string  = "select morn_id,morn_text,usr_name,morn_timestamp,morn_status ";
      $q_string .= "from issue_morning ";
      $q_string .= "left join users on users.usr_id = issue_morning.morn_user ";
      $q_string .= "where morn_issue = " . $formVars['id'] . " ";
      $q_string .= "order by morn_timestamp ";
      $q_issue_morning = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_issue_morning = mysqli_fetch_array($q_issue_morning)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('"      . $Issueroot . "/morning.fill.php?id=" . $a_issue_morning['morn_id'] . "');showDiv('morning-hide');\">";
        $linkdel   = "<a href=\"#\" onclick=\"delete_morning('" . $Issueroot . "/morning.del.php?id="  . $a_issue_morning['morn_id'] . "');\">";
        $linkend   = "</a>";
        $linktext  = "x";

        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . $linktext                                . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_morning['morn_timestamp']       . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_morning['usr_name']             . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_morning['morn_text']            . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $status[$a_issue_morning['morn_status']] . $linkend . "</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_issue_morning);

      $output .= "</table>";

      print "document.getElementById('morning_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
