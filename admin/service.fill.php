<?php
# Script: service.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "service.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_service");

      $q_string  = "select svc_name,svc_acronym,svc_availability,svc_downtime,svc_mtbf,svc_geographic,svc_mttr,svc_resource,svc_restore ";
      $q_string .= "from inv_service ";
      $q_string .= "where svc_id = " . $formVars['id'];
      $q_inv_service = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_service = mysqli_fetch_array($q_inv_service);
      mysqli_free_result($q_inv_service);

      print "document.formUpdate.svc_name.value = '"         . mysqli_real_escape_string($db, $a_inv_service['svc_name'])         . "';\n";
      print "document.formUpdate.svc_acronym.value = '"      . mysqli_real_escape_string($db, $a_inv_service['svc_acronym'])      . "';\n";
      print "document.formUpdate.svc_availability.value = '" . mysqli_real_escape_string($db, $a_inv_service['svc_availability']) . "';\n";
      print "document.formUpdate.svc_downtime.value = '"     . mysqli_real_escape_string($db, $a_inv_service['svc_downtime'])     . "';\n";
      print "document.formUpdate.svc_mtbf.value = '"         . mysqli_real_escape_string($db, $a_inv_service['svc_mtbf'])         . "';\n";
      print "document.formUpdate.svc_mttr.value = '"         . mysqli_real_escape_string($db, $a_inv_service['svc_mttr'])         . "';\n";
      print "document.formUpdate.svc_restore.value = '"      . mysqli_real_escape_string($db, $a_inv_service['svc_restore'])      . "';\n";

      if ($a_inv_service['svc_geographic']) {
        print "document.formUpdate.svc_geographic.checked = true\n;";
       } else {
        print "document.formUpdate.svc_geographic.checked = false\n;";
      }

      if ($a_inv_service['svc_resource']) {
        print "document.formUpdate.svc_resource.checked = true\n;";
       } else {
        print "document.formUpdate.svc_resource.checked = false\n;";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
