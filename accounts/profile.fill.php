<?php
# Script: profile.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "profile.fill.php";
    $formVars['id'] = 0;
    if (isset($_SESSION['uid'])) {
      $formVars['id'] = clean($_SESSION['uid'], 10);
    }

    if (check_userlevel($db, $AL_Guest)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_users");

      $q_string  = "select usr_id,usr_first,usr_last,usr_email,usr_phone,usr_freq,usr_notify,";
      $q_string .= "usr_theme,usr_reset,usr_manager,usr_title ";
      $q_string .= "from inv_users ";
      $q_string .= "where usr_id = " . $formVars['id'];
      $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_users = mysqli_fetch_array($q_inv_users);
      mysqli_free_result($q_inv_users);

      $theme    = return_Index($db, $a_inv_users['usr_theme'],    "select theme_id from inv_themes order by theme_title");
      $manager  = return_Index($db, $a_inv_users['usr_manager'],  "select usr_id from inv_users where usr_disabled = 0 order by usr_last,usr_first");
      $title    = return_Index($db, $a_inv_users['usr_title'],    "select tit_id from inv_titles order by tit_name");

      print "document.user.usr_first.value = '"      . mysqli_real_escape_string($db, $a_inv_users['usr_first'])    . "';\n";
      print "document.user.usr_last.value = '"       . mysqli_real_escape_string($db, $a_inv_users['usr_last'])     . "';\n";
      print "document.user.usr_email.value = '"      . mysqli_real_escape_string($db, $a_inv_users['usr_email'])    . "';\n";
      print "document.user.usr_phone.value = '"      . mysqli_real_escape_string($db, $a_inv_users['usr_phone'])    . "';\n";
      print "document.user.usr_notify.value = '"     . mysqli_real_escape_string($db, $a_inv_users['usr_notify'])   . "';\n";
      print "document.user.usr_freq.value = '"       . mysqli_real_escape_string($db, $a_inv_users['usr_freq'])     . "';\n";

      if ($theme > 0) {
        print "document.user.usr_theme['"    . $theme     . "'].selected = true;\n";
      }
      if ($manager > 0) {
        print "document.user.usr_manager['"  . $manager   . "'].selected = true;\n";
      }
      if ($title > 0) {
        print "document.user.usr_title['"    . $title     . "'].selected = true;\n";
      }

      if ($a_inv_users['usr_reset']) {
        print "document.user.usr_reset.checked = true;\n";
      } else {
        print "document.user.usr_reset.checked = false;\n";
      }

      print "document.user.update.disabled = false;\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
