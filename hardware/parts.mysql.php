<?php
# Script: parts.mysql.php
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
    $package = "parts.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel(2)) {
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
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "part_name    = \"" . $formVars['part_name']    . "\"," .
            "part_type    =   " . $formVars['part_type']    . "," . 
            "part_acronym = \"" . $formVars['part_acronym'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into parts set part_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update parts set " . $q_string . " where part_id = " . $formVars['id'];
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['part_name']);

          mysql_query($query) or die($query . ": " . mysql_error());

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>";
      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Part Listing</th>";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('part-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"part-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Part Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a part to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Part Management</strong> title bar to toggle the <strong>Part Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      if (check_userlevel(1)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>";
      $output .= "  <th class=\"ui-state-default\">Part Name</th>";
      $output .= "  <th class=\"ui-state-default\">Device Acronym</th>";
      $output .= "  <th class=\"ui-state-default\">Container Type</th>";
      $output .= "</tr>";

      $q_string  = "select part_id,part_name,part_type,part_acronym ";
      $q_string .= "from parts ";
      $q_string .= "order by part_name";
      $q_parts = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_parts = mysql_fetch_array($q_parts)) {

        $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('parts.fill.php?id="     . $a_parts['part_id']   . "');jQuery('#dialogPart').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('parts.del.php?id=" . $a_parts['part_id'] . "');\">";
        $linkend   = "</a>";

        if ($a_parts['part_type'] == 1) {
          $parttype = "Primary";
        } else {
          $parttype = "";
        }

        $output .= "<tr>";
        if (check_userlevel(1)){
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                       . "</td>";
        }
        $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_parts['part_id']      . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_parts['part_name']    . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_parts['part_acronym'] . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $parttype                . $linkend . "</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysql_free_result($q_parts);

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.parts.part_name.value = '';\n";
      print "document.parts.part_acronym.value = '';\n";
      print "document.parts.part_type.checked = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
