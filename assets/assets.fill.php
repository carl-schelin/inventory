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

      $q_string  = "select ast_companyid,ast_name,ast_asset,ast_serial,ast_parentid,ast_modelid,ast_unit,";
      $q_string .= "ast_vendor,ast_managed,ast_purchased,ast_built,ast_active,ast_retired,ast_reused,ast_endsupport,";
      $q_string .= "ast_facing ";
      $q_string .= "from inv_assets ";
      $q_string .= "where ast_id = " . $formVars['id'];
      $q_inv_assets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_assets = mysqli_fetch_array($q_inv_assets);
      mysqli_free_result($q_inv_assets);

      $q_string  = "select inv_id ";
      $q_string .= "from inv_inventory ";
      $q_string .= "order by inv_name ";
      $astinventory = return_Index($db, $a_inv_assets['ast_companyid'], $q_string);

      $q_string  = "select mod_id ";
      $q_string .= "from inv_models ";
      $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
      $q_string .= "order by ven_name,mod_name ";
      $astmodel = return_Index($db, $a_inv_assets['ast_modelid'], $q_string);

      print "document.formUpdate.ast_name.value = '"            . mysqli_real_escape_string($db, $a_inv_assets['ast_name'])            . "';\n";
      print "document.formUpdate.ast_asset.value = '"           . mysqli_real_escape_string($db, $a_inv_assets['ast_asset'])           . "';\n";
      print "document.formUpdate.ast_serial.value = '"          . mysqli_real_escape_string($db, $a_inv_assets['ast_serial'])          . "';\n";
      print "document.formUpdate.ast_parentid.value = '"        . mysqli_real_escape_string($db, $a_inv_assets['ast_parentid'])        . "';\n";
      print "document.formUpdate.ast_unit.value = '"            . mysqli_real_escape_string($db, $a_inv_assets['ast_unit'])            . "';\n";

      if ($a_inv_assets['ast_purchased'] != '1971-01-01') {
        print "document.formUpdate.ast_purchased.value = '"  . mysqli_real_escape_string($db, $a_inv_assets['ast_purchased']) . "';\n";
      }
      if ($a_inv_assets['ast_built'] != '1971-01-01') {
        print "document.formUpdate.ast_built.value = '"      . mysqli_real_escape_string($db, $a_inv_assets['ast_built']) . "';\n";
      }
      if ($a_inv_assets['ast_active'] != '1971-01-01') {
        print "document.formUpdate.ast_active.value = '"     . mysqli_real_escape_string($db, $a_inv_assets['ast_active']) . "';\n";
      }
      if ($a_inv_assets['ast_retired'] != '1971-01-01') {
        print "document.formUpdate.ast_retired.value = '"    . mysqli_real_escape_string($db, $a_inv_assets['ast_retired']) . "';\n";
      }
      if ($a_inv_assets['ast_reused'] != '1971-01-01') {
        print "document.formUpdate.ast_reused.value = '"     . mysqli_real_escape_string($db, $a_inv_assets['ast_reused']) . "';\n";
      }
      if ($a_inv_assets['ast_endsupport'] != '1971-01-01') {
        print "document.formUpdate.ast_endsupport.value = '" . mysqli_real_escape_string($db, $a_inv_assets['ast_endsupport']) . "';\n";
      }

      print "document.formUpdate.ast_companyid['" . $astinventory . "'].selected = true;\n";
      print "document.formUpdate.ast_modelid['" . $astmodel . "'].selected = true;\n";

      if ($a_inv_assets['ast_facing'] == 1) {
        print "document.formUpdate.ast_facing.checked = true;\n";
      } else {
        print "document.formUpdate.ast_facing.checked = false;\n";
      }
      if ($a_inv_assets['ast_vendor'] == 1) {
        print "document.formUpdate.ast_vendor.checked = true;\n";
      } else {
        print "document.formUpdate.ast_vendor.checked = false;\n";
      }
      if ($a_inv_assets['ast_managed'] == 1) {
        print "document.formUpdate.ast_managed.checked = true;\n";
      } else {
        print "document.formUpdate.ast_managed.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

      print "clear_fields()\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
