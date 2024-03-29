<?php
# Script: inventory.hardware.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "inventory.hardware.php";
    $formVars['id']       = clean($_GET['id'],       10);
    $formVars['function'] = clean($_GET['function'], 10);
    $formVars['status']   = clean($_GET['status'],   10);
    $formVars['select']   = clean($_GET['select'],   60);

    if (check_userlevel($db, $AL_Edit)) {

# check to see if the person editing is a member of a group that can edit this information; if not, zero out 'type' so no changes can be made.
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

# build date
# this is a text edit field
      if ($formVars['function'] == 'hpb') {
        if ($formVars['status'] == 1) {
# give me the cell pointer you just clicked on.
          print "var cell = document.getElementById('" . $cellid . "');\n";
# give me the text in that cell
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

# remove the underscores
          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

# blank the cell
          print "cell.innerHTML = '&nbsp;';\n";
# remove the function call
          print "cell.setAttribute(\"onclick\", \"\");\n";

# create an input field so the data can be edited
          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"hardware_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"12\");\n";

# put the new input field into the cell
          print "cell.appendChild(infield);\n";

# put the cursor into the new cell
          print "document.getElementById('edit_data').focus();\n";
# highlight the text
          print "document.getElementById('edit_data').select();\n";
# and ready for editing
        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

# give me the cell pointer you just finished
          print "var cell = document.getElementById('" . $cellid . "');\n";

# update the function so it can be clicked again; matches the info in the original
          print "cell.setAttribute(\"onclick\", \"edit_Hardware(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

# update the inventory with the updated information
          $q_string  = "update ";
          $q_string .= "inv_hardware ";
          $q_string .= "set ";
          $q_string .= "hw_built = '" . $formVars['select'] . "' ";
          $q_string .= "where hw_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# replace the input field with the updated data.
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";
        }
      }

# active/live date
# this is a text edit field
      if ($formVars['function'] == 'hpa') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"hardware_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"12\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
          print "document.getElementById('edit_data').select();\n";
        }
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Hardware(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inv_hardware ";
          $q_string .= "set ";
          $q_string .= "hw_active = '" . $formVars['select'] . "' ";
          $q_string .= "where hw_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";
        }
      }



# old functions
# ====================

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

          $q_string  = "select grp_id,grp_name ";
          $q_string .= "from inv_groups ";
          $q_string .= "where grp_disabled = 0 ";
          $q_string .= "order by grp_name ";
          $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_inv_groups = mysqli_fetch_array($q_inv_groups) ) {
            print "if (celltext == \"" . $a_inv_groups['grp_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_groups['grp_name']) . "\"," . $a_inv_groups['grp_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_groups['grp_name']) . "\"," . $a_inv_groups['grp_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_groups').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          if ($formVars['type'] == 1) {
            print "cell.setAttribute(\"onclick\", \"systems_Group('" . $formVars['id'] . "');" . "\");\n";
          } else {
            print "cell.setAttribute(\"onclick\", \"applications_Group('" . $formVars['id'] . "');" . "\");\n";
          }

          $q_string  = "select grp_id,grp_name ";
          $q_string .= "from inv_groups ";
          $q_string .= "where grp_id = " . $formVars['select'] . " ";
          $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_groups) > 0) {
            $a_inv_groups = mysqli_fetch_array($q_inv_groups);
          } else {
            $a_inv_groups['grp_id']   = 0;
            $a_inv_groups['grp_name'] = "Unassigned";
          }

          $display = $a_inv_groups['grp_name'];

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

          $q_string  = "select svc_id,svc_name ";
          $q_string .= "from inv_service ";
          $q_string .= "order by svc_id ";
          $q_inv_service = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_inv_service = mysqli_fetch_array($q_inv_service) ) {
            print "if (celltext == \"" . $a_inv_service['svc_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_service['svc_name']) . "\"," . $a_inv_service['svc_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_service['svc_name']) . "\"," . $a_inv_service['svc_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_service').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"service_Class('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "select svc_id,svc_name ";
          $q_string .= "from inv_service ";
          $q_string .= "where svc_id = " . $formVars['select'] . " ";
          $q_inv_service = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_service) > 0) {
            $a_inv_service = mysqli_fetch_array($q_inv_service);
          } else {
            $a_inv_service['svc_id']   = 0;
            $a_inv_service['svc_name'] = "Unassigned";
          }

          $display = $a_inv_service['svc_name'];

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

          $q_string  = "select loc_id,loc_name ";
          $q_string .= "from inv_locations ";
          $q_string .= "where loc_type = 1 ";
          $q_string .= "order by loc_name ";
          $q_inv_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_inv_locations = mysqli_fetch_array($q_inv_locations) ) {
            print "if (celltext == \"" . $a_inv_locations['loc_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_locations['loc_name']) . "\"," . $a_inv_locations['loc_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_locations['loc_name']) . "\"," . $a_inv_locations['loc_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_location').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"data_Center('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "select loc_id,loc_name ";
          $q_string .= "from inv_locations ";
          $q_string .= "where loc_id = " . $formVars['select'] . " ";
          $q_inv_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_locations) > 0) {
            $a_inv_locations = mysqli_fetch_array($q_inv_locations);
          } else {
            $a_inv_locations['loc_id']   = 0;
            $a_inv_locations['loc_name'] = "Unassigned";
          }

          $display = $a_inv_locations['loc_name'];

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }

