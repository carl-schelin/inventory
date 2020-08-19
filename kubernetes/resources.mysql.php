<?php
# Script: resources.mysql.php
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
    $package = "resources.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],               10);
        $formVars['res_name']         = clean($_GET['res_name'],        100);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['res_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "res_name        = \"" . $formVars['res_name'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into resources set res_id = NULL, " . $q_string;
            $message = "Resources added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update resources set " . $q_string . " where res_id = " . $formVars['id'];
            $message = "Resources updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['res_name']);

          mysql_query($query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysql_error()));

#          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Resources Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('resources-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"resources-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Resources Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on Resources to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"60\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\" width=\"30\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Resources</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select res_id,res_name ";
      $q_string .= "from resources ";
      $q_string .= "order by res_name ";
      $q_resources = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_resources) > 0) {
        while ($a_resources = mysql_fetch_array($q_resources)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('resources.fill.php?id=" . $a_resources['res_id'] . "');jQuery('#dialogResources').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('resources.del.php?id="  . $a_resources['res_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel($AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content delete\">"   . $linkstart . $a_resources['res_id']          . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_resources['res_name']        . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysql_free_result($q_resources);

      print "document.getElementById('resources_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.resources.res_name.value = '';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
