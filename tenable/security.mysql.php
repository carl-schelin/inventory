<?php
# Script: security.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "security.mysql.php";
    $formVars['update']         = clean($_GET['update'],       10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],             10);
        $formVars['sec_name']     = clean($_GET['sec_name'],      255);
        $formVars['sec_family']   = clean($_GET['sec_family'],     10);
        $formVars['sec_severity'] = clean($_GET['sec_severity'],   10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['sec_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "sec_name        = \"" . $formVars['sec_name']       . "\"," . 
            "sec_family      =   " . $formVars['sec_family']     . "," . 
            "sec_severity    =   " . $formVars['sec_severity'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_security set sec_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_security set " . $q_string . " where sec_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['sec_name']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Security Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('security-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";
      $output .= "<div id=\"security-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Security Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Highlighted</strong> - This device is the <span class=\"ui-state-highlight\">Primary</span> or main piece of equipment. It generally holds the other components.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a device to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Security Management</strong> title bar to toggle the <strong>Security Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Security</th>\n";
      $output .= "  <th class=\"ui-state-default\">Family</th>\n";
      $output .= "  <th class=\"ui-state-default\">Severity</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select sec_id,sec_name,fam_name,sev_name ";
      $q_string .= "from inv_security ";
      $q_string .= "left join family on family.fam_id = inv_security.sec_family ";
      $q_string .= "left join severity on severity.sev_id = inv_security.sec_severity ";
      $q_string .= "order by sec_name";
      $q_inv_security = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_security) > 0) {
        while ($a_inv_security = mysqli_fetch_array($q_inv_security)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('security.fill.php?id="  . $a_inv_security['sec_id'] . "');showDiv('security-hide');\">";
          $linkdel   = "<a href=\"#\" onclick=\"delete_line('security.del.php?id=" . $a_inv_security['sec_id'] . "');\">";
          $linkend   = "</a>";

          $output   .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . 'x'                     . $linkend . "</td>";
          }
          $output   .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . $a_inv_security['sec_id']   . $linkend . "</td>";
          $output   .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_security['sec_name'] . $linkend . "</td>";
          $output   .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_security['fam_name'] . $linkend . "</td>";
          $output   .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_security['sev_name'] . $linkend . "</td>";
          $output   .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_security);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
