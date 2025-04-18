<?php
# Script: cpu.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "cpu.mysql.php";
    $formVars['update']         = clean($_GET['update'],       10);
    $formVars['mod_type']       = 8;

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],           10);
        $formVars['mod_vendor']     = clean($_GET['mod_vendor'],   10);
        $formVars['mod_name']       = clean($_GET['mod_name'],    100);
        $formVars['mod_size']       = clean($_GET['mod_size'],    100);
        $formVars['mod_speed']      = clean($_GET['mod_speed'],    20);
        $formVars['mod_eopur']      = clean($_GET['mod_eopur'],    30);
        $formVars['mod_eoship']     = clean($_GET['mod_eoship'],   30);
        $formVars['mod_eol']        = clean($_GET['mod_eol'],      30);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        $formVars['mod_plugs'] = 0;
        $formVars['mod_plugtype'] = 0;
        $formVars['mod_volts'] = 0;
        $formVars['mod_virtual'] = 0;
        $formVars['mod_primary'] = 0;

        if (strlen($formVars['mod_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "mod_vendor     =   " . $formVars['mod_vendor']   . "," .
            "mod_name       = \"" . $formVars['mod_name']     . "\"," .
            "mod_type       = \"" . $formVars['mod_type']     . "\"," .
            "mod_primary    =   " . $formVars['mod_primary']  . "," .
            "mod_size       = \"" . $formVars['mod_size']     . "\"," .
            "mod_speed      = \"" . $formVars['mod_speed']    . "\"," .
            "mod_eopur      = \"" . $formVars['mod_eopur']    . "\"," .
            "mod_eoship     = \"" . $formVars['mod_eoship']   . "\"," .
            "mod_eol        = \"" . $formVars['mod_eol']      . "\"," .
            "mod_plugs      =   " . $formVars['mod_plugs']    . "," .
            "mod_plugtype   =   " . $formVars['mod_plugtype'] . "," .
            "mod_volts      =   " . $formVars['mod_volts']    . "," .
            "mod_draw       = \"" . $formVars['mod_draw']     . "\"," .
            "mod_start      = \"" . $formVars['mod_start']    . "\"," .
            "mod_btu        = \"" . $formVars['mod_btu']      . "\"," .
            "mod_virtual    =   " . $formVars['mod_virtual'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_models set mod_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_models set " . $q_string . " where mod_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['mod_name']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete CPU</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Vendor</th>\n";
      $output .= "  <th class=\"ui-state-default\">Model</th>\n";
      $output .= "  <th class=\"ui-state-default\">Number of Cores</th>\n";
      $output .= "  <th class=\"ui-state-default\">Speed</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select mod_id,ven_name,mod_name,mod_size,mod_speed ";
      $q_string .= "from inv_models ";
      $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
      $q_string .= "where mod_type = " . $formVars['mod_type'] . " ";
      $q_string .= "order by ven_name,mod_name ";
      $q_inv_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_models) > 0) {
        while ($a_inv_models = mysqli_fetch_array($q_inv_models)) {

          $class = "ui-widget-content";

          $linkstart = "<a href=\"#\" onclick=\"show_file('cpu.fill.php?id="  . $a_inv_models['mod_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('cpu.del.php?id=" . $a_inv_models['mod_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select ast_id ";
          $q_string .= "from inv_assets ";
          $q_string .= "where ast_modelid = " . $a_inv_models['mod_id'] . " ";
          $q_inv_assets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $total = mysqli_num_rows($q_inv_assets);

          $q_string  = "select hw_id ";
          $q_string .= "from inv_hardware ";
          $q_string .= "left join inv_inventory on inv_inventory.inv_id = inv_hardware.hw_companyid ";
          $q_string .= "where hw_vendorid = " . $a_inv_models['mod_id'] . " and hw_type = " . $formVars['mod_type'] . " and inv_status = 0 ";
          $q_inv_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $total = mysqli_num_rows($q_inv_hardware);

          $totallink = '';
          if ($total > 0) {
            $totallink = "<a href=\"cpu.members.php?type=" . $formVars['mod_type'] . "&model=" . $a_inv_models['mod_id'] . "\" target=\"_blank\">";
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"" . $class . " delete\">"              . $a_inv_models['ven_name']            . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_models['mod_name'] . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $a_inv_models['mod_size']            . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $a_inv_models['mod_speed']           . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">" . $totallink . $total                    . $linkend . "</td>";
          $output .= "</tr>";
        }

        $output .= "</table>";
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"6\">No cpus found</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_inv_models);

      print "document.getElementById('mysql_table').innerHTML = '"    . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
