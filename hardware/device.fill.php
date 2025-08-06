<?php
# Script: device.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "device.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_models");

      $q_string  = "select mod_vendor,mod_name,mod_type,mod_eopur,mod_eoship,mod_eol,";
      $q_string .= "mod_virtual,mod_desc,mod_height,mod_weight,mod_depth,mod_front,";
      $q_string .= "mod_rear,mod_plugs,mod_plugtype,mod_volts,mod_draw,mod_start,mod_btu ";
      $q_string .= "from inv_models ";
      $q_string .= "where mod_id = " . $formVars['id'];
      $q_inv_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_models = mysqli_fetch_array($q_inv_models);
      mysqli_free_result($q_inv_models);

      $modvendor   = return_Index($db, $a_inv_models['mod_vendor'],   "select ven_id from inv_vendors order by ven_name");
      $modtype     = return_Index($db, $a_inv_models['mod_type'],     "select part_id from inv_parts order by part_name");
      $modfront    = return_Index($db, $a_inv_models['mod_front'],    "select img_id from inv_images where img_facing = 1 order by img_title");
      $modrear     = return_Index($db, $a_inv_models['mod_rear'],     "select img_id from inv_images where img_facing = 0 order by img_title");
      $modplugtype = return_Index($db, $a_inv_models['mod_plugtype'], "select plug_id from inv_int_plugtype order by plug_text");
      $modvolts    = return_Index($db, $a_inv_models['mod_volts'],    "select volt_id from inv_int_volts order by volt_text");

      print "document.formUpdate.mod_name.value = '"    . mysqli_real_escape_string($db, $a_inv_models['mod_name'])   . "';\n";
      print "document.formUpdate.mod_desc.value = '"    . mysqli_real_escape_string($db, $a_inv_models['mod_desc'])   . "';\n";
      print "document.formUpdate.mod_height.value = '"  . mysqli_real_escape_string($db, $a_inv_models['mod_height']) . "';\n";
      print "document.formUpdate.mod_weight.value = '"  . mysqli_real_escape_string($db, $a_inv_models['mod_weight']) . "';\n";
      print "document.formUpdate.mod_plugs.value = '"   . mysqli_real_escape_string($db, $a_inv_models['mod_plugs'])  . "';\n";
      print "document.formUpdate.mod_draw.value = '"    . mysqli_real_escape_string($db, $a_inv_models['mod_draw'])   . "';\n";
      print "document.formUpdate.mod_start.value = '"   . mysqli_real_escape_string($db, $a_inv_models['mod_start'])  . "';\n";
      print "document.formUpdate.mod_btu.value = '"     . mysqli_real_escape_string($db, $a_inv_models['mod_btu'])    . "';\n";

      if ($a_inv_models['mod_eopur'] != '1971-01-01') {
        print "document.formUpdate.mod_eopur.value = '"   . mysqli_real_escape_string($db, $a_inv_models['mod_eopur'])  . "';\n";
      }
      if ($a_inv_models['mod_eoship'] != '1971-01-01') {
        print "document.formUpdate.mod_eoship.value = '"  . mysqli_real_escape_string($db, $a_inv_models['mod_eoship']) . "';\n";
      }
      if ($a_inv_models['mod_eol'] != '1971-01-01') {
        print "document.formUpdate.mod_eol.value = '"     . mysqli_real_escape_string($db, $a_inv_models['mod_eol'])    . "';\n";
      }

      print "document.formUpdate.mod_vendor['"   . $modvendor    . "'].selected = true;\n";
      print "document.formUpdate.mod_type['"     . $modtype      . "'].selected = true;\n";
      print "document.formUpdate.mod_front['"    . $modfront     . "'].selected = true;\n";
      print "document.formUpdate.mod_rear['"     . $modrear      . "'].selected = true;\n";
      print "document.formUpdate.mod_plugtype['" . $modplugtype  . "'].selected = true;\n";
      print "document.formUpdate.mod_volts['"    . $modvolts     . "'].selected = true;\n";

      if ($a_inv_models['mod_virtual']) {
        print "document.formUpdate.mod_virtual.checked = true;\n";
      } else {
        print "document.formUpdate.mod_virtual.checked = false;\n";
      }
      if ($a_inv_models['mod_depth']) {
        print "document.formUpdate.mod_depth.checked = true;\n";
      } else {
        print "document.formUpdate.mod_depth.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
