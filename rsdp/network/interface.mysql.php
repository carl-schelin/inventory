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
    $formVars['update']         = clean($_GET['update'],      10);
    $formVars['rsdp']           = clean($_GET['rsdp'],        10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Edit)) {
# Note: This is used for editing interfaces so there will be no new entries (no 'insert into' db calls).
      if ($formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['if_id'],       10);
        $formVars['if_ip']          = clean($_GET['if_ip'],       60);
        $formVars['if_mask']        = clean($_GET['if_mask'],     10);
        $formVars['if_gate']        = clean($_GET['if_gate'],     20);
        $formVars['if_vlan']        = clean($_GET['if_vlan'],     20);
        $formVars['if_switch']      = clean($_GET['if_switch'],   50);
        $formVars['if_port']        = clean($_GET['if_port'],     50);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['if_ip']) > 0 || strlen($formVars['if_switch']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "if_rsdp      =   " . $formVars['rsdp']         . "," . 
            "if_ip        = \"" . $formVars['if_ip']        . "\"," . 
            "if_mask      =   " . $formVars['if_mask']      . "," . 
            "if_gate      = \"" . $formVars['if_gate']      . "\"," . 
            "if_vlan      = \"" . $formVars['if_vlan']      . "\"," . 
            "if_switch    = \"" . $formVars['if_switch']    . "\"," .
            "if_port      = \"" . $formVars['if_port']      . "\"";

          if ($formVars['update'] == 1) {
            $query = "update rsdp_interface set " . $q_string . " where if_id = " . $formVars['id'];
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['if_ip']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

        } else {
          print "alert('A required field has not been populated.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

# need to skip switch check if a virtual machine
      $virtual = rsdp_Virtual($formVars['rsdp']);

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "<th class=\"ui-state-default\">Network Listing</th>";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('network-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"network-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Network Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Name</strong> - This is the assigned DNS name for this IP/Interface. If there is a &gt; symbel next to the name, this is a child of a virtual interface.</li>\n";
      $output .= "    <li><strong>Interface</strong> - This is the internal device name of this Interface.</li>\n";
      $output .= "    <li><strong>IP</strong> - If an IP is requested, there will be an input field. Enter an IP on a network identified by the Zone column.</li>\n";
      $output .= "    <li><strong>Resolves To: </strong> - This column indicates whether an IP is in DNS, is currently in use, or is available for use\n";
      $output .= "    <ul>\n";
      $output .= "      <li><strong>Highlighted</strong> - This IP responds to <strong>ping</strong> which indicates <span class=\"ui-state-error\">the IP is already in use</span> and should not be assigned.</li>\n";
      $output .= "      <li><strong>Highlighted</strong> - This IP is not responding to <strong>ping</strong> which indicates <span class=\"ui-state-highlight\">the IP can be used</span>.</li>\n";
      $output .= "    </ul></li>\n";
      $output .= "    <li><strong>Netmask</strong> - Select the appropriate Netmask for this IP address. Default is /24.</li>\n";
      $output .= "    <li><strong>Gateway</strong> - Enter the network Gateway for this IP address. Default is the entered IP with the fourth octet set to .254.\n";
      $output .= "    <li><strong>VLan</strong> - Enter the VLan for this IP address. Default is vl and the third octet. Example: 10.105.80.91 will have the VLan field defaulted to <strong>vl80</strong>.</li>\n";
      $output .= "    <li><strong>Zone</strong> - This is an indication of which network should be used for the requested IP Address. It's meant to help in assigning IP Addresses. IPs in one Zone will be on the same network but IPs in a different zone will be on a different network.</li>\n";
      $output .= "    <li><strong>Switch</strong> - If this is a physical interface and Switch information is requested, there will be an input field. Enter the Switch here. This identifies the device for the Data Center personnel.</li>\n";
      $output .= "    <li><strong>Port</strong> - If Switch information is requested, there will be an input field here. Enter the Port information. This identifies the port to be used to the Data Center personnel.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>The remaining columns provide additional information on the system Interface. If this is a Virtual System, the physical componants will not be visible.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table id=\"interface\" class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "<th class=\"ui-state-default\">Name</th>";
      $output .= "<th class=\"ui-state-default\">Interface</th>";
      $output .= "<th class=\"ui-state-default\">IP</th>";
      $output .= "<th class=\"ui-state-default\">Resolves To:</th>";
      $output .= "<th class=\"ui-state-default\">Netmask</th>";
      $output .= "<th class=\"ui-state-default\">Gateway</th>";
      $output .= "<th class=\"ui-state-default\">VLan</th>";
      $output .= "<th class=\"ui-state-default\">Zone</th>";
      if ($virtual == 0) {
        $output .= "<th class=\"ui-state-default\">Switch</th>";
        $output .= "<th class=\"ui-state-default\">Port</th>";
        $output .= "<th class=\"ui-state-default\">Media</th>";
        $output .= "<th class=\"ui-state-default\">Speed</th>";
        $output .= "<th class=\"ui-state-default\">Duplex</th>";
        $output .= "<th class=\"ui-state-default\">Redundant</th>";
      }
      $output .= "<th class=\"ui-state-default\">Type</th>";
      $output .= "</tr>";

      $count = 1;
# automatically set the if_save for the button
      $submit = 1;

      $q_string  = "select if_id,if_name,if_sysport,if_interface,if_ip,if_gate,if_mask,if_vlan,if_switch,if_port,";
      $q_string .= "zone_name,med_text,spd_text,dup_text,red_text,itp_acronym,if_description,if_ipcheck,if_swcheck ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
      $q_string .= "left join int_media on int_media.med_id = rsdp_interface.if_media ";
      $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
      $q_string .= "left join int_speed on int_speed.spd_id = rsdp_interface.if_speed ";
      $q_string .= "left join int_duplex on int_duplex.dup_id = rsdp_interface.if_duplex ";
      $q_string .= "left join int_redundancy on int_redundancy.red_id = rsdp_interface.if_redundant ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_if_id = 0 ";
      $q_string .= "order by if_interface";
      $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

        $class = " class=\"ui-widget-content\"";
        if (filter_var($a_rsdp_interface['if_ip'], FILTER_VALIDATE_IP)) {
          $ping = " class=\"ui-state-highlight\" ";
          $checked = "&#x2713;&nbsp;";
          if (ping($a_rsdp_interface['if_ip'])) {
            $ping = " class=\"ui-state-error\" ";
            $checked = "&#x2717;&nbsp;";
          }
          $dns = gethostbyaddr($a_rsdp_interface['if_ip']);
        } else {
          $ping = " class=\"ui-widget-content\"";
          $checked = '';
          $dns = $a_rsdp_interface['if_ip'];
        }

# just general clean up - the lines below are quite complicated to accommodate networking.
        $if_ip     = "if_ip_"     . $a_rsdp_interface['if_id'];
        $if_mask   = "if_mask_"   . $a_rsdp_interface['if_id'];
        $if_gate   = "if_gate_"   . $a_rsdp_interface['if_id'];
        $if_vlan   = "if_vlan_"   . $a_rsdp_interface['if_id'];
        $if_switch = "if_switch_" . $a_rsdp_interface['if_id'];
        $if_port   = "if_port_"   . $a_rsdp_interface['if_id'];

# new method requested by Cameron
        $output .= "<tr id=\"" . $a_rsdp_interface['if_id'] . "\">";
        $output .=   "<td" . $class . ">" . $a_rsdp_interface['if_name'];
        $output .= "<input type=\"hidden\" id=\"if_ipcheck_" . $a_rsdp_interface['if_id'] . "\" value=\"" . $a_rsdp_interface['if_ipcheck'] . "\">";
        $output .= "<input type=\"hidden\" id=\"if_swcheck_" . $a_rsdp_interface['if_id'] . "\" value=\"" . $a_rsdp_interface['if_swcheck'] . "\">";
        $output .= "</td>";
        $output .=   "<td" . $class . ">" . $a_rsdp_interface['if_interface'] . "</td>";

        if ($a_rsdp_interface['if_ipcheck']) {
          $output .=   "<td id=\"val_ip_" . $a_rsdp_interface['if_id'] . "\">" . "<input type=\"text\" tabindex=\"" . $count++ . "\" id=\"" . $if_ip . "\" name=\"" . $if_ip . "\" value=\"" . $a_rsdp_interface['if_ip'] . "\" size=\"18\"  onkeydown=\"javascript: if (event.keyCode == 9) {inet_defaults(" . $a_rsdp_interface['if_id'] . ");}\" onchange=\"validate_Form();\"></td>";
          $output .=   "<td" . $ping  . ">" . $checked . $dns . "</td>";

          $output .= "  <td id=\"val_mask_" . $a_rsdp_interface['if_id'] . "\"><select id=\"" . $if_mask . "\" name=\"" . $if_mask . "\" onchange=\"validate_Form();\">\n";

          for ($i = 0; $i < 129; $i++) {
            if ($i > 32) {
              if ($a_rsdp_interface['if_mask'] == $i) {
                $output .=  "<option selected value=\"" . $i . "\">IPv6/" . $i . "</option>\n";
              } else {
                $output .=  "<option value=\"" . $i . "\">IPv6/" . $i . "</option>\n";
              }
            } else {
              if ($a_rsdp_interface['if_mask'] == $i) {
                $output .=  "<option selected value=\"" . $i . "\">" . createNetmaskAddr($i) . "/" . $i . "</option>\n";
              } else {
                $output .=  "<option value=\"" . $i . "\">" . createNetmaskAddr($i) . "/" . $i . "</option>\n";
              }
            }
          }
          $output .=   "<td id=\"val_gate_" . $a_rsdp_interface['if_id'] . "\">" . "<input type=\"text\" id=\"" . $if_gate . "\" name=\"" . $if_gate . "\" value=\"" . $a_rsdp_interface['if_gate'] . "\" size=\"18\" onchange=\"validate_Form();\"></td>";
          $output .=   "<td id=\"val_vlan_" . $a_rsdp_interface['if_id'] . "\">" . "<input type=\"text\" id=\"" . $if_vlan . "\" name=\"" . $if_vlan . "\" value=\"" . $a_rsdp_interface['if_vlan'] . "\" size=\"10\" onchange=\"validate_Form();\"></td>";

# if any of the required fields are not set, set if_save to zero.
          if ($a_rsdp_interface['if_ip'] == '') {
            $submit = 0;
          }
          if ($a_rsdp_interface['if_mask'] == 0) {
            $submit = 0;
          }
          if ($a_rsdp_interface['if_gate'] == '') {
            $submit = 0;
          }
          if ($a_rsdp_interface['if_vlan'] == '') {
            $submit = 0;
          }

        } else {
          $output .=   "<td id=\"val_ip_" . $a_rsdp_interface['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_ip . "\" name=\"" . $if_ip . "\" value=\"\"></td>";
          $output .=   "<td" . $class . ">&nbsp;</td>";
          $output .=   "<td id=\"val_mask_" . $a_rsdp_interface['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_mask . "\" name=\"" . $if_mask . "\" value=\"0\"></td>";
          $output .=   "<td id=\"val_gate_" . $a_rsdp_interface['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_gate . "\" name=\"" . $if_gate . "\" value=\"\"></td>";
          $output .=   "<td id=\"val_vlan_" . $a_rsdp_interface['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_vlan . "\" name=\"" . $if_vlan . "\" value=\"\"></td>";
        }

        $output .= "  <td" . $class . ">" . $a_rsdp_interface['zone_name']    . "</td>";

        if ($virtual) {
          $output .= "<input type=\"hidden\" id=\"" . $if_switch . "\" name=\"" . $if_switch . "\" value=\"\">\n";
          $output .= "<input type=\"hidden\" id=\"" . $if_port   . "\" name=\"" . $if_port   . "\" value=\"\">\n";
        } else {
          if ($a_rsdp_interface['if_swcheck']) {
            $output .= "  <td id=\"val_switch_" . $a_rsdp_interface['if_id'] . "\">" . "<input type=\"text\" id=\"" . $if_switch . "\" name=\"" . $if_switch . "\" value=\"" . $a_rsdp_interface['if_switch'] . "\" size=\"10\" onchange=\"validate_Form();\"></td>";
            $output .= "  <td id=\"val_port_" . $a_rsdp_interface['if_id'] . "\">" . "<input type=\"text\" id=\"" . $if_port   . "\" name=\"" . $if_port   . "\" value=\"" . $a_rsdp_interface['if_port']   . "\" size=\"10\" onchange=\"validate_Form();\"></td>";

# if any of the required switch fields are missing, set if_save to zero
            if ($a_rsdp_interface['if_switch'] == '') {
              $submit = 0;
            }
            if ($a_rsdp_interface['if_port'] == '') {
              $submit = 0;
            }

          } else {
            $output .= "<td id=\"val_switch_" . $a_rsdp_interface['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_switch . "\" name=\"" . $if_switch . "\" value=\"\"></td>\n";
            $output .= "<td id=\"val_port_" . $a_rsdp_interface['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_port   . "\" name=\"" . $if_port   . "\" value=\"\"></td>\n";
          }
          $output .= "  <td" . $class . ">" . $a_rsdp_interface['med_text']     . "</td>";
          $output .= "  <td" . $class . ">" . $a_rsdp_interface['spd_text']     . "</td>";
          $output .= "  <td" . $class . ">" . $a_rsdp_interface['dup_text']     . "</td>";
          $output .= "  <td" . $class . ">" . $a_rsdp_interface['red_text']     . "</td>";
        }
        $output .= "  <td" . $class . ">" . $a_rsdp_interface['itp_acronym']  . "</td>";
        $output .= "</tr>";


        $q_string  = "select if_id,if_name,if_sysport,if_interface,if_ip,if_gate,if_mask,if_vlan,if_switch,if_port,";
        $q_string .= "zone_name,med_text,spd_text,dup_text,red_text,itp_acronym,if_description,if_ipcheck,if_swcheck ";
        $q_string .= "from rsdp_interface ";
        $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
        $q_string .= "left join int_media on int_media.med_id = rsdp_interface.if_media ";
        $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
        $q_string .= "left join int_speed on int_speed.spd_id = rsdp_interface.if_speed ";
        $q_string .= "left join int_duplex on int_duplex.dup_id = rsdp_interface.if_duplex ";
        $q_string .= "left join int_redundancy on int_redundancy.red_id = rsdp_interface.if_redundant ";
        $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_if_id = " . $a_rsdp_interface['if_id'] . " ";
        $q_string .= "order by if_interface";
        $q_redundant = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_redundant = mysqli_fetch_array($q_redundant)) {

          $class = " class=\"ui-widget-content\"";
          if (filter_var($a_redundant['if_ip'], FILTER_VALIDATE_IP)) {
            $ping = " class=\"ui-state-highlight\" ";
            $checked = "&#x2713;&nbsp;";
            if (ping($a_redundant['if_ip'])) {
              $ping = " class=\"ui-state-error\" ";
              $checked = "&#x2717;&nbsp;";
            }
            $dns = gethostbyaddr($a_redundant['if_ip']);
          } else {
            $ping = " class=\"ui-widget-content\"";
            $checked = '';
            $dns = $a_redundant['if_ip'];
          }

# just general clean up - the lines below are quite complicated to accommodate networking.
          $if_ip     = "if_ip_"     . $a_redundant['if_id'];
          $if_mask   = "if_mask_"   . $a_redundant['if_id'];
          $if_gate   = "if_gate_"   . $a_redundant['if_id'];
          $if_vlan   = "if_vlan_"   . $a_redundant['if_id'];
          $if_switch = "if_switch_" . $a_redundant['if_id'];
          $if_port   = "if_port_"   . $a_redundant['if_id'];

# new method requested by Cameron
          $output .= "<tr id=\"" . $a_redundant['if_id'] . "\">";
          $output .=   "<td" . $class . ">&gt; " . $a_redundant['if_name'];
          $output .= "<input type=\"hidden\" id=\"if_ipcheck_" . $a_redundant['if_id'] . "\" value=\"" . $a_redundant['if_ipcheck'] . "\">";
          $output .= "<input type=\"hidden\" id=\"if_swcheck_" . $a_redundant['if_id'] . "\" value=\"" . $a_redundant['if_swcheck'] . "\">";
          $output .= "</td>";
          $output .=   "<td" . $class . ">" . $a_redundant['if_interface'] . "</td>";

          if ($a_redundant['if_ipcheck']) {
            $output .=   "<td id=\"val_ip_" . $a_redundant['if_id'] . "\">" . "<input type=\"text\" tabindex=\"" . $count++ . "\" id=\"" . $if_ip . "\" name=\"" . $if_ip . "\" value=\"" . $a_redundant['if_ip'] . "\" size=\"12\"  onkeydown=\"javascript: if (event.keyCode == 9) {inet_defaults(" . $a_redundant['if_id'] . ");}\" onchange=\"validate_Form();\"></td>";
            $output .=   "<td" . $ping  . ">" . $checked . $dns . "</td>";

            $output .= "  <td id=\"val_mask_" . $a_redundant['if_id'] . "\"><select id=\"" . $if_mask . "\" name=\"" . $if_mask . "\" onchange=\"validate_Form();\">\n";

            for ($i = 0; $i < 129; $i++) {
              if ($i > 32) {
                if ($a_redundant['if_mask'] == $i) {
                  $output .=  "<option selected value=\"" . $i . "\">IPv6/" . $i . "</option>\n";
                } else {
                  $output .=  "<option value=\"" . $i . "\">IPv6/" . $i . "</option>\n";
                }
              } else {
                if ($a_redundant['if_mask'] == $i) {
                  $output .=  "<option selected value=\"" . $i . "\">" . createNetmaskAddr($i) . "/" . $i . "</option>\n";
                } else {
                  $output .=  "<option value=\"" . $i . "\">" . createNetmaskAddr($i) . "/" . $i . "</option>\n";
                }
              }
            }
            $output .=   "<td id=\"val_gate_" . $a_redundant['if_id'] . "\">" . "<input type=\"text\" id=\"" . $if_gate . "\" name=\"" . $if_gate . "\" value=\"" . $a_redundant['if_gate'] . "\" size=\"12\" onchange=\"validate_Form();\"></td>";
            $output .=   "<td id=\"val_vlan_" . $a_redundant['if_id'] . "\">" . "<input type=\"text\" id=\"" . $if_vlan . "\" name=\"" . $if_vlan . "\" value=\"" . $a_redundant['if_vlan'] . "\" size=\"10\" onchange=\"validate_Form();\"></td>";

# if any of the required fields are not set, set if_save to zero.
            if ($a_redundant['if_ip'] == '') {
              $submit = 0;
            }
            if ($a_redundant['if_mask'] == 0) {
              $submit = 0;
            }
            if ($a_redundant['if_gate'] == '') {
              $submit = 0;
            }
            if ($a_redundant['if_vlan'] == '') {
              $submit = 0;
            }

          } else {
            $output .=   "<td id=\"val_ip_" . $a_redundant['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_ip . "\" name=\"" . $if_ip . "\" value=\"\"></td>";
            $output .=   "<td" . $class . ">&nbsp;</td>";
            $output .=   "<td id=\"val_mask_" . $a_redundant['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_mask . "\" name=\"" . $if_mask . "\" value=\"0\"></td>";
            $output .=   "<td id=\"val_gate_" . $a_redundant['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_gate . "\" name=\"" . $if_gate . "\" value=\"\"></td>";
            $output .=   "<td id=\"val_vlan_" . $a_redundant['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_vlan . "\" name=\"" . $if_vlan . "\" value=\"\"></td>";
          }

          $output .= "  <td" . $class . ">" . $a_redundant['zone_name']    . "</td>";

          if ($virtual) {
            $output .= "<input type=\"hidden\" id=\"" . $if_switch . "\" name=\"" . $if_switch . "\" value=\"\">\n";
            $output .= "<input type=\"hidden\" id=\"" . $if_port   . "\" name=\"" . $if_port   . "\" value=\"\">\n";
          } else {
            if ($a_redundant['if_swcheck']) {
              $output .= "  <td id=\"val_switch_" . $a_redundant['if_id'] . "\">" . "<input type=\"text\" id=\"" . $if_switch . "\" name=\"" . $if_switch . "\" value=\"" . $a_redundant['if_switch'] . "\" size=\"10\" onchange=\"validate_Form();\"></td>";
              $output .= "  <td id=\"val_port_"   . $a_redundant['if_id'] . "\">" . "<input type=\"text\" id=\"" . $if_port   . "\" name=\"" . $if_port   . "\" value=\"" . $a_redundant['if_port']   . "\" size=\"10\" onchange=\"validate_Form();\"></td>";

# if any of the required switch fields are missing, set if_save to zero
              if ($a_redundant['if_switch'] == '') {
                $submit = 0;
              }
              if ($a_redundant['if_port'] == '') {
                $submit = 0;
              }

            } else {
              $output .= "<td id=\"val_switch_" . $a_redundant['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_switch . "\" name=\"" . $if_switch . "\" value=\"\"></td>\n";
              $output .= "<td id=\"val_port_" . $a_redundant['if_id'] . "\"><input type=\"hidden\" id=\"" . $if_port   . "\" name=\"" . $if_port   . "\" value=\"\"></td>\n";
            }
            $output .= "  <td" . $class . ">" . $a_redundant['med_text']     . "</td>";
            $output .= "  <td" . $class . ">" . $a_redundant['spd_text']     . "</td>";
            $output .= "  <td" . $class . ">" . $a_redundant['dup_text']     . "</td>";
            $output .= "  <td" . $class . ">" . $a_redundant['red_text']     . "</td>";
          }
          $output .= "  <td" . $class . ">" . $a_redundant['itp_acronym']  . "</td>";
          $output .= "</tr>";
        }
      }

      $output .= "</table>";

      mysqli_free_result($q_rsdp_interface);

      print "document.getElementById('interface_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

      print "document.rsdp.if_save.value = " . $submit . ";\n";

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
