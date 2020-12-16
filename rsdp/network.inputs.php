<?php
# Script: network.inputs.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "network.inputs.php";
    $formVars['id']       = clean($_GET['id'],       10);
    $formVars['status']   = clean($_GET['status'],   10);
    $formVars['type']     = clean($_GET['type'],     10);
    $formVars['select']   = clean($_GET['select'],   60);

    $formVars['rsdp'] = substr($formVars['id'], 3);

    if (check_userlevel($db, $AL_Edit)) {

# next check to see if the person is a requestor, platform POC, or an admin; if not, zero out 'type' so no changes can be made.
      $q_string  = "select rsdp_requestor,rsdp_platformspoc,rsdp_platform ";
      $q_string .= "from rsdp_server ";
      $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
      $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

      if ($_SESSION['uid'] != $a_rsdp_server['rsdp_requestor'] && $_SESSION['uid'] != $a_rsdp_server['rsdp_platformspoc'] && $_SESSION['group'] != $a_rsdp_server['rsdp_platform']) {
        $formVars['type'] = 0;
      }

# Process when clicked to change:
# 1. change trigger status 1 (show) to 0 (clear) so clicking again will clear it.
# 2. clear field
# 3. Build new element (input, select, etc) with onselect; trigger
# 4. Present element
# 5. when selected;
# 6. clear element
# 7. update with new text

# 1 or 2 == Groups
# this will be a drop down.

      if ($formVars['type'] == 1 || $formVars['type'] == 2) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",      \"" . $formVars['id'] . "_groups\");\n";
          print "selbox.setAttribute(\"name\",    \"" . $formVars['id'] . "_groups\");\n";
          if ($formVars['type'] == 1) {
            print "selbox.setAttribute(\"onchange\",\"select_Systems('" . $formVars['id'] . "');\");\n";
            print "selbox.setAttribute(\"onblur\",\"select_Systems('" . $formVars['id'] . "');\");\n";
          } else {
            print "selbox.setAttribute(\"onchange\",\"select_Applications('" . $formVars['id'] . "');\");\n";
            print "selbox.setAttribute(\"onblur\",\"select_Applications('" . $formVars['id'] . "');\");\n";
          }

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select grp_id,grp_name ";
          $q_string .= "from groups ";
          $q_string .= "where grp_disabled = 0 ";
          $q_string .= "order by grp_name ";
          $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_groups = mysqli_fetch_array($q_groups) ) {
            print "if (celltext == \"" . $a_groups['grp_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($a_groups['grp_name']) . "\"," . $a_groups['grp_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($a_groups['grp_name']) . "\"," . $a_groups['grp_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_groups').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          if ($formVars['type'] == 1) {
            print "cell.setAttribute(\"onclick\", \"systems_Group('" . $formVars['id'] . "');" . "\");\n";
          } else {
            print "cell.setAttribute(\"onclick\", \"applications_Group('" . $formVars['id'] . "');" . "\");\n";
          }

          $q_string  = "select grp_id,grp_name ";
          $q_string .= "from groups ";
          $q_string .= "where grp_id = " . $formVars['select'] . " ";
          $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_groups) > 0) {
            $a_groups = mysqli_fetch_array($q_groups);
          } else {
            $a_groups['grp_id']   = 0;
            $a_groups['grp_name'] = "Unassigned";
          }

          $display = $a_groups['grp_name'];

          $q_string  = "update ";
          $q_string .= "rsdp_server ";
          $q_string .= "set ";
          if ($formVars['type'] == 1) {
            $q_string .= "rsdp_platform = " . $a_groups['grp_id'] . " ";
          }
          if ($formVars['type'] == 2) {
            $q_string .= "rsdp_application = " . $a_groups['grp_id'] . " ";
          }
          $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";

        }
      }

