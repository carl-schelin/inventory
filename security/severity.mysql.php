<?php
# Script: severity.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "severity.mysql.php";
    $formVars['update']         = clean($_GET['update'],       10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],         10);
        $formVars['sev_name']     = clean($_GET['sev_name'],   60);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['sev_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "sev_name     = \"" . $formVars['sev_name']   . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into severity set sev_id = NULL, " . $q_string;
            $message = "Severity added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update severity set " . $q_string . " where sev_id = " . $formVars['id'];
            $message = "Severity updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['sev_name']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Severity Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('severity-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";
      $output .= "<div id=\"severity-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Severity Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Highlighted</strong> - This device is the <span class=\"ui-state-highlight\">Primary</span> or main piece of equipment. It generally holds the other components.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a device to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Severity Management</strong> title bar to toggle the <strong>Severity Form</strong>.</li>\n";
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
      $output .= "  <th class=\"ui-state-default\">Severity</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select sev_id,sev_name ";
      $q_string .= "from severity ";
      $q_string .= "order by sev_name";
      $q_severity = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_severity) > 0) {
        while ($a_severity = mysqli_fetch_array($q_severity)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('severity.fill.php?id="  . $a_severity['sev_id'] . "');showDiv('severity-hide');\">";
          $linkdel   = "<a href=\"#\" onclick=\"delete_line('severity.del.php?id=" . $a_severity['sev_id'] . "');\">";
          $linkend   = "</a>";

          $output   .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . 'x'                     . $linkend . "</td>";
          }
          $output   .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . $a_severity['sev_id']   . $linkend . "</td>";
          $output   .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_severity['sev_name'] . $linkend . "</td>";
          $output   .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No records found</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_severity);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.severity.update.disabled = true;\n";
      print "document.severity.sev_name.value = '';\n";
      print "document.severity.sev_name.focus();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
