<?php
# Script: city.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "city.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],             10);
        $formVars['ct_city']         = clean($_GET['ct_city'],       255);
        $formVars['ct_state']        = clean($_GET['ct_state'],       10);
        $formVars['ct_clli']         = clean($_GET['ct_clli'],        10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
    
        if (strlen($formVars['ct_city']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "ct_city        = \"" . $formVars['ct_city']   . "\"," .
            "ct_state       =   " . $formVars['ct_state']  . "," . 
            "ct_clli        = \"" . $formVars['ct_clli']   . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into cities set ct_id = NULL, " . $q_string;
            $message = "City added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update cities set " . $q_string . " where ct_id = " . $formVars['id'];
            $message = "City modified.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['ct_city']);

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
      $output .= "  <th class=\"ui-state-default\">City/County Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('city-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"city-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>City/County Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a city to edit it.</li>\n";
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
      $output .= "  <th class=\"ui-state-default\">City/County</th>\n";
      $output .= "  <th class=\"ui-state-default\">State</th>\n";
      $output .= "  <th class=\"ui-state-default\">CLLI Code</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ct_id,ct_city,st_state,ct_clli ";
      $q_string .= "from cities ";
      $q_string .= "left join states on states.st_id = cities.ct_state ";
      $q_string .= "order by ct_city ";
      $q_cities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_cities) > 0) {
        while ($a_cities = mysqli_fetch_array($q_cities)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('city.fill.php?id="  . $a_cities['ct_id'] . "');jQuery('#dialogCity').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('city.del.php?id=" . $a_cities['ct_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                         . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content delete\">"   . $linkstart . $a_cities['ct_id']       . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_cities['ct_city']     . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_cities['st_state']    . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_cities['ct_clli']     . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_cities);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
