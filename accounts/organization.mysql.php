<?php
# Script: organization.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "organization.mysql.php";
    $formVars['update']   = clean($_GET['update'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']            = clean($_GET['id'],            10);
        $formVars['org_name']      = clean($_GET['org_name'],      60);
        $formVars['org_manager']   = clean($_GET['org_manager'],   10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['org_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "org_name      = \"" . $formVars['org_name'] . "\"," .
            "org_manager   =   " . $formVars['org_name'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into organizations set org_id = null," . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update organizations set " . $q_string . " where org_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['org_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Organization</th>\n";
      } else {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Organization</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Organization</th>\n";
      $output .= "  <th class=\"ui-state-default\">Manager</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select org_id,org_name,usr_last,usr_first ";
      $q_string .= "from organizations ";
      $q_string .= "left join users on users.usr_id = organizations.org_manager ";
      $q_string .= "order by org_name ";
      $q_organizations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_organizations) > 0) {
        while ($a_organizations = mysqli_fetch_array($q_organizations)) {

          $total = 0;
          $q_string  = "select bus_id ";
          $q_string .= "from business ";
          $q_string .= "where bus_organization = " . $a_organizations['org_id'] . " ";
          $q_business = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_business) > 0) {
            while ($a_business = mysqli_fetch_array($q_business)) {
              $total++;
            }
          }

          if (check_userlevel($db, $AL_Admin)) {
            $linkstart = "<a href=\"#\" onclick=\"show_file('organization.fill.php?id=" . $a_organizations['org_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
            if ($total > 0) {
              $linkdel = 'Members &gt; 0';
            } else {
              $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('organization.del.php?id=" . $a_organizations['org_id'] . "');\">";
            }
            $linkend   = "</a>";
          } else {
            $linkstart = '';
            $linkdel = 'Viewing';
            $linkend = '';
          }

          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_organizations['org_name'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_organizations['usr_last'] . ", " . $a_organizations['usr_first'] . "</td>\n";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $total . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_organizations);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
