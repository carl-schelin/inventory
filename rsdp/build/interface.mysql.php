<?php
# Script: interface.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "interface.mysql.php";
    $formVars['update']         = clean($_GET['update'],          10);
    $formVars['if_rsdp']        = clean($_GET['if_rsdp'],         10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['if_id'],           10);
        $formVars['if_name']        = clean($_GET['if_name'],         60);
        $formVars['if_sysport']     = clean($_GET['if_sysport'],      60);
        $formVars['if_ipcheck']     = clean($_GET['if_ipcheck'],      10);
        $formVars['if_interface']   = clean($_GET['if_interface'],    30);
        $formVars['if_type']        = clean($_GET['if_type'],         10);
        $formVars['if_virtual']     = clean($_GET['if_virtual'],      10);
        $formVars['if_monitored']   = clean($_GET['if_monitored'],    10);
        $formVars['if_description'] = clean($_GET['if_description'], 255);
        $formVars['if_redundant']   = clean($_GET['if_redundant'],    10);
        $formVars['if_groupname']   = clean($_GET['if_groupname'],    20);
        $formVars['if_if_id']       = clean($_GET['if_if_id'],        10);
        $formVars['if_media']       = clean($_GET['if_media'],        10);
        $formVars['if_speed']       = clean($_GET['if_speed'],        10);
        $formVars['if_duplex']      = clean($_GET['if_duplex'],       10);
        $formVars['if_swcheck']     = clean($_GET['if_swcheck'],      10);
        $formVars['if_zone']        = clean($_GET['if_zone'],         10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['if_ipcheck'] == 'true') {
          $formVars['if_ipcheck'] = 1;
        } else {
          $formVars['if_ipcheck'] = 0;
        }
        if ($formVars['if_virtual'] == 'true') {
          $formVars['if_virtual'] = 1;
        } else {
          $formVars['if_virtual'] = 0;
        }
        if ($formVars['if_monitored'] == 'true') {
          $formVars['if_monitored'] = 1;
        } else {
          $formVars['if_monitored'] = 0;
        }
        if ($formVars['if_swcheck'] == 'true') {
          $formVars['if_swcheck'] = 1;
        } else {
          $formVars['if_swcheck'] = 0;
        }

        if (strlen($formVars['if_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "if_rsdp        =   " . $formVars['if_rsdp']        . "," . 
            "if_name        = \"" . $formVars['if_name']        . "\"," .
            "if_sysport     = \"" . $formVars['if_sysport']     . "\"," . 
            "if_ipcheck     =   " . $formVars['if_ipcheck']     . "," .
            "if_interface   = \"" . $formVars['if_interface']   . "\"," . 
            "if_type        =   " . $formVars['if_type']        . "," . 
            "if_virtual     =   " . $formVars['if_virtual']     . "," . 
            "if_monitored   =   " . $formVars['if_monitored']   . "," . 
            "if_description = \"" . $formVars['if_description'] . "\"," . 
            "if_redundant   =   " . $formVars['if_redundant']   . "," .
            "if_groupname   = \"" . $formVars['if_groupname']   . "\"," .
            "if_if_id       =   " . $formVars['if_if_id']       . "," . 
            "if_media       =   " . $formVars['if_media']       . "," . 
            "if_speed       =   " . $formVars['if_speed']       . "," . 
            "if_duplex      =   " . $formVars['if_duplex']      . "," . 
            "if_swcheck     =   " . $formVars['if_swcheck']     . "," .
            "if_zone        =   " . $formVars['if_zone'];

          if ($formVars['update'] == 0) {
            $query = "insert into rsdp_interface set if_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update rsdp_interface set " . $q_string . " where if_id = " . $formVars['id'];
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['if_name']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      if ($formVars['update'] == -2) {
        $formVars['copyfrom']       = clean($_GET['copyfrom'],        10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select if_id,if_name,if_sysport,if_interface,if_zone,if_ipcheck,if_swcheck,if_virtual,if_monitored,";
          $q_string .= "if_speed,if_duplex,if_redundant,if_groupname,if_media,if_type,if_cid,if_description ";
          $q_string .= "from rsdp_interface ";
          $q_string .= "where if_rsdp = " . $formVars['copyfrom'] . " and if_if_id = 0 ";
          $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

            $q_string =
              "if_rsdp        =   " . $formVars['if_rsdp']                . "," .
              "if_name        = \"" . $a_rsdp_interface['if_name']        . "\"," .
              "if_sysport     = \"" . $a_rsdp_interface['if_syport']      . "\"," .
              "if_interface   = \"" . $a_rsdp_interface['if_interface']   . "\"," .
              "if_zone        =   " . $a_rsdp_interface['if_zone']        . "," .
              "if_ipcheck     =   " . $a_rsdp_interface['if_ipcheck']     . "," .
              "if_speed       =   " . $a_rsdp_interface['if_speed']       . "," .
              "if_duplex      =   " . $a_rsdp_interface['if_duplex']      . "," .
              "if_redundant   =   " . $a_rsdp_interface['if_redundant']   . "," .
              "if_groupname   = \"" . $a_rsdp_interface['if_groupname']   . "\"," .
              "if_media       =   " . $a_rsdp_interface['if_media']       . "," .
              "if_type        =   " . $a_rsdp_interface['if_type']        . "," .
              "if_swcheck     =   " . $a_rsdp_interface['if_swcheck']     . "," .
              "if_virtual     =   " . $a_rsdp_interface['if_virtual']     . "," .
              "if_monitored   =   " . $a_rsdp_interface['if_monitored']   . "," .
              "if_cid         = \"" . $a_rsdp_interface['if_cid']         . "\"," .
              "if_description = \"" . $a_rsdp_interface['if_description'] . "\"";

            $query = "insert into rsdp_interface set if_id = NULL, " . $q_string;
            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

            $response = last_insert_id();

            $q_string  = "select if_name,if_sysport,if_interface,if_zone,if_ipcheck,if_swcheck,if_virtual,if_monitored,";
            $q_string .= "if_speed,if_duplex,if_redundant,if_groupname,if_media,if_type,if_cid,if_description ";
            $q_string .= "from rsdp_interface ";
            $q_string .= "where if_rsdp = " . $formVars['copyfrom'] . " and if_if_id = " . $a_rsdp_interface['if_id'] . " ";
            $q_redundant = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            while ($a_redundant = mysqli_fetch_array($q_redundant)) {

              $q_string =
                "if_rsdp        =   " . $formVars['if_rsdp']           . "," .
                "if_if_id       =   " . $response                      . "," .
                "if_name        = \"" . $a_redundant['if_name']        . "\"," .
                "if_sysport     = \"" . $a_redundant['if_sysport']     . "\"," .
                "if_interface   = \"" . $a_redundant['if_interface']   . "\"," .
                "if_zone        =   " . $a_redundant['if_zone']        . "," .
                "if_ipcheck     =   " . $a_redundant['if_ipcheck']     . "," .
                "if_speed       =   " . $a_redundant['if_speed']       . "," .
                "if_duplex      =   " . $a_redundant['if_duplex']      . "," .
                "if_redundant   =   " . $a_redundant['if_redundant']   . "," .
                "if_groupname   = \"" . $a_redundant['if_groupname']   . "\"," .
                "if_media       =   " . $a_redundant['if_media']       . "," .
                "if_type        =   " . $a_redundant['if_type']        . "," .
                "if_swcheck     =   " . $a_redundant['if_swcheck']     . "," .
                "if_virtual     =   " . $a_redundant['if_virtual']     . "," .
                "if_monitored   =   " . $a_redundant['if_monitored']   . "," .
                "if_cid         = \"" . $a_redundant['if_cid']         . "\"," .
                "if_description = \"" . $a_redundant['if_description'] . "\"";

              $query = "insert into rsdp_interface set if_id = NULL, " . $q_string;
              mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
            }
          }
        }
      }


      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Interface Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('interface-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"interface-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Interface Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Highlighted</strong> - This interface is a <span class=\"ui-state-highlight\">Virtual Interface</span>.</li>\n";
      $output .= "    <li><strong>Highlighted</strong> - This interface doesn't have the <span class=\"ui-state-error\">IP Required</span> checkbox checked or the <span class=\"ui-state-error\">Switch Configuration Required</span> checkbox checked. While not required in certain situations, this indication ensures you are aware of the missing information.</li>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Clicking the <strong>x</strong> will delete this interface from this server.</li>\n";
      $output .= "    <li><strong>Virtual Memberships</strong> - If a physical interface is a member of a virtual interface, it will be designated with a &gt; to the left of the name and will listed under the virtual interface.\n";
      $output .= "    <ul>\n";
      $output .= "      <li><strong>Solaris</strong> virtual interfaces end in :number (e1000g1:1, e1000g5:1, etc).</li>\n";
      $output .= "      <li><strong>Linux</strong> virtual interfaces begin with bond (bond0, bond0.87, bond1, etc).</li>\n";
      $output .= "      <li><strong>HP-UX</strong> virtual interfaces are in the 900 range (lan900, lan901, etc).</li>\n";
      $output .= "      <li><strong>Windows</strong> virtual interfaces.</li>\n";
      $output .= "    </ul></li>\n";
      $output .= "    <li><strong>Virtual</strong> - A Virtual interface will be identified with a (v) next to the Logical Interface name. Not all Virtual interfaces are part of a Redundancy group.</li>\n";
      $output .= "    <li><strong>Monitored</strong> - An interface that will be monitored by OpenView will be identified with a (m) next to the Interface name.</li>\n";
      $output .= "    <li><strong>IP?</strong> - If an IP is required for this Interface, a &#10003; will be in this column.</li>\n";
      $output .= "    <li><strong>Switch?</strong> - If a switch needs to be configured for this physical Interface, a &#10003; will be in this column.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on an interface to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Interface Management</strong> title bar to toggle the <strong>Interface Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "<th class=\"ui-state-default\">Del</th>\n";
      $output .= "<th class=\"ui-state-default\">Name</th>\n";
      $output .= "<th class=\"ui-state-default\">IP?</th>\n";
      $output .= "<th class=\"ui-state-default\">Switch?</th>\n";
      $output .= "<th class=\"ui-state-default\">Slot/Port</th>\n";
      $output .= "<th class=\"ui-state-default\">Face</th>\n";
      $output .= "<th class=\"ui-state-default\">Zone</th>\n";
      $output .= "<th class=\"ui-state-default\">Media</th>\n";
      $output .= "<th class=\"ui-state-default\">Speed</th>\n";
      $output .= "<th class=\"ui-state-default\">Duplex</th>\n";
      $output .= "<th class=\"ui-state-default\">Redundant</th>\n";
      $output .= "<th class=\"ui-state-default\">Type</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select if_id,if_name,if_sysport,if_interface,if_ipcheck,if_swcheck,zone_name,med_text,";
      $q_string .= "spd_text,dup_text,if_redundant,if_virtual,red_text,itp_acronym,if_type,if_virtual,if_monitored ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "left join ip_zones       on ip_zones.zone_id      = rsdp_interface.if_zone ";
      $q_string .= "left join int_media      on int_media.med_id      = rsdp_interface.if_media ";
      $q_string .= "left join inttype        on inttype.itp_id        = rsdp_interface.if_type ";
      $q_string .= "left join int_speed      on int_speed.spd_id      = rsdp_interface.if_speed ";
      $q_string .= "left join int_duplex     on int_duplex.dup_id     = rsdp_interface.if_duplex ";
      $q_string .= "left join int_redundancy on int_redundancy.red_id = rsdp_interface.if_redundant ";
      $q_string .= "where if_rsdp = " . $formVars['if_rsdp'] . " and if_if_id = 0 ";
      $q_string .= "order by if_name,if_interface";
      $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_interface) > 0) {
        while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('interface.fill.php?id=" . $a_rsdp_interface['if_id'] . "');jQuery('#dialogInterface').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_interface('interface.del.php?id=" . $a_rsdp_interface['if_id'] . "');\">";
          $linkend   = "</a>";

          $class = "ui-widget-content";
          if ($a_rsdp_interface['if_redundant']) {
            $class = "ui-state-highlight";
          }

          $ipcheck = '';
          if ($a_rsdp_interface['if_ipcheck']) {
            $ipcheck = "&#10003;";
          }
          $swcheck = '';
          if ($a_rsdp_interface['if_swcheck']) {
            $swcheck = "&#10003;";
          }

          $checked = $class;
          if ($a_rsdp_interface['if_ipcheck'] == 0 && $a_rsdp_interface['if_swcheck'] == 0) {
            $checked = "ui-state-error";
          }

          $virtual = '';
          if ($a_rsdp_interface['if_virtual']) {
            $virtual = ' (v)';
          }
          $monitored = '';
          if ($a_rsdp_interface['if_monitored']) {
            $monitored = ' (m)';
          }

          $output .= "<tr>\n";
          $output .=   "<td class=\"" . $class   . " delete\">" . $linkdel                                                        . "</td>\n";
          $output .=   "<td class=\"" . $class   . "\">"        . $linkstart . $a_rsdp_interface['if_name'] . $virtual . $monitored . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $checked . " delete\">" . $linkstart . $ipcheck                                . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $checked . " delete\">" . $linkstart . $swcheck                                . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $class   . "\">"        . $linkstart . $a_rsdp_interface['if_sysport']         . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $class   . "\">"        . $linkstart . $a_rsdp_interface['if_interface']       . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $class   . "\">"        . $linkstart . $a_rsdp_interface['zone_name']          . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $class   . "\">"        . $linkstart . $a_rsdp_interface['med_text']           . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $class   . "\">"        . $linkstart . $a_rsdp_interface['spd_text']           . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $class   . "\">"        . $linkstart . $a_rsdp_interface['dup_text']           . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $class   . "\">"        . $linkstart . $a_rsdp_interface['red_text']           . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $class   . "\">"        . $linkstart . $a_rsdp_interface['itp_acronym']        . $linkend . "</td>\n";
          $output .= "</tr>\n";


          $q_string  = "select if_id,if_redundant,if_name,if_sysport,if_interface,zone_name,if_ipcheck,";
          $q_string .= "if_swcheck,med_text,spd_text,dup_text,red_text,itp_acronym,if_type,if_virtual,if_monitored ";
          $q_string .= "from rsdp_interface ";
          $q_string .= "left join ip_zones       on ip_zones.zone_id      = rsdp_interface.if_zone ";
          $q_string .= "left join int_media      on int_media.med_id      = rsdp_interface.if_media ";
          $q_string .= "left join inttype        on inttype.itp_id        = rsdp_interface.if_type ";
          $q_string .= "left join int_speed      on int_speed.spd_id      = rsdp_interface.if_speed ";
          $q_string .= "left join int_duplex     on int_duplex.dup_id     = rsdp_interface.if_duplex ";
          $q_string .= "left join int_redundancy on int_redundancy.red_id = rsdp_interface.if_redundant ";
          $q_string .= "where if_rsdp = " . $formVars['if_rsdp'] . " and if_if_id = " . $a_rsdp_interface['if_id'] . " ";
          $q_string .= "order by if_name,if_interface";
          $q_redundant = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_redundant) > 0) {
            while ($a_redundant = mysqli_fetch_array($q_redundant)) {

              $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('interface.fill.php?id=" . $a_redundant['if_id'] . "');jQuery('#dialogInterface').dialog('open');\">";
              $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_interface('interface.del.php?id=" . $a_redundant['if_id'] . "');\">";
              $linkend   = "</a>";

              $class = "ui-widget-content";
              if ($a_redundant['if_redundant']) {
                $class = "ui-state-highlight";
              }

              $ipcheck = '';
              if ($a_redundant['if_ipcheck']) {
                $ipcheck = "&#10003;";
              }
              $swcheck = '';
              if ($a_redundant['if_swcheck']) {
                $swcheck = "&#10003;";
              }
              $checked = $class;
              if ($a_redundant['if_ipcheck'] == 0 && $a_redundant['if_swcheck'] == 0) {
                $checked = "ui-state-error";
              }

              $virtual = '';
              if ($a_redundant['if_virtual']) {
                $virtual = ' (v)';
              }
              $monitored = '';
              if ($a_redundant['if_monitored']) {
                $monitored = ' (m)';
              }

              $output .= "<tr>\n";
              $output .=   "<td class=\"" . $class   . " delete\">"  . $linkdel                                                   . "</td>\n";
              $output .=   "<td class=\"" . $class   . "\">&gt; "    . $linkstart . $a_redundant['if_name'] . $virtual . $monitored . $linkend . "</td>\n";
              $output .=   "<td class=\"" . $checked . " delete\">"  . $linkstart . $ipcheck                           . $linkend . "</td>\n";
              $output .=   "<td class=\"" . $checked . " delete\">"  . $linkstart . $swcheck                           . $linkend . "</td>\n";
              $output .=   "<td class=\"" . $class   . "\">"         . $linkstart . $a_redundant['if_sysport']         . $linkend . "</td>\n";
              $output .=   "<td class=\"" . $class   . "\">"         . $linkstart . $a_redundant['if_interface']       . $linkend . "</td>\n";
              $output .=   "<td class=\"" . $class   . "\">"         . $linkstart . $a_redundant['zone_name']          . $linkend . "</td>\n";
              $output .=   "<td class=\"" . $class   . "\">"         . $linkstart . $a_redundant['med_text']           . $linkend . "</td>\n";
              $output .=   "<td class=\"" . $class   . "\">"         . $linkstart . $a_redundant['spd_text']           . $linkend . "</td>\n";
              $output .=   "<td class=\"" . $class   . "\">"         . $linkstart . $a_redundant['dup_text']           . $linkend . "</td>\n";
              $output .=   "<td class=\"" . $class   . "\">"         . $linkstart . $a_redundant['red_text']           . $linkend . "</td>\n";
              $output .=   "<td class=\"" . $class   . "\">"         . $linkstart . $a_redundant['itp_acronym']        . $linkend . "</td>\n";
              $output .= "</tr>\n";
            }
          }
        }
      } else {
        $output .= "<tr>\n";
        $output .=   "<td class=\"ui-widget-content\" colspan=\"12\">No interfaces found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_rsdp_interface);

      print "document.getElementById('interface_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.interface.if_name.value = '';\n";
      print "document.interface.if_sysport.value = '';\n";
      print "document.interface.if_ipcheck.checked = false;\n";
      print "document.interface.if_interface.value = '';\n";
      print "document.interface.if_type[1].selected = true;\n";
      print "document.interface.if_virtual.checked = false;\n";
      print "document.interface.if_monitored.checked = false;\n";
      print "document.interface.if_description.value = '';\n";
      print "document.interface.if_redundant[0].selected = true;\n";
      print "document.interface.if_groupname.value = '';\n";
      print "document.interface.if_if_id[0].selected = true;\n";
      print "document.interface.if_media[0].selected = true;\n";
      print "document.interface.if_speed[0].selected = true;\n";
      print "document.interface.if_duplex[0].selected = true;\n";
      print "document.interface.if_swcheck.checked = false;\n";
      print "document.interface.if_zone[0].selected = true;\n";

# rebuild the int_int_id drop down in case of changes in the virtual interface listing
      print "var selbox = document.interface.if_if_id;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

      $q_string  = "select if_id,if_interface ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_rsdp = " . $formVars['if_rsdp'] . " and if_redundant > 0 ";
      $q_string .= "order by if_interface";
      $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {
        print "selbox.options[selbox.options.length] = new Option(\"" . htmlspecialchars($a_rsdp_interface['if_interface']) . "\"," . $a_rsdp_interface['if_id'] . ");\n";
      }

      print "\nvalidate_Interface();\n";
      print "\nvalidate_Form();\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
