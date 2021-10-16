<?php
# Script: business.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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
        $formVars['bus_org']  = clean($_GET['bus_org'],  10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['bus_unit'] == '') {
          $formVars['bus_unit'] = 0;
        }

        if (strlen($formVars['bus_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "bus_name = \"" . $formVars['bus_name'] . "\"," .
            "bus_unit =   " . $formVars['bus_unit'] . "," .
            "bus_org  =   " . $formVars['bus_org'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into business set bus_id = null," . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update business set " . $q_string . " where bus_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['bus_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Business</th>\n";
      } else {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Business</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Business Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Organization</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select bus_id,bus_unit,bus_name,org_name ";
      $q_string .= "from business ";
      $q_string .= "left join organizations on organizations.org_id = business.bus_org ";
      $q_string .= "order by bus_name ";
      $q_business = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_business) > 0) {
        while ($a_business = mysqli_fetch_array($q_business)) {

          $total = 0;
          $q_string  = "select dep_id ";
          $q_string .= "from department ";
          $q_string .= "where dep_unit = " . $a_business['bus_id'] . " ";
          $q_department = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_department) > 0) {
            while ($a_department = mysqli_fetch_array($q_department)) {
              $total++;
            }
          }

          if (check_userlevel($db, $AL_Admin)) {
            $linkstart = "<a href=\"#\" onclick=\"show_file('business.fill.php?id="  . $a_business['bus_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
            if ($total > 0) {
              $linkdel = 'Members &gt; 0';
            } else {
              $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('business.del.php?id=" . $a_business['bus_id'] . "');\">";
            }
            $linkend   = "</a>";
          } else {
            $linkstart = '';
            $linkdel = 'Viewing';
            $linkend = '';
          }

          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_business['bus_name'] . " (" . $a_business['bus_unit'] . ")" . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_business['org_name'] . "</td>\n";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $total                       . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_business);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
