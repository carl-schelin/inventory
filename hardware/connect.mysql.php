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
    $formVars['sort']      = clean($_GET['sort'],       30);
    $formVars['dest']      = clean($_GET['dest'],       30);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['sort'] == '') {
      $orderby = "order by ast_name,port_name ";
      $leftjoin = "left join inv_assets  on inv_assets.ast_id  = inv_ports.port_deviceid ";
    } else {
      if ($formVars['dest'] == 'source') {
        $orderby = "order by " . $formVars['sort'] . ",ast_name ";
        $leftjoin = "left join inv_assets  on inv_assets.ast_id  = inv_ports.port_deviceid ";
      } else {
        $orderby = "order by " . $formVars['sort'] . ",ast_name ";
        $leftjoin = "left join inv_assets  on inv_assets.ast_id  = inv_outlets.out_deviceid ";
      }
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

      $output  = "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Connection</th>";
      }

      $output .= "  <th class=\"ui-state-default\"><a href=\"connect.php?dest=source&sort=ast_name"  . "\">" . "Source Device</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"connect.php?dest=source&sort=port_name" . "\">" . "Power Supply</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"connect.php?dest=target&sort=ast_name"  . "\">" . "Target Device</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"connect.php?dest=target&sort=out_name"  . "\">" . "Power Outlet</a></th>";
      $output .= "  <th class=\"ui-state-default\">Connection Type</th>";
      $output .= "</tr>";

      $total = 0;
      $q_string  = "select con_id,con_type,port_deviceid,port_name,out_deviceid,out_name ";
      $q_string .= "from inv_connect ";
      $q_string .= "left join inv_ports   on inv_ports.port_id  = inv_connect.con_sourceid ";
      $q_string .= "left join inv_outlets on inv_outlets.out_id = inv_connect.con_targetid ";
      $q_string .= $leftjoin;
      $q_string .= $orderby;
      $q_inv_connect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_connect) > 0) {
        while ($a_inv_connect = mysqli_fetch_array($q_inv_connect)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('connect.fill.php?id="     . $a_inv_connect['con_id']   . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('connect.del.php?id=" . $a_inv_connect['con_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select ast_name ";
          $q_string .= "from inv_assets ";
          $q_string .= "where ast_id = " . $a_inv_connect['port_deviceid'] . " ";
          $q_inv_source = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_inv_source = mysqli_fetch_array($q_inv_source);

          $q_string  = "select ast_name ";
          $q_string .= "from inv_assets ";
          $q_string .= "where ast_id = " . $a_inv_connect['out_deviceid'] . " ";
          $q_inv_target = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_inv_target = mysqli_fetch_array($q_inv_target);

          $contype = "Network Interface";
          if ($a_inv_connect['con_type'] == 2) {
            $contype = "Fiber";
          }
          if ($a_inv_connect['con_type'] == 3) {
            $contype = "Power";
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_source['ast_name']    . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_connect['port_name']    . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_target['ast_name']              . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_connect['out_name']              . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $contype                                 . "</td>";
          $output .= "</tr>";
        }
      } else {
          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No Connections found</td>";
          $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_connect);

      print "document.getElementById('mysql_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
