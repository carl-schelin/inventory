<?php
# Script: inventory.detail.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "inventory.detail.php";
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


# parent listing
# this will be a drop down.
      if ($formVars['function'] == 'ipt') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\"edit_data\");\n";
          print "selbox.setAttribute(\"name\",\"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select inv_id,inv_name ";
          $q_string .= "from inventory ";
          $q_string .= "where inv_status = 0 and inv_virtual = 0 ";
          $q_string .= "order by inv_name ";
          $q_parent = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          while ($a_parent = mysqli_fetch_array($q_parent) ) {
            print "if (celltext == \"" . $a_parent['inv_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_parent['inv_name']) . "\"," . $a_parent['inv_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_parent['inv_name']) . "\"," . $a_parent['inv_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select inv_id,inv_name ";
          $q_string .= "from inventory ";
          $q_string .= "where inv_id = " . $formVars['select'] . " ";
          $q_parent = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_parent) > 0) {
            $a_parent = mysqli_fetch_array($q_parent);
          } else {
            $a_parent['inv_id']   = 0;
            $a_parent['inv_name'] = "Orphan";
          }

          $display = $a_parent['inv_name'];

          $q_string  = "update ";
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_companyid = " . $a_parent['inv_id'] . " ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }


# system function
# this is a text edit field
      if ($formVars['function'] == 'ifn') {
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
          print "infield.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"30\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_function = '" . $formVars['select'] . "' ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";

        }
      }


# system admins
# this will be a drop down.
      if ($formVars['function'] == 'isa') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",      \"edit_data\");\n";
          print "selbox.setAttribute(\"name\",    \"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select grp_id,grp_name ";
          $q_string .= "from a_groups ";
          $q_string .= "where grp_disabled = 0 ";
          $q_string .= "order by grp_name ";
          $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_groups = mysqli_fetch_array($q_groups) ) {
            print "if (celltext == \"" . $a_groups['grp_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_groups['grp_name']) . "\"," . $a_groups['grp_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_groups['grp_name']) . "\"," . $a_groups['grp_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select grp_id,grp_name ";
          $q_string .= "from a_groups ";
          $q_string .= "where grp_id = " . $formVars['select'] . " ";
          $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_groups) > 0) {
            $a_groups = mysqli_fetch_array($q_groups);
          } else {
            $a_groups['grp_id']   = 0;
            $a_groups['grp_name'] = "Unassigned";
          }

          $display = $a_groups['grp_name'];

# Update the main listing
          $q_string  = "update ";
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_manager = " . $a_groups['grp_id'] . " ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# update the hardware listing; all hardware for this server
          $q_string  = "update ";
          $q_string .= "hardware ";
          $q_string .= "set ";
          $q_string .= "hw_group = " . $a_groups['grp_id'] . " ";          
          $q_string .= "where hw_companyid = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# update the software listing; only software that's owned by the old group
          $q_string  = "update ";
          $q_string .= "software ";
          $q_string .= "set ";
          $q_string .= "sw_group = " . $a_groups['grp_id'] . " ";          
          $q_string .= "where sw_companyid = " . $formVars['id'] . " and sw_group = " . $formVars['select'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# update file system ownershps
          $q_string  = "update ";
          $q_string .= "filesystem ";
          $q_string .= "set ";
          $q_string .= "fs_group = " . $a_groups['grp_id'] . " ";          
          $q_string .= "where fs_companyid = " . $formVars['id'] . " and fs_group = " . $formVars['select'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";

        }
      }

