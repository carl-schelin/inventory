<?php
# Script: devicetype.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "devicetype.mysql.php";
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
            $q_string = "insert into inv_device set dev_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_device set " . $q_string . " where dev_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['dev_type']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
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
      $q_string .= "from inv_device ";
      $q_string .= "left join inv_users on inv_users.usr_id = inv_device.dev_userid ";
      $q_string .= "order by dev_type";
      $q_inv_device = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_device) > 0) {
        while ($a_inv_device = mysqli_fetch_array($q_inv_device)) {

          if ($a_inv_device['dev_infrastructure']) {
            $infrastructure = 'Yes';
          } else {
            $infrastructure = 'No';
          }

          $linkstart = "<a href=\"#\" onclick=\"show_file('devicetype.fill.php?id="  . $a_inv_device['dev_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_device('devicetype.del.php?id=" . $a_inv_device['dev_id'] . "');\">";
          $linkend = "</a>";

          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_inv_device['dev_type']                                     . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_device['dev_description']                              . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $infrastructure                                               . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_device['dev_notes']                                    . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_device['usr_last'] . ", " . $a_inv_device['usr_first'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_inv_device['dev_update']                                   . $linkend . "</td>";
          $output .= "</tr>";

        }
        $output .= "</table>";
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No Device Types defined</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_inv_device);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