# 6 - function
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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"server_Function('" . $formVars['id'] . "');" . "\");\n";

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

          $q_string  = "select os_id,os_software ";
          $q_string .= "from inv_operatingsystem ";
          $q_string .= "where os_delete = 0 ";
          $q_string .= "order by os_software ";
          $q_inv_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_inv_operatingsystem = mysqli_fetch_array($q_inv_operatingsystem) ) {
            print "if (celltext == \"" . $a_inv_operatingsystem['os_software'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_operatingsystem['os_software']) . "\"," . $a_inv_operatingsystem['os_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_operatingsystem['os_software']) . "\"," . $a_inv_operatingsystem['os_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $formVars['id'] . "_system').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"operating_System('" . $formVars['id'] . "');" . "\");\n";

          $q_string  = "select os_id,os_software ";
          $q_string .= "from inv_operatingsystem ";
          $q_string .= "where os_id = " . $formVars['select'] . " ";
          $q_inv_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_operatingsystem) > 0) {
            $a_inv_operatingsystem = mysqli_fetch_array($q_inv_operatingsystem);
          } else {
            $a_inv_operatingsystem['os_id']   = 0;
            $a_inv_operatingsystem['os_software'] = "Unassigned";
          }

          $display = $a_inv_operatingsystem['os_software'];

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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"central_Processor('" . $formVars['id'] . "');" . "\");\n";

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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"system_Memory('" . $formVars['id'] . "');" . "\");\n";

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
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"system_Size('" . $formVars['id'] . "');" . "\");\n";

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

          $q_string  = "select usr_id,usr_last,usr_first ";
          $q_string .= "from inv_users ";
          $q_string .= "where usr_disabled = 0 ";
          $q_string .= "order by usr_last,usr_first ";
          $q_inv_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

// create the javascript bit for populating the user dropdown box.
          while ($a_inv_users = mysqli_fetch_array($q_inv_users) ) {
            print "if (celltext == \"" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first']) . "\"," . $a_inv_users['usr_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first']) . "\"," . $a_inv_users['usr_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('" . $idstring . "').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $formVars['id'] . "');\n";

          print "cell.setAttribute(\"onclick\", \"" . $onclickstring . "\");\n";

          $q_string  = "select usr_id,usr_last,usr_first ";
          $q_string .= "from inv_users ";
          $q_string .= "where usr_id = " . $formVars['select'] . " ";
          $q_inv_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_users) > 0) {
            $a_inv_users = mysqli_fetch_array($q_inv_users);

            $display = $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'];
          } else {
            $a_inv_users['usr_id']   = 0;

            $display = "--";
          }

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
