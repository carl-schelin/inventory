<?php
# Script: designed.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "designed.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_designed");

      $q_string  = "select san_id,san_checklist ";
      $q_string .= "from rsdp_designed ";
      $q_string .= "where san_rsdp = " . $formVars['rsdp'];
      $q_rsdp_designed = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_designed) > 0) {
        $a_rsdp_designed = mysqli_fetch_array($q_rsdp_designed);

        if ($a_rsdp_designed['san_checklist']) {
          print "document.rsdp.san_checklist.checked = true;\n";
        } else {
          print "document.rsdp.san_checklist.checked = false;\n";
        }

        print "document.rsdp.id.value = " . $a_rsdp_designed['san_id'] . ";\n";

      }

      mysqli_free_result($q_rsdp_designed);

      print "validate_Form();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
