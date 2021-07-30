<?php
# Script: storage.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "storage.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from models");

      $q_string  = "select mod_vendor,mod_name,mod_size,mod_speed,mod_eopur,mod_eoship,mod_eol ";
      $q_string .= "from models ";
      $q_string .= "where mod_id = " . $formVars['id'];
      $q_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_models = mysqli_fetch_array($q_models);
      mysqli_free_result($q_models);

      $modvendor = return_Index($db, $a_models['mod_vendor'], "select ven_id from vendors order by ven_name");

      print "document.updateDialog.mod_name.value = '"   . mysqli_real_escape_string($db, $a_models['mod_name'])   . "';\n";
      print "document.updateDialog.mod_size.value = '"   . mysqli_real_escape_string($db, $a_models['mod_size'])   . "';\n";
      print "document.updateDialog.mod_speed.value = '"  . mysqli_real_escape_string($db, $a_models['mod_speed'])  . "';\n";
      print "document.updateDialog.mod_eopur.value = '"  . mysqli_real_escape_string($db, $a_models['mod_eopur'])  . "';\n";
      print "document.updateDialog.mod_eoship.value = '" . mysqli_real_escape_string($db, $a_models['mod_eoship']) . "';\n";
      print "document.updateDialog.mod_eol.value = '"    . mysqli_real_escape_string($db, $a_models['mod_eol'])    . "';\n";

      print "document.updateDialog.mod_vendor['" . $modvendor . "'].selected = true;\n";

      print "document.updateDialog.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
