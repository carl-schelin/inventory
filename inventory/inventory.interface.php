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
      $q_string .= "from inv_inventory ";
      $q_string .= "where inv_id = " . $formVars['id'] . " ";
      $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_inv_inventory = mysqli_fetch_array($q_inv_inventory);

# if not a member of the group that can edit this server, default to zero which bypasses all the edit functions.
      if (check_grouplevel($db, $a_inv_inventory['inv_manager']) == 0) {
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

# need to determine if it's a child to see if I need to add the "< " back in to the output.
          $q_string  = "select int_int_id,int_virtual ";
          $q_string .= "from inv_interface ";
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
          $q_string .= "inv_interface ";
          $q_string .= "set ";
          $q_string .= "int_domain = '" . $formVars['select'] . "' ";
          $q_string .= "where int_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '" . $child . "<u>" . $formVars['select'] . "</u>" . $virtual . "';\n";

        }
      }

# edit the domain name field
      if ($formVars['function'] == 'fdn') {
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

# need to determine if it's a child to see if I need to add the "< " back in to the output.
          $q_string  = "select int_int_id,int_virtual ";
          $q_string .= "from inv_interface ";
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
          $q_string .= "inv_interface ";
          $q_string .= "set ";
          $q_string .= "int_domain = '" . $formVars['select'] . "' ";
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
        $q_string .= "from inv_interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_inv_interface = mysqli_fetch_array($q_inv_interface);

        if ($a_inv_interface['int_management']) {
          $a_inv_interface['int_management'] = 0;
        } else {
          $a_inv_interface['int_management'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "inv_interface ";
        $q_string .= "set ";
        $q_string .= "int_management = " . $a_inv_interface['int_management'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_inv_interface['int_management']) {
          print "document.getElementById('" . $cellid . "').checked = true;\n";
        } else {
          print "document.getElementById('" . $cellid . "').checked = false;\n";
        }
      }

# check or uncheck the ssh checkbox
      if ($formVars['function'] == 'fsh') {
        $q_string  = "select int_login ";
        $q_string .= "from inv_interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_inv_interface = mysqli_fetch_array($q_inv_interface);

        if ($a_inv_interface['int_login']) {
          $a_inv_interface['int_login'] = 0;
        } else {
          $a_inv_interface['int_login'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "inv_interface ";
        $q_string .= "set ";
        $q_string .= "int_login = " . $a_inv_interface['int_login'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_inv_interface['int_login']) {
          print "document.getElementById('" . $cellid . "').checked = true;\n";
        } else {
          print "document.getElementById('" . $cellid . "').checked = false;\n";
        }
      }

# check or uncheck the backup checkbox
      if ($formVars['function'] == 'fbu') {
        $q_string  = "select int_backup ";
        $q_string .= "from inv_interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_inv_interface = mysqli_fetch_array($q_inv_interface);

        if ($a_inv_interface['int_backup']) {
          $a_inv_interface['int_backup'] = 0;
        } else {
          $a_inv_interface['int_backup'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "inv_interface ";
        $q_string .= "set ";
        $q_string .= "int_backup = " . $a_inv_interface['int_backup'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_inv_interface['int_backup']) {
          print "document.getElementById('" . $cellid . "').checked = true;\n";
        } else {
          print "document.getElementById('" . $cellid . "').checked = false;\n";
        }
      }

# check or uncheck the openview checkbox
      if ($formVars['function'] == 'fov') {
        $q_string  = "select int_openview ";
        $q_string .= "from inv_interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_inv_interface = mysqli_fetch_array($q_inv_interface);

        if ($a_inv_interface['int_openview']) {
          $a_inv_interface['int_openview'] = 0;
        } else {
          $a_inv_interface['int_openview'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "inv_interface ";
        $q_string .= "set ";
        $q_string .= "int_openview = " . $a_inv_interface['int_openview'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_inv_interface['int_openview']) {
          print "document.getElementById('" . $cellid . "').checked = true;\n";
        } else {
          print "document.getElementById('" . $cellid . "').checked = false;\n";
        }
      }

# check or uncheck the nagios checkbox
      if ($formVars['function'] == 'fng') {
        $q_string  = "select int_nagios ";
        $q_string .= "from inv_interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_inv_interface = mysqli_fetch_array($q_inv_interface);

        if ($a_inv_interface['int_nagios']) {
          $a_inv_interface['int_nagios'] = 0;
        } else {
          $a_inv_interface['int_nagios'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "inv_interface ";
        $q_string .= "set ";
        $q_string .= "int_nagios = " . $a_inv_interface['int_nagios'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_inv_interface['int_nagios']) {
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

          $q_string  = "select itp_id,itp_name,itp_acronym ";
          $q_string .= "from inv_int_types ";
          $q_string .= "order by itp_name ";
          $q_inv_int_types = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

// create the javascript bit for populating the user dropdown box.
          while ($a_inv_int_types = mysqli_fetch_array($q_inv_int_types) ) {
            print "if (celltext == \"" . $a_inv_int_types['itp_acronym'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_int_types['itp_name']) . "\"," . $a_inv_int_types['itp_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_int_types['itp_name']) . "\"," . $a_inv_int_types['itp_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select itp_id,itp_acronym ";
          $q_string .= "from inv_int_types ";
          $q_string .= "where itp_id = " . $formVars['select'] . " ";
          $q_inv_int_types = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_int_types) > 0) {
            $a_inv_int_types = mysqli_fetch_array($q_inv_int_types);
          } else {
            $a_inv_int_types['itp_id']   = 0;
            $a_inv_int_types['itp_acronym'] = "Unassigned";
          }

          $display = $a_inv_int_types['itp_acronym'];

          $q_string  = "update ";
          $q_string .= "inv_interface ";
          $q_string .= "set ";
          $q_string .= "int_type = " . $a_inv_int_types['itp_id'] . " ";
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inv_interface ";
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

# edit the mac address field
      if ($formVars['function'] == 'fae') {
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inv_interface ";
          $q_string .= "set ";
          $q_string .= "int_eth = '" . $formVars['select'] . "' ";
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inv_interface ";
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $display = $formVars['select'];

          $q_string  = "update ";
          $q_string .= "inv_interface ";
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

          $q_string  = "select zone_id,zone_zone ";
          $q_string .= "from inv_net_zones ";
          $q_string .= "order by zone_zone ";
          $q_inv_net_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

// create the javascript bit for populating the user dropdown box.
          while ($a_inv_net_zones = mysqli_fetch_array($q_inv_net_zones) ) {
            print "if (celltext == \"" . $a_inv_net_zones['zone_zone'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_net_zones['zone_zone']) . "\"," . $a_inv_net_zones['zone_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_net_zones['zone_zone']) . "\"," . $a_inv_net_zones['zone_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select zone_id,zone_zone ";
          $q_string .= "from inv_net_zones ";
          $q_string .= "where zone_id = " . $formVars['select'] . " ";
          $q_inv_net_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_net_zones) > 0) {
            $a_inv_net_zones = mysqli_fetch_array($q_inv_net_zones);
          } else {
            $a_inv_net_zones['zone_id']   = 0;
            $a_inv_net_zones['zone_zone'] = "Unassigned";
          }

          $display = $a_inv_net_zones['zone_zone'];

          $q_string  = "update ";
          $q_string .= "inv_interface ";
          $q_string .= "set ";
          $q_string .= "int_zone = " . $a_inv_net_zones['zone_id'] . " ";
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inv_interface ";
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . "'," . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inv_interface ";
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inv_interface ";
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

          $q_string  = "select med_id,med_text ";
          $q_string .= "from inv_int_media ";
          $q_string .= "order by med_text ";
          $q_inv_int_media = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

// create the javascript bit for populating the user dropdown box.
          while ($a_inv_int_media = mysqli_fetch_array($q_inv_int_media) ) {
            print "if (celltext == \"" . $a_inv_int_media['med_text'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_int_media['med_text']) . "\"," . $a_inv_int_media['med_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_int_media['med_text']) . "\"," . $a_inv_int_media['med_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select med_id,med_text ";
          $q_string .= "from inv_int_media ";
          $q_string .= "where med_id = " . $formVars['select'] . " ";
          $q_inv_int_media = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_int_media) > 0) {
            $a_inv_int_media = mysqli_fetch_array($q_inv_int_media);
          } else {
            $a_inv_int_media['med_id']   = 0;
            $a_inv_int_media['med_text'] = "Unassigned";
          }

          $display = $a_inv_int_media['med_text'];

          $q_string  = "update ";
          $q_string .= "inv_interface ";
          $q_string .= "set ";
          $q_string .= "inv_int_media = " . $a_inv_int_media['med_id'] . " ";
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inv_interface ";
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Interface(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inv_interface ";
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
