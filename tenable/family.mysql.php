<?php
# Script: family.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "family.mysql.php";
    $formVars['update']         = clean($_GET['update'],       10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],         10);
        $formVars['fam_name']     = clean($_GET['fam_name'],   60);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['fam_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "fam_name     = \"" . $formVars['fam_name']   . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_family set fam_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_family set " . $q_string . " where fam_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['fam_name']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Family Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('family-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";
      $output .= "<div id=\"family-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Family Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Highlighted</strong> - This device is the <span class=\"ui-state-highlight\">Primary</span> or main piece of equipment. It generally holds the other components.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a device to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Family Management</strong> title bar to toggle the <strong>Family Form</strong>.</li>\n";
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
      $output .= "  <th class=\"ui-state-default\">Family</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select fam_id,fam_name ";
      $q_string .= "from inv_family ";
      $q_string .= "order by fam_name";
      $q_inv_family = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_family) > 0) {
        while ($a_inv_family = mysqli_fetch_array($q_inv_family)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('family.fill.php?id="  . $a_inv_family['fam_id'] . "');showDiv('family-hide');\">";
          $linkdel   = "<a href=\"#\" onclick=\"delete_line('family.del.php?id=" . $a_inv_family['fam_id'] . "');\">";
          $linkend   = "</a>";

          $output   .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . 'x'                     . $linkend . "</td>";
          }
          $output   .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . $a_inv_family['fam_id']   . $linkend . "</td>";
          $output   .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_family['fam_name'] . $linkend . "</td>";
          $output   .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No records found</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_family);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.family.update.disabled = true;\n";
      print "document.family.fam_name.value = '';\n";
      print "document.family.fam_name.focus();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
