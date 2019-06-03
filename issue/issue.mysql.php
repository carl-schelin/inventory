<?php
# Script: issue.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login($AL_Edit);

  $package = "issue.mysql.php";

  $formVars['iss_companyid']     = clean($_POST['id'],               10);
  $formVars['iss_discovered']    = clean($_POST['iss_discovered'],   15);
  $formVars['iss_user']          = clean($_POST['iss_user'],         10);
  $formVars['iss_subject']       = clean($_POST['iss_subject'],      70);

  logaccess($_SESSION['uid'], $package, "Creating a new record.");

  if (strlen($formVars['iss_subject']) > 0) {

    $query = "insert into issue set iss_id = NULL, " . 
      "iss_companyid  =   " . $formVars['iss_companyid']  . "," . 
      "iss_discovered = \"" . $formVars['iss_discovered'] . "\"," . 
      "iss_user       =   " . $formVars['iss_user']       . "," . 
      "iss_timestamp  = \"" . date("Y-m-d H:i:s")         . "\"," . 
      "iss_subject    = \"" . $formVars['iss_subject']    . "\"";

    logaccess($_SESSION['uid'], $package, "Adding detail: " . $formVars['iss_companyid']);

    $result = mysql_query($query) or die($query . ": " . mysql_error());

    $query = "select last_insert_id()";
    $q_result = mysql_query($query) or die($query . ": " . mysql_error());
    $a_result = mysql_fetch_array($q_result);

    $issue = $a_result['last_insert_id()'];

    $url = $Issueroot . "/ticket.php?id=" . $issue . "&server=" . $formVars['iss_companyid'];

  } else {
    $url = $Issueroot . "/issue.php";
  }

  header('Location: ' . $url);

?>
