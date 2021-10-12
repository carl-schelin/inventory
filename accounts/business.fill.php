<?php
# Script: business.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "business.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from business");

      $q_string  = "select bus_org,bus_unit,bus_name ";
      $q_string .= "from business ";
      $q_string .= "where bus_id = " . $formVars['id'];
      $q_business = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_business = mysqli_fetch_array($q_business);
      mysqli_free_result($q_business);

      $busorg = return_Index($db, $a_business['bus_org'], "select org_id from organizations order by org_name");

      print "document.formUpdate.bus_unit.value = '" . mysqli_real_escape_string($db, $a_business['bus_unit']) . "';\n";
      print "document.formUpdate.bus_name.value = '" . mysqli_real_escape_string($db, $a_business['bus_name']) . "';\n";

      if ($busorg > 0) {
        print "document.formUpdate.bus_org['" . $busorg  . "'].selected = true;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
