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
        $formVars['id']             = clean($_GET['id'],                 10);
        $formVars['dep_name']       = clean($_GET['dep_name'],           60);
        $formVars['dep_business']   = clean($_GET['dep_business'],       10);
        $formVars['dep_manager']    = clean($_GET['dep_manager'],        10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['dep_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "dep_name       = \"" . $formVars['dep_name']     . "\"," .
            "dep_business   =   " . $formVars['dep_business'] . "," . 
            "dep_manager    =   " . $formVars['dep_manager'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_department set dep_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_department set " . $q_string . " where dep_id = " . $formVars['id'];
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
      $output .= "  <th class=\"ui-state-default\">Manager</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select dep_id,dep_name,org_name,bus_name,usr_last,usr_first ";
      $q_string .= "from inv_department ";
      $q_string .= "left join inv_business      on inv_business.bus_id      = inv_department.dep_business ";
      $q_string .= "left join inv_organizations on inv_organizations.org_id = inv_business.bus_organization ";
      $q_string .= "left join inv_users         on inv_users.usr_id         = inv_department.dep_manager ";
      $q_string .= "order by dep_name,bus_name,org_name ";
      $q_inv_department = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_department) > 0) {
        while ($a_inv_department = mysqli_fetch_array($q_inv_department)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('department.fill.php?id="  . $a_inv_department['dep_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('department.del.php?id=" . $a_inv_department['dep_id'] . "');\">";
          $linkend   = "</a>";

          $total = 0;
          $q_string  = "select grp_id ";
          $q_string .= "from inv_groups ";
          $q_string .= "where grp_department = " . $a_inv_department['dep_id'] . " ";
          $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_groups) > 0) {
            while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
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
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_department['dep_name'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_department['bus_name'] . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_department['org_name'] . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_department['usr_last'] . ", " . $a_inv_department['usr_first'] . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $total                    . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_inv_department);

      $output .= "</table>";

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
