<?php
# Script: comments.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "comments.mysql.php";
    $formVars['update']          = clean($_GET['update'], 10);
    $formVars['id']              = clean($_GET['id'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars["bug_id"]          = clean($_GET["bug_id"],          10);
        $formVars["bug_text"]        = mysqli_real_escape_string($db, clean($_GET["bug_text"],      2000));
        $formVars["bug_timestamp"]   = clean($_GET["bug_timestamp"],   60);
        $formVars["bug_user"]        = clean($_GET["bug_user"],        10);

        if ($formVars['bug_timestamp'] == "YYYY-MM-DD HH:MM:SS" || $formVars['bug_timestamp'] == '' || $formVars['bug_timestamp'] == 'Current Time') {
          $formVars['bug_timestamp'] = date("Y-m-d H:i:s");
        }

        if (strlen($formVars['bug_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "bug_bug_id    =   " . $formVars['id']            . "," . 
            "bug_text      = \"" . $formVars['bug_text']      . "\"," . 
            "bug_timestamp = \"" . $formVars['bug_timestamp'] . "\"," . 
            "bug_user      =   " . $formVars['bug_user'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into bugs_detail set bug_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update bugs_detail set " . $q_string . " where bug_id = " . $formVars['bug_id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['bug_id']);

          mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $q_string  = "select bug_closed ";
      $q_string .= "from inv_bugs ";
      $q_string .= "where bug_id = " . $formVars['id'];
      $q_inv_bugs = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_inv_bugs = mysqli_fetch_array($q_inv_bugs);


      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Comment</th>";
      $output .= "  <th class=\"ui-state-default\">Date/Time</th>";
      $output .= "  <th class=\"ui-state-default\">User</th>";
      $output .= "  <th class=\"ui-state-default\">Detail</th>";
      $output .= "</tr>";

      $q_string  = "select bug_id,bug_text,bug_timestamp,usr_first,usr_last ";
      $q_string .= "from bugs_detail ";
      $q_string .= "left join inv_users on inv_users.usr_id = bugs_detail.bug_user ";
      $q_string .= "where bug_bug_id = " . $formVars['id'] . " ";
      $q_string .= "order by bug_timestamp desc ";
      $q_bugs_detail = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_bugs_detail) > 0) {
        while ($a_bugs_detail = mysqli_fetch_array($q_bugs_detail)) {

          if ($a_inv_bugs['bug_closed'] == '1971-01-01') {
            $linkstart = "<a href=\"#comments\" onclick=\"show_file('"     . $Bugroot . "/comments.fill.php?id=" . $a_bugs_detail['bug_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
            $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_detail('comments.del.php?id=" . $a_bugs_detail['bug_id'] . "');\">";
            $linkend   = "</a>";
          } else {
            $linkstart = '';
            $linkend = '';
            $linkdel = "--";
          }

          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_bugs_detail['bug_timestamp']                                . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_bugs_detail['usr_first'] . " " . $a_bugs_detail['usr_last'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_bugs_detail['bug_text']                                                . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No comments found</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_bugs_detail);

      $output .= "</table>";

      print "document.getElementById('detail_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
