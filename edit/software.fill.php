<?php
# Script: software.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Fill in the table for editing.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "software.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from software");

      $q_string  = "select svr_id,svr_companyid,svr_softwareid,svr_groupid,svr_certid,";
      $q_string .= "svr_facing,svr_primary,svr_locked ";
      $q_string .= "from svr_software ";
      $q_string .= "where svr_id = " . $formVars['id'];
      $q_svr_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_svr_software = mysqli_fetch_array($q_svr_software);
      mysqli_free_result($q_svr_software);

      $softwareid      = return_Index($db, $a_svr_software['svr_softwareid'], "select sw_id from software left join products on products.prod_id = software.sw_product order by sw_software,prod_name");
      $groupid         = return_Index($db, $a_svr_software['svr_groupid'],    "select grp_id from inv_groups where grp_disabled = 0 order by grp_name");
      $certid          = return_Index($db, $a_svr_software['svr_certid'],     "select cert_id from certs order by cert_url");

      if ($softwareid > 0) {
        print "document.formSoftwareUpdate.svr_softwareid['"   . $softwareid . "'].selected = true;\n";
      }
      if ($groupid > 0) {
        print "document.formSoftwareUpdate.svr_groupid['"      . $groupid    . "'].selected = true;\n";
      }
      if ($certid > 0) {
        print "document.formSoftwareUpdate.svr_certid['"       . $certid     . "'].selected = true;\n";
      }

      if ($a_svr_software['svr_facing']) {
        print "document.formSoftwareUpdate.svr_facing.checked = true;\n";
      } else {
        print "document.formSoftwareUpdate.svr_facing.checked = false;\n";
      }
      if ($a_svr_software['svr_primary']) {
        print "document.formSoftwareUpdate.svr_primary.checked = true;\n";
      } else {
        print "document.formSoftwareUpdate.svr_primary.checked = false;\n";
      }
      if ($a_svr_software['svr_locked']) {
        print "document.formSoftwareUpdate.svr_locked.checked = true;\n";
      } else {
        print "document.formSoftwareUpdate.svr_locked.checked = false;\n";
      }

      print "document.formSoftwareUpdate.svr_id.value = " . $formVars['id'] . ";\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
