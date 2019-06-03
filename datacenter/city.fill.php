<?php
# Script: city.fill.php
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
    $package = "city.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from cities");

      $q_string  = "select ct_city,ct_state,ct_clli ";
      $q_string .= "from cities ";
      $q_string .= "where ct_id = " . $formVars['id'];
      $q_cities = mysql_query($q_string) or die (mysql_error());
      $a_cities = mysql_fetch_array($q_cities);
      mysql_free_result($q_cities);

      $state = return_Index($a_cities['ct_state'], "select st_id from states order by st_state");

      print "document.cities.ct_city.value = '"    . mysql_real_escape_string($a_cities['ct_city'])    . "';\n";
      print "document.cities.ct_clli.value = '"    . mysql_real_escape_string($a_cities['ct_clli'])    . "';\n";

      print "document.cities.ct_state['" . $state . "'].selected = true;\n";

      print "document.cities.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
