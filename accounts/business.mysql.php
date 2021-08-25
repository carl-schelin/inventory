<?php
# Script: business.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "business.mysql.php";
    $formVars['update']   = clean($_GET['update'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']       = clean($_GET['id'],       10);
        $formVars['bus_unit'] = clean($_GET['bus_unit'], 10);
        $formVars['bus_name'] = clean($_GET['bus_name'], 60);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['bus_unit'] == '') {
          $formVars['bus_unit'] = 0;
        }

        if (strlen($formVars['bus_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "bus_unit =   " . $formVars['bus_unit'] . "," .
            "bus_name = \"" . $formVars['bus_name'] . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into business_unit set bus_id = null," . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update business_unit set " . $q_string . " where bus_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['bus_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Business Unit</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Business Unit Name</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select bus_id,bus_unit,bus_name ";
      $q_string .= "from business_unit ";
      $q_string .= "order by bus_name ";
      $q_business_unit = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_business_unit) > 0) {
        while ($a_business_unit = mysqli_fetch_array($q_business_unit)) {


          $linkstart = "<a href=\"#\" onclick=\"show_file('business.fill.php?id="  . $a_business_unit['bus_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('business.del.php?id=" . $a_business_unit['bus_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                             . "</td>\n";
          }
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_business_unit['bus_name'] . " (" . $a_business_unit['bus_unit'] . ")" . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_business_unit);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
