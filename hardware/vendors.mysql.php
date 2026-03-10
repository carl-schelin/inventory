<?php
# Script: vendors.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "vendors.mysql.php";
    $formVars['update']         = clean($_GET['update'],       10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],           10);
        $formVars['ven_name']       = clean($_GET['ven_name'],    100);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['ven_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "ven_name       = \"" . $formVars['ven_name']     . "\" ";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_vendors set ven_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_vendors set " . $q_string . " where ven_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['ven_name']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Vendor</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Vendor</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ven_id,ven_name ";
      $q_string .= "from inv_vendors ";
      $q_string .= "order by ven_name ";
      $q_inv_vendors = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_vendors) > 0) {
        while ($a_inv_vendors = mysqli_fetch_array($q_inv_vendors)) {

          $class = "ui-widget-content";

          $linkstart = "<a href=\"#\" onclick=\"show_file('vendors.fill.php?id="  . $a_inv_vendors['ven_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('vendors.del.php?id=" . $a_inv_vendors['ven_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select mod_id ";
          $q_string .= "from inv_models ";
          $q_string .= "where mod_vendor = " . $a_inv_vendors['ven_id'] . " ";
          $q_inv_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $total = mysqli_num_rows($q_inv_models);

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_inv_vendors['ven_name'] . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $total                           . "</td>";
          $output .= "</tr>";
        }

        $output .= "</table>";
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">No vendors found</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_inv_vendors);

      print "document.getElementById('mysql_table').innerHTML = '"    . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
