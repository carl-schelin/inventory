<?php
# Script: bugs.open.del.php
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
    $package = "bugs.open.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(4)) {
      $q_string  = "select bug_id ";
      $q_string .= "from bugs_detail ";
      $q_string .= "where bug_bug_id = " . $formVars['id'];
      $q_bugs_detail = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_bugs_detail = mysql_fetch_array($q_bugs_detail)) {

        logaccess($_SESSION['uid'], $package, "Deleting " . $a_bugs_detail['bug_id'] . " from bugs_detail");

        $q_string  = "delete ";
        $q_string .= "from bugs_detail ";
        $q_string .= "where bug_id = " . $a_bugs_detail['bug_id'];
        $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      }

      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from bugs");

      $q_string  = "delete ";
      $q_string .= "from bugs ";
      $q_string .= "where bug_id = " . $formVars['id'];
      $insert = mysql_query($q_string) or die($q_string . ": " . mysql_error());

      print "alert('Bug deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>