# application admins
# this will be a drop down.
      if ($formVars['function'] == 'iaa') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",      \"edit_data\");\n";
          print "selbox.setAttribute(\"name\",    \"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select grp_id,grp_name ";
          $q_string .= "from a_groups ";
          $q_string .= "where grp_disabled = 0 ";
          $q_string .= "order by grp_name ";
          $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_groups = mysqli_fetch_array($q_groups) ) {
            print "if (celltext == \"" . $a_groups['grp_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_groups['grp_name']) . "\"," . $a_groups['grp_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_groups['grp_name']) . "\"," . $a_groups['grp_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select grp_id,grp_name ";
          $q_string .= "from a_groups ";
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
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_appadmin = " . $a_groups['grp_id'] . " ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# update the software listing; only software that's owned by the old group
          $q_string  = "update ";
          $q_string .= "software ";
          $q_string .= "set ";
          $q_string .= "sw_group = " . $a_groups['grp_id'] . " ";          
          $q_string .= "where sw_companyid = " . $formVars['id'] . " and sw_group = " . $formVars['select'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# update file system ownershps
          $q_string  = "update ";
          $q_string .= "filesystem ";
          $q_string .= "set ";
          $q_string .= "fs_group = " . $a_groups['grp_id'] . " ";          
          $q_string .= "where fs_companyid = " . $formVars['id'] . " and fs_group = " . $formVars['select'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";

        }
      }

# product listing
# this will be a drop down.
# NOTE: As the Project data will have different listings per Product, IF the product changes from current to a new product, the inv_project entry is zero'd out and the text blanked.
      if ($formVars['function'] == 'ipr') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\"edit_data\");\n";
          print "selbox.setAttribute(\"name\",\"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select prod_id,prod_name ";
          $q_string .= "from products ";
          $q_string .= "order by prod_name ";
          $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_products = mysqli_fetch_array($q_products) ) {
            print "if (celltext == \"" . $a_products['prod_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_products['prod_name']) . "\"," . $a_products['prod_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_products['prod_name']) . "\"," . $a_products['prod_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

# since project is dependent upon what product is listed, we need to see if the inv_product is different than the selected product.
# if different, set inv_project = 0 and set the project field to 'Unassigned'.

          $q_string  = "select inv_product ";
          $q_string .= "from inventory ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $q_invcheck = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_invcheck = mysqli_fetch_array($q_invcheck);

          if ($a_invcheck['inv_product'] != $formVars['select']) {
            $q_string  = "update ";
            $q_string .= "inventory ";
            $q_string .= "set ";
            $q_string .= "inv_project = 0 ";
            $q_string .= "where inv_id = " . $formVars['id'] . " ";
            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

            print "var cellproject = document.getElementById('ipj" . $formVars['id'] . "');\n";
            print "cellproject.innerHTML = '<u>Unassigned</u>';\n";
          }

          $q_string  = "select prod_id,prod_name ";
          $q_string .= "from products ";
          $q_string .= "where prod_id = " . $formVars['select'] . " ";
          $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_products) > 0) {
            $a_products = mysqli_fetch_array($q_products);
          } else {
            $a_products['prod_id']   = 0;
            $a_products['prod_name'] = "Unassigned";
          }

          $display = $a_products['prod_name'];

          $q_string  = "update ";
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_product = " . $a_products['prod_id'] . " ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }

# project listing
# this will be a drop down.
# NOTE: This needs to be a list of the available Projects for the current identified Product!
# get the PID from the inventory listing for the id vs trying to read the field.
      if ($formVars['function'] == 'ipj') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\"edit_data\");\n";
          print "selbox.setAttribute(\"name\",\"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select inv_manager,inv_product ";
          $q_string .= "from inventory ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $q_invcheck = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_invcheck = mysqli_fetch_array($q_invcheck);

          $q_string  = "select prj_id,prj_name ";
          $q_string .= "from projects ";
          $q_string .= "where prj_group = " . $a_invcheck['inv_manager'] . " and prj_product = " . $a_invcheck['inv_product'] . " ";
          $q_string .= "order by prj_name ";
          $q_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_projects = mysqli_fetch_array($q_projects) ) {
            print "if (celltext == \"" . $a_projects['prj_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_projects['prj_name']) . "\"," . $a_projects['prj_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_projects['prj_name']) . "\"," . $a_projects['prj_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select prj_id,prj_name ";
          $q_string .= "from projects ";
          $q_string .= "where prj_id = " . $formVars['select'] . " ";
          $q_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_projects) > 0) {
            $a_projects = mysqli_fetch_array($q_projects);
          } else {
            $a_projects['prj_id']   = 0;
            $a_projects['prj_name'] = "Unassigned";
          }

          $display = $a_projects['prj_name'];

          $q_string  = "update ";
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_project = " . $a_projects['prj_id'] . " ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }

