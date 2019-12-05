<?php
# Script: grouplist.del.php
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
    $package = "grouplist.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Admin)) {
      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from grouplist");

# get the guy you're trying to delete's group id
      $q_string  = "select gpl_group ";
      $q_string .= "from grouplist ";
      $q_string .= "where gpl_id = " . $formVars['id'] . " ";
      $q_grouplist = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_grouplist = mysql_fetch_array($q_grouplist);
      
# now check to see if the deleter is in the same group as the deletee
      $q_string  = "select gpl_id ";
      $q_string .= "from grouplist ";
      if (check_userlevel($AL_Admin) == 0) {
        $q_string .= "where gpl_user = " . $_SESSION['uid'] . " and gpl_group = " . $a_grouplist['gpl_group'] . " ";
      }
      $q_gltest = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
# you are in fact a member of the same group (or an admin)
      if (mysql_num_rows($q_gltest) > 0) {
        $q_string  = "delete ";
        $q_string .= "from grouplist ";
        $q_string .= "where gpl_id = " . $formVars['id'];
        $insert = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));

        print "alert('Membership removed.');\n";
      } else {
        print "alert('You are not allowed to manage groups you aren\'t a member of.');\n";
      }
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>
