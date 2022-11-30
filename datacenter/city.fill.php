<?php
# Script: city.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "city.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_cities");

      $q_string  = "select ct_city,ct_state,ct_clli ";
      $q_string .= "from inv_cities ";
      $q_string .= "where ct_id = " . $formVars['id'];
      $q_inv_cities = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_inv_cities = mysqli_fetch_array($q_inv_cities);
      mysqli_free_result($q_inv_cities);

      $state = return_Index($db, $a_inv_cities['ct_state'], "select st_id from inv_states order by st_state");

      print "document.formUpdate.ct_city.value = '"    . mysqli_real_escape_string($db, $a_inv_cities['ct_city'])    . "';\n";
      print "document.formUpdate.ct_clli.value = '"    . mysqli_real_escape_string($db, $a_inv_cities['ct_clli'])    . "';\n";

      if ($state > 0) {
        print "document.formUpdate.ct_state['" . $state . "'].selected = true;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
