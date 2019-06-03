<?php
# Script: hardware.mysql.php
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
    $package = "hardware.mysql.php";
    $formVars['update']       = clean($_GET['update'],       10);
    $formVars['hw_companyid'] = clean($_GET['hw_companyid'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['hw_companyid'] == '') {
      $formVars['hw_companyid'] = 0;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],           10);
        $formVars['hw_hw_id']     = clean($_GET['hw_hw_id'],     10);
        $formVars['hw_hd_id']     = clean($_GET['hw_hd_id'],     10);
        $formVars['hw_serial']    = clean($_GET['hw_serial'],    30);
        $formVars['hw_asset']     = clean($_GET['hw_asset'],     30);
        $formVars['hw_group']     = clean($_GET['hw_group'],     10);
        $formVars['hw_product']   = clean($_GET['hw_product'],   10);
        $formVars['hw_vendorid']  = clean($_GET['hw_vendorid'],  10);
        $formVars['hw_type']      = clean($_GET['hw_type'],      10);
        $formVars['hw_size']      = clean($_GET['hw_size'],     100);
        $formVars['hw_speed']     = clean($_GET['hw_speed'],     20);
        $formVars['hw_purchased'] = clean($_GET['hw_purchased'], 12);
        $formVars['hw_built']     = clean($_GET['hw_built'],     12);
        $formVars['hw_active']    = clean($_GET['hw_active'],    12);
        $formVars['hw_eol']       = clean($_GET['hw_eol'],       12);
        $formVars['hw_retired']   = clean($_GET['hw_retired'],   12);
        $formVars['hw_reused']    = clean($_GET['hw_reused'],    12);
        $formVars['hw_eolticket'] = clean($_GET['hw_eolticket'], 30);
        $formVars['hw_supportid'] = clean($_GET['hw_supportid'], 10);
        $formVars['hw_response']  = clean($_GET['hw_response'],  10);
        $formVars['hw_deleted']   = clean($_GET['hw_deleted'],   10);
        $formVars['hw_rma']       = clean($_GET['hw_rma'],       50);
        $formVars['hw_note']      = clean($_GET['hw_note'],     255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['hw_group'] == '') {
          $formVars['hw_group'] = 0;
        }
        if ($formVars['hw_product'] == '') {
          $formVars['hw_product'] = 0;
        }
        if ($formVars['hw_deleted'] == "true") {
          $formVars['hw_deleted'] = 1;
# disassociate from physical/virtual container
          $formVars['hw_hw_id'] = 0;
          $formVars['hw_hd_id'] = 0;
        } else {
          $formVars['hw_deleted'] = 0;
        }
# force associating with Primary Machine if associating drive with RAID
        if ($formVars['hw_hd_id'] > 0 && $formVars['hw_hw_id'] == 0) {
          $q_string  = "select hw_id ";
          $q_string .= "from hardware ";
          $q_string .= "where hw_companyid = " . $formVars['hw_companyid'] . " and hw_primary = 1 ";
          $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          if (mysql_num_rows($q_hardware) > 0) {
            $a_hardware = mysql_fetch_array($q_hardware);

            $formVars['hw_hw_id'] = $a_hardware['hw_id'];
          } else {
            $formVars['hw_hw_id'] = 0;
            $formVars['hw_hd_id'] = 0;
          }
        }

# set the hw_primary value here
        $q_string  = "select part_type ";
        $q_string .= "from models ";
        $q_string .= "left join parts on parts.part_id = models.mod_type ";
        $q_string .= "where mod_id = " . $formVars['hw_vendorid'] . " ";
        $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        if (mysql_num_rows($q_models) > 0) {
          $a_models = mysql_fetch_array($q_models);
          $formVars['hw_primary'] = $a_models['part_type'];
        } else {
          $formVars['hw_primary'] = 0;
        }
