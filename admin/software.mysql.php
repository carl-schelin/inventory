<?php
# Script: software.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "software.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],              10);
        $formVars['sw_software']    = clean($_GET['sw_software'],    255);
        $formVars['sw_eol']         = clean($_GET['sw_eol'],          20);
        $formVars['sw_eos']         = clean($_GET['sw_eos'],          20);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['sw_software']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "sw_software    = \"" . $formVars['sw_software']   . "\"," .
            "sw_eol         = \"" . $formVars['sw_eol']        . "\"," .
            "sw_eos         = \"" . $formVars['sw_eos']        . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into sw_support set sw_id = NULL, " . $q_string;
            $message = "Software added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update sw_support set " . $q_string . " where sw_id = " . $formVars['id'];
            $message = "Software updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['sw_software']);

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
      $output .= "  <th class=\"ui-state-default\">Support Contract Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('support-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"support-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Support Contract Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a contract to edit it.</li>\n";
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
      $output .= "  <th class=\"ui-state-default\">Software</th>\n";
      $output .= "  <th class=\"ui-state-default\">End of Support</th>\n";
      $output .= "  <th class=\"ui-state-default\">End of Life</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select sw_id,sw_software,sw_eol,sw_eos ";
      $q_string .= "from sw_support";
      $q_sw_support = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_sw_support) > 0) {
        while ($a_sw_support = mysqli_fetch_array($q_sw_support)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('software.fill.php?id="  . $a_sw_support['sw_id'] . "');jQuery('#dialogSoftware').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('software.del.php?id=" . $a_sw_support['sw_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_sw_support['sw_id']        . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_sw_support['sw_software']  . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_sw_support['sw_eos']       . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_sw_support['sw_eol']       . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_sw_support);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.software.sw_software.value = '';\n";
      print "document.software.sw_eol.value = '';\n";
      print "document.software.sw_eos.value = '';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
