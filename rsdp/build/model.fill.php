<?php
# Script: model.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['pf_model'] . " from models");

      $q_string  = "select mod_vendor,mod_name,mod_type,mod_size,mod_plugs,mod_plugtype,mod_volts,mod_draw,mod_start,mod_virtual ";
      $q_string .= "from models ";
      $q_string .= "where mod_id = " . $formVars['pf_model'];
      $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_models = mysql_fetch_array($q_models);
      mysql_free_result($q_models);

      $mod_type     = return_Index($a_models['mod_type'],     "select part_id from parts order by part_name");
      $mod_plugtype = return_Index($a_models['mod_plugtype'], "select plug_id from int_plugtype order by plug_id");
      $mod_volts    = return_Index($a_models['mod_volts'],    "select volt_id from int_volts order by volt_id");

      print "document.rsdp.mod_type['"     . $mod_type     . "'].selected = true;\n";
      print "document.rsdp.mod_plugtype['" . $mod_plugtype . "'].selected = true;\n";
      print "document.rsdp.mod_volts['"    . $mod_volts    . "'].selected = true;\n";

      print "document.rsdp.mod_size.value = '"  . mysql_real_escape_string($a_models['mod_size'])    . "';\n";
      print "document.rsdp.mod_draw.value = '"  . mysql_real_escape_string($a_models['mod_draw'])    . "';\n";
      print "document.rsdp.mod_start.value = '" . mysql_real_escape_string($a_models['mod_start'])   . "';\n";
      print "document.rsdp.mod_plugs.value = '" . mysql_real_escape_string($a_models['mod_plugs'])   . "';\n";
      print "document.rsdp.virtual.value = '"   . mysql_real_escape_string($a_models['mod_virtual']) . "';\n";

      print "validate_Form();\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
