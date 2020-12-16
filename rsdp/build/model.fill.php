<?php
# Script: model.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "model.fill.php";
    $formVars['pf_model'] = 0;
    if (isset($_GET['pf_model'])) {
      $formVars['pf_model'] = clean($_GET['pf_model'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['pf_model'] . " from models");

      $q_string  = "select mod_vendor,mod_name,mod_type,mod_size,mod_plugs,mod_plugtype,mod_volts,mod_draw,mod_start,mod_virtual ";
      $q_string .= "from models ";
      $q_string .= "where mod_id = " . $formVars['pf_model'];
      $q_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_models = mysqli_fetch_array($q_models);
      mysqli_free_result($q_models);

      $mod_type     = return_Index($db, $a_models['mod_type'],     "select part_id from parts order by part_name");
      $mod_plugtype = return_Index($db, $a_models['mod_plugtype'], "select plug_id from int_plugtype order by plug_id");
      $mod_volts    = return_Index($db, $a_models['mod_volts'],    "select volt_id from int_volts order by volt_id");

      print "document.rsdp.mod_type['"     . $mod_type     . "'].selected = true;\n";
      print "document.rsdp.mod_plugtype['" . $mod_plugtype . "'].selected = true;\n";
      print "document.rsdp.mod_volts['"    . $mod_volts    . "'].selected = true;\n";

      print "document.rsdp.mod_size.value = '"  . mysqli_real_escape_string($db, $a_models['mod_size'])    . "';\n";
      print "document.rsdp.mod_draw.value = '"  . mysqli_real_escape_string($db, $a_models['mod_draw'])    . "';\n";
      print "document.rsdp.mod_start.value = '" . mysqli_real_escape_string($db, $a_models['mod_start'])   . "';\n";
      print "document.rsdp.mod_plugs.value = '" . mysqli_real_escape_string($db, $a_models['mod_plugs'])   . "';\n";
      print "document.rsdp.virtual.value = '"   . mysqli_real_escape_string($db, $a_models['mod_virtual']) . "';\n";

      print "validate_Form();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
