<?php
# Script: service.fill.php
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
    $package = "service.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from service");

      $q_string  = "select svc_name,svc_acronym,svc_availability,svc_downtime,svc_mtbf,svc_geographic,svc_mttr,svc_resource,svc_restore ";
      $q_string .= "from service ";
      $q_string .= "where svc_id = " . $formVars['id'];
      $q_service = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_service = mysqli_fetch_array($q_service);
      mysqli_free_result($q_service);

      print "document.service.svc_name.value = '"         . mysqli_real_escape_string($a_service['svc_name'])         . "';\n";
      print "document.service.svc_acronym.value = '"      . mysqli_real_escape_string($a_service['svc_acronym'])      . "';\n";
      print "document.service.svc_availability.value = '" . mysqli_real_escape_string($a_service['svc_availability']) . "';\n";
      print "document.service.svc_downtime.value = '"     . mysqli_real_escape_string($a_service['svc_downtime'])     . "';\n";
      print "document.service.svc_mtbf.value = '"         . mysqli_real_escape_string($a_service['svc_mtbf'])         . "';\n";
      print "document.service.svc_mttr.value = '"         . mysqli_real_escape_string($a_service['svc_mttr'])         . "';\n";
      print "document.service.svc_restore.value = '"      . mysqli_real_escape_string($a_service['svc_restore'])      . "';\n";

      if ($a_service['svc_geographic']) {
        print "document.service.svc_geographic.checked = true\n;";
       } else {
        print "document.service.svc_geographic.checked = false\n;";
      }

      if ($a_service['svc_resource']) {
        print "document.service.svc_resource.checked = true\n;";
       } else {
        print "document.service.svc_resource.checked = false\n;";
      }

      print "document.service.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
