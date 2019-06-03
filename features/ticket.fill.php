<?php
# Script: ticket.fill.php
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
    $package = "ticket.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from features");

      $q_string  = "select feat_discovered,feat_closed,feat_subject ";
      $q_string .= "from features ";
      $q_string .= "where feat_id = " . $formVars['id'];
      $q_features = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_features = mysql_fetch_array($q_features);

      print "document.start.feat_discovered.value = '" . mysql_real_escape_string($a_features['feat_discovered']) . "';\n";
      print "document.start.feat_subject.value = '"    . mysql_real_escape_string($a_features['feat_subject'])    . "';\n";

      if ($a_features['feat_closed'] == '0000-00-00') {
        print "document.start.feat_closed.value = 'Current Date';\n";

        print "document.getElementById('feat_discovered').innerHTML = '" . mysql_real_escape_string($a_features['feat_discovered']) . "';\n";
        print "document.getElementById('feat_closed').innerHTML = '"     . mysql_real_escape_string($a_features['feat_closed'])     . "';\n";
        print "document.getElementById('feat_subject').innerHTML = '"    . mysql_real_escape_string($a_features['feat_subject'])    . "';\n";
      } else {
        print "document.start.feat_closed.value = '" . mysql_real_escape_string($a_features['feat_closed']) . "';\n";
      }

      print "document.start.feat_id.value = " . $formVars['id'] . ";\n";

      print "document.start.featupdate.disabled = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
