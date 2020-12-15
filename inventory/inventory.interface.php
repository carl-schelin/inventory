<?php
# Script: inventory.interface.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "inventory.interface.php";
    $formVars['id']       = clean($_GET['id'],       10);
    $formVars['function'] = clean($_GET['function'], 10);
    $formVars['status']   = clean($_GET['status'],   10);
    $formVars['select']   = clean($_GET['select'],   60);

    if (check_userlevel($db, $AL_Edit)) {

# check to see if the person editing is a member of a group that can edit this information; if not, blank out 'function' so no changes can be made.
      $q_string  = "select inv_manager ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['id'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_inventory = mysqli_fetch_array($q_inventory);

# if not a member of the group that can edit this server, default to zero which bypasses all the edit functions.
      if (check_grouplevel($db, $a_inventory['inv_manager']) == 0) {
        $formVars['function'] = '';
      }

      $cellid = $formVars['function'] . $formVars['id'];

# Process when clicked to change:
# 1. change trigger status 1 (show) to 0 (clear) so clicking again will clear it.
# 2. clear field
# 3. Build new element (input, select, etc) with onselect; trigger
# 4. Present element
# 5. when selected;
# 6. clear element
# 7. update with new text

# edit the server name field
      if ($formVars['function'] == 'fsn') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&gt; \",\"\");\n";
          print "celltext = celltext.replace(\" (v)\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"15\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

# need to determine if it's a child to see if I need to add the "< " back in to the output.
          $q_string  = "select int_int_id,int_virtual ";
          $q_string .= "from interface ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $q_intcheck = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_intcheck = mysqli_fetch_array($q_intcheck);

          $child = '';
          if ($a_intcheck['int_int_id'] > 0) {
            $child = '< ';
          }

          $virtual = '';
          if ($a_intcheck['int_virtual'] > 0) {
            $virtual = ' (v)';
          }

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_server = '" . $formVars['select'] . "' ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '" . $child . "<u>" . $formVars['select'] . "</u>" . $virtual . "';\n";

        }
      }

