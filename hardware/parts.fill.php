<?php
# Script: parts.fill.php
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
    $package = "parts.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from parts");

      $q_string  = "select part_name,part_type,part_acronym ";
      $q_string .= "from parts ";
      $q_string .= "where part_id = " . $formVars['id'];
      $q_parts = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_parts = mysql_fetch_array($q_parts);
      mysql_free_result($q_parts);

      print "document.parts.part_name.value = '"    . mysql_real_escape_string($a_parts['part_name'])    . "';\n";
      print "document.parts.part_acronym.value = '" . mysql_real_escape_string($a_parts['part_acronym']) . "';\n";

      if ($a_parts['part_type']) {
        print "document.parts.part_type.checked = true;\n";
      } else {
        print "document.parts.part_type.checked = false;\n";
      }

      print "document.parts.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
