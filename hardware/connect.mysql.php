<?php
# Script: connect.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "connect.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);
    $formVars['csv']       = clean($_GET['csv'],        30);
    $formVars['type']      = clean($_GET['type'],       30);
    $formVars['view']      = clean($_GET['view'],       30);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    $where = "";
    if ($formVars['type'] != '') {
      $where = "where pt_name = \"" . $formVars['type'] . "\" ";
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],            10);
        $formVars['con_sourceid']     = clean($_GET['con_sourceid'],  10);
        $formVars['con_targetid']     = clean($_GET['con_targetid'],  10);
        $formVars['con_type']         = clean($_GET['con_type'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['con_sourceid']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "con_sourceid =   " . $formVars['con_sourceid']    . "," .
            "con_targetid =   " . $formVars['con_targetid']    . "," .
            "con_type     =   " . $formVars['con_type'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_connect set con_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_connect set " . $q_string . " where con_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['con_sourceid']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      if ($formVars['csv'] == 'true') {
        $output  = "\"Source Device\"" . ",";
        $output .= "\"Power Supply\"" . ",";
        $output .= "\"Target Device\"" . ",";
        $output .= "\"Power Outlet\"" . ",";
        $output .= "\"Connection type\"" . "</br>\n";
      } else {
        $output  = "<table class=\"ui-styled-table\">";
        $output .= "<tr>";
        if (check_userlevel($db, $AL_Admin)) {
          $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Connection</th>";
        }
        $output .= "  <th class=\"ui-state-default\"><a href=\"connect.php?dest=source&sort=ast_name"  . "\">" . "Source Device</a></th>";
        $output .= "  <th class=\"ui-state-default\">Source Port</th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"connect.php?dest=target&sort=ast_name"  . "\">" . "Target Device</a></th>";
        $output .= "  <th class=\"ui-state-default\">Target Port</th>";
        $output .= "  <th class=\"ui-state-default\">Connection Type</th>";
        $output .= "</tr>";
      }

# show network connections first
# network connections are patch to patch vs power to outlet or fiber to fiber
# so get the connection when type == Network Interface
# the source and target are from the patch table.
      $q_string  = "select con_id,con_sourceid,con_targetid,ast_name,pat_name ";
      $q_string .= "from inv_connect ";
      $q_string .= "left join inv_patch on inv_patch.pat_id = inv_connect.con_sourceid ";
      $q_string .= "left join inv_assets on inv_assets.ast_id = inv_patch.pat_deviceid ";
      $q_string .= "left join inv_powertype on inv_powertype.pt_id = inv_connect.con_type ";
      $q_string .= "where pt_name = \"" . "Network Interface" . "\" ";
      $q_string .= "order by ast_name,pat_name ";
      $q_inv_connect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_connect) > 0) {
        while ($a_inv_connect = mysqli_fetch_array($q_inv_connect)) {
          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('connect.fill.php?id=" . $a_inv_connect['con_id'] . "');jQuery('#dialogUpdateCat5').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('connect.del.php?id=" . $a_inv_connect['con_id'] . "');\">";
          $linkend   = "</a>";
          $typestart = "<a href=\"connect.php?type=Network%20Interface\">";
          $viewlink  = "<a href=\"connect.php?view=" . $a_inv_connect['con_sourceid'] . "&type=Network%20Interface\">";
          $viewfilter = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/filter.webp\" height=\"10\">";

          $q_string  = "select ast_name,pat_name ";
          $q_string .= "from inv_patch ";
          $q_string .= "left join inv_assets on inv_assets.ast_id = inv_patch.pat_deviceid ";
          $q_string .= "where pat_id = " . $a_inv_connect['con_targetid'] . " ";
          $q_inv_patch = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_patch) > 0) {
            $a_inv_patch = mysqli_fetch_array($q_inv_patch);
          }

          if ($formVars['csv'] == "true") {
            $output .= "\"" . $a_inv_connect['ast_name'] . "\",";
            $output .= "\"" . $a_inv_connect['pat_name'] . "\",";
            $output .= "\"" . $a_inv_patch['ast_name'] . "\",";
            $output .= "\"" . $a_inv_patch['pat_name'] . "\",";
            $output .= "\"Network Interface\"</br>\n";
          } else {
            $output .= "<tr>";
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"        . $viewlink . $viewfilter . $linkend . $linkstart . $a_inv_connect['ast_name']    . $linkend . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_connect['pat_name']    . $linkend . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_patch['ast_name']   . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_patch['pat_name'] . "</td>";
            $output .= "  <td class=\"ui-widget-content delete\">" . $typestart . "Network Interface" . $linkend . "</td>";
            $output .= "</tr>";
          }
        }
      }

