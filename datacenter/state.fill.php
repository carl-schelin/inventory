<?php
# Script: state.fill.php
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
    $package = "state.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from states");

      $q_string  = "select st_state,st_acronym,st_country ";
      $q_string .= "from states ";
      $q_string .= "where st_id = " . $formVars['id'];
      $q_states = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_states = mysqli_fetch_array($q_states);
      mysqli_free_result($q_states);

      $country = return_Index($a_states['st_country'], "select cn_id from country order by cn_country");

      print "document.states.st_acronym.value = '"  . mysqli_real_escape_string($a_states['st_acronym'])   . "';\n";
      print "document.states.st_state.value = '"    . mysqli_real_escape_string($a_states['st_state'])    . "';\n";

      print "document.states.st_country['" . $country . "'].selected = true;\n";

      print "document.states.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
