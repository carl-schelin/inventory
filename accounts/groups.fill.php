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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from a_groups");

      $q_string  = "select grp_disabled,grp_name,grp_email,grp_manager,";
      $q_string .= "grp_organization,grp_role,grp_status,grp_server,grp_import ";
      $q_string .= "from a_groups ";
      $q_string .= "where grp_id = " . $formVars['id'];
      $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_groups = mysqli_fetch_array($q_groups);
      mysqli_free_result($q_groups);

      $organization = return_Index($db, $a_groups['grp_organization'], "select org_id from organizations order by org_name");
      $role         = return_Index($db, $a_groups['grp_role'],         "select role_id from roles order by role_name");
      $manager      = return_Index($db, $a_groups['grp_manager'],      "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");

      print "document.groups.grp_name.value = '"      . mysqli_real_escape_string($db, $a_groups['grp_name'])      . "';\n";
      print "document.groups.grp_email.value = '"     . mysqli_real_escape_string($db, $a_groups['grp_email'])     . "';\n";

      print "document.groups.grp_organization['"  . $organization . "'].selected = true;\n";
      print "document.groups.grp_role['"          . $role         . "'].selected = true;\n";
      print "document.groups.grp_manager['"       . $manager      . "'].selected = true;\n";

      print "document.groups.grp_disabled['" . $a_groups['grp_disabled'] . "'].selected = 'true';\n";

      if ($a_groups['grp_status']) {
        print "document.groups.grp_status.checked = true;\n";
      } else {
        print "document.groups.grp_status.checked = false;\n";
      }
      if ($a_groups['grp_server']) {
        print "document.groups.grp_server.checked = true;\n";
      } else {
        print "document.groups.grp_server.checked = false;\n";
      }
      if ($a_groups['grp_import']) {
        print "document.groups.grp_import.checked = true;\n";
      } else {
        print "document.groups.grp_import.checked = false;\n";
      }

      print "document.groups.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
