<?php
# Script: ticket.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "ticket.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from features");

      $q_string  = "select feat_discovered,feat_closed,feat_subject ";
      $q_string .= "from features ";
      $q_string .= "where feat_id = " . $formVars['id'];
      $q_features = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_features = mysqli_fetch_array($q_features);

      print "document.start.feat_discovered.value = '" . mysqli_real_escape_string($db, $a_features['feat_discovered']) . "';\n";
      print "document.start.feat_subject.value = '"    . mysqli_real_escape_string($db, $a_features['feat_subject'])    . "';\n";

      if ($a_features['feat_closed'] == '1971-01-01') {
        print "document.start.feat_closed.value = 'Current Date';\n";

        print "document.getElementById('feat_discovered').innerHTML = '" . mysqli_real_escape_string($db, $a_features['feat_discovered']) . "';\n";
        print "document.getElementById('feat_closed').innerHTML = '"     . mysqli_real_escape_string($db, $a_features['feat_closed'])     . "';\n";
        print "document.getElementById('feat_subject').innerHTML = '"    . mysqli_real_escape_string($db, $a_features['feat_subject'])    . "';\n";
      } else {
        print "document.start.feat_closed.value = '" . mysqli_real_escape_string($db, $a_features['feat_closed']) . "';\n";
      }

      print "document.start.feat_id.value = " . $formVars['id'] . ";\n";

      print "document.start.featupdate.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
