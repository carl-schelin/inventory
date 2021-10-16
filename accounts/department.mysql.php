<?php
# Script: department.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
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
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "dep_name = \"" . $formVars['dep_name'] . "\"," .
            "dep_dept =   " . $formVars['dep_dept'] . "," . 
            "dep_unit =   " . $formVars['dep_unit'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into department set dep_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update department set " . $q_string . " where dep_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['dep_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Department</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Department Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Business</th>\n";
      $output .= "  <th class=\"ui-state-default\">Organization</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select dep_id,dep_unit,dep_dept,dep_name,bus_name,org_name ";
      $q_string .= "from department ";
      $q_string .= "left join business on business.bus_unit = department.dep_unit ";
      $q_string .= "left join organizations on organizations.org_id = business.bus_org ";
      $q_string .= "order by dep_name,bus_name,org_name ";
      $q_department = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_department) > 0) {
        while ($a_department = mysqli_fetch_array($q_department)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('department.fill.php?id="  . $a_department['dep_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('department.del.php?id=" . $a_department['dep_id'] . "');\">";
          $linkend   = "</a>";

          $total = 0;
          $q_string  = "select grp_id ";
          $q_string .= "from a_groups ";
          $q_string .= "where grp_organization = " . $a_department['dep_id'] . " ";
          $q_a_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_a_groups) > 0) {
            while ($a_a_groups = mysqli_fetch_array($q_a_groups)) {
              $total++;
            }
          }

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_department['dep_name'] . " (" . $a_department['dep_dept'] . ")" . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_department['bus_name'] . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_department['org_name'] . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $total                    . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_department);

      $output .= "</table>";

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
