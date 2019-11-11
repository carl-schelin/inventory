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
    $formVars['id'] = clean($_GET['id'], 10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if (check_userlevel(2)) {

      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">";
      if (check_userlevel($AL_Edit)) {
        if (check_grouplevel($a_inventory['inv_manager'])) {
          $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#comments\" target=\"_blank\"><img src=\"/inventory/imgs/pencil.gif\">";
        }
      }
      $output .= "System Comments";
      if (check_userlevel($AL_Edit)) {
        if (check_grouplevel($a_inventory['inv_manager'])) {
          $output .= "</a>";
        }
      }
      $output .= "</th>";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('comment-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"comment-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";


      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Date/Time</th>";
      $output .= "  <th class=\"ui-state-default\">User</th>";
      $output .= "  <th class=\"ui-state-default\">Detail</th>";
      $output .= "</tr>";

      $q_string  = "select com_id,com_text,com_timestamp,usr_first,usr_last ";
      $q_string .= "from comments ";
      $q_string .= "left join users on users.usr_id = comments.com_user ";
      $q_string .= "where com_companyid = " . $formVars['id'] . " ";
      $q_string .= "order by com_timestamp desc ";
      $q_comments = mysql_query($q_string) or die ($q_string . ": " . mysql_error());
      while ($a_comments = mysql_fetch_array($q_comments)) {

        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_comments['com_timestamp']                             . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_comments['usr_first'] . " " . $a_comments['usr_last'] . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_comments['com_text']                                  . "</td>";
        $output .= "</tr>";
      }

      mysql_free_result($q_comments);

      $output .= "</table>";

      print "document.getElementById('comments_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
