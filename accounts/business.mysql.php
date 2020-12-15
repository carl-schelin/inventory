<?php
# Script: business.mysql.php
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
            $query = "insert into business_unit set bus_id = null," . $q_string;
            $message = "Business Unit added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update business_unit set " . $q_string . " where bus_id = " . $formVars['id'];
            $message = "Business Unit updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['bus_name']);

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
      $output .= "  <th class=\"ui-state-default\">Business Unit Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('business-unit-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"business-unit-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Business Unit Listing</strong>\n";
      $output .= "  <ul>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "    <li><strong>Delete (x)</strong> - Click here to delete this business unit from the Inventory.</li>\n";
      }
      $output .= "    <li><strong>Editing</strong> - Click on a business unit to toggle the form and edit the business unit.</li>\n";
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
      $output .= "  <th class=\"ui-state-default\">Business Unit ID</th>\n";
      $output .= "  <th class=\"ui-state-default\">Business Unit Name</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select bus_id,bus_unit,bus_name ";
      $q_string .= "from business_unit ";
      $q_string .= "order by bus_name ";
      $q_business_unit = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_business_unit) > 0) {
        while ($a_business_unit = mysqli_fetch_array($q_business_unit)) {


          $linkstart = "<a href=\"#\" onclick=\"show_file('business.fill.php?id="  . $a_business_unit['bus_id'] . "');jQuery('#dialogBusiness').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('business.del.php?id=" . $a_business_unit['bus_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                             . "</td>\n";
          }
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_business_unit['bus_id']   . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_business_unit['bus_unit'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_business_unit['bus_name'] . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_business_unit);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

      print "document.business.bus_unit.value = '';\n";
      print "document.business.bus_name.value = '';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
