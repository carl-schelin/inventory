<?php
# Script: types.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "types.mysql.php";
    $formVars['update']   = clean($_GET['update'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']       = clean($_GET['id'],       10);
        $formVars['mt_name']  = clean($_GET['mt_name'],  30);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['mt_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "mt_name = \"" . $formVars['mt_name'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into mon_type set mt_id = null," . $q_string;
            $message = "Monitoring Types added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update mon_type set " . $q_string . " where mt_id = " . $formVars['id'];
            $message = "Monitoring Types updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['mt_name']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Monitoring Types Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('types-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"types-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Monitoring Types Listing</strong>\n";
      $output .= "  <ul>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "    <li><strong>Delete (x)</strong> - Click here to delete this monitoring type from the Inventory.</li>\n";
      }
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Monitoring Type</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select mt_id,mt_name ";
      $q_string .= "from mon_type ";
      $q_string .= "order by mt_name ";
      $q_mon_type = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_mon_type) > 0) {
        while ($a_mon_type = mysqli_fetch_array($q_mon_type)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('types.fill.php?id=" . $a_mon_type['mt_id'] . "');jQuery('#dialogTypes').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('types.del.php?id=" . $a_mon_type['mt_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\" width=\"60\">" . $linkdel   . "</td>\n";
          }
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_mon_type['mt_id']   . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_mon_type['mt_name'] . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_mon_type);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

      print "document.types.mt_name.value = '';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
