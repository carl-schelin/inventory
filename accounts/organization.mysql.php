<?php
# Script: organization.mysql.php
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
    $package = "organization.mysql.php";
    $formVars['update']   = clean($_GET['update'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']       = clean($_GET['id'],       10);
        $formVars['org_name'] = clean($_GET['org_name'], 60);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['org_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "org_name = \"" . $formVars['org_name'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into organizations set org_id = null," . $q_string;
            $message = "Organization added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update organizations set " . $q_string . " where org_id = " . $formVars['id'];
            $message = "Organization updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['org_name']);

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
      $output .= "  <th class=\"ui-state-default\">Organization Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('organization-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"organization-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Organization Listing</strong>\n";
      $output .= "  <ul>\n";
      if (check_userlevel(1)) {
        $output .= "    <li><strong>Delete (x)</strong> - Click here to delete this organization from the Inventory.</li>\n";
      }
      $output .= "    <li><strong>Editing</strong> - Click on an organization to toggle the form and edit the organization.</li>\n";
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
      $output .= "  <th class=\"ui-state-default\">Business Unit Name</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select org_id,org_name ";
      $q_string .= "from organizations ";
      $q_string .= "order by org_name ";
      $q_organizations = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_organizations) > 0) {
        while ($a_organizations = mysql_fetch_array($q_organizations)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('organization.fill.php?id=" . $a_organizations['org_id'] . "');jQuery('#dialogOrganization').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('organization.del.php?id=" . $a_organizations['org_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          if (check_userlevel(1)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>\n";
          }
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_organizations['org_id']   . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_organizations['org_name'] . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysql_free_result($q_organizations);

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n";

      print "document.organization.org_name.value = '';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
