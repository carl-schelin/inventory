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

      $q_string  = "select com_text,com_timestamp,com_user ";
      $q_string .= "from comments ";
      $q_string .= "where com_id = " . $formVars['id'];
      $q_comments = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_comments = mysql_fetch_array($q_comments);
      mysql_free_result($q_comments);

      $selected = return_Index($a_comments['com_user'],       "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");

      print "document.edit.com_text.value = '"      . mysql_real_escape_string($a_comments['com_text'])      . "';\n";
      print "document.edit.com_timestamp.value = '" . mysql_real_escape_string($a_comments['com_timestamp']) . "';\n";

      print "document.edit.com_user['" . $selected . "'].selected = true;\n";

      print "document.edit.com_id.value = " . $formVars['id'] . ";\n";

      print "document.edit.comupdate.disabled = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
