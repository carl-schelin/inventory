<?php
# Script: build.fill.php
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
    $package = "build.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_platform");

      $q_string  = "select pf_id,pf_model,pf_asset,pf_serial,pf_redundant,pf_row,pf_rack,pf_unit,pf_special ";
      $q_string .= "from rsdp_platform ";
      $q_string .= "where pf_rsdp = " . $formVars['rsdp'] . " ";
      $q_string .= "order by pf_id ";
      $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $numrows = mysqli_num_rows($q_rsdp_platform);

      if (mysqli_num_rows($q_rsdp_platform) > 0) {
        $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);

        $pf_model = return_Index($a_rsdp_platform['pf_model'], "select mod_id from models where mod_primary = 1 order by mod_vendor,mod_name");

        print "document.rsdp.pf_model['" . $pf_model . "'].selected = true;\n";

        print "document.rsdp.pf_asset.value = '"   . mysqli_real_escape_string($a_rsdp_platform['pf_asset'])   . "';\n";
        print "document.rsdp.pf_serial.value = '"  . mysqli_real_escape_string($a_rsdp_platform['pf_serial'])  . "';\n";

        print "document.rsdp.pf_row.value = '"     . mysqli_real_escape_string($a_rsdp_platform['pf_row'])     . "';\n";
        print "document.rsdp.pf_rack.value = '"    . mysqli_real_escape_string($a_rsdp_platform['pf_rack'])    . "';\n";
        print "document.rsdp.pf_unit.value = "     . mysqli_real_escape_string($a_rsdp_platform['pf_unit'])    . ";\n";
        print "document.rsdp.pf_special.value = '" . mysqli_real_escape_string($a_rsdp_platform['pf_special']) . "';\n";

        if ($a_rsdp_platform['pf_redundant']) {
          print "document.rsdp.pf_redundant.checked = true;\n";
        } else {
          print "document.rsdp.pf_redundant.checked = false;\n";
        }

        print "document.rsdp.pf_id.value = " . $a_rsdp_platform['pf_id'] . ";\n";

        print "show_file('model.fill.php' + '?pf_model=' + document.rsdp.pf_model.value);\n";

        if ($numrows > 1) {
          print "alert(\"WARNING: There are duplicate Platform rows (" . $numrows . ")\");\n";
        }
      }
      mysqli_free_result($q_rsdp_platform);

      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_osteam");

      $q_string  = "select os_id,os_sysname,os_fqdn,os_software ";
      $q_string .= "from rsdp_osteam ";
      $q_string .= "where os_rsdp = " . $formVars['rsdp'] . " ";
      $q_string .= "order by os_id ";
      $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);
      $numrows = mysqli_num_rows($q_rsdp_osteam);

      if (mysqli_num_rows($q_rsdp_osteam) > 0) {
        $os_software  = return_Index($a_rsdp_osteam['os_software'], "select os_id from operatingsystem where os_delete = 0 order by os_software");

        print "document.rsdp.os_sysname.value = '" . mysqli_real_escape_string($a_rsdp_osteam['os_sysname']) . "';\n";
        print "document.rsdp.os_fqdn.value = '"    . mysqli_real_escape_string($a_rsdp_osteam['os_fqdn'])    . "';\n";

        print "document.rsdp.os_software['" . $os_software . "'].selected = true;\n";

        print "document.rsdp.os_id.value = " . $a_rsdp_osteam['os_id'] . ";\n";

        print "show_file('validate.hostname.php' + '?rsdp=' + document.rsdp.rsdp.value + '&os_sysname=' + document.rsdp.os_sysname.value);\n";

        if ($numrows > 1) {
          print "alert(\"WARNING: There are duplicate OSTeam rows (" . $numrows . ")\");\n";
        }
      }
      mysqli_free_result($q_rsdp_osteam);

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
