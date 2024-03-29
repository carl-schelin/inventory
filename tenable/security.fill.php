<?php
# Script: security.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "security.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_security");

      $q_string  = "select sec_name,sec_family.sec_severity ";
      $q_string .= "from inv_security ";
      $q_string .= "where sec_id = " . $formVars['id'];
      $q_inv_security = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_security = mysqli_fetch_array($a_inv_security);
      mysqli_free_result($q_inv_security);

      $family   = return_Index($db, $a_inv_security['sec_family'],   "select fam_id from inv_family order by fam_name");
      $severity = return_Index($db, $a_inv_security['sec_severity'], "select sev_id from inv_severity order by sev_name");

      print "document.security.sec_name.value = '"   . mysqli_real_escape_string($db, $a_inv_security['sec_name'])   . "';\n";

      print "document.security.sec_family['"   . $family   . "'].selected = true;\n";
      print "document.security.sec_severity['" . $severity . "'].selected = true;\n";

      print "document.security.id.value = " . $formVars['id'] . ";\n";

      print "document.security.sec_name.focus();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
