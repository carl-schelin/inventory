<?php
# Script: roles.mysql.php
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
    $package = "roles.mysql.php";
    $formVars['update']   = clean($_GET['update'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']        = clean($_GET['id'],        10);
        $formVars['role_name'] = clean($_GET['role_name'], 60);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['role_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "role_name = \"" . $formVars['role_name'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into roles set role_id = null," . $q_string;
            $message = "Role added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update roles set " . $q_string . " where role_id = " . $formVars['id'];
            $message = "Role updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['role_name']);

          mysql_query($query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysql_error()));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Role Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('role-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"role-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Role Listing</strong>\n";
      $output .= "  <ul>\n";
      if (check_userlevel($AL_Admin)) {
        $output .= "    <li><strong>Delete (x)</strong> - Click here to delete this role from the Inventory.</li>\n";
      }
      $output .= "    <li><strong>Editing</strong> - Click on a role to toggle the form and edit the role.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Role Name</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select role_id,role_name ";
      $q_string .= "from roles ";
      $q_string .= "order by role_name ";
      $q_roles = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_roles) > 0) {
        while ($a_roles = mysql_fetch_array($q_roles)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('roles.fill.php?id="  . $a_roles['role_id'] . "');jQuery('#dialogRole').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('roles.del.php?id=" . $a_roles['role_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          if (check_userlevel($AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>\n";
          }
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_roles['role_id']   . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_roles['role_name'] . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysql_free_result($q_roles);

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n";

      print "document.role.role_name.value = '';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
