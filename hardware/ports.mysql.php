<?php
# Script: ports.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "ports.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);
    $formVars['sort']      = clean($_GET['sort'],       30);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['sort'] == '') {
      $orderby = "order by ast_name,port_name ";
    } else {
      $orderby = "order by " . $formVars['sort'] . " ";
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']                = clean($_GET['id'],             10);
        $formVars['port_deviceid']     = clean($_GET['port_deviceid'],  10);
        $formVars['port_name']         = clean($_GET['port_name'],      60);
        $formVars['port_type']         = clean($_GET['port_type'],      10);
        $formVars['port_active']       = clean($_GET['port_active'],    10);
        $formVars['port_desc']         = clean($_GET['port_desc'],     100);
        $formVars['port_facing']       = clean($_GET['port_facing'],    10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['port_active'] == 'true') {
          $formVars['port_active'] = 1;
        } else {
          $formVars['port_active'] = 0;
        }
        if ($formVars['port_facing'] == 'true') {
          $formVars['port_facing'] = 1;
        } else {
          $formVars['port_facing'] = 0;
        }

        if (strlen($formVars['port_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "port_deviceid =   " . $formVars['port_deviceid']    . "," .
            "port_name     = \"" . $formVars['port_name']        . "\"," .
            "port_type     =   " . $formVars['port_type']        . "," . 
            "port_active   =   " . $formVars['port_active']      . "," .
            "port_desc     = \"" . $formVars['port_desc']        . "\"," .
            "port_facing   =   " . $formVars['port_facing'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_ports set port_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_ports set " . $q_string . " where port_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['port_name']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Port</th>";
      }
      $output .= "  <th class=\"ui-state-default\"><a href=\"ports.php?sort=ast_name"    . "\">" . "Device</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"ports.php?sort=port_name"   . "\">" . "Port Name</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"ports.php?sort=plug_text"   . "\">" . "Plug Type</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"ports.php?sort=port_active" . "\">" . "Active</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"ports.php?sort=port_facing" . "\">" . "Facing</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"ports.php?sort=port_desc"   . "\">" . "Description</a></th>";
      $output .= "  <th class=\"ui-state-default\">Connection</th>";
      $output .= "</tr>";

      $total = 0;
      $q_string  = "select port_id,port_name,port_active,port_desc,port_facing,plug_text,ast_name ";
      $q_string .= "from inv_ports ";
      $q_string .= "left join inv_assets on inv_assets.ast_id = inv_ports.port_deviceid ";
      $q_string .= "left join inv_int_plugtype on inv_int_plugtype.plug_id = inv_ports.port_type ";
      $q_string .= $orderby;
      $q_inv_ports = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_ports) > 0) {
        while ($a_inv_ports = mysqli_fetch_array($q_inv_ports)) {

          $class = "ui-widget-content";

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('ports.fill.php?id="     . $a_inv_ports['port_id']   . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('ports.del.php?id=" . $a_inv_ports['port_id'] . "');\">";
          $linkend   = "</a>";

#          $q_string  = "select mod_id ";
#          $q_string .= "from inv_models ";
#          $q_string .= "where mod_type = " . $a_inv_parts['part_id'] . " ";
#          $q_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
#          $total = mysqli_num_rows($q_models);

# process:
# assume ast_name,port_name is sourceid and ast_name,plug_text is targetid
# get targetid which gives j
# to create a connection, select the asset name (pdu-r103) and outlet name (out_name) 
# from the db based on the connection
          $q_string  = "select out_name ";
          $q_string .= "from inv_outlets ";

          $active = 'No';
          if ($a_inv_ports['port_active']) {
            $active = 'Yes';
          }
          $facing = 'Rear';
          if ($a_inv_ports['port_facing']) {
            $facing = 'Front';
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"" . $class . " delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_ports['ast_name']    . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_ports['port_name']              . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_ports['plug_text']              . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $active                                . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $facing                                . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_ports['port_desc']              . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $total                                 . "</td>";
          $output .= "</tr>";
        }
      } else {
          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"8\">No Power Ports found</td>";
          $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_ports);

      print "document.getElementById('mysql_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
