<?php
# Script: grouplist.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "grouplist.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Admin)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_grouplist");

      $q_string  = "select gpl_group,gpl_user,gpl_edit ";
      $q_string .= "from inv_grouplist ";
      $q_string .= "where gpl_id = " . $formVars['id'];
      $q_inv_grouplist = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_grouplist = mysqli_fetch_array($q_inv_grouplist);
      mysqli_free_result($q_inv_grouplist);

      $q_string  = "select grp_id ";
      $q_string .= "from inv_groups ";
      $q_string .= "left join inv_grouplist on inv_grouplist.gpl_group = inv_groups.grp_id ";
      if (check_userlevel($db, $AL_Admin) == 0) {
        $q_string .= "where gpl_user = " . $_SESSION['uid'] . " ";
      }
      $q_string .= "group by grp_name";

      $gpl_group  = return_Index($db, $a_inv_grouplist['gpl_group'], $q_string);
      $gpl_user   = return_Index($db, $a_inv_grouplist['gpl_user'], "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");

      if ($gpl_group > 0) {
        print "document.formUpdate.gpl_group['" . $gpl_group   . "'].selected = true;\n";
      }
      if ($gpl_user > 0) {
        print "document.formUpdate.gpl_user['"  . $gpl_user    . "'].selected = true;\n";
      }

      if ($a_inv_grouplist['gpl_edit']) {
        print "document.formUpdate.gpl_edit.checked = true;\n";
      } else {
        print "document.formUpdate.gpl_edit.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
