<?php
# Script: state.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "state.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_states");

      $q_string  = "select st_state,st_acronym,st_country ";
      $q_string .= "from inv_states ";
      $q_string .= "where st_id = " . $formVars['id'];
      $q_inv_states = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_inv_states = mysqli_fetch_array($q_inv_states);
      mysqli_free_result($q_inv_states);

      $country = return_Index($db, $a_inv_states['st_country'], "select cn_id from country order by cn_country");

      print "document.formUpdate.st_acronym.value = '"  . mysqli_real_escape_string($db, $a_inv_states['st_acronym'])   . "';\n";
      print "document.formUpdate.st_state.value = '"    . mysqli_real_escape_string($db, $a_inv_states['st_state'])    . "';\n";

      if ($country > 0) {
        print "document.formUpdate.st_country['" . $country . "'].selected = true;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
