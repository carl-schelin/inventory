<?php
# Script: tags.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from tags");

      $q_string  = "select tag_name,tag_view ";
      $q_string .= "from tags ";
      $q_string .= "where tag_id = " . $formVars['id'] . " and tag_type = 1 ";
      $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_tags = mysqli_fetch_array($q_tags);
      mysqli_free_result($q_tags);

      print "document.edit.tag_name.value = '" . mysqli_real_escape_string($db, $a_tags['tag_name']) . "';\n";

      print "document.edit.tag_view['" . $a_tags['tag_view'] . "'].checked = true;\n";

      print "document.edit.tag_id.value = " . $formVars['id'] . ";\n";

      print "document.edit.tag_update.disabled = false;\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