# service class
# this will be a drop down.
      if ($formVars['function'] == 'isc') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\"edit_data\");\n";
          print "selbox.setAttribute(\"name\",\"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select svc_id,svc_name ";
          $q_string .= "from service ";
          $q_string .= "order by svc_id ";
          $q_service = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_service = mysqli_fetch_array($q_service) ) {
            print "if (celltext == \"" . $a_service['svc_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_service['svc_name']) . "\"," . $a_service['svc_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_service['svc_name']) . "\"," . $a_service['svc_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

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
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_class = " . $a_service['svc_id'] . " ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }

# maintenance window
# this will be a drop down.
      if ($formVars['function'] == 'imw') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\"edit_data\");\n";
          print "selbox.setAttribute(\"name\",\"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";

          $q_string  = "select win_id,win_text ";
          $q_string .= "from maint_window ";
          $q_string .= "order by win_text ";
          $q_window = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_window = mysqli_fetch_array($q_window) ) {
            print "if (celltext == \"" . $a_window['win_text'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_window['win_text']) . "\"," . $a_window['win_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_window['win_text']) . "\"," . $a_window['win_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "select win_id,win_text ";
          $q_string .= "from maint_window ";
          $q_string .= "where win_id = " . $formVars['select'] . " ";
          $q_window = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_window = mysqli_fetch_array($q_window);

          $display = $a_window['win_text'];

          $q_string  = "update ";
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_maint = " . $a_window['win_id'] . " ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }


# data center locations
# this will be a drop down.
      if ($formVars['function'] == 'ilc') {
        if ($formVars['status'] == 1) {
          print "var cell = document.getElementById('" . $cellid . "');\n";
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

          print "cell.innerHTML = '&nbsp;';\n";
          print "cell.setAttribute(\"onclick\", \"\");\n";

          print "var selbox = document.createElement('select');\n";
          print "selbox.setAttribute(\"id\",\"edit_data\");\n";
          print "selbox.setAttribute(\"name\",\"edit_data\");\n";
          print "selbox.setAttribute(\"onchange\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "selbox.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";

          print "selbox.options.length = 0;\n";
          print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

          $q_string  = "select loc_id,loc_name ";
          $q_string .= "from locations ";
          $q_string .= "where loc_type = 1 ";
          $q_string .= "order by loc_name ";
          $q_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          while ($a_locations = mysqli_fetch_array($q_locations) ) {
            print "if (celltext == \"" . $a_locations['loc_name'] . "\") {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_locations['loc_name']) . "\"," . $a_locations['loc_id'] . ",1,1);\n";
            print "} else {\n";
            print "  selbox.options[selbox.options.length] = new Option(\"" . mysqli_real_escape_string($db, $a_locations['loc_name']) . "\"," . $a_locations['loc_id'] . ",0,0);\n";
            print "}\n";
          }

          print "cell.appendChild(selbox);\n";

          print "document.getElementById('edit_data').focus();\n";

        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

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
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_location = " . $a_locations['loc_id'] . " ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $display . "</u>';\n";
        }
      }

# row
# this is a text edit field
      if ($formVars['function'] == 'irw') {
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
          print "infield.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"5\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_fqdn = '" . $formVars['select'] . "' ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";
        }
      }

# rack
# this is a text edit field
      if ($formVars['function'] == 'irk') {
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
          print "infield.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"5\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_fqdn = '" . $formVars['select'] . "' ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";
        }
      }

# unit
# this is a text edit field
      if ($formVars['function'] == 'iun') {
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
          print "infield.setAttribute(\"onblur\",\"detail_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"5\");\n";

          print "cell.appendChild(infield);\n";

          print "document.getElementById('edit_data').focus();\n";
        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

          print "var cell = document.getElementById('" . $cellid . "');\n";

          print "cell.setAttribute(\"onclick\", \"edit_Detail(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

          $q_string  = "update ";
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_fqdn = '" . $formVars['select'] . "' ";
          $q_string .= "where inv_id = " . $formVars['id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";
        }
      }

