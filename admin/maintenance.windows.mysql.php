<?php
# Script: maintenance.windows.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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
        $formVars['man_text']         = clean($_GET['man_text'],        100);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['man_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "man_text        = \"" . $formVars['man_text'] . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_maintenance set man_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_maintenance set " . $q_string . " where man_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['man_text']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Maintenance Window</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Maintenance Window</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select man_id,man_text ";
      $q_string .= "from inv_maintenance ";
      $q_string .= "order by man_text ";
      $q_inv_maintenance = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_maintenance) > 0) {
        while ($a_inv_maintenance = mysqli_fetch_array($q_inv_maintenance)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('maintenance.windows.fill.php?id=" . $a_inv_maintenance['man_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('maintenance.windows.del.php?id="  . $a_inv_maintenance['man_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_inv_maintenance['man_text']        . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_maintenance);

      print "document.getElementById('maintenance_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
