<?php
# Script: hardware.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "hardware.mysql.php";
    $formVars['update']         = clean($_GET['update'],       10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],           10);
        $formVars['mod_vendor']     = clean($_GET['mod_vendor'],   24);
        $formVars['mod_name']       = clean($_GET['mod_name'],    100);
        $formVars['mod_type']       = clean($_GET['mod_type'],     10);
        $formVars['mod_size']       = clean($_GET['mod_size'],    100);
        $formVars['mod_speed']      = clean($_GET['mod_speed'],    20);
        $formVars['mod_eopur']      = clean($_GET['mod_eopur'],    30);
        $formVars['mod_eoship']     = clean($_GET['mod_eoship'],   30);
        $formVars['mod_eol']        = clean($_GET['mod_eol'],      30);
        $formVars['mod_plugs']      = clean($_GET['mod_plugs'],    10);
        $formVars['mod_plugtype']   = clean($_GET['mod_plugtype'], 10);
        $formVars['mod_volts']      = clean($_GET['mod_volts'],    10);
        $formVars['mod_draw']       = clean($_GET['mod_draw'],     20);
        $formVars['mod_start']      = clean($_GET['mod_start'],    20);
        $formVars['mod_btu']        = clean($_GET['mod_btu'],      30);
        $formVars['mod_virtual']    = clean($_GET['mod_virtual'],  10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['mod_plugs'] == '') {
          $formVars['mod_plugs'] = 0;
        }
        if ($formVars['mod_plugtype'] == '') {
          $formVars['mod_plugtype'] = 0;
        }
        if ($formVars['mod_volts'] == '') {
          $formVars['mod_volts'] = 0;
        }
        if ($formVars['mod_virtual'] == 'true') {
          $formVars['mod_virtual'] = 1;
        } else {
          $formVars['mod_virtual'] = 0;
        }

        if (strlen($formVars['mod_vendor']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# Set the primary field from the parts table
          $q_string  = "select part_type ";
          $q_string .= "from parts ";
          $q_string .= "where part_id = " . $formVars['mod_type'];
          $q_parts = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_parts = mysqli_fetch_array($q_parts);

          $formVars['mod_primary'] = $a_parts['part_type'];

          $q_string =
            "mod_vendor     = \"" . $formVars['mod_vendor']   . "\"," .
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
            $query = "insert into models set mod_id = NULL, " . $q_string;
            $message = "Model added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update models set " . $q_string . " where mod_id = " . $formVars['id'];
            $message = "Model updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['mod_name']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $server  = "<table class=\"ui-styled-table\">\n";
      $server .= "<tr>\n";
      $server .= "  <th class=\"ui-state-default\">Server Listing</th>\n";
      $server .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('server-listing-help');\">Help</a></th>\n";
      $server .= "</tr>\n";
      $server .= "</table>\n";
      $server .= "<div id=\"server-listing-help\" style=\"display: none\">\n";

      $disk  = "<table class=\"ui-styled-table\">\n";
      $disk .= "<tr>\n";
      $disk .= "  <th class=\"ui-state-default\">Hard Disk Listing</th>\n";
      $disk .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('disk-listing-help');\">Help</a></th>\n";
      $disk .= "</tr>\n";
      $disk .= "</table>\n";
      $disk .= "<div id=\"disk-listing-help\" style=\"display: none\">\n";

      $cpu  = "<table class=\"ui-styled-table\">\n";
      $cpu .= "<tr>\n";
      $cpu .= "  <th class=\"ui-state-default\">CPU Listing</th>\n";
      $cpu .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('cpu-listing-help');\">Help</a></th>\n";
      $cpu .= "</tr>\n";
      $cpu .= "</table>\n";
      $cpu .= "<div id=\"cpu-listing-help\" style=\"display: none\">\n";

      $memory  = "<table class=\"ui-styled-table\">\n";
      $memory .= "<tr>\n";
      $memory .= "  <th class=\"ui-state-default\">Memory Listing</th>\n";
      $memory .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('memory-listing-help');\">Help</a></th>\n";
      $memory .= "</tr>\n";
      $memory .= "</table>\n";
      $memory .= "<div id=\"memory-listing-help\" style=\"display: none\">\n";

      $misc  = "<table class=\"ui-styled-table\">\n";
      $misc .= "<tr>\n";
      $misc .= "  <th class=\"ui-state-default\">Miscellaneous Parts Listing</th>\n";
      $misc .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('misc-listing-help');\">Help</a></th>\n";
      $misc .= "</tr>\n";
      $misc .= "</table>\n";
      $misc .= "<div id=\"misc-listing-help\" style=\"display: none\">\n";


      $header  = "<div class=\"main-help ui-widget-content\">\n";
      $header .= "<ul>\n";
      $header .= "  <li><strong>Hardware Listing</strong>\n";
      $header .= "  <ul>\n";
      $header .= "    <li><strong>Highlighted</strong> - This device is the <span class=\"ui-state-highlight\">Primary</span> or main piece of equipment. It generally holds the other components.</li>\n";
      $header .= "    <li><strong>Editing</strong> - Click on a device to edit it.</li>\n";
      $header .= "  </ul></li>\n";
      $header .= "</ul>\n";

      $header .= "<ul>\n";
      $header .= "  <li><strong>Notes</strong>\n";
      $header .= "  <ul>\n";
      $header .= "    <li>Click the <strong>Hardware Management</strong> title bar to toggle the <strong>Hardware Form</strong>.</li>\n";
      $header .= "  </ul></li>\n";
      $header .= "</ul>\n";

      $header .= "</div>\n";

      $header .= "</div>\n";

      $header .= "<table class=\"ui-styled-table\">\n";
      $header .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $header .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $header .= "  <th class=\"ui-state-default\">Id</th>\n";
      $header .= "  <th class=\"ui-state-default\">Vendor</th>\n";
      $header .= "  <th class=\"ui-state-default\">Model</th>\n";
      $header .= "  <th class=\"ui-state-default\">Type</th>\n";

      $primary  = "  <th class=\"ui-state-default\">Volts</th>\n";
      $primary .= "  <th class=\"ui-state-default\">Draw</th>\n";
      $primary .= "  <th class=\"ui-state-default\">BTU</th>\n";
      $primary .= "  <th class=\"ui-state-default\">Size</th>\n";
      $primary .= "</tr>\n";

      $secondary  = "  <th class=\"ui-state-default\" colspan=\"2\">Size</th>\n";
      $secondary .= "  <th class=\"ui-state-default\">Speed</th>\n";
      $secondary .= "</tr>\n";

      $server = $server . $header . $primary;
      $disk   = $disk   . $header . $secondary;
      $cpu    = $cpu    . $header . $secondary;
      $memory = $memory . $header . $secondary;
      $misc   = $misc   . $header . $secondary;

      $q_string  = "select mod_id,mod_vendor,mod_name,mod_type,mod_size,mod_speed,volt_text,mod_start,mod_draw,mod_btu,part_type,part_name ";
      $q_string .= "from models ";
      $q_string .= "left join parts on parts.part_id = models.mod_type ";
      $q_string .= "left join int_volts on int_volts.volt_id = models.mod_volts ";
      $q_string .= "order by mod_vendor,mod_name";
      $q_models = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_models) > 0) {
        while ($a_models = mysqli_fetch_array($q_models)) {

          if ($a_models['part_type']) {
            $class = "ui-state-highlight";
          } else {
            $class = "ui-widget-content";
          }

          $linkstart = "<a href=\"#\" onclick=\"show_file('hardware.fill.php?id="  . $a_models['mod_id'] . "');showDiv('hardware-hide');\">";
          $linkdel   = "<a href=\"#\" onclick=\"delete_line('hardware.del.php?id=" . $a_models['mod_id'] . "');\">";
          $linkend   = "</a>";

          $table  = "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $table .= "  <td class=\"" . $class . " delete\">" . $linkdel . 'x'                     . $linkend . "</td>";
          }
          $table .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_models['mod_id']     . $linkend . "</td>";
          $table .= "  <td class=\"" . $class . "\">" . $linkstart . $a_models['mod_vendor'] . $linkend . "</td>";
          $table .= "  <td class=\"" . $class . "\">" . $linkstart . $a_models['mod_name']   . $linkend . "</td>";
          $table .= "  <td class=\"" . $class . "\">" . $linkstart . $a_models['part_name']  . $linkend . "</td>";
          if ($a_models['part_type'] == 1) {
            $table .= "  <td class=\"" . $class . "\">" . $linkstart . $a_models['volt_text'] . $linkend . "</td>";
            $table .= "  <td class=\"" . $class . "\">" . $linkstart . $a_models['mod_draw']  . $linkend . "</td>";
            $table .= "  <td class=\"" . $class . "\">" . $linkstart . $a_models['mod_btu']   . $linkend . "</td>";
            $table .= "  <td class=\"" . $class . "\">" . $linkstart . $a_models['mod_size']   . $linkend . "</td>";
          } else {
            $table .= "  <td class=\"" . $class . "\" colspan=\"2\">" . $linkstart . $a_models['mod_size']  . $linkend . "</td>";
            $table .= "  <td class=\"" . $class . "\">"               . $linkstart . $a_models['mod_speed'] . $linkend . "</td>";
          }
          $table .= "</tr>";

          if ($a_models['part_type'] == 1) {
            $server .= $table;
          } else {
            if ($a_models['mod_type'] == 2) {
              $disk .= $table;
            } else {
              if ($a_models['mod_type'] == 8) {
                $cpu .= $table;
              } else {
                if ($a_models['mod_type'] == 4) {
                  $memory .= $table;
                } else {
                  $misc .= $table;
                }
              }
            }
          }
        }

        $footer = "</table>";
      } else {
        $server .= "<p>No servers found</p>\n";
        $server .= "<p>No disks found</p>\n";
        $server .= "<p>No cpus found</p>\n";
        $server .= "<p>No memory found</p>\n";
        $server .= "<p>No miscellaneous found</p>\n";
      }

      $server .= $footer;
      $disk   .= $footer;
      $cpu    .= $footer;
      $memory .= $footer;
      $misc   .= $footer;

      mysqli_free_result($q_models);

      print "document.getElementById('server_mysql').innerHTML = '" . mysqli_real_escape_string($db, $server) . "';\n\n";
      print "document.getElementById('disk_mysql').innerHTML = '"   . mysqli_real_escape_string($db, $disk) . "';\n\n";
      print "document.getElementById('cpu_mysql').innerHTML = '"    . mysqli_real_escape_string($db, $cpu) . "';\n\n";
      print "document.getElementById('memory_mysql').innerHTML = '" . mysqli_real_escape_string($db, $memory) . "';\n\n";
      print "document.getElementById('misc_mysql').innerHTML = '"   . mysqli_real_escape_string($db, $misc) . "';\n\n";

      print "document.hardware.update.disabled = true;\n";
      print "document.hardware.mod_vendor.focus();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