# 4 == service class
# this will be a drop down.
      if ($formVars['type'] == 4) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\""   . $formVars['id'] . "_service\");\n";
          print "selbox.setAttribute(\"name\",\"" . $formVars['id'] . "_service\");\n";
          print "selbox.setAttribute(\"onchange\",\"select_Service('" . $formVars['id'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"select_Service('" . $formVars['id'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select svc_id,svc_name ";
          $q_string .= "from service ";
          $q_string .= "order by svc_id ";
          $q_service = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_service = mysqli_fetch_array($q_service) ) {
            print "if (celltext == \"" . $a_service['svc_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($a_service['svc_name']) . "\"," . $a_service['svc_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($a_service['svc_name']) . "\"," . $a_service['svc_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_service').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"service_Class('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "select svc_id,svc_name ";
          $q_string .= "from service ";
          $q_string .= "where svc_id = " . $formVars['select'] . " ";
          $q_service = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_service) > 0) {
            $a_service = mysqli_fetch_array($q_service);
          } else {
            $a_service['svc_id']   = 0;
            $a_service['svc_name'] = "Unassigned";
          }

          $display = $a_service['svc_name'];

          $q_string  = "update ";
          $q_string .= "rsdp_server ";
          $q_string .= "set ";
          $q_string .= "rsdp_service = " . $a_service['svc_id'] . " ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }

# 5 == data centers
# this will be a drop down.
      if ($formVars['type'] == 5) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\""   . $formVars['id'] . "_location\");\n";
          print "selbox.setAttribute(\"name\",\"" . $formVars['id'] . "_location\");\n";
          print "selbox.setAttribute(\"onchange\",\"select_Location('" . $formVars['id'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"select_Location('" . $formVars['id'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select loc_id,loc_name ";
          $q_string .= "from locations ";
          $q_string .= "where loc_type = 1 ";
          $q_string .= "order by loc_name ";
          $q_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_locations = mysqli_fetch_array($q_locations) ) {
            print "if (celltext == \"" . $a_locations['loc_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($a_locations['loc_name']) . "\"," . $a_locations['loc_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($a_locations['loc_name']) . "\"," . $a_locations['loc_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_location').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"data_Center('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "select loc_id,loc_name ";
          $q_string .= "from locations ";
          $q_string .= "where loc_id = " . $formVars['select'] . " ";
          $q_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_locations) > 0) {
            $a_locations = mysqli_fetch_array($q_locations);
          } else {
            $a_locations['loc_id']   = 0;
            $a_locations['loc_name'] = "Unassigned";
          }

          $display = $a_locations['loc_name'];

          $q_string  = "update ";
          $q_string .= "rsdp_server ";
          $q_string .= "set ";
          $q_string .= "rsdp_location = " . $a_locations['loc_id'] . " ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }


      if ($formVars['type'] == 6) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_function\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_function\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Function('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"30\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_function').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"server_Function('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_server ";
          $q_string .= "set ";
          $q_string .= "rsdp_function = '" . $formVars['select'] . "' ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }

      }


# 7 == operating systems
# this will be a drop down.
      if ($formVars['type'] == 7) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\""   . $formVars['id'] . "_system\");\n";
          print "selbox.setAttribute(\"name\",\"" . $formVars['id'] . "_system\");\n";
          print "selbox.setAttribute(\"onchange\",\"select_Platform('" . $formVars['id'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"select_Platform('" . $formVars['id'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select os_id,os_software ";
          $q_string .= "from operatingsystem ";
          $q_string .= "where os_delete = 0 ";
          $q_string .= "order by os_software ";
          $q_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_operatingsystem = mysqli_fetch_array($q_operatingsystem) ) {
            print "if (celltext == \"" . $a_operatingsystem['os_software'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($a_operatingsystem['os_software']) . "\"," . $a_operatingsystem['os_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($a_operatingsystem['os_software']) . "\"," . $a_operatingsystem['os_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_system').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"operating_System('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "select os_id,os_software ";
          $q_string .= "from operatingsystem ";
          $q_string .= "where os_id = " . $formVars['select'] . " ";
          $q_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_operatingsystem) > 0) {
            $a_operatingsystem = mysqli_fetch_array($q_operatingsystem);
          } else {
            $a_operatingsystem['os_id']   = 0;
            $a_operatingsystem['os_software'] = "Unassigned";
          }

          $display = $a_operatingsystem['os_software'];

          $q_string  = "update ";
          $q_string .= "rsdp_osteam ";
          $q_string .= "set ";
          $q_string .= "os_software = " . $a_operatingsystem['os_id'] . " ";
          $q_string .= "where os_rsdp = " . $formVars['rsdp'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }


      if ($formVars['type'] == 8) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_cpu\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_cpu\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Processor('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"10\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_cpu').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"central_Processor('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_server ";
          $q_string .= "set ";
          $q_string .= "rsdp_processors = '" . $formVars['select'] . "' ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }

      }


      if ($formVars['type'] == 9) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";
          print "celltext = celltext.replace(\" GB\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_memory\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_memory\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Memory('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"10\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_memory').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"system_Memory('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_server ";
          $q_string .= "set ";
          $q_string .= "rsdp_memory = '" . $formVars['select'] . "' ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $formVars['select'] . " GB</u>';\n";

        }
      }


      if ($formVars['type'] == 10) {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";
          print "celltext = celltext.replace(\" GB\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"" . $formVars['id'] . "_size\");\n";
          print "infield.setAttribute(\"name\",\"" . $formVars['id'] . "_size\");\n";
          print "infield.setAttribute(\"onblur\",\"select_Size('" . $formVars['id'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"10\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('" . $formVars['id'] . "_size').focus();\n";
        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"system_Size('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "rsdp_server ";
          $q_string .= "set ";
          $q_string .= "rsdp_ossize = '" . $formVars['select'] . "' ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $formVars['select'] . " GB</u>';\n";

        }
      }

