<?php
# Script: patching.fill.php
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
    $package = "patching.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from patching");

      $q_string  = "select patch_name,patch_user,patch_group,patch_date ";
      $q_string .= "from patching ";
      $q_string .= "where patch_id = " . $formVars['id'];
      $q_patching = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_patching = mysqli_fetch_array($q_patching);
      mysqli_free_result($q_patching);

      $user  = return_Index($db, $a_patching['patch_user'],   "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");
      $group = return_Index($db, $a_patching['patch_group'],  "select grp_id from groups where grp_disabled = 0 order by grp_name");

      print "document.patching.patch_name.value = '" . mysqli_real_escape_string($a_patching['patch_name']) . "';\n";
      print "document.patching.patch_date.value = '" . mysqli_real_escape_string($a_patching['patch_date']) . "';\n";

      print "document.patching.patch_user['"  . $user  . "'].selected = true;\n";
      print "document.patching.patch_group['" . $group . "'].selected = true;\n";

      print "document.patching.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
