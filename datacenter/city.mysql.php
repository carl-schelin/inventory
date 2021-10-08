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
            $q_string = "insert into cities set ct_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update cities set " . $q_string . " where ct_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['ct_city']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete City/County</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">City/County</th>\n";
      $output .= "  <th class=\"ui-state-default\">State</th>\n";
      $output .= "  <th class=\"ui-state-default\">CLLI Code</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ct_id,ct_city,st_state,ct_clli ";
      $q_string .= "from cities ";
      $q_string .= "left join states on states.st_id = cities.ct_state ";
      $q_string .= "order by ct_city,st_state ";
      $q_cities = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_cities) > 0) {
        while ($a_cities = mysqli_fetch_array($q_cities)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('city.fill.php?id="  . $a_cities['ct_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('city.del.php?id=" . $a_cities['ct_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                         . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_cities['ct_city']     . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                       . $a_cities['st_state']               . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                       . $a_cities['ct_clli']                . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>";
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
