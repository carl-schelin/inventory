<?php
# Script: groups.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "groups.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Admin)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_groups");

      $q_string  = "select grp_disabled,grp_name,grp_email,grp_manager,";
      $q_string .= "grp_department,grp_status,grp_server,grp_import ";
      $q_string .= "from inv_groups ";
      $q_string .= "where grp_id = " . $formVars['id'];
      $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_groups = mysqli_fetch_array($q_inv_groups);
      mysqli_free_result($q_inv_groups);

      $department = return_Index($db, $a_inv_groups['grp_department'], "select dep_id from department order by dep_name");
      $manager    = return_Index($db, $a_inv_groups['grp_manager'],    "select usr_id from inv_users where usr_disabled = 0 order by usr_last,usr_first");

      print "document.formUpdate.grp_name.value = '"      . mysqli_real_escape_string($db, $a_inv_groups['grp_name'])      . "';\n";
      print "document.formUpdate.grp_email.value = '"     . mysqli_real_escape_string($db, $a_inv_groups['grp_email'])     . "';\n";

      if ($department > 0) {
        print "document.formUpdate.grp_department['"    . $department . "'].selected = true;\n";
      }
      if ($manager > 0) {
        print "document.formUpdate.grp_manager['"       . $manager      . "'].selected = true;\n";
      }

      print "document.formUpdate.grp_disabled['" . $a_inv_groups['grp_disabled'] . "'].selected = 'true';\n";

      if ($a_inv_groups['grp_status']) {
        print "document.formUpdate.grp_status.checked = true;\n";
      } else {
        print "document.formUpdate.grp_status.checked = false;\n";
      }
      if ($a_inv_groups['grp_server']) {
        print "document.formUpdate.grp_server.checked = true;\n";
      } else {
        print "document.formUpdate.grp_server.checked = false;\n";
      }
      if ($a_inv_groups['grp_import']) {
        print "document.formUpdate.grp_import.checked = true;\n";
      } else {
        print "document.formUpdate.grp_import.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
