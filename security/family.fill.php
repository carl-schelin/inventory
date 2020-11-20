<?php
# Script: family.fill.php
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
    $package = "family.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from family");

      $q_string  = "select fam_name ";
      $q_string .= "from family ";
      $q_string .= "where fam_id = " . $formVars['id'];
      $q_family = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_family = mysqli_fetch_array($q_family);
      mysqli_free_result($q_family);

      print "document.family.fam_name.value = '"   . mysqli_real_escape_string($a_family['fam_name'])   . "';\n";

      print "document.family.id.value = " . $formVars['id'] . ";\n";

      print "document.family.update.disabled = false;\n";
      print "document.family.fam_name.focus();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
