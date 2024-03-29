<?php
# Script: hardware.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_models");

      $q_string  = "select mod_vendor,mod_name,mod_type,mod_size,mod_speed,mod_eopur,mod_eoship,mod_eol,";
      $q_string .= "mod_plugs,mod_plugtype,mod_volts,mod_start,mod_draw,mod_btu,mod_virtual ";
      $q_string .= "from inv_models ";
      $q_string .= "where mod_id = " . $formVars['id'];
      $q_inv_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_models = mysqli_fetch_array($q_inv_models);
      mysqli_free_result($q_inv_models);

      $type = return_Index($db, $a_inv_models['mod_type'], "select part_id from inv_parts order by part_name");


      print "document.hardware.mod_vendor.value = '" . mysqli_real_escape_string($db, $a_inv_models['mod_vendor']) . "';\n";
      print "document.hardware.mod_name.value = '"   . mysqli_real_escape_string($db, $a_inv_models['mod_name'])   . "';\n";
      print "document.hardware.mod_size.value = '"   . mysqli_real_escape_string($db, $a_inv_models['mod_size'])   . "';\n";
      print "document.hardware.mod_speed.value = '"  . mysqli_real_escape_string($db, $a_inv_models['mod_speed'])  . "';\n";
      print "document.hardware.mod_eopur.value = '"  . mysqli_real_escape_string($db, $a_inv_models['mod_eopur'])  . "';\n";
      print "document.hardware.mod_eoship.value = '" . mysqli_real_escape_string($db, $a_inv_models['mod_eoship']) . "';\n";
      print "document.hardware.mod_eol.value = '"    . mysqli_real_escape_string($db, $a_inv_models['mod_eol'])    . "';\n";
      print "document.hardware.mod_plugs.value = '"  . mysqli_real_escape_string($db, $a_inv_models['mod_plugs'])  . "';\n";
      print "document.hardware.mod_start.value = '"  . mysqli_real_escape_string($db, $a_inv_models['mod_start'])  . "';\n";
      print "document.hardware.mod_draw.value = '"   . mysqli_real_escape_string($db, $a_inv_models['mod_draw'])   . "';\n";
      print "document.hardware.mod_btu.value = '"    . mysqli_real_escape_string($db, $a_inv_models['mod_btu'])    . "';\n";

      if ($type > 0) {
        print "document.hardware.mod_type['"     . $type                     . "'].selected = true;\n";
      }
      if ($a_inv_models['mod_volts'] > 0) {
        print "document.hardware.mod_volts['"    . $a_inv_models['mod_volts']    . "'].selected = true;\n";
      }
      if ($a_inv_models['mod_plugtype'] > 0) {
        print "document.hardware.mod_plugtype['" . $a_inv_models['mod_plugtype'] . "'].selected = true;\n";
      }

      if ($a_inv_models['mod_virtual']) {
        print "document.hardware.mod_virtual.checked = true;\n";
      } else {
        print "document.hardware.mod_virtual.checked = false;\n";
      }

      print "document.hardware.id.value = " . $formVars['id'] . ";\n";

      print "document.hardware.update.disabled = false;\n";
      print "document.hardware.mod_vendor.focus();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
