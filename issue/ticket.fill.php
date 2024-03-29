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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_issue");

      $q_string  = "select iss_discovered,iss_closed,iss_subject ";
      $q_string .= "from inv_issue ";
      $q_string .= "where iss_id = " . $formVars['id'];
      $q_inv_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_inv_issue = mysqli_fetch_array($q_inv_issue);

      print "document.start.iss_discovered.value = '" . mysqli_real_escape_string($db, $a_inv_issue['iss_discovered']) . "';\n";
      print "document.start.iss_subject.value = '"    . mysqli_real_escape_string($db, $a_inv_issue['iss_subject'])    . "';\n";

      if ($a_inv_issue['iss_closed'] == '1971-01-01') {
        print "document.start.iss_closed.value = 'Current Date';\n";

        print "document.getElementById('iss_discovered').innerHTML = '" . mysqli_real_escape_string($db, $a_inv_issue['iss_discovered']) . "';\n";
        print "document.getElementById('iss_closed').innerHTML = '"     . mysqli_real_escape_string($db, $a_inv_issue['iss_closed'])     . "';\n";
        print "document.getElementById('iss_subject').innerHTML = '"    . mysqli_real_escape_string($db, $a_inv_issue['iss_subject'])    . "';\n";
      } else {
        print "document.start.iss_closed.value = '" . mysqli_real_escape_string($db, $a_inv_issue['iss_closed']) . "';\n";
      }

      print "document.start.iss_id.value = " . $formVars['id'] . ";\n";

      print "document.start.issupdate.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
