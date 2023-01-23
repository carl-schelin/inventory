<?php
# Script: subzones.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "subzones.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_sub_zones");

      $q_string  = "select sub_name,sub_zone,sub_description ";
      $q_string .= "from inv_sub_zones ";
      $q_string .= "where sub_id = " . $formVars['id'];
      $q_inv_sub_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_sub_zones = mysqli_fetch_array($q_inv_sub_zones);
      mysqli_free_result($q_inv_sub_zones);

      $subzone     = return_Index($db, $a_inv_sub_zones['sub_zone'],     "select zone_id from inv_net_zones order by zone_zone");

      print "document.formUpdate.sub_name.value = '"        . mysqli_real_escape_string($db, $a_inv_sub_zones['sub_name'])        . "';\n";
      print "document.formUpdate.sub_description.value = '" . mysqli_real_escape_string($db, $a_inv_sub_zones['sub_description']) . "';\n";

      print "document.formUpdate.sub_zone['"     . $subzone               . "'].selected = true;\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