# automatically not the primary if deleted regardless of what the gear is
        if ($formVars['hw_deleted'] == 1) {
          $formVars['hw_primary'] = 0;
        }

        if ($formVars['hw_companyid'] > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string = 
            "hw_companyid =   " . $formVars['hw_companyid'] . "," . 
            "hw_hw_id     =   " . $formVars['hw_hw_id']     . "," . 
            "hw_hd_id     =   " . $formVars['hw_hd_id']     . "," . 
            "hw_serial    = \"" . $formVars['hw_serial']    . "\"," . 
            "hw_asset     = \"" . $formVars['hw_asset']     . "\"," . 
            "hw_group     =   " . $formVars['hw_group']     . "," . 
            "hw_product   =   " . $formVars['hw_product']   . "," .
            "hw_vendorid  =   " . $formVars['hw_vendorid']  . "," . 
            "hw_type      =   " . $formVars['hw_type']      . "," . 
            "hw_size      = \"" . $formVars['hw_size']      . "\"," . 
            "hw_speed     = \"" . $formVars['hw_speed']     . "\"," . 
            "hw_purchased = \"" . $formVars['hw_purchased'] . "\"," . 
            "hw_built     = \"" . $formVars['hw_built']     . "\"," . 
            "hw_active    = \"" . $formVars['hw_active']    . "\"," . 
            "hw_eol       = \"" . $formVars['hw_eol']       . "\"," . 
            "hw_retired   = \"" . $formVars['hw_retired']   . "\"," . 
            "hw_reused    = \"" . $formVars['hw_reused']    . "\"," . 
            "hw_eolticket = \"" . $formVars['hw_eolticket'] . "\"," . 
            "hw_update    = \"" . date('Y-m-d')             . "\"," . 
            "hw_user      =   " . $_SESSION['uid']          . "," .
            "hw_supportid =   " . $formVars['hw_supportid'] . "," .
            "hw_response  =   " . $formVars['hw_response']  . "," .
            "hw_deleted   =   " . $formVars['hw_deleted']   . "," . 
            "hw_primary   =   " . $formVars['hw_primary']   . "," . 
            "hw_verified  =   " . "0"                       . "," . 
            "hw_rma       = \"" . $formVars['hw_rma']       . "\"," . 
            "hw_note      = \"" . $formVars['hw_note']      . "\"";

          if ($formVars['update'] == 0) {
            if ($formVars['hw_primary']) {
              changelog($formVars['hw_companyid'], $formVars['hw_serial'],   "New Primary Hardware", $_SESSION['uid'], "hardware", "hw_serial", 0);
              changelog($formVars['hw_companyid'], $formVars['hw_vendorid'], "New Primary Hardware", $_SESSION['uid'], "hardware", "hw_vendorid", 0);
            }

            $query = "insert into hardware set hw_id = NULL, " . $q_string;
            $message = "Hardware added.";
          }

          if ($formVars['update'] == 1) {

# only check for changes on primary hardware
            if ($formVars['hw_primary']) {
              $q_hwtable  = "select hw_serial,hw_vendorid ";
              $q_hwtable .= "from hardware ";
              $q_hwtable .= "where hw_id = " . $formVars['id'];
              $q_hardware = mysql_query($q_hwtable) or die($q_hwtable . ": " . mysql_error());
              $a_hardware = mysql_fetch_array($q_hardware);

# for changelog requirements, compare old hw_serial with new hw_serial. If changed, save the old data before it changes
              if ($a_hardware['hw_serial'] != $formVars['hw_serial']) {
                changelog($formVars['hw_companyid'], $a_hardware['hw_serial'], "Serial Number Change", $_SESSION['uid'], "hardware", "hw_serial", 0);
              }

# for changelog requirements, compare old hw_vendorid with new hw_vendorid. If changed, save the old data before it changes
              if ($a_hardware['hw_vendorid'] != $formVars['hw_vendorid']) {
                changelog($formVars['hw_companyid'], $a_hardware['hw_vendorid'], "Vendor Change", $_SESSION['uid'], "hardware", "hw_vendorid", 0);
              }
            }

            $query = "update hardware set " . $q_string . " where hw_id = " . $formVars['id'];
            $message = "Hardware updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['id']);

          mysql_query($query) or die($query . ": " . mysql_error());

          print "alert('" . $message . "');\n";
# One additional bit is to set the status bit to 0 if a server is live and 1 if it's retired. Flip off the ssh bit if retired.
          if ($formVars['hw_primary'] == 1) {
            if ($formVars['hw_retired'] == '0000-00-00' && $formVars['hw_reused'] == '0000-00-00') {
              $invstatus = 0;
              $invssh = '';
            } else {
              $invstatus = 1;
              $invssh = ',inv_ssh = 0 ';
            }
            $q_string  = "update inventory ";
            $q_string .= "set inv_status = " . $invstatus . $invssh . " ";
            $q_string .= "where inv_id = " . $formVars['hw_companyid'];
            mysql_query($q_string) or die($q_string . ": " . mysql_error());
          }
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

#################
### Todo: E-mail Lynda's group when a system has been retired
### Perhaps use the same code as shipping and receiving in the Issue Tracker
#################

# check to see if the server live, retired, or reused dates have changed and notify equipment management
#    if ($formVars['id'] > 0 && $formVars['hw_primary'] == 1) {
#      $q_string  = "select hw_built,hw_active,hw_retired,hw_reused,hw_vendorid from hardware ";
#      $q_string .= "where hw_id = " . $formVars['id'];
#      $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
#      $a_hardware = mysql_fetch_array($q_hardware);

# get model info as well to make sure she's not notified of virtual machine activity.
#      $q_string = "select mod";
#
#      if ($a_hardware['hw_active'] == '0000-00-00' && $formVars['hw_active'] != '0000-00-00') {
#
#        $q_string = "select inv_name from inventory where inv_id = " . $formVars['hw_companyid'];
#        $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
#        $a_inventory = mysql_fetch_array($q_inventory);
#
#        $headers  = "From: Inventory DB <root@" . $Sitehttp . ">\r\n";
#        $headers .= "MIME-Version: 1.0\r\n";
#        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
#
#        $body  = "<p>A physical piece of equipment has become a live, production system.</p>";
#
#        $body .= "<ul>";
#        $body .= "  <li>System Name: " . $a_inventory['inv_name'] . "</li>";
#        $body .= "  <li>Asset Tag: " . $formVars['hw_asset'] . "</li>";
#        $body .= "  <li>Serial Number: " . $formVars['hw_serial'] . "</li>";
#        $body .= "</ul>";
#      }

# send e-mail to Lynda Lilly
#      $q_string = "select usr_email from users where usr_id = 13";
#      $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
#      $a_users = mysql_fetch_array($q_users);
#
#      mail($a_users['usr_email'], "Inventory: Production System Active", $body, $headers);
#    }


# only want to copy the details of the equipment and not the identifying information such as 
# serial numbers, asset tags, service tags, or rma info
      if ($formVars['update'] == -2) {
        $formVars['copyfrom'] = clean($_GET['copyfrom'], 10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select hw_type,hw_vendorid,hw_speed,hw_supportid,hw_size,hw_primary ";
          $q_string .= "from hardware ";
          $q_string .= "where hw_companyid = " . $formVars['copyfrom'];
          $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          while ($a_hardware = mysql_fetch_array($q_hardware)) {

            $q_string =
              "hw_companyid =   " . $formVars['hw_companyid']   . "," . 
              "hw_group     =   " . $formVars['hw_group']       . "," . 
              "hw_product   =   " . $formVars['hw_product']     . "," . 
              "hw_vendorid  =   " . $a_hardware['hw_vendorid']  . "," . 
              "hw_type      =   " . $a_hardware['hw_type']      . "," . 
              "hw_size      = \"" . $a_hardware['hw_size']      . "\"," . 
              "hw_speed     = \"" . $a_hardware['hw_speed']     . "\"," . 
              "hw_supportid =   " . $a_hardware['hw_supportid'] . "," .
              "hw_primary   =   " . $a_hardware['hw_primary'];

            $query = "insert into hardware set hw_id = NULL, " . $q_string;
            mysql_query($query) or die($query . ": " . mysql_error());
          }
        }
      }


      if ($formVars['update'] == -3) {
        logaccess($_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"hw_refresh\" value=\"Refresh Hardware Listing\" onClick=\"javascript:attach_hardware('hardware.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"hw_update\"  value=\"Update Hardware\"          onClick=\"javascript:attach_hardware('hardware.mysql.php',  1);hideDiv('hardware-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"hw_id\"      value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"hw_addbtn\"  value=\"Add New Hardware\"         onClick=\"javascript:attach_hardware('hardware.mysql.php', 0);\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\" title=\"Does not copy Unique data\">\n";
        $output .= "<input type=\"button\" name=\"copyitem\"   value=\"Copy Hardware Table From:\" onClick=\"javascript:attach_hardware('hardware.mysql.php', -2);\">\n";
        $output .= "<select name=\"hw_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name";
        $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        while ($a_inventory = mysql_fetch_array($q_inventory)) {
          $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"4\">Hardware Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" title=\"Company Asset Tag\">Asset Tag: <input type=\"text\" name=\"hw_asset\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\" title=\"Hardware Serial Number\">Serial: <input type=\"text\" name=\"hw_serial\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\"><label>Mark this item as deleted? <input type=\"checkbox\" name=\"hw_deleted\"></label></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" title=\"Hardware type\">Type <select name=\"hw_type\" onclick=\"javascript:attach_hardwaretype('hardware.options.php?hw_type=' + hw_type.value);\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select part_id,part_name ";
        $q_string .= "from parts ";
        $q_string .= "order by part_name ";
        $q_parts = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        while ($a_parts = mysql_fetch_array($q_parts)) {
          $output .= "<option value=\"" . $a_parts['part_id'] . "\">" . $a_parts['part_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Model <select name=\"hw_vendorid\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";
        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Size <input type=\"text\" name=\"hw_size\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Speed <input type=\"text\" name=\"hw_speed\" size=\"20\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";


        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"4\">Support Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" title=\"Support company and contract number\">Support Company <select name=\"hw_supportid\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select sup_id,sup_company,sup_contract ";
        $q_string .= "from support ";
        $q_string .= "order by sup_company,sup_contract ";
        $q_support = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        while  ($a_support = mysql_fetch_array($q_support)) {
          $output .= "<option value=\"" . $a_support['sup_id'] . "\">" . $a_support['sup_company'] . " (" . $a_support['sup_contract'] . ")</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Response Level <select name=\"hw_response\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select slv_id,slv_value ";
        $q_string .= "from supportlevel ";
        $q_string .= "order by slv_value";
        $q_supportlevel = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        while ($a_supportlevel = mysql_fetch_array($q_supportlevel)) {
          $output .= "<option value=\"" . $a_supportlevel['slv_id'] . "\">" . $a_supportlevel['slv_value'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">RMA: <input type=\"text\" name=\"hw_rma\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Contract Confirmation: <span id=\"hw_contract\">No</span></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"4\">Container/Redundancy Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Main Hardware Container <select name=\"hw_hw_id\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select hw_id,mod_vendor,mod_name ";
        $q_string .= "from hardware ";
        $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
        $q_string .= "where hw_companyid = " . $formVars['hw_companyid'] . " and hw_hw_id = 0 ";
        $q_hwselect = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        while ($a_hwselect = mysql_fetch_array($q_hwselect)) {
          $output .= "<option value=\"" . $a_hwselect['hw_id'] . "\">" . $a_hwselect['mod_vendor'] . ": " . $a_hwselect['mod_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Hard Disk Redundancy <select name=\"hw_hd_id\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select hw_id,hw_serial,hw_asset,mod_name ";
        $q_string .= "from hardware ";
        $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
        $q_string .= "where hw_companyid = " . $formVars['hw_companyid'] . " and mod_name like \"RAID%\" ";
        $q_hwselect = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        while ($a_hwselect = mysql_fetch_array($q_hwselect)) {
          $output .= "<option value=\"" . $a_hwselect['hw_id'] . "\">" . $a_hwselect['hw_asset'] . $a_hwselect['hw_serial'] . " " . $a_hwselect['mod_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";


        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"7\">Life-Cycle Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Purchased   <input type=\"text\" name=\"hw_purchased\" value=\"0000-00-00\" size=\"11\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Built       <input type=\"text\" name=\"hw_built\"     value=\"" . date('Y-m-d') . "\" size=\"11\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Live        <input type=\"text\" name=\"hw_active\"    value=\"0000-00-00\" size=\"11\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">End of Life <input type=\"text\" name=\"hw_eol\"       value=\"0000-00-00\" size=\"11\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Ticket      <input type=\"text\" name=\"hw_eolticket\" value=\"\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Retired     <input type=\"text\" name=\"hw_retired\"   value=\"0000-00-00\" size=\"11\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Reused      <input type=\"text\" name=\"hw_reused\"    value=\"0000-00-00\" size=\"11\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">Notes Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Note: <input type=\"text\" name=\"hw_note\" size=\"80\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('hardware_form').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

# set up the hardware drop down to refresh the hardware listing
      print "var selbox = document.edit.hw_hw_id;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

# retrieve hardware list
      $q_string  = "select hw_id,mod_vendor,mod_name ";
      $q_string .= "from hardware ";
      $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
      $q_string .= "where hw_companyid = " . $formVars['hw_companyid'] . " and hw_hw_id = 0 ";
      $q_hwselect = mysql_query($q_string) or die($q_string . ": " . mysql_error());

# create the javascript bit for populating the hardware dropdown box.
      while ($a_hwselect = mysql_fetch_array($q_hwselect)) {
        print "selbox.options[selbox.options.length] = new Option(\"" . $a_hwselect['mod_vendor'] . ": " . $a_hwselect['mod_name'] . "\"," . $a_hwselect['hw_id'] . ");\n";
      }


# set up the hardware drop down to refresh the hardware listing
      print "var selbox = document.edit.hw_hd_id;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

# retrieve hardware list
      $q_string  = "select hw_id,hw_serial,hw_asset,mod_name ";
      $q_string .= "from hardware ";
      $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
      $q_string .= "where hw_companyid = " . $formVars['hw_companyid'] . " and mod_name like \"RAID%\" ";
      $q_hwselect = mysql_query($q_string) or die($q_string . ": " . mysql_error());

# create the javascript bit for populating the model dropdown box.
      while ($a_hwselect = mysql_fetch_array($q_hwselect)) {
        print "selbox.options[selbox.options.length] = new Option(\"" . $a_hwselect['hw_asset'] . $a_hwselect['hw_serial'] . " " . $a_hwselect['mod_name'] . "\"," . $a_hwselect['hw_id'] . ");\n";
      }


      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\" colspan=\"8\">Hardware Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('hardware-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"hardware-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Hardware Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Highlighted</strong> - This hardware item is the <span class=\"ui-state-highlight\">Primary Container</span> and typically contains all the other listed equipment.</li>\n";
      $output .= "    <li><strong>Delete (x)</strong> - The first deletion only marks the hardware as deleted but maintains the association. Deleted hardware continues to show up but <span class=\"ui-state-error\">appropriately identified</span>. Clicking the <strong>x</strong> again will break the association with this server.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a device to bring up the form and edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Rows marked with a checkmark in the Updated column have been automatically captured where possible.</li>\n";
      $output .= "    <li>Click the <strong>Hardware Management</strong> title bar to toggle the <strong>Hardware Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Rem</th>\n";
      if (return_Virtual($formVars['hw_companyid']) == 0) {
        $output .=   "<th class=\"ui-state-default\">Asset</th>\n";
        $output .=   "<th class=\"ui-state-default\">Serial</th>\n";
      }
      $output .=   "<th class=\"ui-state-default\">Model</th>\n";
      $output .=   "<th class=\"ui-state-default\">Type</th>\n";
      $output .=   "<th class=\"ui-state-default\">Size</th>\n";
      $output .=   "<th class=\"ui-state-default\">Speed</th>\n";
      $output .=   "<th class=\"ui-state-default\">Updated</th>\n";
      $output .= "</tr>\n";

      $primary = 0;
      $q_string  = "select hw_id,hw_companyid,part_name,hw_serial,hw_asset,hw_product,hw_speed,hw_size,";
      $q_string .= "mod_name,hw_active,hw_eol,hw_group,hw_primary,hw_retired,hw_deleted,hw_note,hw_rma,hw_verified,hw_update ";
      $q_string .= "from hardware ";
      $q_string .= "left join parts on hardware.hw_type = parts.part_id ";
      $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
      $q_string .= "where hw_companyid = " . $formVars['hw_companyid'] . " and hw_hw_id = 0 and hw_hd_id = 0 ";
      $q_string .= "order by hw_type,hw_size,hw_vendorid,hw_serial,hw_asset ";
      $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_hardware) > 0) {
        while ($a_hardware = mysql_fetch_array($q_hardware)) {

          if ($a_hardware['hw_rma'] == '') {
            $rma = '';
            $rmanote = '';
          } else {
            $rma = '-';
            $rmanote = " RMA: " . $a_hardware['hw_rma'];
          }

          if ($a_hardware['hw_primary'] == 1) {
            $primary++;
            $class = "class=\"ui-state-highlight";
          } else {
            $class = "class=\"ui-widget-content";
          }

          $hwnote = " title=\"" . $a_hardware['hw_note'] . $rmanote . "\"";
          if ($a_hardware['hw_deleted'] == 1) {
            $class = "class=\"ui-state-error";
            $title = "title=\"Device removed: " . $a_hardware['hw_retired'] . "\"";
            $deltitle = "title = \"Remove association\"";
            $linkdel = "<input type=\"button\" value=\"Delete\" onClick=\"javascript:delete_hardware('hardware.del.php?id=" . $a_hardware['hw_id'] . "');\">";
          } else {
            $title = "title=\"Edit Hardware\"";
            $deltitle = "title = \"Identify as Deleted\"";
            $linkdel = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:remove_hardware('hardware.remove.php?id=" . $a_hardware['hw_id'] . "&retired=" . $a_hardware['hw_retired'] . "');\">";
          }

          $checkmark = "";
          if ($a_hardware['hw_verified']) {
            $checkmark = "&#x2713;";
          }

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('hardware.fill.php?id=" . $a_hardware['hw_id'] . "');showDiv('hardware-hide');\">";
          $linkend = "</a>";
  
          $output .= "<tr>\n";
          $output .= "<td " . $class . " delete\" " . $deltitle . ">" . $linkdel                                                           . "</td>\n";
          if (return_Virtual($formVars['hw_companyid']) == 0) {
            $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hardware['hw_asset']             . $linkend . "</td>\n";
            $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hardware['hw_serial']            . $linkend . "</td>\n";
          }
          $output .= "<td " . $class .        "\" " . $hwnote   . ">" . $linkstart . $rma . $a_hardware['mod_name']             . $linkend . "</td>\n";
          $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hardware['part_name']            . $linkend . "</td>\n";
          $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hardware['hw_size']              . $linkend . "</td>\n";
          $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hardware['hw_speed']             . $linkend . "</td>\n";
          $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hardware['hw_update']            . $checkmark . $linkend . "</td>\n";
          $output .= "</tr>\n";


# any associated equipment
          $q_string  = "select hw_id,hw_companyid,part_name,hw_serial,hw_asset,hw_product,hw_speed,hw_size,";
          $q_string .= "mod_name,hw_active,hw_eol,hw_group,hw_primary,hw_retired,hw_deleted,hw_note,hw_rma,hw_verified,hw_update ";
          $q_string .= "from hardware ";
          $q_string .= "left join parts on hardware.hw_type = parts.part_id ";
          $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
          $q_string .= "where hw_companyid = " . $formVars['hw_companyid'] . " and hw_hw_id = " . $a_hardware['hw_id'] . " and hw_hd_id = 0 ";
          $q_string .= "order by hw_type,hw_size,hw_vendorid,hw_serial,hw_asset ";
          $q_hwselect = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          if (mysql_num_rows($q_hwselect) > 0) {
            while ($a_hwselect = mysql_fetch_array($q_hwselect)) {

              if ($a_hwselect['hw_rma'] == '') {
                $rma = '';
                $rmanote = '';
              } else {
                $rma = '-';
                $rmanote = " RMA: " . $a_hwselect['hw_rma'];
              }

              if ($a_hwselect['hw_primary'] == 1) {
                $primary++;
                $class = "class=\"ui-state-highlight";
              } else {
                $class = "class=\"ui-widget-content";
              }

              $hwnote = " title=\"" . $a_hwselect['hw_note'] . $rmanote . "\"";
              if ($a_hwselect['hw_deleted'] == 1) {
                $class = "class=\"ui-state-error";
                $title = "title=\"Device removed: " . $a_hwselect['hw_retired'] . "\"";
                $deltitle = "title = \"Remove association\"";
                $linkdel = "<input type=\"button\" value=\"Delete\" onClick=\"javascript:delete_hardware('hardware.del.php?id=" . $a_hwselect['hw_id'] . "');\">";
              } else {
                $title = "title=\"Edit Hardware\"";
                $deltitle = "title = \"Identify as Deleted\"";
                $linkdel = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:remove_hardware('hardware.remove.php?id=" . $a_hwselect['hw_id'] . "&retired=" . $a_hwselect['hw_retired'] . "');\">";
              }

              $checkmark = "";
              if ($a_hwselect['hw_verified']) {
                $checkmark = "&#x2713;";
              }

              $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('hardware.fill.php?id=" . $a_hwselect['hw_id'] . "');showDiv('hardware-hide');\">";
              $linkend = "</a>";
  
              $output .= "<tr>\n";
              $output .= "<td " . $class . " delete\" " . $deltitle . ">" . $linkdel                                                           . "</td>\n";
              if (return_Virtual($formVars['hw_companyid']) == 0) {
                $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwselect['hw_asset']             . $linkend . "</td>\n";
                $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwselect['hw_serial']            . $linkend . "</td>\n";
              }
              $output .= "<td " . $class .        "\" " . $hwnote   . ">&gt; " . $linkstart . $rma . $a_hwselect['mod_name']             . $linkend . "</td>\n";
              $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwselect['part_name']            . $linkend . "</td>\n";
              $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwselect['hw_size']              . $linkend . "</td>\n";
              $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwselect['hw_speed']             . $linkend . "</td>\n";
              $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwselect['hw_update']            . $checkmark . $linkend . "</td>\n";
              $output .= "</tr>\n";



# any associated hard disks
              $q_string  = "select hw_id,hw_companyid,part_name,hw_serial,hw_asset,hw_product,hw_speed,hw_size,";
              $q_string .= "mod_name,hw_active,hw_eol,hw_group,hw_primary,hw_retired,hw_deleted,hw_note,hw_rma,hw_verified,hw_update ";
              $q_string .= "from hardware ";
              $q_string .= "left join parts on hardware.hw_type = parts.part_id ";
              $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
              $q_string .= "where hw_companyid = " . $formVars['hw_companyid'] . " and hw_hw_id = " . $a_hardware['hw_id'] . " and hw_hd_id = " . $a_hwselect['hw_id'] . " ";
              $q_string .= "order by hw_type,hw_size,hw_vendorid,hw_serial,hw_asset ";
              $q_hwdisk = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_hwdisk) > 0) {
                while ($a_hwdisk = mysql_fetch_array($q_hwdisk)) {

                  if ($a_hwdisk['hw_rma'] == '') {
                    $rma = '';
                    $rmanote = '';
                  } else {
                    $rma = '-';
                    $rmanote = " RMA: " . $a_hwdisk['hw_rma'];
                  }

                  if ($a_hwdisk['hw_primary'] == 1) {
                    $primary++;
                    $class = "class=\"ui-state-highlight";
                  } else {
                    $class = "class=\"ui-widget-content";
                  }

                  $hwnote = " title=\"" . $a_hwdisk['hw_note'] . $rmanote . "\"";
                  if ($a_hwdisk['hw_deleted'] == 1) {
                    $class = "class=\"ui-state-error";
                    $title = "title=\"Device removed: " . $a_hwdisk['hw_retired'] . "\"";
                    $deltitle = "title = \"Remove association\"";
                    $linkdel = "<input type=\"button\" value=\"Delete\" onClick=\"javascript:delete_hardware('hardware.del.php?id=" . $a_hwdisk['hw_id'] . "');\">";
                  } else {
                    $title = "title=\"Edit Hardware\"";
                    $deltitle = "title = \"Identify as Deleted\"";
                    $linkdel = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:remove_hardware('hardware.remove.php?id=" . $a_hwdisk['hw_id'] . "&retired=" . $a_hwdisk['hw_retired'] . "');\">";
                  }

                  $checkmark = "";
                  if ($a_hwdisk['hw_verified']) {
                    $checkmark = "&#x2713;";
                  }

                  $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('hardware.fill.php?id=" . $a_hwdisk['hw_id'] . "');showDiv('hardware-hide');\">";
                  $linkend = "</a>";
  
                  $output .= "<tr>\n";
                  $output .= "<td " . $class . " delete\" " . $deltitle . ">" . $linkdel                                                           . "</td>\n";
                  if (return_Virtual($formVars['hw_companyid']) == 0) {
                    $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwdisk['hw_asset']             . $linkend . "</td>\n";
                    $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwdisk['hw_serial']            . $linkend . "</td>\n";
                  }
                  $output .= "<td " . $class .        "\" " . $hwnote   . ">&gt;&gt; " . $linkstart . $rma . $a_hwdisk['mod_name']             . $linkend . "</td>\n";
                  $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwdisk['part_name']            . $linkend . "</td>\n";
                  $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwdisk['hw_size']              . $linkend . "</td>\n";
                  $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwdisk['hw_speed']             . $linkend . "</td>\n";
                  $output .= "<td " . $class .        "\" " . $title    . ">" . $linkstart .        $a_hwdisk['hw_update']            . $checkmark . $linkend . "</td>\n";
                  $output .= "</tr>\n";

                }
              }

            }
          }

        }
      } else {
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\" colspan=\"9\">No Hardware associated with this system." . $formVars['hw_companyid'] . "</td>\n";
        $output .= "</tr>\n";
      }

      mysql_free_result($q_hardware);

      $output .= "</table>\n";

      print "document.getElementById('hardware_table').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.edit.hw_update.disabled = true;\n";

# Warn folks if there aren't any type device
      if ($primary == 0 && $formVars['hw_companyid'] != 0) {
        print "alert(\"ERROR: A primary hardware device hasn't been identified.\\n\\nReturn to Hardware and ensure a model has been associated\\nwith the primary hardware device or a device has been added.\");\n";
      }
# Warn folks if a system has more than 1 primary device.
      if ($primary > 1 && $formVars['hw_companyid'] != 0) {
        print "alert(\"ERROR: " . $primary . " primary devices have been associated with this server.\\n\\nThis is likely due to an old deleted device record. Select the Hardware\\ntab and edit the deleted device and update it. It'll clear the primary\\ndevice flag.\");\n";
      }

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
