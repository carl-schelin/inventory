<?php
# Script: routing.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "routing.mysql.php";
    $formVars['update']          = clean($_GET['update'],          10);
    $formVars['route_companyid'] = clean($_GET['route_companyid'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['route_companyid'] == '') {
      $formVars['route_companyid'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],              10);
        $formVars['route_address']   = clean($_GET['route_address'],   60);
        $formVars['route_mask']      = clean($_GET['route_mask'],      10);
        $formVars['route_gateway']   = clean($_GET['route_gateway'],   60);
        $formVars['route_source']    = clean($_GET['route_source'],    60);
        $formVars['route_interface'] = clean($_GET['route_interface'], 10);
        $formVars['route_desc']      = clean($_GET['route_desc'],      60);
        $formVars['route_static']    = clean($_GET['route_static'],    10);
        $formVars['route_propagate'] = clean($_GET['route_propagate'], 10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['route_propagate'] == '') {
          $formVars['route_propagate'] = "no";
        }
        if ($formVars['route_static'] == 'true') {
          $formVars['route_static'] = 1;
        } else {
          $formVars['route_static'] = 0;
        }

        if (strlen($formVars['route_address']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string = 
            "route_companyid =   " . $formVars['route_companyid'] . "," .
            "route_address   = \"" . $formVars['route_address']   . "\"," .
            "route_mask      =   " . $formVars['route_mask']      . "," .
            "route_gateway   = \"" . $formVars['route_gateway']   . "\"," .
            "route_source    = \"" . $formVars['route_source']    . "\"," .
            "route_interface =   " . $formVars['route_interface'] . "," .
            "route_desc      = \"" . $formVars['route_desc']      . "\"," . 
            "route_verified  =   " . 0                            . "," .
            "route_static    =   " . $formVars['route_static']    . "," .
            "route_user      =   " . $_SESSION['uid']             . "," .
            "route_update    = \"" . date('Y-m-d')                . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into routing set route_id = NULL," . $q_string;
            $message = "Route added.";
          }

          if ($formVars['update'] == 1) {
            $query = "update routing set " . $q_string . " where route_id = " . $formVars['id'];
            $message = "Route updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['var_name']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";

# if repeat description is confirmed, load each route where route_address = route_address and update the interface.
          if ($formVars['route_propagate'] == 'yes') {
            $q_string  = "select route_id ";
            $q_string .= "from routing ";
            $q_string .= "where route_address = '" . $formVars['route_address'] . "' ";
            $q_routing = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_routing = mysqli_fetch_array($q_routing)) {
              $query = "update routing set route_desc = '" . $formVars['route_desc'] . "' where route_id = " . $a_routing['route_id'];
              mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
            }
          }

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

# copy routes from an existing server to the current server
      if ($formVars['update'] == -2) {
        $formVars['copyfrom'] = clean($_GET['copyfrom'], 10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select route_address,route_mask,route_gateway,route_source,route_interface,route_desc,route_static ";
          $q_string .= "from routing ";
          $q_string .= "where route_companyid = " . $formVars['copyfrom'];
          $q_routing = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_routing = mysqli_fetch_array($q_routing)) {

# so try to match the copied server's interface name with the current server.
# They should be close to the same, otherwise default to 0
            $q_string  = "select int_face ";
            $q_string .= "from interface ";
            $q_string .= "where int_id = " . $a_routing['route_interface'];
            $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            $a_interface = mysqli_fetch_array($q_interface);

            $q_string  = "select int_id ";
            $q_string .= "from interface ";
            $q_string .= "where int_companyid = " . $formVars['route_companyid'] . " and int_face = '" . $a_interface['int_face'] . "'";
            $q_interface2 = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            $a_interface2 = mysqli_fetch_array($q_interface2);

            if ($a_interface2['int_id'] == '') {
              $a_interface2['int_id'] = 0;
            }

            $q_string = 
              "route_companyid =   " . $formVars['route_companyid']  . "," .
              "route_address   = \"" . $a_routing['route_address']   . "\"," .
              "route_mask      =   " . $a_routing['route_mask']      . "," .
              "route_gateway   = \"" . $a_routing['route_gateway']   . "\"," .
              "route_soruce    = \"" . $a_routing['route_source']    . "\"," .
              "route_interface =   " . $a_interface2['int_id']       . "," .
              "route_static    =   " . $a_routing['route_static']    . "," .
              "route_desc      = \"" . $a_routing['route_desc']      . "\"";

            $query = "insert into routing set route_id = NULL," . $q_string;
            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
          }
        }
      }


      if ($formVars['update'] == -3) {
        logaccess($db, $_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"route_refresh\" value=\"Refresh Route Listing\" onClick=\"javascript:attach_route('routing.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"route_update\"  value=\"Update Route\"          onClick=\"javascript:attach_route('routing.mysql.php', 1);hideDiv('routing-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"route_id\"      value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"route_addbtn\"  value=\"Add Route\"             onClick=\"javascript:attach_route('routing.mysql.php', 0);\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"route_copyitem\" value=\"Copy Routing Table From:\" onClick=\"javascript:attach_route('routing.mysql.php', -2);\">\n";
        $output .= "<select name=\"route_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name";
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          $q_string  = "select route_id ";
          $q_string .= "from routing ";
          $q_string .= "where route_companyid = " . $a_inventory['inv_id'] . " ";
          $q_routing = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $route_total = mysqli_num_rows($q_routing);

          if ($route_total > 0) {
            $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . " (" . $route_total . ")</option>\n";
          }
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"4\">Route Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Route <input type=\"text\" name=\"route_address\" size=\"20\"> Static Route? <input type=\"checkbox\" name=\"route_static\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Gateway <input type=\"text\" name=\"route_gateway\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Subnet Mask: <select name=\"route_mask\">\n";

        for ($i = 0; $i < 129; $i++) {
          if ($i > 32) {
            $output .=  "<option value=\"" . $i . "\">IPv6/" . $i . "</option>\n";
          } else {
            $output .=  "<option value=\"" . $i . "\">" . createNetmaskAddr($i) . "/" . $i . "</option>\n";
          }
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Interface: <select name=\"route_interface\">\n";
        $output .= "<option value=\"0\">Added</option>\n";

        $q_string  = "select int_id,int_face,int_ip6 ";
        $q_string .= "from interface ";
        $q_string .= "where int_companyid = " . $formVars['route_companyid'] . " ";
        $q_string .= "order by int_face";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_interface = mysqli_fetch_array($q_interface)) {
          if ($a_interface['int_ip6']) {
            $output .= "<option value=\"" . $a_interface['int_id'] . "\">" . $a_interface['int_face'] . " (ip6)</option>\n";
          } else {
            $output .= "<option value=\"" . $a_interface['int_id'] . "\">" . $a_interface['int_face'] . "</option>\n";
          }
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Interface <input disabled type=\"text\" name=\"interface\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Source IP <input type=\"text\" name=\"route_source\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">Description: <input type=\"text\" name=\"route_desc\" size=\"80\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('routing_form').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $os = return_System($db, $formVars['route_companyid']);

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\" colspan=\"8\">Route Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('routing-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"routing-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Route Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Delete this route.</li>\n";
      $output .= "    <li><strong>Ping Failure</strong> -  This route was <span class=\"ui-state-error\">not successfully reached</span> via ping.</li>\n";
      $output .= "    <li><strong>Ping Success</strong> - This route was <span class=\"ui-state-highlight\">successfully reached</span> via ping.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a route to bring up the form and edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Rows marked with a checkmark in the Updated column have been automatically captured where possible.</li>\n";
      $output .= "    <li>Click the <strong>Route Management</strong> title bar to toggle the <strong>Route Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Del</th>\n";
      $output .=   "<th class=\"ui-state-default\">Static</th>\n";
      $output .=   "<th class=\"ui-state-default\">Destination</th>\n";
#      $output .=   "<th class=\"ui-state-default\">DNS</th>\n";
      $output .=   "<th class=\"ui-state-default\">Gateway</th>\n";
      $output .=   "<th class=\"ui-state-default\">Iface</th>\n";
      $output .=   "<th class=\"ui-state-default\">Source IP</th>\n";
      $output .=   "<th class=\"ui-state-default\">Description</th>\n";
      $output .=   "<th class=\"ui-state-default\">Updated</th>\n";
      $output .= "</tr>\n";

      $interface = array();
      $sunroute = '';
      $q_string  = "select route_id,route_address,route_mask,route_source,route_gateway,route_interface,route_verified,route_desc,route_update,route_static,inv_manager ";
      $q_string .= "from routing ";
      $q_string .= "left join inventory on inventory.inv_id = routing.route_companyid ";
      $q_string .= "where route_companyid = " . $formVars['route_companyid'] . " ";
      $q_string .= "order by route_address";
      $q_routing = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_routing) > 0) {
        while ($a_routing = mysqli_fetch_array($q_routing)) {

          $q_string  = "select int_face ";
          $q_string .= "from interface ";
          $q_string .= "where int_id = " . $a_routing['route_interface'];
          $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_interface = mysqli_fetch_array($q_interface);

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('routing.fill.php?id=" . $a_routing['route_id'] . "');showDiv('routing-hide');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_route('routing.del.php?id=" . $a_routing['route_id'] . "');\">";
          $linkend   = "</a>";

          $ping = ' class="ui-widget-content"';
#          $dns = '';
# validate the IP before trying to ping or look it up (unnecessary delays)
#          if (filter_var($a_routing['route_address'], FILTER_VALIDATE_IP) && ($a_interface['int_face'] != 'lo' || $a_interface['int_face'] != 'lo0')) {
# ensure it's a -host based ip, no need to ping or look up -net ranges.
#            if ($a_routing['route_mask'] == 32) {
#              $ping = ' class="ui-state-error" ';
#              if (ping($a_routing['route_address'])) {
#                $ping = ' class="ui-state-highlight" ';
#              }
#              $dns = gethostbyaddr($a_routing['route_address']);
#            }
#          }

          $static = 'No';
          if ($a_routing['route_static']) {
            $static = 'Yes';
          }
          $checked = "";
          if ($a_routing['route_verified']) {
            $checked = "&#x2713;";
          }

          $output .= "<tr>\n";
          $output .=   "<td class=\"ui-widget-content delete\">" . $linkdel                                                                             . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $static                                                      . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_routing['route_address'] . "/" . $a_routing['route_mask'] . $linkend . "</td>\n";
#          $output .= "  <td" . $ping . ">"                       . $linkstart . $dns                                                         . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_routing['route_gateway']                                  . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_interface['int_face']                                     . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_routing['route_source']                                   . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_routing['route_desc']                                     . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_routing['route_update'] . $checked                        . $linkend . "</td>\n";
          $output .= "</tr>\n";

          if ($os == "Linux") {
            if ($a_routing['route_address'] != '0.0.0.0' && $a_routing['route_gateway'] != '0.0.0.0' && $a_routing['route_static']) {
              $interface[$a_interface['int_face']] .= "<br>" . $a_routing['route_address'] . "/" . $a_routing['route_mask'] . " via " . $a_routing['route_gateway'] . " dev " . $a_interface['int_face'];
              if ($a_routing['route_source'] != '') {
                $interface[$a_interface['int_face']] .= " src " . $a_routing['route_source'];
              }
            }
          }
          if ($os == "SunOS") {
            if ($a_routing['route_address'] != 'default' && substr($a_routing['route_address'], strlen($a_routing['route_address']) - 1) != '0' && $a_routing['route_address'] != '127.0.0.1') {
              if ($a_routing['route_mask'] == 32) {
                $flag = "-host ";
              } else {
                $flag = "-net ";
              }
              $sunroute .= "<br>route add " . $flag . $a_routing['route_address'] . " " . $a_routing['route_gateway'];
            }
          }
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No Routes defined.</td>\n";
        $output .= "</tr>\n";
      }

      mysqli_free_result($q_routing);

      $output .= "</table>\n";

      if ($os == "Linux") {
        $output .= "<h4 class=\"ui-widget\">Possible Linux Route Files</h4>";
        foreach ($interface as $i => $value) {
          $output .= "<p class=\"ui-widget-content\"><strong>/etc/sysconfig/network-scripts/route-" . $i . ":</strong>" . $interface[$i] . "</p>";
        }
      }
      if ($os == "SunOS") {
        $output .= "<h4 class=\"ui-widget\">Possible Solaris Route Files</h4>";
        $output .= "<p class=\"ui-widget-content\">/etc/inet/static_routes: " . $sunroute . "</p>";
      }

      print "document.getElementById('routing_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.edit.route_update.disabled = true;\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
