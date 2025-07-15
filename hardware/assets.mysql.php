<?php
# Script: assets.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "assets.mysql.php";
    $formVars['update']         = clean($_GET['update'],       10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']                 = clean($_GET['id'],               10);
        $formVars['ast_asset']          = clean($_GET['ast_asset'],        10);
        $formVars['ast_serial']         = clean($_GET['ast_serial'],      100);
        $formVars['ast_parentid']       = clean($_GET['ast_parentid'],    100);
        $formVars['ast_modelid']        = clean($_GET['ast_modelid'],      20);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['ast_parentid'] == '') {
          $formVars['ast_parentid'] = "0";
        }
        if ($formVars['ast_modelid'] == '') {
          $formVars['ast_modelid'] = "0";
        }

        if (strlen($formVars['ast_serial']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "ast_asset       = \"" . $formVars['ast_asset']    . "\"," .
            "ast_serial      = \"" . $formVars['ast_serial']   . "\"," .
            "ast_parentid    =   " . $formVars['ast_parentid'] . "," .
            "ast_modelid     =   " . $formVars['ast_modelid'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_assets set ast_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_assets set " . $q_string . " where ast_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['ast_serial']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Asset</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Device</th>\n";
      $output .= "  <th class=\"ui-state-default\">Asset Tag</th>\n";
      $output .= "  <th class=\"ui-state-default\">Serial Number</th>\n";
      $output .= "  <th class=\"ui-state-default\">Assignment</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ast_id,ast_asset,ast_serial,mod_name,ast_parentid,ven_name ";
      $q_string .= "from inv_assets ";
      $q_string .= "left join inv_models on inv_models.mod_id = inv_assets.ast_modelid ";
      $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
      $q_string .= "order by ast_asset,mod_name ";
      $q_inv_assets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_assets) > 0) {
        while ($a_inv_assets = mysqli_fetch_array($q_inv_assets)) {

          $class = "ui-widget-content";

          $linkstart = "<a href=\"#\" onclick=\"show_file('assets.fill.php?id="  . $a_inv_assets['ast_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('assets.del.php?id=" . $a_inv_assets['ast_id'] . "');\">";
          $linkend   = "</a>";

          $total = 0;

          $totallink = '';
          if ($total > 0) {
            $totallink = "<a href=\"assets.members.php?id=" . $formVars['ast_id'] . "\" target=\"_blank\">";
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"" . $class . " delete\">"              . $a_inv_assets['ven_name'] . " " . $a_inv_assets['mod_name']                . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_inv_assets['ast_asset']    . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_inv_assets['ast_serial']   . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $a_inv_assets['ast_parentid']            . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">" . $totallink . $total                        . $linkend . "</td>";
          $output .= "</tr>";
        }

        $output .= "</table>";
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"6\">No Assets found</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_inv_assets);

      print "document.getElementById('mysql_table').innerHTML = '"    . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
