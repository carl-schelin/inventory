<?php
# Script: country.mysql.php
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
    $package = "country.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],              10);
        $formVars['cn_acronym']       = clean($_GET['cn_acronym'],      10);
        $formVars['cn_country']       = clean($_GET['cn_country'],     255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
    
        if (strlen($formVars['cn_country']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "cn_acronym = \"" . $formVars['cn_acronym'] . "\"," . 
            "cn_country = \"" . $formVars['cn_country'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into country set cn_id = NULL, " . $q_string;
            $message = "Country added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update country set " . $q_string . " where cn_id = " . $formVars['id'];
            $message = "Country modified.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['cn_country']);

          mysql_query($query) or die($query . ": " . mysql_error());

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Country Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('country-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"country-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Country Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a country to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel(1)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Acronym</th>\n";
      $output .= "  <th class=\"ui-state-default\">Country</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select cn_id,cn_acronym,cn_country ";
      $q_string .= "from country ";
      $q_string .= "order by cn_country ";
      $q_country = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_country) > 0) {
        while ($a_country = mysql_fetch_array($q_country)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('country.fill.php?id="  . $a_country['cn_id'] . "');jQuery('#dialogCountry').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('country.del.php?id=" . $a_country['cn_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel(1)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel    . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content delete\">"   . $linkstart . $a_country['cn_id']          . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_country['cn_acronym']     . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_country['cn_country']     . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysql_free_result($q_country);

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
