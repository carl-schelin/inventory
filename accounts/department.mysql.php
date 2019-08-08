<?php
# Script: department.mysql.php
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
    $package = "department.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']       = clean($_GET['id'],             10);
        $formVars['dep_unit'] = clean($_GET['dep_unit'],       10);
        $formVars['dep_dept'] = clean($_GET['dep_dept'],       10);
        $formVars['dep_name'] = clean($_GET['dep_name'],       60);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['dep_unit'] == '') {
          $formVars['dep_unit'] = 0;
        }
        if ($formVars['dep_dept'] == '') {
          $formVars['dep_dept'] = 0;
        }

        if (strlen($formVars['dep_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "dep_unit =   " . $formVars['dep_unit'] . "," . 
            "dep_dept =   " . $formVars['dep_dept'] . "," . 
            "dep_name = \"" . $formVars['dep_name'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into department set dep_id = NULL, " . $q_string;
            $message = "Department added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update department set " . $q_string . " where dep_id = " . $formVars['id'];
            $message = "Department updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['dep_name']);

          mysql_query($query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysql_error()));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "  <th class=\"ui-state-default\">Department Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('department-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"department-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Department Listing</strong>\n";
      $output .= "  <ul>\n";
      if (check_userlevel(1)) {
        $output .= "    <li><strong>Delete (x)</strong> - Click here to delete this department from the Inventory.</li>\n";
      }
      $output .= "    <li><strong>Editing</strong> - Click on a department to toggle the form and edit the department.</li>\n";
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
      $output .= "  <th class=\"ui-state-default\">Business Unit ID</th>\n";
      $output .= "  <th class=\"ui-state-default\">Department ID</th>\n";
      $output .= "  <th class=\"ui-state-default\">Department Name</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select dep_id,dep_unit,dep_dept,dep_name,bus_name ";
      $q_string .= "from department ";
      $q_string .= "left join business_unit on business_unit.bus_unit = department.dep_unit ";
      $q_department = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_department) > 0) {
        while ($a_department = mysql_fetch_array($q_department)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('department.fill.php?id="  . $a_department['dep_id'] . "');jQuery('#dialogDepartment').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('department.del.php?id=" . $a_department['dep_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel(1)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_department['dep_id']                                            . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_department['dep_unit'] . " (" . $a_department['bus_name'] . ")" . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_department['dep_dept']                                          . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_department['dep_name']                                          . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>";
        $output .= "</tr>";
      }

      mysql_free_result($q_department);

      $output .= "</table>";

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.department.dep_unit.value = 0;\n";
      print "document.department.dep_dept.value = 0;\n";
      print "document.department.dep_name.value = '';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
