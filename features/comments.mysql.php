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
        $formVars["feat_id"]          = clean($_GET["feat_id"],          10);
        $formVars["feat_text"]        = clean($_GET["feat_text"],      2000);
        $formVars["feat_timestamp"]   = clean($_GET["feat_timestamp"],   60);
        $formVars["feat_user"]        = clean($_GET["feat_user"],        10);

        if ($formVars['feat_timestamp'] == "YYYY-MM-DD HH:MM:SS" || $formVars['feat_timestamp'] == '' || $formVars['feat_timestamp'] == 'Current Time') {
          $formVars['feat_timestamp'] = date("Y-m-d H:i:s");
        }

        if (strlen($formVars['feat_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "feat_feat_id   =   " . $formVars['id']             . "," . 
            "feat_text      = \"" . $formVars['feat_text']      . "\"," . 
            "feat_timestamp = \"" . $formVars['feat_timestamp'] . "\"," . 
            "feat_user      =   " . $formVars['feat_user'];

          if ($formVars['update'] == 0) {
            $query = "insert into inv_features_detail set feat_id = NULL, " . $q_string;
            $message = "Comment added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update inv_features_detail set " . $q_string . " where feat_id = " . $formVars['feat_id'];
            $message = "Comment updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['feat_id']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $q_string  = "select feat_closed ";
      $q_string .= "from inv_features ";
      $q_string .= "where feat_id = " . $formVars['id'];
      $q_inv_features = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_features = mysqli_fetch_array($q_inv_features);


      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Feature Request Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('request-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"request-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Feature Request Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Click here to Delete this detail record.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a detail record to load the data which lets you make changes.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Feature Request Management</strong> title bar to toggle the <strong>Feature Request Form</strong>.</li>\n";
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

      $q_string  = "select feat_id,feat_text,feat_timestamp,usr_first,usr_last ";
      $q_string .= "from inv_features_detail ";
      $q_string .= "left join inv_users on inv_users.usr_id = inv_features_detail.feat_user ";
      $q_string .= "where feat_feat_id = " . $formVars['id'] . " ";
      $q_string .= "order by feat_timestamp desc ";
      $q_inv_features_detail = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_inv_features_detail = mysqli_fetch_array($q_inv_features_detail)) {

        if ($a_inv_features['feat_closed'] == '1971-01-01') {
          $linkstart = "<a href=\"#\" onclick=\"show_file('"     . $Featureroot . "/comments.fill.php?id=" . $a_inv_features_detail['feat_id'] . "');showDiv('request-hide');\">";
          $linkdel   = "<a href=\"#\" onclick=\"delete_detail('" . $Featureroot . "/comments.del.php?id="  . $a_inv_features_detail['feat_id'] . "');\">";
          $linkend   = "</a>";
          $linktext  = "x";
        } else {
          $linkstart = '';
          $linkend = '';
          $linkdel = '';
          $linktext  = "--";
        }

        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . $linktext                                                              . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_features_detail['feat_timestamp']                                   . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_features_detail['usr_first'] . " " . $a_inv_features_detail['usr_last'] . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_features_detail['feat_text']                                                   . "</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_inv_features_detail);

      $output .= "</table>";

      print "document.getElementById('detail_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

      if ($a_inv_features['feat_closed'] == '1971-01-01') {
        print "document.start.feat_text.value = '';\n";
        print "document.start.feat_timestamp.value = 'Current Time';\n";
        print "document.start.featupdate.disabled = true;\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
