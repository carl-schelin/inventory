<?php
# Script: hypervisor.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "hypervisor.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    $orderby = "order by hv_name";

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],                10);
        $formVars['hv_name']        = clean($_GET['hv_name'],          255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['hv_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "hv_name           = \"" . $formVars['hv_name']        . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_hypervisor set hv_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_hypervisor set " . $q_string . " where hv_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['hv_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

##############
### Now build the displayed table information
##############

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "<th class=\"ui-state-default\" width=\"160\">Delete Hypervisor</th>\n";
      }
      $output .= "<th class=\"ui-state-default\">Name</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select hv_id,hv_name ";
      $q_string .= "from inv_hypervisor ";
      $q_string .= $orderby;
      $q_inv_hypervisor = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_hypervisor) > 0) {
        while ($a_inv_hypervisor = mysqli_fetch_array($q_inv_hypervisor)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('hypervisor.fill.php?id="  . $a_inv_hypervisor['hv_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('hypervisor.del.php?id=" . $a_inv_hypervisor['hv_id'] . "');\">";
          $linkend   = "</a>";

          $total = 0;
          $q_string  = "select count(img_id) ";
          $q_string .= "from inv_image ";
          $q_string .= "where img_hypervisor = " . $a_inv_hypervisor['hv_id'] . " ";
          $q_inv_image = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_image) > 0) {
            $a_inv_image = mysqli_fetch_array($q_inv_image);
            $total = $a_inv_image['count(img_id)'];
          }

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total > 0) {
              $output .= "<td class=\"ui-widget-content delete\">In Use</td>\n";
            } else {
              $output .= "<td class=\"ui-widget-content delete\">" . $linkdel . "</td>\n";
            }
          }
          $output .= "<td class=\"ui-widget-content\">" . $linkstart . $a_inv_hypervisor['hv_name'] . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\" colspan=\"2\">No hypervisors to display.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_hypervisor);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
