<?php
# Script: comments.mysql.php
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
        $formVars["det_id"]          = clean($_GET["det_id"],          10);
        $formVars["det_text"]        = clean($_GET["det_text"],      2000);
        $formVars["det_timestamp"]   = clean($_GET["det_timestamp"],   60);
        $formVars["det_user"]        = clean($_GET["det_user"],        10);

        if ($formVars['det_timestamp'] == "YYYY-MM-DD HH:MM:SS" || $formVars['det_timestamp'] == '' || $formVars['det_timestamp'] == 'Current Time') {
          $formVars['det_timestamp'] = date("Y-m-d H:i:s");
        }

        if (strlen($formVars['det_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "det_issue     =   " . $formVars['id']            . "," . 
            "det_text      = \"" . $formVars['det_text']      . "\"," . 
            "det_timestamp = \"" . $formVars['det_timestamp'] . "\"," . 
            "det_user      =   " . $formVars['det_user'];

          if ($formVars['update'] == 0) {
            $query = "insert into issue_detail set det_id = NULL, " . $q_string;
            $message = "Comment added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update issue_detail set " . $q_string . " where det_id = " . $formVars['det_id'];
            $message = "Comment updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['det_id']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $q_string  = "select iss_closed ";
      $q_string .= "from issue ";
      $q_string .= "where iss_id = " . $formVars['id'];
      $q_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_issue = mysqli_fetch_array($q_issue);


      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Problem Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('problem-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"problem-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Problem Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Click here to Delete this detail record.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a detail record to load the data which lets you make changes.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Problem Management</strong> title bar to toggle the <strong>Problem Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">Date/Time</th>";
      $output .= "  <th class=\"ui-state-default\">User</th>";
      $output .= "  <th class=\"ui-state-default\">Detail</th>";
      $output .= "</tr>";

      $q_string  = "select det_id,det_text,det_timestamp,usr_name ";
      $q_string .= "from issue_detail ";
      $q_string .= "left join users on users.usr_id = issue_detail.det_user ";
      $q_string .= "where det_issue = " . $formVars['id'] . " ";
      $q_string .= "order by det_timestamp desc ";
      $q_issue_detail = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      while ($a_issue_detail = mysqli_fetch_array($q_issue_detail)) {

        $updated = preg_replace("/\[:hash:\]/", "#", $a_issue_detail['det_text']);

        if ($a_issue['iss_closed'] == '0000-00-00') {
          $linkstart = "<a href=\"#\" onclick=\"show_file('"     . $Issueroot . "/comments.fill.php?id=" . $a_issue_detail['det_id'] . "');showDiv('problem-hide');\">";
          $linkdel   = "<a href=\"#\" onclick=\"delete_detail('" . $Issueroot . "/comments.del.php?id="  . $a_issue_detail['det_id'] . "');\">";
          $linkend   = "</a>";
          $linktext  = "x";
        } else {
          $linkstart = '';
          $linkend = '';
          $linkdel = '';
          $linktext  = "--";
        }

        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . $linktext                         . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_detail['det_timestamp']  . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_detail['usr_name']       . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $updated                                                  . "</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_issue_detail);

      $output .= "</table>";

      print "document.getElementById('detail_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

      if ($a_issue['iss_closed'] == '0000-00-00') {
        print "document.start.det_text.value = '';\n";
        print "document.start.det_timestamp.value = 'Current Time';\n";
        print "document.start.detupdate.disabled = true;\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
