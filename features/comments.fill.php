<?php
# Script: comments.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_features_detail");

      $q_string  = "select feat_text,feat_timestamp,feat_user ";
      $q_string .= "from inv_features_detail ";
      $q_string .= "where feat_id = " . $formVars['id'];
      $q_inv_features_detail = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_features_detail = mysqli_fetch_array($q_inv_features_detail);
      mysqli_free_result($q_inv_features_detail);

      $selected = return_Index($db, $a_inv_features_detail['feat_user'],       "select usr_id from inv_users where usr_disabled = 0 order by usr_last,usr_first");

      print "document.start.feat_text.value = '"      . mysqli_real_escape_string($db, $a_inv_features_detail['feat_text'])      . "';\n";
      print "document.start.feat_timestamp.value = '" . mysqli_real_escape_string($db, $a_inv_features_detail['feat_timestamp']) . "';\n";

      print "document.start.feat_user['" . $selected . "'].selected = true;\n";

      print "document.start.feat_id.value = " . $formVars['id'] . ";\n";

      print "document.start.featupdate.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
