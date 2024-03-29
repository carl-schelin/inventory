<?php
# Script: organization.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "organization.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from organization");

      $q_string  = "select org_name,org_manager ";
      $q_string  = "select org_name ";
      $q_string .= "from inv_organizations ";
      $q_string .= "where org_id = " . $formVars['id'];
      $q_inv_organizations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_organizations = mysqli_fetch_array($q_inv_organizations);
      mysqli_free_result($q_inv_organizations);

      $manager = return_Index($db, $a_inv_organizations['org_manager'], 'select usr_id from inv_users where usr_disabled = 0 order by usr_last,usr_first');

      print "document.formUpdate.org_name.value = '" . mysqli_real_escape_string($db, $a_inv_organizations['org_name']) . "';\n";

      if ($manager > 0) {
        print "document.formUpdate.org_manager['" . $manager . "'].selected = true;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
