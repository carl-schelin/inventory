<?php
# Script: country.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "country.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_country");

      $q_string  = "select cn_acronym,cn_country ";
      $q_string .= "from inv_country ";
      $q_string .= "where cn_id = " . $formVars['id'];
      $q_inv_country = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_inv_country = mysqli_fetch_array($q_inv_country);
      mysqli_free_result($q_inv_country);

      print "document.formUpdate.cn_acronym.value = '" . mysqli_real_escape_string($db, $a_inv_country['cn_acronym']) . "';\n";
      print "document.formUpdate.cn_country.value = '" . mysqli_real_escape_string($db, $a_inv_country['cn_country']) . "';\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
