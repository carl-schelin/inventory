<?php
# Script: san.fill.php
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
    $package = "san.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from rsdp_san");

      $q_string  = "select san_sysport,san_switch,san_port,med_text,san_wwnnzone ";
      $q_string .= "from rsdp_san ";
      $q_string .= "left join int_media on int_media.med_id = rsdp_san.san_media ";
      $q_string .= "where san_id = " . $formVars['id'];
      $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_san = mysqli_fetch_array($q_rsdp_san);
      mysqli_free_result($q_rsdp_san);

      print "document.getElementById('san_sysport').innerHTML = '" . mysqli_real_escape_string($a_rsdp_san['san_sysport']) . "';\n";
      print "document.getElementById('san_switch').innerHTML = '"  . mysqli_real_escape_string($a_rsdp_san['san_switch'])  . "';\n";
      print "document.getElementById('san_port').innerHTML = '"    . mysqli_real_escape_string($a_rsdp_san['san_port'])    . "';\n";
      print "document.getElementById('san_media').innerHTML = '"   . mysqli_real_escape_string($a_rsdp_san['san_media'])   . "';\n";

      print "document.san.san_wwnnzone.value = '"    . mysqli_real_escape_string($a_rsdp_san['san_wwnnzone']) . "';\n";

      print "document.san.san_id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
