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
        $formVars['id']                = clean($_GET['id'],                10);
        $formVars['bus_name']          = clean($_GET['bus_name'],          60);
        $formVars['bus_organization']  = clean($_GET['bus_organization'],  10);
        $formVars['bus_manager']       = clean($_GET['bus_manager'],       10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['bus_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "bus_name             = \"" . $formVars['bus_name']         . "\"," .
            "bus_organization     =   " . $formVars['bus_organization'] . "," .
            "bus_manager          =   " . $formVars['bus_manager'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_business set bus_id = null," . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_business set " . $q_string . " where bus_id = " . $formVars['id'];
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
      $output .= "  <th class=\"ui-state-default\">Manager</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select bus_id,bus_name,org_name,usr_last,usr_first ";
      $q_string .= "from inv_business ";
      $q_string .= "left join inv_organizations on inv_organizations.org_id = inv_business.bus_organization ";
      $q_string .= "left join inv_users on inv_users.usr_id = inv_business.bus_manager ";
      $q_string .= "order by bus_name ";
      $q_inv_business = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_business) > 0) {
        while ($a_inv_business = mysqli_fetch_array($q_inv_business)) {

          $total = 0;
          $q_string  = "select dep_id ";
          $q_string .= "from inv_department ";
          $q_string .= "where dep_business = " . $a_inv_business['bus_id'] . " ";
          $q_inv_department = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_department) > 0) {
            while ($a_inv_department = mysqli_fetch_array($q_inv_department)) {
              $total++;
            }
          }

          if (check_userlevel($db, $AL_Admin)) {
            $linkstart = "<a href=\"#\" onclick=\"show_file('business.fill.php?id="  . $a_inv_business['bus_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
            if ($total > 0) {
              $linkdel = 'Members &gt; 0';
            } else {
              $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('business.del.php?id=" . $a_inv_business['bus_id'] . "');\">";
            }
            $linkend   = "</a>";
          } else {
            $linkstart = '';
            $linkdel = 'Viewing';
            $linkend = '';
          }

          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_business['bus_name'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_business['org_name'] . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_business['usr_last'] . ", " . $a_inv_business['usr_first'] . "</td>\n";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $total                       . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_inv_business);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
