<?php
# Script: users.fill.php
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
    $package = "users.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from manageusers");

      $q_string  = "select mu_username,mu_name,mu_email,mu_account,mu_comment,mu_locked,mu_ticket ";
      $q_string .= "from manageusers ";
      $q_string .= "where mu_id = " . $formVars['id'];
      $q_manageusers = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_manageusers = mysqli_fetch_array($q_manageusers);
      mysqli_free_result($q_manageusers);

      print "document.users.mu_username.value = '" . mysqli_real_escape_string($a_manageusers['mu_username']) . "';\n";
      print "document.users.mu_name.value = '"     . mysqli_real_escape_string($a_manageusers['mu_name'])     . "';\n";
      print "document.users.mu_email.value = '"    . mysqli_real_escape_string($a_manageusers['mu_email'])    . "';\n";
      print "document.users.mu_comment.value = '"  . mysqli_real_escape_string($a_manageusers['mu_comment'])  . "';\n";
      print "document.users.mu_ticket.value = '"   . mysqli_real_escape_string($a_manageusers['mu_ticket'])   . "';\n";

      print "document.users.mu_account[" . $a_manageusers['mu_account'] . "].checked = true;\n";

      if ($a_manageusers['mu_locked']) {
        print "document.users.mu_locked.checked = true;\n";
      } else {
        print "document.users.mu_locked.checked = false;\n";
      }

      print "document.users.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
