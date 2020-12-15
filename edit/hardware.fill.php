<?php
# Script: hardware.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Fill in the table for editing.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "hardware.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from hardware");

      $q_string  = "select hw_id,hw_companyid,hw_serial,hw_asset,hw_group,hw_product,hw_vendorid,hw_rma,";
      $q_string .= "hw_type,hw_size,hw_speed,hw_purchased,hw_built,hw_active,hw_eol,hw_retired,hw_reused,hw_supportid,";
      $q_string .= "hw_deleted,hw_note,hw_response,hw_supid_verified,hw_eolticket,hw_hw_id,hw_hd_id ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_id = " . $formVars['id'];
      $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_hardware = mysqli_fetch_array($q_hardware);
      mysqli_free_result($q_hardware);

// set up the model type drop down to match the type of the retrieved record.
      print "var selbox = document.edit.hw_vendorid;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

// retrieve type list
      $q_string  = "select mod_id,mod_vendor,mod_name from models ";
      $q_string .= "where mod_type = " . $a_hardware['hw_type'] . " ";
      $q_string .= "order by mod_vendor,mod_name";
      $q_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

// create the javascript bit for populating the model dropdown box.
      while ($a_models = mysqli_fetch_array($q_models) ) {
        print "selbox.options[selbox.options.length] = new Option(\"" . $a_models['mod_name'] . " (" . $a_models['mod_vendor'] . ")\"," . $a_models['mod_id'] . ");\n";
      }

# for the fill part, we want to not present the current hw_id as a selectable option (can't be a sub-system of yourself).
# set up the hardware drop down to refresh the hardware listing
      print "var selbox = document.edit.hw_hw_id;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

# retrieve hardware list
      $q_string  = "select hw_id,mod_vendor,mod_name ";
      $q_string .= "from hardware ";
      $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
      $q_string .= "where hw_companyid = " . $a_hardware['hw_companyid'] . " and hw_hw_id = 0 and hw_id != " . $formVars['id'] . " ";
      $q_hwselect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

# create the javascript bit for populating the hardware dropdown box.
      while ($a_hwselect = mysqli_fetch_array($q_hwselect)) {
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
      $q_string .= "where hw_companyid = " . $a_hardware['hw_companyid'] . " and mod_name like \"RAID%\" and hw_id != " . $formVars['id'] . " ";
      $q_hwselect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

# create the javascript bit for populating the model dropdown box.
      while ($a_hwselect = mysqli_fetch_array($q_hwselect)) {
        print "selbox.options[selbox.options.length] = new Option(\"" . $a_hwselect['hw_asset'] . $a_hwselect['hw_serial'] . " " . $a_hwselect['mod_name'] . "\"," . $a_hwselect['hw_id'] . ");\n";
      }

      $product  = return_Index($db, $a_hardware['hw_product'],   "select prod_id from products order by prod_name");
      $model    = return_Index($db, $a_hardware['hw_vendorid'],  "select mod_id from models where mod_type = " . $a_hardware['hw_type'] . " order by mod_vendor,mod_name");
      $type     = return_Index($db, $a_hardware['hw_type'],      "select part_id from parts order by part_name");
      $support  = return_Index($db, $a_hardware['hw_supportid'], "select sup_id from support order by sup_company,sup_contract");
      $response = return_Index($db, $a_hardware['hw_response'],  "select slv_id from supportlevel order by slv_value");
      $hwselect = return_Index($db, $a_hardware['hw_hw_id'],     "select hw_id from hardware where hw_companyid = " . $a_hardware['hw_companyid'] . " and hw_hw_id = 0 and hw_id != " . $formVars['id']);
      $hwdisk   = return_Index($db, $a_hardware['hw_hd_id'],     "select hw_id from hardware left join models on models.mod_id = hardware.hw_vendorid where hw_companyid = " . $a_hardware['hw_companyid'] . " and mod_name like \"RAID%\" and hw_id != " . $formVars['id']);

      print "document.edit.hw_serial.value = '"    . mysqli_real_escape_string($db, $a_hardware['hw_serial'])    . "';\n";
      print "document.edit.hw_asset.value = '"     . mysqli_real_escape_string($db, $a_hardware['hw_asset'])     . "';\n";
      print "document.edit.hw_size.value = '"      . mysqli_real_escape_string($db, $a_hardware['hw_size'])      . "';\n";
      print "document.edit.hw_speed.value = '"     . mysqli_real_escape_string($db, $a_hardware['hw_speed'])     . "';\n";
      print "document.edit.hw_purchased.value = '" . mysqli_real_escape_string($db, $a_hardware['hw_purchased']) . "';\n";
      print "document.edit.hw_built.value = '"     . mysqli_real_escape_string($db, $a_hardware['hw_built'])     . "';\n";
      print "document.edit.hw_active.value = '"    . mysqli_real_escape_string($db, $a_hardware['hw_active'])    . "';\n";
      print "document.edit.hw_eol.value = '"       . mysqli_real_escape_string($db, $a_hardware['hw_eol'])       . "';\n";
      print "document.edit.hw_retired.value = '"   . mysqli_real_escape_string($db, $a_hardware['hw_retired'])   . "';\n";
      print "document.edit.hw_reused.value = '"    . mysqli_real_escape_string($db, $a_hardware['hw_reused'])    . "';\n";
      print "document.edit.hw_eolticket.value = '" . mysqli_real_escape_string($db, $a_hardware['hw_eolticket']) . "';\n";
      print "document.edit.hw_rma.value = '"       . mysqli_real_escape_string($db, $a_hardware['hw_rma'])       . "';\n";
      print "document.edit.hw_note.value = '"      . mysqli_real_escape_string($db, $a_hardware['hw_note'])      . "';\n";

      print "document.edit.hw_vendorid['"  . $model    . "'].selected = true;\n";
      print "document.edit.hw_type['"      . $type     . "'].selected = true;\n";
      print "document.edit.hw_supportid['" . $support  . "'].selected = true;\n";
      print "document.edit.hw_response['"  . $response . "'].selected = true;\n";
      print "document.edit.hw_hw_id['"     . $hwselect . "'].selected = true;\n";
      print "document.edit.hw_hd_id['"     . $hwdisk   . "'].selected = true;\n";

      if ($a_hardware['hw_deleted']) {
        print "document.edit.hw_deleted.checked = true;\n";
      } else {
        print "document.edit.hw_deleted.checked = false;\n";
      }

      if ($a_hardware['hw_supid_verified']) {
        print "document.getElementById('hw_contract').innerHTML = 'Yes';\n\n";
      } else {
        print "document.getElementById('hw_contract').innerHTML = 'No';\n\n";
      }

      print "document.edit.hw_id.value = " . $formVars['id'] . ";\n";

      print "document.edit.hw_update.disabled = false;\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
