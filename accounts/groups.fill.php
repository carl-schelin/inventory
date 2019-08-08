<?php
# Script: groups.fill.php
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
    $package = "groups.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(1)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from groups");

      $q_string  = "select grp_disabled,grp_name,grp_email,grp_magic,grp_category,grp_changelog,grp_manager,grp_clfile,";
      $q_string .= "grp_clserver,grp_report,grp_organization,grp_role,grp_clscript,grp_status,grp_server,grp_import ";
      $q_string .= "from groups ";
      $q_string .= "where grp_id = " . $formVars['id'];
      $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_groups = mysql_fetch_array($q_groups);
      mysql_free_result($q_groups);

      $organization = return_Index($a_groups['grp_organization'], "select org_id from organizations order by org_name");
      $role         = return_Index($a_groups['grp_role'],         "select role_id from roles order by role_name");
      $manager      = return_Index($a_groups['grp_manager'],      "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");

      print "document.groups.grp_name.value = '"      . mysql_real_escape_string($a_groups['grp_name'])      . "';\n";
      print "document.groups.grp_email.value = '"     . mysql_real_escape_string($a_groups['grp_email'])     . "';\n";
      print "document.groups.grp_magic.value = '"     . mysql_real_escape_string($a_groups['grp_magic'])     . "';\n";
      print "document.groups.grp_category.value = '"  . mysql_real_escape_string($a_groups['grp_category'])  . "';\n";
      print "document.groups.grp_changelog.value = '" . mysql_real_escape_string($a_groups['grp_changelog']) . "';\n";
      print "document.groups.grp_clfile.value = '"    . mysql_real_escape_string($a_groups['grp_clfile'])    . "';\n";
      print "document.groups.grp_clserver.value = '"  . mysql_real_escape_string($a_groups['grp_clserver'])  . "';\n";
      print "document.groups.grp_clscript.value = '"  . mysql_real_escape_string($a_groups['grp_clscript'])  . "';\n";
      print "document.groups.grp_report.value = '"    . mysql_real_escape_string($a_groups['grp_report'])    . "';\n";

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
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
