<?php
# Script: outlets.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "outlets.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_outlets");

      $q_string  = "select out_deviceid,out_name,out_type,out_active,out_desc,out_facing,out_verified ";
      $q_string .= "from inv_outlets ";
      $q_string .= "where out_id = " . $formVars['id'];
      $q_inv_outlets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_outlets = mysqli_fetch_array($q_inv_outlets);
      mysqli_free_result($q_inv_outlets);

      $outdeviceid = return_Index($db, $a_inv_outlets['out_deviceid'], "select ast_id from inv_assets where ast_name != \"\" order by ast_name ");
      $outtype     = return_Index($db, $a_inv_outlets['out_type'],     "select plug_id from inv_int_plugtype order by plug_text");

      print "document.formUpdate.out_name.value = '"    . mysqli_real_escape_string($db, $a_inv_outlets['out_name'])    . "';\n";
      print "document.formUpdate.out_desc.value = '"    . mysqli_real_escape_string($db, $a_inv_outlets['out_desc'])    . "';\n";

      print "document.formUpdate.out_deviceid['" . $outdeviceid . "'].selected = true;\n";
      print "document.formUpdate.out_type['"     . $outtype     . "'].selected = true;\n";

      if ($a_inv_outlets['out_active']) {
        print "document.formUpdate.out_active.checked = true;\n";
      } else {
        print "document.formUpdate.out_active.checked = false;\n";
      }
      if ($a_inv_outlets['out_facing']) {
        print "document.formUpdate.out_facing.checked = true;\n";
      } else {
        print "document.formUpdate.out_facing.checked = false;\n";
      }
      if ($a_inv_outlets['out_verified']) {
        print "document.formUpdate.out_verified.checked = true;\n";
      } else {
        print "document.formUpdate.out_verified.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
