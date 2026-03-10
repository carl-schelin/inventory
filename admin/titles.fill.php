<?php
# Script: titles.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "titles.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_titles");

      $q_string  = "select tit_name,tit_level,tit_order ";
      $q_string .= "from inv_titles ";
      $q_string .= "where tit_id = " . $formVars['id'];
      $q_inv_titles = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_titles = mysqli_fetch_array($q_inv_titles);
      mysqli_free_result($q_inv_titles);

      print "document.formUpdate.tit_name.value = '"   . mysqli_real_escape_string($db, $a_inv_titles['tit_name'])  . "';\n";
      print "document.formUpdate.tit_level.value  = '" . mysqli_real_escape_string($db, $a_inv_titles['tit_level']) . "';\n";
      print "document.formUpdate.tit_order.value = '"  . mysqli_real_escape_string($db, $a_inv_titles['tit_order']) . "';\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