# checkboxes. change the live info
# with this one, we're just setting today's date if checked or blanking it out to 1971-01-01 if returning to 'inwork'
# to start, get the inv_status
      if ($formVars['function'] == 'ilv') {
        $q_string  = "select inv_status,hw_id,hw_active ";
        $q_string .= "from inventory ";
        $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
        $q_string .= "where inv_id = " . $formVars['id'] . " and hw_primary = 1 and hw_deleted = 0 ";
        $q_invlive = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_invlive = mysqli_fetch_array($q_invlive);

        if ($a_invlive['hw_active'] == '1971-01-01') {
          $a_invlive['hw_active'] = date('Y-m-d');
        } else {
          $a_invlive['hw_active'] = '1971-01-01';
        }

        $q_string  = "update ";
        $q_string .= "hardware ";
        $q_string .= "set ";
        $q_string .= "hw_active = '" . $a_invlive['hw_active'] . "' ";
        $q_string .= "where hw_id = " . $a_invlive['hw_id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_invlive['hw_active'] == '1971-01-01') {
          print "document.getElementById('ilv" . $formVars['id'] . "').checked = false;\n";
        } else {
          print "document.getElementById('ilv" . $formVars['id'] . "').checked = true;\n";
        }
      }

# checkboxes. change call path
      if ($formVars['function'] == 'icp') {
        $q_string  = "select inv_callpath ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_id = " . $formVars['id'] . " ";
        $q_invcp = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_invcp = mysqli_fetch_array($q_interface);

        if ($a_invcp['inv_callpath']) {
          $a_invcp['inv_callpath'] = 0;
        } else {
          $a_invcp['inv_callpath'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "inventory ";
        $q_string .= "set ";
        $q_string .= "inv_callpath = " . $a_invcp['inv_callpath'] . " ";
        $q_string .= "where inv_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_invcp['inv_callpath']) {
          print "document.getElementById('icp" . $formVars['id'] . "').checked = true;\n";
        } else {
          print "document.getElementById('icp" . $formVars['id'] . "').checked = false;\n";
        }
      }

# checkboxes. change ansible
      if ($formVars['function'] == 'ian') {
        $q_string  = "select inv_ansible ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_id = " . $formVars['id'] . " ";
        $q_invans = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_invans = mysqli_fetch_array($q_invans);

        if ($a_invans['inv_ansible']) {
          $a_invans['inv_ansible'] = 0;
        } else {
          $a_invans['inv_ansible'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "inventory ";
        $q_string .= "set ";
        $q_string .= "inv_ansible = " . $a_invans['inv_ansible'] . " ";
        $q_string .= "where inv_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_invans['inv_ansible']) {
          print "document.getElementById('ian" . $formVars['id'] . "').checked = true;\n";
        } else {
          print "document.getElementById('ian" . $formVars['id'] . "').checked = false;\n";
        }
      }

# checkboxes. change unixsvc/ssh
      if ($formVars['function'] == 'ius') {
        $q_string  = "select inv_ssh ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_id = " . $formVars['id'] . " ";
        $q_invssh = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_invssh = mysqli_fetch_array($q_invssh);

        if ($a_invssh['inv_ssh']) {
          $a_invssh['inv_ssh'] = 0;
        } else {
          $a_invssh['inv_ssh'] = 1;
        }

        $q_string  = "update ";
        $q_string .= "inventory ";
        $q_string .= "set ";
        $q_string .= "inv_ssh = " . $a_invssh['inv_ssh'] . " ";
        $q_string .= "where inv_id = " . $formVars['id'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if ($a_invssh['inv_ssh']) {
          print "document.getElementById('ius" . $formVars['id'] . "').checked = true;\n";
        } else {
          print "document.getElementById('ius" . $formVars['id'] . "').checked = false;\n";
        }
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
