<?php
# Script: parts.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from parts");

      $q_string  = "select part_name,part_type,part_acronym ";
      $q_string .= "from parts ";
      $q_string .= "where part_id = " . $formVars['id'];
      $q_parts = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_parts = mysqli_fetch_array($q_parts);
      mysqli_free_result($q_parts);

      print "document.formUpdate.part_name.value = '"    . mysqli_real_escape_string($db, $a_parts['part_name'])    . "';\n";
      print "document.formUpdate.part_acronym.value = '" . mysqli_real_escape_string($db, $a_parts['part_acronym']) . "';\n";

      if ($a_parts['part_type']) {
        print "document.formUpdate.part_type.checked = true;\n";
      } else {
        print "document.formUpdate.part_type.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