# next up is to show power and outlets for the inside check
      $q_string  = "select con_id,con_sourceid,con_targetid,ast_name,port_name ";
      $q_string .= "from inv_connect ";
      $q_string .= "left join inv_ports on inv_ports.port_id = inv_connect.con_sourceid ";
      $q_string .= "left join inv_assets on inv_assets.ast_id = inv_ports.port_deviceid ";
      $q_string .= "left join inv_powertype on inv_powertype.pt_id = inv_connect.con_type ";
      $q_string .= "where pt_name = \"" . "Power" . "\" ";
      $q_string .= "order by ast_name,port_name ";
      $q_inv_connect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_connect) > 0) {
        while ($a_inv_connect = mysqli_fetch_array($q_inv_connect)) {
          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('connect.fill.php?id=" . $a_inv_connect['con_id'] . "');jQuery('#dialogUpdatePower').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('connect.del.php?id=" . $a_inv_connect['con_id'] . "');\">";
          $linkend   = "</a>";
          $typestart = "<a href=\"connect.php?type=Power\">";
          $viewlink  = "<a href=\"connect.php?view=" . $a_inv_connect['con_sourceid'] . "&type=Power\">";
          $viewfilter = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/filter.webp\" height=\"10\">";

          $q_string  = "select ast_name,out_name ";
          $q_string .= "from inv_outlets ";
          $q_string .= "left join inv_assets on inv_assets.ast_id = inv_outlets.out_deviceid ";
          $q_string .= "where out_id = " . $a_inv_connect['con_targetid'] . " ";
          $q_inv_outlets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_outlets) > 0) {
            $a_inv_outlets = mysqli_fetch_array($q_inv_outlets);
          }

          if ($formVars['csv'] == "true") {
            $output .= "\"" . $a_inv_connect['ast_name'] . "\",";
            $output .= "\"" . $a_inv_connect['port_name'] . "\",";
            $output .= "\"" . $a_inv_outlets['ast_name'] . "\",";
            $output .= "\"" . $a_inv_outlets['out_name'] . "\",";
            $output .= "\"Power\"</br>\n";
          } else {
            $output .= "<tr>";
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"        . $viewlink . $viewfilter . $linkend . $linkstart . $a_inv_connect['ast_name']    . $linkend . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_connect['port_name']    . $linkend . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_outlets['ast_name']   . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_outlets['out_name'] . "</td>";
            $output .= "  <td class=\"ui-widget-content delete\">" . $typestart . "Power" . $linkend . "</td>";
            $output .= "</tr>";
          }
        }
      }

# finally, show fiber
      $q_string  = "select con_id,con_sourceid,con_targetid,ast_name,fib_name ";
      $q_string .= "from inv_connect ";
      $q_string .= "left join inv_fiber on inv_fiber.fib_id = inv_connect.con_sourceid ";
      $q_string .= "left join inv_assets on inv_assets.ast_id = inv_fiber.fib_deviceid ";
      $q_string .= "left join inv_powertype on inv_powertype.pt_id = inv_connect.con_type ";
      $q_string .= "where pt_name = \"" . "Fibre" . "\" ";
      $q_string .= "order by ast_name,fib_name ";
      $q_inv_connect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_connect) > 0) {
        while ($a_inv_connect = mysqli_fetch_array($q_inv_connect)) {
          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('connect.fill.php?id=" . $a_inv_connect['con_id'] . "');jQuery('#dialogUpdateFiber').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('connect.del.php?id=" . $a_inv_connect['con_id'] . "');\">";
          $linkend   = "</a>";
          $typestart = "<a href=\"connect.php?type=Fibre\">";
          $viewlink  = "<a href=\"connect.php?view=" . $a_inv_connect['con_sourceid'] . "&type=Fibre\">";
          $viewfilter = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/filter.webp\" height=\"10\">";

          $q_string  = "select ast_name,fib_name ";
          $q_string .= "from inv_fiber ";
          $q_string .= "left join inv_assets on inv_assets.ast_id = inv_fiber.fib_deviceid ";
          $q_string .= "where fib_id = " . $a_inv_connect['con_targetid'] . " ";
          $q_inv_fiber = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_fiber) > 0) {
            $a_inv_fiber = mysqli_fetch_array($q_inv_fiber);
          }

          if ($formVars['csv'] == "true") {
            $output .= "\"" . $a_inv_connect['ast_name'] . "\",";
            $output .= "\"" . $a_inv_connect['fib_name'] . "\",";
            $output .= "\"" . $a_inv_fiber['ast_name'] . "\",";
            $output .= "\"" . $a_inv_fiber['fib_name'] . "\",";
            $output .= "\"Fibre\"</br>\n";
          } else {
            $output .= "<tr>";
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"        . $viewlink . $viewfilter . $linkend . $linkstart . $a_inv_connect['ast_name']    . $linkend . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_connect['fib_name']    . $linkend . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_fiber['ast_name']   . "</td>";
            $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_fiber['fib_name'] . "</td>";
            $output .= "  <td class=\"ui-widget-content delete\">" . $typestart . "Fibre" . $linkend . "</td>";
            $output .= "</tr>";
          }
        }
      }

      if ($formVars['csv'] == "true") {
        $output .= "</br>\n";
      } else {
        $output .= "</table>";
      }

      mysqli_free_result($q_inv_connect);

      print "document.getElementById('mysql_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
