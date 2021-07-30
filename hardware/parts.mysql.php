<?php
# Script: parts.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "parts.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],            10);
        $formVars['part_name']    = clean($_GET['part_name'],    100);
        $formVars['part_type']    = clean($_GET['part_type'],     10);
        $formVars['part_acronym'] = clean($_GET['part_acronym'],  10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['part_type'] == 'true') {
          $formVars['part_type'] = 1;
        } else {
          $formVars['part_type'] = 0;
        }

        if (strlen($formVars['part_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "part_name    = \"" . $formVars['part_name']    . "\"," .
            "part_type    =   " . $formVars['part_type']    . "," . 
            "part_acronym = \"" . $formVars['part_acronym'] . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into parts set part_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update parts set " . $q_string . " where part_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['part_name']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Part</th>";
      }
      $output .= "  <th class=\"ui-state-default\">Part Name</th>";
      $output .= "  <th class=\"ui-state-default\">Device Acronym</th>";
      $output .= "  <th class=\"ui-state-default\">Container Type</th>";
      $output .= "  <th class=\"ui-state-default\">Members</th>";
      $output .= "</tr>";

      $q_string  = "select part_id,part_name,part_type,part_acronym ";
      $q_string .= "from parts ";
      $q_string .= "order by part_name";
      $q_parts = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_parts) > 0) {
        while ($a_parts = mysqli_fetch_array($q_parts)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('parts.fill.php?id="     . $a_parts['part_id']   . "');jQuery('#dialogUpdate').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('parts.del.php?id=" . $a_parts['part_id'] . "');\">";
          $linkend   = "</a>";

          if ($a_parts['part_type'] == 1) {
            $parttype = "Primary";
          } else {
            $parttype = "";
          }

          $q_string  = "select mod_id ";
          $q_string .= "from models ";
          $q_string .= "where mod_type = " . $a_parts['part_id'] . " ";
          $q_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $total = mysqli_num_rows($q_models);

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_parts['part_name']    . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_parts['part_acronym']            . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                     . $parttype                           . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $total                              . "</td>";
          $output .= "</tr>";
        }
      } else {
          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No parts found</td>";
          $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_parts);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
