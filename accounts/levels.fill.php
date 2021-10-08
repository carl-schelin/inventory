<?php
# Script: levels.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "levels.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Admin)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from levels");

      $q_string  = "select lvl_name,lvl_level,lvl_disabled ";
      $q_string .= "from levels ";
      $q_string .= "where lvl_id = " . $formVars['id'];
      $q_levels = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_levels = mysqli_fetch_array($q_levels);
      mysqli_free_result($q_levels);

      print "document.formUpdate.lvl_name.value = '"  . mysqli_real_escape_string($db, $a_levels['lvl_name'])  . "';\n";
      print "document.formUpdate.lvl_level.value = '" . mysqli_real_escape_string($db, $a_levels['lvl_level']) . "';\n";

      print "document.formUpdate.lvl_disabled['" . $a_levels['lvl_disabled'] . "'].selected = 'true';\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
