<?php
# Script: tags.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Fill in the table for editing.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "tags.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from tags");

      $q_string  = "select tag_name,tag_view ";
      $q_string .= "from tags ";
      $q_string .= "where tag_id = " . $formVars['id'];
      $q_tags = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_tags = mysql_fetch_array($q_tags);
      mysql_free_result($q_tags);

      print "document.edit.tag_name.value = '" . mysql_real_escape_string($a_tags['tag_name']) . "';\n";

      print "document.edit.tag_view['" . $a_tags['tag_view'] . "'].checked = true;\n";

      print "document.edit.tag_id.value = " . $formVars['id'] . ";\n";

      print "document.edit.tag_id.disabled = false;\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
