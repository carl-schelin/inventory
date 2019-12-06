<?php
# Script: inventory.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Fill in the table for editing.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "inventory.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inventory");

      $q_string  = "select inv_name,inv_companyid,inv_function,inv_callpath,inv_document,inv_centrify,";
      $q_string .= "       inv_adzone,inv_domain,inv_ssh,inv_location,inv_rack,inv_row,inv_unit,inv_zone,inv_front,";
      $q_string .= "       inv_rear,inv_manager,inv_appadmin,inv_class,inv_response,inv_mstart,inv_mend,inv_ansible,";
      $q_string .= "       inv_mdow,inv_minterval,inv_product,inv_project,inv_department,inv_notes,inv_clusterid ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['id'];
      $q_inventory = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_inventory = mysql_fetch_array($q_inventory);

      if (mysql_num_rows($q_inventory) > 0) {

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
        $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
        $q_string .= "where mod_type = 13 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name ";

        $invcompanyid  = return_Index($a_inventory['inv_companyid'],  $q_string);

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
        $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
        $q_string .= "where mod_type = 48 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name ";
 
        $invclusterid  = return_Index($a_inventory['inv_clusterid'],  $q_string);

        $invlocation   = return_Index($a_inventory['inv_location'],   "select loc_id from locations left join cities on cities.ct_id = locations.loc_city order by ct_city,loc_name");
        $invzone       = return_Index($a_inventory['inv_zone'],       "select zone_id from zones order by zone_name");
        $invfront      = return_Index($a_inventory['inv_front'],      "select img_id from images where img_facing = 1 order by img_title,img_file");
        $invrear       = return_Index($a_inventory['inv_rear'],       "select img_id from images where img_facing = 0 order by img_title,img_file");
        $invmanager    = return_Index($a_inventory['inv_manager'],    "select grp_id from groups where grp_disabled = 0 order by grp_name");
        $invappadmin   = return_Index($a_inventory['inv_appadmin'],   "select grp_id from groups where grp_disabled = 0 order by grp_name");
        $invclass      = return_Index($a_inventory['inv_class'],      "select svc_id from service order by svc_id");
        $invresponse   = return_Index($a_inventory['inv_response'],   "select slv_id from supportlevel order by slv_value");
        $invproduct    = return_Index($a_inventory['inv_product'],    "select prod_id from products order by prod_name");
        $invproject    = return_Index($a_inventory['inv_project'],    "select prj_id from projects where prj_product = " . $a_inventory['inv_product'] . " order by prj_name");
        $invdepartment = return_Index($a_inventory['inv_department'], "select dep_id from department order by dep_unit,dep_name");

        print "document.edit.inv_name.value = '"     . mysql_real_escape_string($a_inventory['inv_name'])     . "';\n";
        print "document.edit.inv_function.value = '" . mysql_real_escape_string($a_inventory['inv_function']) . "';\n";
        print "document.edit.inv_document.value = '" . mysql_real_escape_string($a_inventory['inv_document']) . "';\n";
        print "document.edit.inv_centrify.value = '" . mysql_real_escape_string($a_inventory['inv_centrify']) . "';\n";
        print "document.edit.inv_adzone.value = '"   . mysql_real_escape_string($a_inventory['inv_adzone'])   . "';\n";
        print "document.edit.inv_domain.value = '"   . mysql_real_escape_string($a_inventory['inv_domain'])   . "';\n";
        print "document.edit.inv_rack.value = '"     . mysql_real_escape_string($a_inventory['inv_rack'])     . "';\n";
        print "document.edit.inv_row.value = '"      . mysql_real_escape_string($a_inventory['inv_row'])      . "';\n";
        print "document.edit.inv_unit.value = '"     . mysql_real_escape_string($a_inventory['inv_unit'])     . "';\n";
        print "document.edit.inv_notes.value = '"    . mysql_real_escape_string($a_inventory['inv_notes'])    . "';\n";

        print "document.edit.inv_companyid['"  . $invcompanyid              . "'].selected = true;\n";
        print "document.edit.inv_clusterid['"  . $invclusterid              . "'].selected = true;\n";
        print "document.edit.inv_location['"   . $invlocation               . "'].selected = true;\n";
        print "document.edit.inv_zone['"       . $invzone                   . "'].selected = true;\n";
        print "document.edit.inv_front['"      . $invfront                  . "'].selected = true;\n";
        print "document.edit.inv_rear['"       . $invrear                   . "'].selected = true;\n";
        print "document.edit.inv_manager['"    . $invmanager                . "'].selected = true;\n";
        print "document.edit.inv_appadmin['"   . $invappadmin               . "'].selected = true;\n";
        print "document.edit.inv_class['"      . $invclass                  . "'].selected = true;\n";
        print "document.edit.inv_response['"   . $invresponse               . "'].selected = true;\n";
        print "document.edit.inv_product['"    . $invproduct                . "'].selected = true;\n";
        print "document.edit.inv_project['"    . $invproject                . "'].selected = true;\n";
        print "document.edit.inv_department['" . $invdepartment             . "'].selected = true;\n";

        if ($a_inventory['inv_callpath']) {
          print "document.edit.inv_callpath.checked = true;\n";
        } else {
          print "document.edit.inv_callpath.checked = false;\n";
        }
        if ($a_inventory['inv_ssh']) {
          print "document.edit.inv_ssh.checked = true;\n";
        } else {
          print "document.edit.inv_ssh.checked = false;\n";
        }
        if ($a_inventory['inv_ansible']) {
          print "document.edit.inv_ansible.checked = true;\n";
        } else {
          print "document.edit.inv_ansible.checked = false;\n";
        }

        print "document.edit.id.value = " . $formVars['id'] . ";\n";

        print "document.edit.update.disabled = false;\n";

      } else {
        print "document.edit.inv_name.value = 'Blank';\n";
        print "document.edit.inv_function.value = 'New Server';\n";
      }

      print "check_hostname();\n";

      mysql_free_result($q_inventory);
    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
