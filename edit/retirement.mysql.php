<?php
# Script: retire.mysql.php
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
    $package = "retire.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);
    $formVars['ret_companyid']  = clean($_GET['ret_companyid'],  10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['ret_companyid'] == '') {
      $formVars['ret_companyid'] = 0;
    }

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],           10);
        $formVars['fs_backup']    = clean($_GET['fs_backup'],    10);
        $formVars['fs_device']    = clean($_GET['fs_device'],    60);
        $formVars['fs_mount']     = clean($_GET['fs_mount'],     60);
        $formVars['fs_size']      = clean($_GET['fs_size'],      10);
        $formVars['fs_wwid']      = clean($_GET['fs_wwid'],     100);
        $formVars['fs_subsystem'] = clean($_GET['fs_subsystem'], 30);
        $formVars['fs_volume']    = clean($_GET['fs_volume'],   100);
        $formVars['fs_lun']       = clean($_GET['fs_lun'],       10);
        $formVars['fs_volid']     = clean($_GET['fs_volid'],     30);
        $formVars['fs_path']      = clean($_GET['fs_path'],      30);
        $formVars['fs_switch']    = clean($_GET['fs_switch'],    50);
        $formVars['fs_port']      = clean($_GET['fs_port'],      50);
        $formVars['fs_sysport']   = clean($_GET['fs_sysport'],   50);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['fs_backup'] == 'true') {
          $formVars['fs_backup'] = 1;
        } else {
          $formVars['fs_backup'] = 0;
        }
        if ($formVars['fs_size'] == '') {
          $formVars['fs_size'] = 0;
        }
        if ($formVars['fs_lun'] == '') {
          $formVars['fs_lun'] = 0;
        }

        if ($formVars['fs_companyid'] > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string = 
            "fs_companyid =   " . $formVars['fs_companyid'] . "," .
            "fs_backup    =   " . $formVars['fs_backup']    . "," .
            "fs_device    = \"" . $formVars['fs_device']    . "\"," .
            "fs_mount     = \"" . $formVars['fs_mount']     . "\"," .
            "fs_size      =   " . $formVars['fs_size']      . "," .
            "fs_wwid      = \"" . $formVars['fs_wwid']      . "\"," .
            "fs_subsystem = \"" . $formVars['fs_subsystem'] . "\"," .
            "fs_volume    = \"" . $formVars['fs_volume']    . "\"," .
            "fs_lun       =   " . $formVars['fs_lun']       . "," .
            "fs_volid     = \"" . $formVars['fs_volid']     . "\"," .
            "fs_path      = \"" . $formVars['fs_path']      . "\"," . 
            "fs_switch    = \"" . $formVars['fs_switch']    . "\"," . 
            "fs_port      = \"" . $formVars['fs_port']      . "\"," . 
            "fs_sysport   = \"" . $formVars['fs_sysport']   . "\"," . 
            "fs_verified  =   " . "0"                       . "," . 
            "fs_user      =   " . $_SESSION['uid']          . "," . 
            "fs_update    = \"" . date('Y-m-d')             . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into filesystem set fs_id = NULL," . $q_string;
            $message = "Filesystem added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update filesystem set " . $q_string . " where fs_id = " . $formVars['id'];
            $message = "Filesystem updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['fs_companyid']);

          mysql_query($query) or die($query . ": " . mysql_error());

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      if ($formVars['update'] == -2) {
        $formVars['copyfrom']     = clean($_GET['copyfrom'],     10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select fs_backup,fs_device,fs_mount,fs_size,fs_wwid,fs_subsystem,fs_volume,fs_lun,fs_volid,fs_path,fs_switch,fs_port,fs_sysport ";
          $q_string .= "from filesystem ";
          $q_string .= "where fs_companyid = " . $formVars['copyfrom'];
          $q_filesystem = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          while ($a_filesystem = mysql_fetch_array($q_filesystem)) {

            $q_string = 
              "fs_companyid =   " . $formVars['fs_companyid']     . "," .
              "fs_backup    =   " . $a_filesystem['fs_backup']    . "," .
              "fs_device    = \"" . $a_filesystem['fs_device']    . "\"," .
              "fs_mount     = \"" . $a_filesystem['fs_mount']     . "\"," .
              "fs_size      =   " . $a_filesystem['fs_size']      . "," .
              "fs_wwid      = \"" . $a_filesystem['fs_wwid']      . "\"," .
              "fs_subsystem = \"" . $a_filesystem['fs_subsystem'] . "\"," .
              "fs_volume    = \"" . $a_filesystem['fs_volume']    . "\"," .
              "fs_lun       =   " . $a_filesystem['fs_lun']       . "," .
              "fs_volid     = \"" . $a_filesystem['fs_volid']     . "\"," .
              "fs_path      = \"" . $a_filesystem['fs_path']      . "\"," .
              "fs_switch    = \"" . $a_filesystem['fs_switch']    . "\"," .
              "fs_port      = \"" . $a_filesystem['fs_port']      . "\"," .
              "fs_sysport   = \"" . $a_filesystem['fs_sysport']   . "\"";

            $query = "insert into filesystem set fs_id = NULL, " . $q_string;
            mysql_query($query) or die($query . ": " . mysql_error());
          }
        }
      }


      if ($formVars['update'] == -3) {
        logaccess($_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"fs_refresh\" value=\"Refresh Filesystem Listing\" onClick=\"javascript:attach_filesystem('filesystem.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"fs_update\"  value=\"Update Filesystem\" onClick=\"javascript:attach_filesystem('filesystem.mysql.php', 1);hideDiv('filesystem-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"fs_id\" value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"fs_addbtn\"  value=\"Add Filesystem\"    onClick=\"javascript:attach_filesystem('filesystem.mysql.php', 0);\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"copyitem\"  value=\"Copy Filesystem Table From:\" onClick=\"javascript:attach_filesystem('filesystem.mysql.php', -2);\">\n";
        $output .= "<select name=\"fs_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name";
        $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        while ($a_inventory = mysql_fetch_array($q_inventory)) {
          $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . htmlspecialchars($a_inventory['inv_name']) . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"4\">Filesystem Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\"><label>Back up? <input type=\"checkbox\" name=\"fs_backup\"></label></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Device:* <input type=\"text\" name=\"fs_device\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Mount Point:* <input type=\"text\" name=\"fs_mount\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Size:* <input type=\"text\" name=\"fs_size\" size=\"10\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"3\">SAN Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">WWID: <input type=\"text\" name=\"fs_wwid\" size=\"30\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Subsystem: <input type=\"text\" name=\"fs_subsystem\" size=\"30\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">LUN: <input type=\"text\" name=\"fs_lun\" size=\"10\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Volume: <input type=\"text\" name=\"fs_volume\" size=\"30\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">VolID: <input type=\"text\" name=\"fs_volid\" size=\"30\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Path: <input type=\"text\" name=\"fs_path\" size=\"10\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Switch: <input type=\"text\" name=\"fs_switch\" size=\"30\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Port: <input type=\"text\" name=\"fs_port\" size=\"10\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Server Port: <input type=\"text\" name=\"fs_sysport\" size=\"30\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('retire_form').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Filesystem Listing</th>\n";
      $output .=   "<th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('filesystem-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<div id=\"filesystem-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Filesystem Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Highlighted</strong> - Filesystems that are <span class=\"ui-state-highlight\">highlighted</span> are <strong>not</strong> being backed up if the Backup Form \"Include all filesystems\" checkbox is not checked.</li>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Clicking the <strong>x</strong> will delete this filesystem from this server.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a filesystem to toggle the form for editing.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Rows marked with a checkmark in the Updated column have been automatically captured where possible.</li>\n";
      $output .= "    <li>Click the <strong>Filesystem Management</strong> title bar to toggle the <strong>Filesystem Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Monitoring</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"button ui-widget-content\"><input type=\"button\" name=\"fs_refresh\" value=\"Generate Ticket\" onClick=\"javascript:attach_filesystem('retire.ticket.php', -1);\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\"><textarea id=\"monitoring\" name=\"monitoring\" cols=\"130\" rows=\"4\"";
      $output .= "  onKeyDown=\"textCounter(document.edit.monitoring, document.edit.monLen, 1024);\"";
      $output .= "  onKeyUp  =\"textCounter(document.edit.monitoring, document.edit.monLen, 1024);\">";
      $output .= "Please remove monitoring for the following systems/IPs:\n";
      $output .= "\n";
      $q_string  = "select int_server,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $formVars['ret_companyid'] . " ";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_interface = mysql_fetch_array($q_interface)) {
        if ($a_interface['int_addr'] != '' && $a_interface['int_server'] != 'localhost') {
          $output .= $a_interface['int_server'] . " - " . $a_interface['int_addr'] . "\n";
        }
      }
      $output .= "</textarea><br><input readonly type=\"text\" name=\"monLen\" size=\"5\" maxlength=\"5\" value=\"1024\"> characters left</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";
     

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Backups</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"button ui-widget-content\"><input type=\"button\" name=\"fs_refresh\" value=\"Generate Ticket\" onClick=\"javascript:attach_filesystem('retire.ticket.php', -1);\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\"><textarea id=\"backups\" name=\"backups\" cols=\"130\" rows=\"4\"";
      $output .= "  onKeyDown=\"textCounter(document.edit.backups, document.edit.bupLen, 1024);\"";
      $output .= "  onKeyUp  =\"textCounter(document.edit.backups, document.edit.bupLen, 1024);\">";
      $output .= "Please remove any backups for the following systems:\n";
      $output .= "\n";
      $q_string  = "select int_server,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $formVars['ret_companyid'] . " ";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_interface = mysql_fetch_array($q_interface)) {
        if ($a_interface['int_addr'] != '' && $a_interface['int_server'] != 'localhost') {
          $output .= $a_interface['int_server'] . " - " . $a_interface['int_addr'] . "\n";
        }
      }
      $output .= "</textarea><br><input readonly type=\"text\" name=\"bupLen\" size=\"5\" maxlength=\"5\" value=\"1024\"> characters left</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Network Engineering</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"button ui-widget-content\"><input type=\"button\" name=\"fs_refresh\" value=\"Generate Ticket\" onClick=\"javascript:attach_filesystem('retire.ticket.php', -1);\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\"><textarea id=\"networks\" name=\"networks\" cols=\"130\" rows=\"4\"";
      $output .= "  onKeyDown=\"textCounter(document.edit.networks, document.edit.netLen, 1024);\"";
      $output .= "  onKeyUp  =\"textCounter(document.edit.networks, document.edit.netLen, 1024);\">";
      $output .= "Please clear any entries for the following IPs and make them available for reallocation:\n";
      $output .= "\n";
      $q_string  = "select int_server,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $formVars['ret_companyid'] . " ";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_interface = mysql_fetch_array($q_interface)) {
        if ($a_interface['int_addr'] != '' && $a_interface['int_server'] != 'localhost') {
          $output .= $a_interface['int_server'] . " - " . $a_interface['int_addr'] . "\n";
        }
      }
      $output .= "</textarea><br><input readonly type=\"text\" name=\"netLen\" size=\"5\" maxlength=\"5\" value=\"1024\"> characters left</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";
     

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Information Security</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"button ui-widget-content\"><input type=\"button\" name=\"fs_refresh\" value=\"Generate Ticket\" onClick=\"javascript:attach_filesystem('retire.ticket.php', -1);\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\"><textarea id=\"infosec\" name=\"infosec\" cols=\"130\" rows=\"6\"";
      $output .= "  onKeyDown=\"textCounter(document.edit.infosec, document.edit.secLen, 1024);\"";
      $output .= "  onKeyUp  =\"textCounter(document.edit.infosec, document.edit.secLen, 1024);\">";
      $output .= "Please clear any firewall entries for the following servers/IP addresses.\n";
      $output .= "\n";
      $output .= "These systems are located in the Longmont Lab.\n";
      $output .= "\n";
      $q_string  = "select int_server,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $formVars['ret_companyid'] . " ";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_interface = mysql_fetch_array($q_interface)) {
        if ($a_interface['int_addr'] != '' && $a_interface['int_server'] != 'localhost') {
          $output .= $a_interface['int_server'] . " - " . $a_interface['int_addr'] . "\n";
        }
      }
      $output .= "</textarea><br><input readonly type=\"text\" name=\"secLen\" size=\"5\" maxlength=\"5\" value=\"1024\"> characters left</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";
     

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Network Engineering</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"button ui-widget-content\">";
      $output .= "<input type=\"button\" name=\"fs_refresh\" value=\"Generate Ticket\" onClick=\"javascript:attach_filesystem('retire.ticket.php', -1);\"></td>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\"><textarea id=\"neteng\" name=\"neteng\" cols=\"130\" rows=\"9\"";
      $output .= "  onKeyDown=\"textCounter(document.edit.neteng, document.edit.netengLen, 1024);\"";
      $output .= "  onKeyUp  =\"textCounter(document.edit.neteng, document.edit.netengLen, 1024);\">";
      $output .= "Please remove the following servers/IPs from DNS.\n";
      $output .= "\n";
      $output .= "Non-authoritative answer:\n";
      $output .= "Name:   level3ora.corp.intrado.pri\n";
      $output .= "Address: 10.105.80.119\n";
      $output .= "\n";
      $output .= "Non-authoritative answer:\n";
      $output .= "Name:   level3vdb.corp.intrado.pri\n";
      $output .= "Address: 10.100.44.65\n";
      $output .= "</textarea><br><input readonly type=\"text\" name=\"netengLen\" size=\"5\" maxlength=\"5\" value=\"1024\"> characters left</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Windows Admins</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"button ui-widget-content\">";
      $output .= "<input type=\"button\" name=\"fs_refresh\" value=\"Generate Ticket\" onClick=\"javascript:attach_filesystem('retire.ticket.php', -1);\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\"><textarea id=\"windows\" name=\"windows\" cols=\"130\" rows=\"9\"";
      $output .= "  onKeyDown=\"textCounter(document.edit.windows, document.edit.winLen, 1024);\"";
      $output .= "  onKeyUp  =\"textCounter(document.edit.windows, document.edit.winLen, 1024);\">";
      $output .= "Please remove the following servers/IPs from DNS.\n";
      $output .= "\n";
      $output .= "Non-authoritative answer:\n";
      $output .= "Name:   level3ora.corp.intrado.pri\n";
      $output .= "Address: 10.105.80.119\n";
      $output .= "\n";
      $output .= "Non-authoritative answer:\n";
      $output .= "Name:   level3vdb.corp.intrado.pri\n";
      $output .= "Address: 10.100.44.65\n";
      $output .= "</textarea><br><input readonly type=\"text\" name=\"winLen\" size=\"5\" maxlength=\"5\" value=\"1024\"> characters left</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Data Center</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"button ui-widget-content\"><input type=\"button\" name=\"fs_refresh\" value=\"Generate Ticket\" onClick=\"javascript:attach_filesystem('retire.ticket.php', -1);\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\"><textarea id=\"datacenter\" name=\"datacenter\" cols=\"130\" rows=\"24\"";
      $output .= "  onKeyDown=\"textCounter(document.edit.datacenter, document.edit.cenLen, 1024);\"";
      $output .= "  onKeyUp  =\"textCounter(document.edit.datacenter, document.edit.cenLen, 1024);\">";
      $output .= "Please uncable and remove the following system. Please deliver the system to my desk at 1K3005 for disposal.\n";
      $output .= "\n";
      $output .= "incojs01$ inventory -f -d incoss02\n";
      $output .= "--------------------\n";
      $output .= "Inventory Management\n";
      $output .= "--------------------\n";
      $output .= "Server: incoss02\n";
      $output .= "Function: Subversion/Scarab Support\n";
      $output .= "Product: Subversion/Scarab\n";
      $output .= "Managed By: UNIX System Administration\n";
      $output .= "----------------------------\n";
      $output .= "Primary Hardware Information\n";
      $output .= "----------------------------\n";
      $output .= "Vendor: Dell\n";
      $output .= "Model: PowerEdge 1850\n";
      $output .= "Serial Number: 9211360393\n";
      $output .= "Asset Tag: L.011371\n";
      $output .= "Dell Service Tag: 48C7F61\n";
      $output .= "--------------------\n";
      $output .= "Location Information\n";
      $output .= "--------------------\n";
      $output .= "Data Center: Intrado Production Data Center - Longmont\n";
      $output .= "Address: 1601 Dry Creek Drive Longmont Colorado 80503\n";
      $output .= "Rack/Unit: H-71/U24\n";
      $output .= "</textarea><br><input readonly type=\"text\" name=\"cenLen\" size=\"5\" maxlength=\"5\" value=\"1024\"> characters left</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Virtualization</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"button ui-widget-content\"><input type=\"button\" name=\"fs_refresh\" value=\"Generate Ticket\" onClick=\"javascript:attach_filesystem('retire.ticket.php', -1);\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\"><textarea id=\"virtualization\" name=\"virtualization\" cols=\"130\" rows=\"5\"";
      $output .= "  onKeyDown=\"textCounter(document.edit.virtualization, document.edit.virtLen, 1024);\"";
      $output .= "  onKeyUp  =\"textCounter(document.edit.virtualization, document.edit.virtLen, 1024);\">";
      $output .= "Please archive and destroy the following Virtual Machine.\n";
      $output .= "\n";
      $output .= "Virtual Machine is located in the Longmont Lab.\n";
      $output .= "\n";
      $output .= "level3ora - 10.105.80.119\n";
      $output .= "</textarea><br><input readonly type=\"text\" name=\"virtLen\" size=\"5\" maxlength=\"5\" value=\"1024\"> characters left</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Storage</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"button ui-widget-content\"><input type=\"button\" name=\"fs_refresh\" value=\"Generate Ticket\" onClick=\"javascript:attach_filesystem('retire.ticket.php', -1);\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\"><textarea id=\"storage\" name=\"storage\" cols=\"130\" rows=\"4\"";
      $output .= "  onKeyDown=\"textCounter(document.edit.storage, document.edit.sanLen, 1024);\"";
      $output .= "  onKeyUp  =\"textCounter(document.edit.storage, document.edit.sanLen, 1024);\">";
      $output .= "Please recover any SAN storage from the following system.\n";
      $output .= "\n";
      $output .= "level3ora - 10.105.80.119\n";
      $output .= "</textarea><br><input readonly type=\"text\" name=\"sanLen\" size=\"5\" maxlength=\"5\" value=\"1024\"> characters left</td>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";


      print "document.getElementById('retire_table').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
