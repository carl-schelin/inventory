<?php
# Script: type.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "type.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from int_types");

      $q_string  = "select itp_name,itp_acronym,itp_description ";
      $q_string .= "from int_types ";
      $q_string .= "where itp_id = " . $formVars['id'];
      $q_int_types = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      $a_int_types = mysqli_fetch_array($q_int_types);
      mysqli_free_result($q_int_types);

      print "document.interfacetype.itp_name.value = '"        . mysqli_real_escape_string($db, $a_int_types['itp_name'])        . "';\n";
      print "document.interfacetype.itp_acronym.value = '"     . mysqli_real_escape_string($db, $a_int_types['itp_acronym'])     . "';\n";
      print "document.interfacetype.itp_description.value = '" . mysqli_real_escape_string($db, $a_int_types['itp_description']) . "';\n";

      print "document.interfacetype.id.value = " . $formVars['id'] . ";\n";

      print "document.interfacetype.update.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
