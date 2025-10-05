<?php
# Script: outlets.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "outlets.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);
    $formVars['sort']           = clean($_GET['sort'],         30);
    $formVars['view']           = clean($_GET['view'],         30);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['sort'] == '') {
      $orderby = "order by ast_name,out_name ";
    } else {
      $orderby = "order by " . $formVars['sort'] . ",out_name ";
    }
    if ($formVars['view'] == '') {
      $where = "";
    } else {
      $where = "where ast_id = " . $formVars['view'] . " ";
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],            10);
        $formVars['out_deviceid']     = clean($_GET['out_deviceid'],  10);
        $formVars['out_name']         = clean($_GET['out_name'],      60);
        $formVars['out_type']         = clean($_GET['out_type'],      10);
        $formVars['out_active']       = clean($_GET['out_active'],    10);
        $formVars['out_desc']         = clean($_GET['out_desc'],     100);
        $formVars['out_facing']       = clean($_GET['out_facing'],    10);
        $formVars['out_verified']     = clean($_GET['out_verified'],  10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['out_active'] == 'true') {
          $formVars['out_active'] = 1;
        } else {
          $formVars['out_active'] = 0;
        }
        if ($formVars['out_facing'] == 'true') {
          $formVars['out_facing'] = 1;
        } else {
          $formVars['out_facing'] = 0;
        }
        if ($formVars['out_verified'] == 'true') {
          $formVars['out_verified'] = 1;
        } else {
          $formVars['out_verified'] = 0;
        }

        if (strlen($formVars['out_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "out_deviceid    =   " . $formVars['out_deviceid']   . "," . 
            "out_name        = \"" . $formVars['out_name']       . "\"," .
            "out_type        =   " . $formVars['out_type']       . "," . 
            "out_active      =   " . $formVars['out_active']     . "," . 
            "out_desc        = \"" . $formVars['out_desc']       . "\"," .
            "out_facing      =   " . $formVars['out_facing']     . "," . 
            "out_verified    =   " . $formVars['out_verified'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_outlets set out_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_outlets set " . $q_string . " where out_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['out_name']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Outlet</th>";
      }
      $output .= "  <th class=\"ui-state-default\"><a href=\"outlets.php?sort=ast_name"     . "\">" . "Device</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"outlets.php?sort=out_name"     . "\">" . "Outlet Name</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"outlets.php?sort=plug_text"    . "\">" . "Plug Type</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"outlets.php?sort=out_active"   . "\">" . "Active</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"outlets.php?sort=out_facing"   . "\">" . "Facing</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"outlets.php?sort=out_verified" . "\">" . "Verified</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"outlets.php?sort=out_desc"     . "\">" . "Description</a></th>";
      $output .= "  <th class=\"ui-state-default\">Connection</th>";
      $output .= "</tr>";

      $total = 0;
      $q_string  = "select out_id,out_name,out_active,out_desc,out_facing,out_verified,plug_text,ast_id,ast_name ";
      $q_string .= "from inv_outlets ";
      $q_string .= "left join inv_assets on inv_assets.ast_id = inv_outlets.out_deviceid ";
      $q_string .= "left join inv_int_plugtype on inv_int_plugtype.plug_id = inv_outlets.out_type ";
      $q_string .= $where;
      $q_string .= $orderby;
      $q_inv_outlets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_outlets) > 0) {
        while ($a_inv_outlets = mysqli_fetch_array($q_inv_outlets)) {

          $class = $vclass = "ui-widget-content";

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('outlets.fill.php?id="     . $a_inv_outlets['out_id']   . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('outlets.del.php?id=" . $a_inv_outlets['out_id'] . "');\">";
          $linkend   = "</a>";
          $viewlink = "<a href=\"outlets.php?view=" . $a_inv_outlets['ast_id'] . "\">";
          $viewfilter = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/filter.webp\" height=\"10\">";

# for the outlets script, need to get the port information
# search the targetid and power type, in the connect table for this outlet 
# get the sourceid and print the port name for that port_id

#select con_sourceid from inv_connect where con_targetid = out_id
#select port_name from inv_ports where port_id = con_sourceid

          $active = 'No';
          $port_name = '--';
          if ($a_inv_outlets['out_active']) {
            $active = 'Yes';
            $class = "ui-state-highlight";

            $q_string  = "select con_sourceid ";
            $q_string .= "from inv_connect ";
            $q_string .= "where con_targetid = " . $a_inv_outlets['out_id'] . " and con_type = 3 ";
            $q_inv_connect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_inv_connect) > 0) {
              $a_inv_connect = mysqli_fetch_array($q_inv_connect);

              $q_string  = "select port_name,ast_name ";
              $q_string .= "from inv_ports ";
              $q_string .= "left join inv_assets on inv_assets.ast_id = inv_ports.port_deviceid ";
              $q_string .= "where port_id = " . $a_inv_connect['con_sourceid'] . " ";
              $q_inv_ports = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_inv_ports) > 0) {
                $a_inv_ports = mysqli_fetch_array($q_inv_ports);
                $port_name = $a_inv_ports['port_name'] . " " . $a_inv_ports['ast_name'];
              }
            }
          }

          $facing = 'Rear';
          if ($a_inv_outlets['out_facing']) {
            $facing = 'Front';
          }
          $verified = 'No';
          $vclass = "ui-state-error";
          if ($a_inv_outlets['out_verified']) {
            $verified = 'Yes';
            $vclass = $class;
          }

          $output .= "<tr>";
          $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
          $output .= "  <td class=\"" . $class . "\">"      . $viewlink . $viewfilter . $linkend . $linkstart . $a_inv_outlets['ast_name']  . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_outlets['out_name']             . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_outlets['plug_text']            . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $active                                . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $facing                                . "</td>";
          $output .= "  <td class=\"" . $vclass . " delete\">"              . $verified                              . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_outlets['out_desc']             . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $port_name                             . "</td>";
          $output .= "</tr>";
        }
      } else {
          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"9\">No Power Outlets found</td>";
          $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_outlets);

      print "document.getElementById('mysql_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
