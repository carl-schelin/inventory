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

      $q_string  = "select bus_name,bus_organization,bus_manager ";
      $q_string .= "from business ";
      $q_string .= "where bus_id = " . $formVars['id'];
      $q_business = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_business = mysqli_fetch_array($q_business);
      mysqli_free_result($q_business);

      $organization = return_Index($db, $a_business['bus_organization'], "select org_id from inv_organizations order by org_name");
      $manager = return_Index($db, $a_business['bus_manager'], 'select usr_id from inv_users where usr_disabled = 0 order by usr_last,usr_first');

      print "document.formUpdate.bus_name.value = '" . mysqli_real_escape_string($db, $a_business['bus_name']) . "';\n";

      if ($organization > 0) {
        print "document.formUpdate.bus_organization['" . $organization  . "'].selected = true;\n";
      }
      if ($manager > 0) {
        print "document.formUpdate.bus_manager['"      . $manager       . "'].selected = true;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
