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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from sub_zones");

      $q_string  = "select sub_name,sub_zone,sub_description ";
      $q_string .= "from sub_zones ";
      $q_string .= "where sub_id = " . $formVars['id'];
      $q_sub_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_sub_zones = mysqli_fetch_array($q_sub_zones);
      mysqli_free_result($q_sub_zones);

      $subzone     = return_Index($db, $a_sub_zones['sub_zone'],     "select zone_id from net_zones order by zone_zone");

      print "document.updateDialog.sub_name.value = '"        . mysqli_real_escape_string($db, $a_sub_zones['sub_name'])        . "';\n";
      print "document.updateDialog.sub_description.value = '" . mysqli_real_escape_string($db, $a_sub_zones['sub_description']) . "';\n";

      print "document.updateDialog.sub_zone['"     . $subzone               . "'].selected = true;\n";

      print "document.updateDialog.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
