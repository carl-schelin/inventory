<?php
# Script: other.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "other.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_models");

      $q_string  = "select mod_vendor,mod_name,mod_type,mod_eopur,mod_eoship,mod_eol ";
      $q_string .= "from inv_models ";
      $q_string .= "where mod_id = " . $formVars['id'];
      $q_inv_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_models = mysqli_fetch_array($q_inv_models);
      mysqli_free_result($q_inv_models);

      $modvendor = return_Index($db, $a_inv_models['mod_vendor'], "select ven_id from vendors order by ven_name");
      $modtype   = return_Index($db, $a_inv_models['mod_type'],   "select type_id from parts order by part_name");

      print "document.formUpdate.mod_vendor.value = '" . mysqli_real_escape_string($db, $a_inv_models['mod_vendor']) . "';\n";
      print "document.formUpdate.mod_name.value = '"   . mysqli_real_escape_string($db, $a_inv_models['mod_name'])   . "';\n";
      print "document.formUpdate.mod_eopur.value = '"  . mysqli_real_escape_string($db, $a_inv_models['mod_eopur'])  . "';\n";
      print "document.formUpdate.mod_eoship.value = '" . mysqli_real_escape_string($db, $a_inv_models['mod_eoship']) . "';\n";
      print "document.formUpdate.mod_eol.value = '"    . mysqli_real_escape_string($db, $a_inv_models['mod_eol'])    . "';\n";

      print "document.formUpdate.mod_vendor['" . $modvendor . "'].selected = true;\n";
      print "document.formUpdate.mod_type['"   . $modtype   . "'].selected = true;\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