# userselection block. Same select setup so just use similar variables.
$userselect = 'no';

# 3 == Applications Admin
if ($formVars['type'] == 3) {
  $idstring = $namestring = $formVars['id'] . "_appadmin";
  $onchangestring = $onblurstring = "select_Appadmin('" . $formVars['id'] . "');";
  $onclickstring = "applications_Admin('" . $formVars['id'] . "');";
  $pocstring = "rsdp_apppoc";
  $userselect = 'yes';
}

# 14 == Platforms Admin
if ($formVars['type'] == 14) {
  $idstring = $namestring = $formVars['id'] . "_platformsadmin";
  $onchangestring = $onblurstring = "select_PlatformsAdmin('" . $formVars['id'] . "');";
  $onclickstring = "platforms_Admin('" . $formVars['id'] . "');";
  $pocstring = "rsdp_platformspoc";
  $userselect = 'yes';
}

# 15 == SAN Admin
if ($formVars['type'] == 15) {
  $idstring = $namestring = $formVars['id'] . "_sanadmin";
  $onchangestring = $onblurstring = "select_SANAdmin('" . $formVars['id'] . "');";
  $onclickstring = "SAN_Admin('" . $formVars['id'] . "');";
  $pocstring = "rsdp_sanpoc";
  $userselect = 'yes';
}

# 16 == Network Admin
if ($formVars['type'] == 16) {
  $idstring = $namestring = $formVars['id'] . "_networkadmin";
  $onchangestring = $onblurstring = "select_NetworkAdmin('" . $formVars['id'] . "');";
  $onclickstring = "network_Admin('" . $formVars['id'] . "');";
  $pocstring = "rsdp_networkpoc";
  $userselect = 'yes';
}

# 17 == Network Admin
if ($formVars['type'] == 17) {
  $idstring = $namestring = $formVars['id'] . "_virtualizationadmin";
  $onchangestring = $onblurstring = "select_VirtualizationAdmin('" . $formVars['id'] . "');";
  $onclickstring = "virtualization_Admin('" . $formVars['id'] . "');";
  $pocstring = "rsdp_virtpoc";
  $userselect = 'yes';
}

