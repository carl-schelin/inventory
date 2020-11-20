<?php
# Script: openview.mysql.php
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
    $package = "openview.mysql.php";

    if (check_userlevel($AL_Edit)) {
      $formVars['id'] = clean($_GET['id'],       10);
      if (isset($_GET['start'])) {
        $formVars['start']    = clean($_GET['start'],    15);
      } else {
        $formVars['start'] = date('Y-m-d', strtotime("7 days ago"));
      }
      if (isset($_GET['end'])) {
        $formVars['end']      = clean($_GET['end'],      15);
      } else {
        $formVars['end'] = date('Y-m-d');
      }
      if (isset($_GET['search_a'])) {
        $formVars['search_a'] = clean($_GET['search_a'], 60);
      } else {
        $formVars['search_a'] = '';
      }

      $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('openview.mysql.php?id=" . $formVars['id'] . "&start=" . $formVars['start'] . "&end=" . $formVars['end'];
      $linkend   = "</a>";

# need to add 24 hours to the end because the default is yyyy-mm-dd 00:00:00 which loses everything that happened during the day.
      $formVars['end'] .= " 24:00:00";

      if (strlen($_SESSION['sort']) > 0) {
        $orderby = "order by " . $_SESSION['sort'] . " ";
        if ($_SESSION['sort'] == '') {
          $_SESSION['sort'] = 'desc';
        } else {
          $_SESSION['sort'] = '';
        }
      } else {
        $orderby = "order by alarm_timestamp desc,inv_name ";
        $_SESSION['sort'] = '';
      }

      if (strlen($formVars['search_a']) > 0) {
        $where = "and alarm_text like \"%" . $formVars['search_a'] . "%\" ";
      } else {
        $where = '';
      }

      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      print "document.getElementById('alarms_mysql').innerHTML = '" . wait_Process('Alarms Waiting...') . "';\n";

      $count = 0;

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Alarm Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><span class=\"ui-state-error\">Highlight</span> are Major and Critical alarms.</li>\n";
      $output .= "  <li><span class=\"ui-state-highlight\">Highlight</span> are Minor alarms.</li>\n";
      $output .= "  <li><span class=\"ui-state-default\">Highlight</span> are Warning alarms.</li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=alarm_id');\">ID</a></th>";
      $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=alarm_timestamp');\">Date</a></th>";
      $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_name');\">Server</a></th>";
      $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=atype_name');\">Level</a></th>";
      $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=alarm_text');\">Alarm</a></th>";
      $output .= "</tr>";

      $q_string  = "select alarm_id,alarm_timestamp,inv_name,atype_name,alarm_text ";
      $q_string .= "from alarms ";
      $q_string .= "left join inventory  on inventory.inv_id    = alarms.alarm_companyid ";
      $q_string .= "left join alarm_type on alarm_type.atype_id = alarms.alarm_level ";
      $q_string .= "where alarm_disabled = 0 and alarm_timestamp >= '" . $formVars['start'] . "' and alarm_timestamp <= '" . $formVars['end'] . "' and alarm_companyid = " . $formVars['id'] . " " . $where;
      $q_string .= $orderby;
      $q_alarms = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_alarms = mysqli_fetch_array($q_alarms)) {

        $class = "ui-widget-content";
        if ($a_alarms['atype_name'] == 'Critical' || $a_alarms['atype_name'] == 'Major') {
          $class = "ui-state-error";
        }
        if ($a_alarms['atype_name'] == 'Minor') {
          $class = "ui-state-highlight";
        }
        if ($a_alarms['atype_name'] == 'Warning') {
          $class = "ui-state-default";
        }

        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\">" . $a_alarms['alarm_id']        . "</a></td>\n";
        $output .= "<td class=\"" . $class . "\">" . $a_alarms['alarm_timestamp'] . "</a></td>\n";
        $output .= "<td class=\"" . $class . "\">" . $a_alarms['inv_name']        . "</a></td>\n";
        $output .= "<td class=\"" . $class . "\">" . $a_alarms['atype_name']      . "</a></td>\n";
        $output .= "<td class=\"" . $class . "\">" . $a_alarms['alarm_text']      . "</a></td>\n";
        $output .= "</tr>\n";
        $count++;

      }

      $output .= "<tr>\n";
      $output .= "<td class=\"ui-widget-content\" colspan=\"5\"><strong>Total Entries: </strong>" . $count . "</td>\n";
      $output .= "</tr>\n";

      $output .= "</table>\n";

      print "document.getElementById('alarms_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }

?>