# check or uncheck the management checkbox
      if ($formVars['function'] == 'fmg') {
        $q_string  = "select int_management ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_management']) {
          $a_interface['int_management'] = 0;
        } else {
          $a_interface['int_management'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "interface ";
        $q_string .= "set ";
        $q_string .= "int_management = " . $a_interface['int_management'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_interface['int_management']) {
          print "document.getElementById('" . $cellid . "').checked = true;\n";
        } else {
          print "document.getElementById('" . $cellid . "').checked = false;\n";
        }
      }

# check or uncheck the ssh checkbox
      if ($formVars['function'] == 'fsh') {
        $q_string  = "select int_login ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_login']) {
          $a_interface['int_login'] = 0;
        } else {
          $a_interface['int_login'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "interface ";
        $q_string .= "set ";
        $q_string .= "int_login = " . $a_interface['int_login'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_interface['int_login']) {
          print "document.getElementById('" . $cellid . "').checked = true;\n";
        } else {
          print "document.getElementById('" . $cellid . "').checked = false;\n";
        }
      }

# check or uncheck the backup checkbox
      if ($formVars['function'] == 'fbu') {
        $q_string  = "select int_backup ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_backup']) {
          $a_interface['int_backup'] = 0;
        } else {
          $a_interface['int_backup'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "interface ";
        $q_string .= "set ";
        $q_string .= "int_backup = " . $a_interface['int_backup'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_interface['int_backup']) {
          print "document.getElementById('" . $cellid . "').checked = true;\n";
        } else {
          print "document.getElementById('" . $cellid . "').checked = false;\n";
        }
      }

# check or uncheck the openview checkbox
      if ($formVars['function'] == 'fov') {
        $q_string  = "select int_openview ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_openview']) {
          $a_interface['int_openview'] = 0;
        } else {
          $a_interface['int_openview'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "interface ";
        $q_string .= "set ";
        $q_string .= "int_openview = " . $a_interface['int_openview'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_interface['int_openview']) {
          print "document.getElementById('" . $cellid . "').checked = true;\n";
        } else {
          print "document.getElementById('" . $cellid . "').checked = false;\n";
        }
      }

# check or uncheck the nagios checkbox
      if ($formVars['function'] == 'fng') {
        $q_string  = "select int_nagios ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_nagios']) {
          $a_interface['int_nagios'] = 0;
        } else {
          $a_interface['int_nagios'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "interface ";
        $q_string .= "set ";
        $q_string .= "int_nagios = " . $a_interface['int_nagios'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_interface['int_nagios']) {
          print "document.getElementById('" . $cellid . "').checked = true;\n";
        } else {
          print "document.getElementById('" . $cellid . "').checked = false;\n";
        }
      }

# edit the interface type field
      if ($formVars['function'] == 'fia') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\"edit_data\");\n";
          print "selbox.setAttribute(\"name\",\"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select itp_id,itp_name,itp_acronym ";
          $q_string .= "from inttype ";
          $q_string .= "order by itp_name ";
          $q_inttype = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

// create the javascript bit for populating the user dropdown box.
          while ($a_inttype = mysqli_fetch_array($q_inttype) ) {
            print "if (celltext == \"" . $a_inttype['itp_acronym'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inttype['itp_name']) . "\"," . $a_inttype['itp_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inttype['itp_name']) . "\"," . $a_inttype['itp_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select itp_id,itp_acronym ";
          $q_string .= "from inttype ";
          $q_string .= "where itp_id = " . $formVars['select'] . " ";
          $q_inttype = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inttype) > 0) {
            $a_inttype = mysqli_fetch_array($q_inttype);
          } else {
            $a_inttype['itp_id']   = 0;
            $a_inttype['itp_acronym'] = "Unassigned";
          }

          $display = $a_inttype['itp_acronym'];

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_type = " . $a_inttype['itp_id'] . " ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }

# edit the interface name
      if ($formVars['function'] == 'ffc') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"15\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_face = '" . $formVars['select'] . "' ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }

# edit the ip address field
      if ($formVars['function'] == 'fad') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"16\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_addr = '" . $formVars['select'] . "' ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }

# edit the network mask
      if ($formVars['function'] == 'fan') {
        if ($formVars['status'] == 1) {

          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "if (celltext == '') {\n";
          print "  celltext = 24;\n";
          print "}\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\"edit_data\");\n";
          print "selbox.setAttribute(\"name\",\"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          for ($i = 0; $i < 129; $i++) {
            print "if (celltext == " . $i . ") {\n";
            if ($i > 32) {
              print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, "IPv6/" . $i) . "\"," . $i . ",1,1);\n";
            } else {
              print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, createNetmaskAddr($i) . "/" . $i) . "\"," . $i . ",1,1);\n";
            }
            print "} else {\n";
            if ($i > 32) {
              print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, "IPv6/" . $i) . "\"," . $i . ",0,0);\n";
            } else {
              print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, createNetmaskAddr($i) . "/" . $i) . "\"," . $i . ",0,0);\n";
            }
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $display = $formVars['select'];

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_mask = " . $formVars['select'] . " ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }


# set the network zone
      if ($formVars['function'] == 'fzn') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\"edit_data\");\n";
          print "selbox.setAttribute(\"name\",\"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select zone_id,zone_name ";
          $q_string .= "from ip_zones ";
          $q_string .= "order by zone_name ";
          $q_ip_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

// create the javascript bit for populating the user dropdown box.
          while ($a_ip_zones = mysqli_fetch_array($q_ip_zones) ) {
            print "if (celltext == \"" . $a_ip_zones['zone_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_ip_zones['zone_name']) . "\"," . $a_ip_zones['zone_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_ip_zones['zone_name']) . "\"," . $a_ip_zones['zone_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select zone_id,zone_name ";
          $q_string .= "from ip_zones ";
          $q_string .= "where zone_id = " . $formVars['select'] . " ";
          $q_ip_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_ip_zones) > 0) {
            $a_ip_zones = mysqli_fetch_array($q_ip_zones);
          } else {
            $a_ip_zones['zone_id']   = 0;
            $a_ip_zones['zone_name'] = "Unassigned";
          }

          $display = $a_ip_zones['zone_name'];

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_zone = " . $a_ip_zones['zone_id'] . " ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }

# edit the gateway address
      if ($formVars['function'] == 'fgw') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"interface_Completed(". $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"16\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_gate = '" . $formVars['select'] . "' ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }

# edit the vlan information
      if ($formVars['function'] == 'fvl') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"10\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . "'," . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_vlan = '" . $formVars['select'] . "' ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }

# physical machines only:
# edit the switch port
      if ($formVars['function'] == 'fsp') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"16\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_sysport = '" . $formVars['select'] . "' ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }

# edit the media data for the interface
      if ($formVars['function'] == 'fmt') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\"edit_data\");\n";
          print "selbox.setAttribute(\"name\",\"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select med_id,med_text ";
          $q_string .= "from int_media ";
          $q_string .= "order by med_text ";
          $q_int_media = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

// create the javascript bit for populating the user dropdown box.
          while ($a_int_media = mysqli_fetch_array($q_int_media) ) {
            print "if (celltext == \"" . $a_int_media['med_text'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_int_media['med_text']) . "\"," . $a_int_media['med_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_int_media['med_text']) . "\"," . $a_int_media['med_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select med_id,med_text ";
          $q_string .= "from int_media ";
          $q_string .= "where med_id = " . $formVars['select'] . " ";
          $q_int_media = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_int_media) > 0) {
            $a_int_media = mysqli_fetch_array($q_int_media);
          } else {
            $a_int_media['med_id']   = 0;
            $a_int_media['med_text'] = "Unassigned";
          }

          $display = $a_int_media['med_text'];

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_media = " . $a_int_media['med_id'] . " ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }

# edit the switch name field
      if ($formVars['function'] == 'fsw') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"16\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_switch = '" . $formVars['select'] . "' ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }

# edit the switch port field
      if ($formVars['function'] == 'fpt') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"interface_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"16\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "interface ";
          $q_string .= "set ";
          $q_string .= "int_port = '" . $formVars['select'] . "' ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
