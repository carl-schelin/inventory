<?php
# Script: issue.open.del.php
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
    $package = "issue.open.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      $q_string  = "select det_id ";
      $q_string .= "from issue_detail ";
      $q_string .= "where det_issue = " . $formVars['id'];
      $q_issue_detail = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_issue_detail = mysql_fetch_array($q_issue_detail)) {

        logaccess($_SESSION['uid'], $package, "Deleting " . $a_issue_detail['det_id'] . " from issue_detail");

        $q_string  = "delete ";
        $q_string .= "from issue_detail ";
        $q_string .= "where det_id = " . $a_issue_detail['det_id'];
        $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      }

      $q_string  = "select sup_id ";
      $q_string .= "from issue_support ";
      $q_string .= "where sup_issue = " . $formVars['id'];
      $q_issue_support = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_issue_support = mysql_fetch_array($q_issue_support)) {
        logaccess($_SESSION['uid'], $package, "Deleting " . $a_issue_support['sup_id'] . " from sup_issue");

        $q_string  = "delete ";
        $q_string .= "from issue_support ";
        $q_string .= "where sup_id = " . $a_issue_support['sup_id'];
        $insert = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      }

      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from issue");

      $q_string  = "delete ";
      $q_string .= "from issue ";
      $q_string .= "where iss_id = " . $formVars['id'];
      $insert = mysql_query($q_string) or die($q_string . ": " . mysql_error());

      print "alert('Issue deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>
