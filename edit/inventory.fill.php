<?php
# Script: inventory.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inventory");

      $q_string  = "select inv_name,inv_companyid,inv_function,inv_callpath,inv_document,";
      $q_string .= "       inv_ssh,inv_location,inv_rack,inv_row,inv_unit,inv_zone,inv_front,";
      $q_string .= "       inv_rear,inv_manager,inv_appadmin,inv_class,inv_response,inv_mstart,inv_mend,inv_ansible,";
      $q_string .= "       inv_mdow,inv_minterval,inv_product,inv_project,inv_department,inv_notes,inv_env,";
      $q_string .= "       inv_appliance,inv_ticket,inv_maint ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['id'];
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inventory = mysqli_fetch_array($q_inventory);

      if (mysqli_num_rows($q_inventory) > 0) {

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
        $q_string .= "left join inv_models on inv_models.mod_id = hardware.hw_vendorid ";
        $q_string .= "where mod_type = 13 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name ";

        $invcompanyid  = return_Index($db, $a_inventory['inv_companyid'],  $q_string);

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
        $q_string .= "left join inv_models on inv_models.mod_id = hardware.hw_vendorid ";
        $q_string .= "where mod_type = 48 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name ";
 
        $invlocation   = return_Index($db, $a_inventory['inv_location'],   "select loc_id from inv_locations left join inv_cities on inv_cities.ct_id = inv_locations.loc_city order by ct_city,loc_name");
        $invzone       = return_Index($db, $a_inventory['inv_zone'],       "select zone_id from timezones order by zone_name");
        $invfront      = return_Index($db, $a_inventory['inv_front'],      "select img_id from images where img_facing = 1 order by img_title,img_file");
        $invrear       = return_Index($db, $a_inventory['inv_rear'],       "select img_id from images where img_facing = 0 order by img_title,img_file");
        $invmanager    = return_Index($db, $a_inventory['inv_manager'],    "select grp_id from inv_groups where grp_disabled = 0 order by grp_name");
        $invappadmin   = return_Index($db, $a_inventory['inv_appadmin'],   "select grp_id from inv_groups where grp_disabled = 0 order by grp_name");
        $invclass      = return_Index($db, $a_inventory['inv_class'],      "select svc_id from inv_service order by svc_id");
        $invresponse   = return_Index($db, $a_inventory['inv_response'],   "select slv_id from supportlevel order by slv_value");
        $invproduct    = return_Index($db, $a_inventory['inv_product'],    "select prod_id from products order by prod_name");
        $invproject    = return_Index($db, $a_inventory['inv_project'],    "select prj_id from projects where prj_product = " . $a_inventory['inv_product'] . " order by prj_name");
        $invdepartment = return_Index($db, $a_inventory['inv_department'], "select dep_id from inv_department order by dep_business,dep_name");
        $invenv        = return_Index($db, $a_inventory['inv_env'],        "select env_id from environment order by env_name");
# no zero in the selection window so off by one each time
        $invmaint      = return_Index($db, $a_inventory['inv_maint'],      "select man_id from inv_maintenance order by man_text") - 1;

        print "document.edit.inv_name.value = '"     . mysqli_real_escape_string($db, $a_inventory['inv_name'])     . "';\n";
        print "document.edit.inv_function.value = '" . mysqli_real_escape_string($db, $a_inventory['inv_function']) . "';\n";
        print "document.edit.inv_document.value = '" . mysqli_real_escape_string($db, $a_inventory['inv_document']) . "';\n";
        print "document.edit.inv_rack.value = '"     . mysqli_real_escape_string($db, $a_inventory['inv_rack'])     . "';\n";
        print "document.edit.inv_row.value = '"      . mysqli_real_escape_string($db, $a_inventory['inv_row'])      . "';\n";
        print "document.edit.inv_unit.value = '"     . mysqli_real_escape_string($db, $a_inventory['inv_unit'])     . "';\n";
        print "document.edit.inv_notes.value = '"    . mysqli_real_escape_string($db, $a_inventory['inv_notes'])    . "';\n";
        print "document.edit.inv_ticket.value = '"   . mysqli_real_escape_string($db, $a_inventory['inv_ticket'])   . "';\n";

        if ($invcompanyid > 0) {
          print "document.edit.inv_companyid['"  . $invcompanyid              . "'].selected = true;\n";
        }
        if ($invlocation > 0) {
          print "document.edit.inv_location['"   . $invlocation               . "'].selected = true;\n";
        }
        if ($invzone > 0) {
          print "document.edit.inv_zone['"       . $invzone                   . "'].selected = true;\n";
        }
        if ($invfront > 0) {
          print "document.edit.inv_front['"      . $invfront                  . "'].selected = true;\n";
        }
        if ($invrear > 0) {
          print "document.edit.inv_rear['"       . $invrear                   . "'].selected = true;\n";
        }
        if ($invmanager > 0) {
          print "document.edit.inv_manager['"    . $invmanager                . "'].selected = true;\n";
        }
        if ($invappadmin > 0) {
          print "document.edit.inv_appadmin['"   . $invappadmin               . "'].selected = true;\n";
        }
        if ($invclass > 0) {
          print "document.edit.inv_class['"      . $invclass                  . "'].selected = true;\n";
        }
        if ($invresponse > 0) {
          print "document.edit.inv_response['"   . $invresponse               . "'].selected = true;\n";
        }
        if ($invproduct > 0) {
          print "document.edit.inv_product['"    . $invproduct                . "'].selected = true;\n";
        }
        if ($invproject > 0) {
          print "document.edit.inv_project['"    . $invproject                . "'].selected = true;\n";
        }
        if ($invdepartment > 0) {
          print "document.edit.inv_department['" . $invdepartment             . "'].selected = true;\n";
        }
        if ($invenv > 0) {
          print "document.edit.inv_env['"        . $invenv                    . "'].selected = true;\n";
        }
        if ($invmaint > 0) {
          print "document.edit.inv_maint['"      . $invmaint                  . "'].selected = true;\n";
        }

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
        if ($a_inventory['inv_appliance']) {
          print "document.edit.inv_appliance.checked = true;\n";
        } else {
          print "document.edit.inv_appliance.checked = false;\n";
        }

        print "document.edit.id.value = " . $formVars['id'] . ";\n";

        print "document.edit.update.disabled = false;\n";

      } else {
        print "document.edit.inv_name.value = 'Blank';\n";
        print "document.edit.inv_function.value = 'New Server';\n";
      }

      print "check_hostname();\n";

      mysqli_free_result($q_inventory);
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
