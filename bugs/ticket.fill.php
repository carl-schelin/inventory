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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from bugs");

      $q_string  = "select bug_discovered,bug_closed,bug_subject ";
      $q_string .= "from bugs ";
      $q_string .= "where bug_id = " . $formVars['id'];
      $q_bugs = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_bugs = mysqli_fetch_array($q_bugs);

      print "document.start.bug_discovered.value = '" . mysqli_real_escape_string($a_bugs['bug_discovered']) . "';\n";
      print "document.start.bug_subject.value = '"    . mysqli_real_escape_string($a_bugs['bug_subject'])    . "';\n";

      if ($a_bugs['bug_closed'] == '0000-00-00') {
        print "document.start.bug_closed.value = 'Current Date';\n";

        print "document.getElementById('bug_discovered').innerHTML = '" . mysqli_real_escape_string($a_bugs['bug_discovered']) . "';\n";
        print "document.getElementById('bug_closed').innerHTML = '"     . mysqli_real_escape_string($a_bugs['bug_closed'])     . "';\n";
        print "document.getElementById('bug_subject').innerHTML = '"    . mysqli_real_escape_string($a_bugs['bug_subject'])    . "';\n";
      } else {
        print "document.start.bug_closed.value = '" . mysqli_real_escape_string($a_bugs['bug_closed']) . "';\n";
      }

      print "document.start.bug_id.value = " . $formVars['id'] . ";\n";

      print "document.start.bugupdate.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
