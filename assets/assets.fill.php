<?php
# Script: assets.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "assets.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_assets");

      $q_string  = "select ast_asset,ast_serial,ast_parentid,ast_modelid ";
      $q_string .= "from inv_assets ";
      $q_string .= "where ast_id = " . $formVars['id'];
      $q_inv_assets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_assets = mysqli_fetch_array($q_inv_assets);
      mysqli_free_result($q_inv_assets);

      $astmodel = return_Index($db, $a_inv_assets['ast_modelid'], "select mod_id from inv_models where mod_primary = 1 order by mod_name");

      print "document.formUpdate.ast_asset.value = '"     . mysqli_real_escape_string($db, $a_inv_assets['ast_asset'])     . "';\n";
      print "document.formUpdate.ast_serial.value = '"    . mysqli_real_escape_string($db, $a_inv_assets['ast_serial'])    . "';\n";
      print "document.formUpdate.ast_parentid.value = '"  . mysqli_real_escape_string($db, $a_inv_assets['ast_parentid'])  . "';\n";

      print "document.formUpdate.ast_modelid['" . $astmodel . "'].selected = true;\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

      print "clear_fields()\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
