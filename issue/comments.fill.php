<?php
# Script: comments.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Fill in the table for editing.
#

  header('Content-Type: text/javascript');

  include ('settings.php');
  $called = 'yes';
  include ($Loginpath . '/check.php');
  include ($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "comments.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from comments");

      $q_string  = "select det_text,det_timestamp,det_user ";
      $q_string .= "from issue_detail ";
      $q_string .= "where det_id = " . $formVars['id'];
      $q_issue_detail = mysql_query($q_string) or die (mysql_error());
      $a_issue_detail = mysql_fetch_array($q_issue_detail);
      mysql_free_result($q_issue_detail);

      $selected = return_Index($a_issue_detail['det_user'],       "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");

      print "document.start.det_text.value = '"      . mysql_real_escape_string($a_issue_detail['det_text'])      . "';\n";
      print "document.start.det_timestamp.value = '" . mysql_real_escape_string($a_issue_detail['det_timestamp']) . "';\n";

      print "document.start.det_user['" . $selected . "'].selected = true;\n";

      print "document.start.det_id.value = " . $formVars['id'] . ";\n";

      print "document.start.detupdate.disabled = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
