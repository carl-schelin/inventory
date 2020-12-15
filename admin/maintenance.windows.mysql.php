<?php
# Script: maintenance.windows.mysql.php
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
    $package = "maintenance.windows.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],               10);
        $formVars['win_text']         = clean($_GET['win_text'],        100);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['win_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "win_text        = \"" . $formVars['win_text'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into window set win_id = NULL, " . $q_string;
            $message = "Maintenance Window added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update window set " . $q_string . " where win_id = " . $formVars['id'];
            $message = "Maintenance Window updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['win_text']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Maintenance Window Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('window-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"window-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Maintenance Window Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Maintenance Window to edit it.</li>\n";
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
      $output .= "  <th class=\"ui-state-default\">Maintenance Window</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select win_id,win_text ";
      $q_string .= "from window ";
      $q_string .= "order by win_text ";
      $q_window = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_window) > 0) {
        while ($a_window = mysqli_fetch_array($q_window)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('maintenance.windows.fill.php?id=" . $a_window['win_id'] . "');jQuery('#dialogWindow').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('maintenance.windows.del.php?id="  . $a_window['win_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content delete\">"   . $linkstart . $a_window['win_id']          . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_window['win_text']        . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_window);

      print "document.getElementById('window_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.window.win_text.value = '';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
