<?php
# Script: users.fill.php
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
    $package = "users.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['pwd_id'] = 0;
    if (isset($_GET['pwd_id'])) {
      $formVars['pwd_id'] = clean($_GET['pwd_id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['id'] == '' || $formVars['id'] == 0) {
        logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from syspwd");

        $q_string  = "select pwd_user,pwd_gecos ";
        $q_string .= "from syspwd ";
        $q_string .= "where pwd_id = " . $formVars['pwd_id'] . " ";
        $q_syspwd = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_syspwd = mysqli_fetch_array($q_syspwd);
        mysqli_free_result($q_syspwd);

        $gecos = explode(",", $a_syspwd['pwd_gecos']);

        $username = $a_syspwd['pwd_user'];
        $name = $gecos[0];
        $email = $gecos[1];
        $account = 0;
        $comment = '';
        $locked = 0;
        $ticket = '';

        print "document.edit.mu_update.disabled = true;\n";
      } else {
        logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from manageusers");

        $q_string  = "select mu_username,mu_name,mu_email,mu_account,mu_comment,mu_locked,mu_ticket ";
        $q_string .= "from manageusers ";
        $q_string .= "where mu_id = " . $formVars['id'];
        $q_manageusers = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_manageusers = mysqli_fetch_array($q_manageusers);
        mysqli_free_result($q_manageusers);

        $username = $a_manageusers['mu_username'];
        $name     = $a_manageusers['mu_name'];
        $email    = $a_manageusers['mu_email'];
        $account  = $a_manageusers['mu_account'];
        $comment  = $a_manageusers['mu_comment'];
        $locked   = $a_manageusers['mu_locked'];
        $ticket   = $a_manageusers['mu_ticket'];

        print "document.edit.mu_id.value = " . $formVars['id'] . ";\n";

        print "document.edit.mu_update.disabled = false;\n";
      }

      print "document.edit.mu_username.value = '" . mysqli_real_escape_string($db, $username) . "';\n";
      print "document.edit.mu_name.value = '"     . mysqli_real_escape_string($db, $name)     . "';\n";
      print "document.edit.mu_email.value = '"    . mysqli_real_escape_string($db, $email)    . "';\n";
      print "document.edit.mu_comment.value = '"  . mysqli_real_escape_string($db, $comment)  . "';\n";
      print "document.edit.mu_ticket.value = '"   . mysqli_real_escape_string($db, $ticket)   . "';\n";

      if ($locked) {
        print "document.edit.mu_locked.checked = true;\n";
      } else {
        print "document.edit.mu_locked.checked = false;\n";
      }

      print "document.edit.mu_account['" . $account . "'].checked = true;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
