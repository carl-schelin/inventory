<?php
# Script: levels.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel($AL_Admin)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from levels");

      $q_string  = "select lvl_name,lvl_level,lvl_disabled ";
      $q_string .= "from levels ";
      $q_string .= "where lvl_id = " . $formVars['id'];
      $q_levels = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_levels = mysql_fetch_array($q_levels);
      mysql_free_result($q_levels);

      print "document.levels.lvl_name.value = '"  . mysql_real_escape_string($a_levels['lvl_name'])  . "';\n";
      print "document.levels.lvl_level.value = '" . mysql_real_escape_string($a_levels['lvl_level']) . "';\n";

      print "document.levels.lvl_disabled['" . $a_levels['lvl_disabled'] . "'].selected = 'true';\n";

      print "document.levels.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
