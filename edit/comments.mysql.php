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
    $formVars['com_companyid']   = clean($_GET['com_companyid'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['com_companyid'] == '') {
      $formVars['com_companyid'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars["id"]              = clean($_GET["id"],              10);
        $formVars["com_text"]        = clean($_GET["com_text"],      2000);
        $formVars["com_timestamp"]   = clean($_GET["com_timestamp"],   60);
        $formVars["com_user"]        = clean($_GET["com_user"],        10);

        if ($formVars['com_user'] == '') {
          $formVars['com_user'] = 0;
        }

        if ($formVars['com_timestamp'] == "YYYY-MM-DD HH:MM:SS" || $formVars['com_timestamp'] == '' || $formVars['com_timestamp'] == 'Current Time') {
          $formVars['com_timestamp'] = date("Y-m-d H:i:s");
        }

        if (strlen($formVars['com_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "com_companyid =   " . $formVars['com_companyid']            . "," . 
            "com_text      = \"" . $formVars['com_text']      . "\"," . 
            "com_timestamp = \"" . $formVars['com_timestamp'] . "\"," . 
            "com_user      =   " . $formVars['com_user'];

          if ($formVars['update'] == 0) {
            $query = "insert into comments set com_id = NULL, " . $q_string;
            $message = "Comment added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update comments set " . $q_string . " where com_id = " . $formVars['id'];
            $message = "Comment updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['id']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Comment Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('comment-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"comment-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Comment Listing</strong>\n";
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

      $q_string  = "select com_id,com_text,com_timestamp,usr_first,usr_last ";
      $q_string .= "from comments ";
      $q_string .= "left join users on users.usr_id = comments.com_user ";
      $q_string .= "where com_companyid = " . $formVars['com_companyid'] . " ";
      $q_string .= "order by com_timestamp desc ";
      $q_comments = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      while ($a_comments = mysqli_fetch_array($q_comments)) {

        $linkstart = "<a href=\"#comments\" onclick=\"show_file('comments.fill.php?id=" . $a_comments['com_id'] . "');showDiv('comments-hide');\">";
        $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_comment('comments.del.php?id="  . $a_comments['com_id'] . "');\">";
        $linkend   = "</a>";

        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                                                         . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_comments['com_timestamp']                             . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_comments['usr_first'] . " " . $a_comments['usr_last'] . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"                     . $a_comments['com_text']                                             . "</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_comments);

      $output .= "</table>";

      print "document.getElementById('comments_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

      print "document.edit.com_text.value = '';\n";
      print "document.edit.com_timestamp.value = 'Current Time';\n";
      print "document.edit.comupdate.disabled = true;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