# 18 == Data Center Admin
if ($formVars['type'] == 18) {
  $idstring = $namestring = $formVars['id'] . "_datacenteradmin";
  $onchangestring = $onblurstring = "select_DataCenterAdmin('" . $formVars['id'] . "');";
  $onclickstring = "datacenter_Admin('" . $formVars['id'] . "');";
  $pocstring = "rsdp_dcpoc";
  $userselect = 'yes';
}

# 19 == Monitoring Admin
if ($formVars['type'] == 19) {
  $idstring = $namestring = $formVars['id'] . "_monitoringadmin";
  $onchangestring = $onblurstring = "select_MonitoringAdmin('" . $formVars['id'] . "');";
  $onclickstring = "monitoring_Admin('" . $formVars['id'] . "');";
  $pocstring = "rsdp_monitorpoc";
  $userselect = 'yes';
}

# 20 == Backup Admin
if ($formVars['type'] == 20) {
  $idstring = $namestring = $formVars['id'] . "_backupadmin";
  $onchangestring = $onblurstring = "select_BackupAdmin('" . $formVars['id'] . "');";
  $onclickstring = "backup_Admin('" . $formVars['id'] . "');";
  $pocstring = "rsdp_backuppoc";
  $userselect = 'yes';
}

# this will be a user drop down selection.
      if ($userselect == 'yes') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";
          print "var celltext = document.getElementById('" . $formVars['id'] . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\""   . $idstring . "\");\n";
          print "selbox.setAttribute(\"name\",\"" . $namestring . "\");\n";
          print "selbox.setAttribute(\"onchange\",\"" . $onchangestring . "\");\n";
          print "selbox.setAttribute(\"onblur\",\"" . $onblurstring . "\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select usr_id,usr_last,usr_first ";
          $q_string .= "from users ";
          $q_string .= "where usr_disabled = 0 ";
          $q_string .= "order by usr_last,usr_first ";
          $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

// create the javascript bit for populating the user dropdown box.
          while ($a_users = mysqli_fetch_array($q_users) ) {
            print "if (celltext == \"" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($a_users['usr_last'] . ", " . $a_users['usr_first']) . "\"," . $a_users['usr_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($a_users['usr_last'] . ", " . $a_users['usr_first']) . "\"," . $a_users['usr_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $idstring . "').focus();\n";

        }
# close down the cell and put the text in plus update rsdp
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"" . $onclickstring . "\");\n";

          $q_string  = "select usr_id,usr_last,usr_first ";
          $q_string .= "from users ";
          $q_string .= "where usr_id = " . $formVars['select'] . " ";
          $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_users) > 0) {
            $a_users = mysqli_fetch_array($q_users);

            $display = $a_users['usr_last'] . ", " . $a_users['usr_first'];
          } else {
            $a_users['usr_id']   = 0;

            $display = "--";
          }

          $q_string  = "update ";
          $q_string .= "rsdp_server ";
          $q_string .= "set ";
          $q_string .= $pocstring . " = " . $a_users['usr_id'] . " ";
          $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }


#select
#21 volt_Text       select_voltText      '_volttext'
#input
#22 power_Draw      select_powerDraw     '_powerdraw'
#input
#23 power_Start     select_powerStart    '_powerstart'
#input
#24 power_Plugs     select_powerPlugs    '_powerplugs'
#select
#25 power_Redundant select_powerRedundant'_powerredundant'
#select
#26 plug_Text       select_plugText      '_plugtext'
#input
#27 start_Row       select_startRow      '_startrow'
#input
#28 start_Rack      select_startRack     '_startrack'
#input
#29 start_Unit      select_startUnit     '_startunit'
#input
#30 number_Units    select_numberUnits   '_numberunits'


    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
