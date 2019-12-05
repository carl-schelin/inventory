<?php
# Script: network.interface.php
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
    $package = "network.interface.php";
    $formVars['id']       = clean($_GET['id'],       10);
    $formVars['status']   = clean($_GET['status'],   10);
    $formVars['type']     = clean($_GET['type'],     10);
    $formVars['select']   = clean($_GET['select'],   60);

    $formVars['interface'] = substr($formVars['id'], 3);

    if (check_userlevel($AL_Edit)) {

# get the rsdp id for a quick test
      $q_string  = "select if_rsdp ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_id = " . $formVars['interface'] . " ";
      $q_rsdp_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_rsdp_interface = mysql_fetch_array($q_rsdp_interface);

# next check to see if the person is a requestor, platform POC, or an admin; if not, zero out 'type' so no changes can be made.
      $q_string  = "select rsdp_requestor,rsdp_platformspoc,rsdp_networkpoc,rsdp_platform ";
      $q_string .= "from rsdp_server ";
      $q_string .= "where rsdp_id = " . $a_rsdp_interface['if_rsdp'] . " ";
      $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

      $flag = 0;
      if ($_SESSION['uid'] == $a_rsdp_server['rsdp_requestor']) {
        $flag = $formVars['type'];
      }
      if ($_SESSION['uid'] == $a_rsdp_server['rsdp_requestor']) {
        $flag = $formVars['type'];
      }
      if ($_SESSION['uid'] == $a_rsdp_server['rsdp_platformspoc']) {
        $flag = $formVars['type'];
      }

      if ($_SESSION['id'] == $a_rsdp_server['rsdp_networkpoc']) {
        $flag = $formVars['type'];
      }
      if ($_SESSION['group'] == $a_rsdp_server['rsdp_platform']) {
        $flag = $formVars['type'];
      }

# if the person coming here is in the Networking group
      if ($_SESSION['group'] == $GRP_Networking) {
        $flag = $formVars['type'];
      }

      $formVars['type'] = $flag;


# Process when clicked to change:
# 1. change trigger status 1 (show) to 0 (clear) so clicking again will clear it.
# 2. clear field
# 3. Build new element (input, select, etc) with onselect; trigger
# 4. Present element
# 5. when selected;
# 6. clear element
# 7. update with new text


      if ($formVars['type'] == 14) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_name\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_name\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Name('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"15\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_name').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Name('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_name = '" . $formVars['select'] . "' ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }


      if ($formVars['type'] == 15) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\""   . $formVars['id'] . "_acronym\");\n";
          print "selbox.setAttribute(\"name\",\"" . $formVars['id'] . "_acronym\");\n";
          print "selbox.setAttribute(\"onchange\",\"select_Acronym('" . $formVars['id'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"select_Acronym('" . $formVars['id'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select itp_id,itp_name,itp_acronym ";
          $q_string .= "from inttype ";
          $q_string .= "order by itp_name ";
          $q_inttype = mysql_query($q_string) or die($q_string . ": " . mysql_error());

// create the javascript bit for populating the user dropdown box.
          while ($a_inttype = mysql_fetch_array($q_inttype) ) {
            print "if (celltext == \"" . $a_inttype['itp_acronym'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysql_real_escape_string($a_inttype['itp_name']) . "\"," . $a_inttype['itp_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysql_real_escape_string($a_inttype['itp_name']) . "\"," . $a_inttype['itp_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_acronym').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Acronym('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "select itp_id,itp_acronym ";
          $q_string .= "from inttype ";
          $q_string .= "where itp_id = " . $formVars['select'] . " ";
          $q_inttype = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          if (mysql_num_rows($q_inttype) > 0) {
            $a_inttype = mysql_fetch_array($q_inttype);
          } else {
            $a_inttype['itp_id']   = 0;
            $a_inttype['itp_acronym'] = "Unassigned";
          }

          $display = $a_inttype['itp_acronym'];

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_type = " . $a_inttype['itp_id'] . " ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }


      if ($formVars['type'] == 16) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_description\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_description\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Description('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"15\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_description').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Description('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_interface = '" . $formVars['select'] . "' ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }


      if ($formVars['type'] == 18) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_address\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_address\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Address('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"16\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_address').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Address('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_ip = '" . $formVars['select'] . "' ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }


      if ($formVars['type'] == 19) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\""   . $formVars['id'] . "_zone\");\n";
          print "selbox.setAttribute(\"name\",\"" . $formVars['id'] . "_zone\");\n";
          print "selbox.setAttribute(\"onchange\",\"select_Zone('" . $formVars['id'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"select_Zone('" . $formVars['id'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select zone_id,zone_name ";
          $q_string .= "from ip_zones ";
          $q_string .= "order by zone_name ";
          $q_ip_zones = mysql_query($q_string) or die($q_string . ": " . mysql_error());

// create the javascript bit for populating the user dropdown box.
          while ($a_ip_zones = mysql_fetch_array($q_ip_zones) ) {
            print "if (celltext == \"" . $a_ip_zones['zone_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysql_real_escape_string($a_ip_zones['zone_name']) . "\"," . $a_ip_zones['zone_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysql_real_escape_string($a_ip_zones['zone_name']) . "\"," . $a_ip_zones['zone_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_zone').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Zone('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "select zone_id,zone_name ";
          $q_string .= "from ip_zones ";
          $q_string .= "where zone_id = " . $formVars['select'] . " ";
          $q_ip_zones = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          if (mysql_num_rows($q_ip_zones) > 0) {
            $a_ip_zones = mysql_fetch_array($q_ip_zones);
          } else {
            $a_ip_zones['zone_id']   = 0;
            $a_ip_zones['zone_name'] = "Unassigned";
          }

          $display = $a_ip_zones['zone_name'];

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_zone = " . $a_ip_zones['zone_id'] . " ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }


      if ($formVars['type'] == 20) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_gateway\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_gateway\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Gateway('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"16\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_gateway').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Gateway('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_gate = '" . $formVars['select'] . "' ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }


      if ($formVars['type'] == 21) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_device\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_device\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Device('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"16\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_device').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Device('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_sysport = '" . $formVars['select'] . "' ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }


      if ($formVars['type'] == 22) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\""   . $formVars['id'] . "_media\");\n";
          print "selbox.setAttribute(\"name\",\"" . $formVars['id'] . "_media\");\n";
          print "selbox.setAttribute(\"onchange\",\"select_Media('" . $formVars['id'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"select_Media('" . $formVars['id'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select med_id,med_text ";
          $q_string .= "from int_media ";
          $q_string .= "order by med_text ";
          $q_int_media = mysql_query($q_string) or die($q_string . ": " . mysql_error());

// create the javascript bit for populating the user dropdown box.
          while ($a_int_media = mysql_fetch_array($q_int_media) ) {
            print "if (celltext == \"" . $a_int_media['med_text'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysql_real_escape_string($a_int_media['med_text']) . "\"," . $a_int_media['med_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysql_real_escape_string($a_int_media['med_text']) . "\"," . $a_int_media['med_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_media').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Media('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "select med_id,med_text ";
          $q_string .= "from int_media ";
          $q_string .= "where med_id = " . $formVars['select'] . " ";
          $q_int_media = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          if (mysql_num_rows($q_int_media) > 0) {
            $a_int_media = mysql_fetch_array($q_int_media);
          } else {
            $a_int_media['med_id']   = 0;
            $a_int_media['med_text'] = "Unassigned";
          }

          $display = $a_int_media['med_text'];

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_media = " . $a_int_media['med_id'] . " ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }


      if ($formVars['type'] == 23) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_switch\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_switch\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Switch('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"16\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_switch').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Switch('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_switch = '" . $formVars['select'] . "' ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }


      if ($formVars['type'] == 24) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_port\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_port\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Port('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"16\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_port').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Port('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_port = '" . $formVars['select'] . "' ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }


      if ($formVars['type'] == 25) {
        if ($formVars['status'] == 1) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "if (celltext == '') {\n";
          print "  celltext = 24;\n";
          print "}\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\""   . $formVars['id'] . "_netmask\");\n";
          print "selbox.setAttribute(\"name\",\"" . $formVars['id'] . "_netmask\");\n";
          print "selbox.setAttribute(\"onchange\",\"select_Netmask('" . $formVars['id'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"select_Netmask('" . $formVars['id'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          for ($i = 0; $i < 129; $i++) {
            print "if (celltext == " . $i . ") {\n";
            if ($i > 32) {
              print "  selbox.options[selbox.options.length] = new Option(\"" . mysql_real_escape_string("IPv6/" . $i) . "\"," . $i . ",1,1);\n";
            } else {
              print "  selbox.options[selbox.options.length] = new Option(\"" . mysql_real_escape_string(createNetmaskAddr($i) . "/" . $i) . "\"," . $i . ",1,1);\n";
            }
            print "} else {\n";
            if ($i > 32) {
              print "  selbox.options[selbox.options.length] = new Option(\"" . mysql_real_escape_string("IPv6/" . $i) . "\"," . $i . ",0,0);\n";
            } else {
              print "  selbox.options[selbox.options.length] = new Option(\"" . mysql_real_escape_string(createNetmaskAddr($i) . "/" . $i) . "\"," . $i . ",0,0);\n";
            }
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_netmask').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_Netmask('" . $formVars['id'] . "');" . "\");\n";

          $display = $formVars['select'];

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_mask = " . $formVars['select'] . " ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }


      if ($formVars['type'] == 26) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\",\"\");\n";
          print "celltext = celltext.replace(\"</u>\",\"\");\n";
          print "celltext = celltext.replace(\"&nbsp;&nbsp;&nbsp;&nbsp;\",\"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_vlan\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_vlan\");\n";
          print "infield.setAttribute(\"onblur\",\"select_VLAN('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"10\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_vlan').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"interface_VLAN('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_interface ";
          $q_string .= "set ";
          $q_string .= "if_vlan = '" . $formVars['select'] . "' ";
          $q_string .= "where if_id = " . $formVars['interface'] . " ";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

          if ($formVars['select'] == '') {
            $formVars['select'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
