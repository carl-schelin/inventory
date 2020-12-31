<?php
# Script: handoff.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "handoff.mysql.php";
    $formVars['update']           = clean($_GET['update'],          10);
    $formVars['off_group']        = clean($_GET['off_group'],       10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],              10);
        $formVars['off_user']       = clean($_GET['off_user'],        10);
        $formVars['off_group']      = clean($_GET['off_group'],       10);
        $formVars['off_timestamp']  = clean($_GET['off_timestamp'],   30);
        $formVars['off_handoff']    = clean($_GET['off_handoff'],   1024);
        $formVars['off_disabled']   = clean($_GET['off_disabled'],    10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['off_timestamp'] == '1971-01-01 00:00:00' || $formVars['off_timestamp'] == 'Current Date' || $formVars['off_timestamp'] == '') {
          $formVars['off_timestamp'] = date('Y-m-d H:i:s');
        }
        if ($formVars['off_disabled'] == 'true') {
          $formVars['off_disabled'] = 1;
        } else {
          $formVars['off_disabled'] = 0;
        }
    
        if (strlen($formVars['off_handoff']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "off_user        =   " . $formVars['off_user']      . "," .
            "off_group       =   " . $formVars['off_group']     . "," .
            "off_timestamp   = \"" . $formVars['off_timestamp'] . "\"," .
            "off_handoff     = \"" . $formVars['off_handoff']   . "\"," .
            "off_disabled    =   " . $formVars['off_disabled'];

          if ($formVars['update'] == 0) {
            $query = "insert into handoff set off_id = NULL, " . $q_string;
            $message = "Handoff added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update handoff set " . $q_string . " where off_id = " . $formVars['id'];
            $message = "Handoff modified.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['id']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Handoff Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('handoff-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"handoff-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Handoff Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a handoff to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Handoff Management</strong> title bar to toggle the <strong>Handoff Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date</th>\n";
      $output .= "  <th class=\"ui-state-default\">Contact</th>\n";
      $output .= "  <th class=\"ui-state-default\">Handoff Detail</th>\n";
      $output .= "</tr>\n";

      if ($formVars['off_group'] == -1) {
        $group = "";
      } else {
        $group = "where off_group = " . $formVars['off_group'] . " ";
      }

      $q_string  = "select off_id,usr_last,usr_first,grp_name,off_timestamp,off_handoff,off_disabled ";
      $q_string .= "from handoff ";
      $q_string .= "left join users on users.usr_id = handoff.off_user ";
      $q_string .= "left join a_groups on a_groups.grp_id = handoff.off_group ";
      $q_string .= $group;
      $q_string .= "order by off_timestamp ";
      $q_handoff = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_handoff) > 0) {
        while ($a_handoff = mysqli_fetch_array($q_handoff)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('handoff.fill.php?id="  . $a_handoff['off_id'] . "');showDiv('handoff-hide');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('handoff.del.php?id=" . $a_handoff['off_id'] . "');\">";
          $linkend   = "</a>";

          if ($a_handoff['off_disabled']) {
            $class = "ui-state-error";
          } else {
            $class = "ui-widget-content";
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
          }
          $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_handoff['off_id']                                    . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_handoff['off_timestamp']                             . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_handoff['usr_last'] . ", " . $a_handoff['usr_first'] . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_handoff['off_handoff']                               . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_handoff);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.handoff.update.disabled = true;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
