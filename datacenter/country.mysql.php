<?php
# Script: country.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "country.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],              10);
        $formVars['cn_acronym']       = clean($_GET['cn_acronym'],      10);
        $formVars['cn_country']       = clean($_GET['cn_country'],     255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
    
        if (strlen($formVars['cn_country']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "cn_acronym = \"" . $formVars['cn_acronym'] . "\"," . 
            "cn_country = \"" . $formVars['cn_country'] . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into country set cn_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update country set " . $q_string . " where cn_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['cn_country']);

          mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Country</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Country</th>\n";
      $output .= "  <th class=\"ui-state-default\">Acronym</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select cn_id,cn_acronym,cn_country ";
      $q_string .= "from country ";
      $q_string .= "order by cn_country ";
      $q_country = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_country) > 0) {
        while ($a_country = mysqli_fetch_array($q_country)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('country.fill.php?id="  . $a_country['cn_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('country.del.php?id=" . $a_country['cn_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel    . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_country['cn_country']     . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                       . $a_country['cn_acronym']                . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_country);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
