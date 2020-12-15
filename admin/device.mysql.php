<?php
# Script: device.mysql.php
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
    $package = "device.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']                 = clean($_GET['id'],                 10);
        $formVars['dev_type']           = clean($_GET['dev_type'],            5);
        $formVars['dev_description']    = clean($_GET['dev_description'],   100);
        $formVars['dev_infrastructure'] = clean($_GET['dev_infrastructure'], 10);
        $formVars['dev_notes']          = clean($_GET['dev_notes'],         100);
        $formVars['dev_update']         = date('Y-m-d');
        $formVars['dev_userid']         = clean($_SESSION['uid'],            10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['dev_infrastructure'] == 'true') {
          $formVars['dev_infrastructure'] = 1;
        } else {
          $formVars['dev_infrastructure'] = 0;
        }

        if (strlen($formVars['dev_type']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "dev_type            = \"" . $formVars['dev_type']           . "\"," .
            "dev_description     = \"" . $formVars['dev_description']    . "\"," .
            "dev_infrastructure  =   " . $formVars['dev_infrastructure'] . "," .
            "dev_notes           = \"" . $formVars['dev_notes']          . "\"," .
            "dev_update          = \"" . $formVars['dev_update']         . "\"," . 
            "dev_userid          =   " . $formVars['dev_userid'];

          if ($formVars['update'] == 0) {
            $query = "insert into device set dev_id = NULL, " . $q_string;
            $message = "Device added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update device set " . $q_string . " where dev_id = " . $formVars['id'];
            $message = "Device updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['dev_type']);

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
      $output .= "  <th class=\"ui-state-default\">Device Type Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('device-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"device-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Device Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Device Type to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">Type</th>";
      $output .= "  <th class=\"ui-state-default\">Description</th>";
      $output .= "  <th class=\"ui-state-default\">Infrastructure</th>";
      $output .= "  <th class=\"ui-state-default\">Notes</th>";
      $output .= "  <th class=\"ui-state-default\">Added By</th>";
      $output .= "  <th class=\"ui-state-default\">Date Added</th>";
      $output .= "</tr>";

      $q_string  = "select dev_id,dev_type,dev_description,dev_infrastructure,dev_notes,dev_update,usr_last,usr_first ";
      $q_string .= "from device ";
      $q_string .= "left join users on users.usr_id = device.dev_userid ";
      $q_string .= "order by dev_type";
      $q_device = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_device) > 0) {
        while ($a_device = mysqli_fetch_array($q_device)) {

          if ($a_device['dev_infrastructure']) {
            $infrastructure = 'Yes';
          } else {
            $infrastructure = 'No';
          }

  if ($_SESSION['uid'] == 2) {
          $linkstart = "<a href=\"#\" onclick=\"show_file('device.fill.php?id="  . $a_device['dev_id'] . "');jQuery('#dialogDevice').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_device('device.del.php?id=" . $a_device['dev_id'] . "');\">";
          $linkend = "</a>";
  } else {
          $linkstart = '';
          $linkdel   = '';
          $linkend   = '';
  }

          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_device['dev_type']                                 . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_device['dev_description']                          . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $infrastructure                                       . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_device['dev_notes']                                . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_device['usr_last'] . ", " . $a_device['usr_first'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_device['dev_update']                               . $linkend . "</td>";
          $output .= "</tr>";

        }
        $output .= "</table>";
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No Device Types defined</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_device);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

  if ($_SESSION['uid'] == 2) {

      print "document.device.dev_type.value = '';\n";
      print "document.device.dev_description.value = '';\n";
      print "document.device.dev_infrastructure.checked = false;\n";
      print "document.device.dev_notes.value = '';\n";
  }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
