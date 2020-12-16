<?php
# Script: support.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "support.fill.php";

    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from issue_support");

      $q_string  = "select sup_company,sup_case,sup_contact,sup_email,sup_phone,sup_govid,sup_timestamp,sup_rating ";
      $q_string .= "from issue_support ";
      $q_string .= "where sup_id = " . $formVars['id'];
      $q_issue_support = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_issue_support = mysqli_fetch_array($q_issue_support);

      print "document.start.sup_company.value = '"   . mysqli_real_escape_string($a_issue_support['sup_company'])   . "';\n";
      print "document.start.sup_case.value = '"      . mysqli_real_escape_string($a_issue_support['sup_case'])      . "';\n";
      print "document.start.sup_contact.value = '"   . mysqli_real_escape_string($a_issue_support['sup_contact'])   . "';\n";
      print "document.start.sup_email.value = '"     . mysqli_real_escape_string($a_issue_support['sup_email'])     . "';\n";
      print "document.start.sup_phone.value = '"     . mysqli_real_escape_string($a_issue_support['sup_phone'])     . "';\n";
      print "document.start.sup_govid.value = '"     . mysqli_real_escape_string($a_issue_support['sup_govid'])     . "';\n";
      print "document.start.sup_timestamp.value = '" . mysqli_real_escape_string($a_issue_support['sup_timestamp']) . "';\n";

      print "document.start.sup_rating['" . $a_issue_support['sup_rating'] . "'].checked = true;\n";

      print "document.start.sup_id.value = " . $formVars['id'] . ";\n";

      print "document.start.supupdate.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
