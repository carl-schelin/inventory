<?php
# Script: issue.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "issue.mysql.php";

  $formVars['iss_companyid']     = clean($_POST['id'],               10);
  $formVars['iss_discovered']    = clean($_POST['iss_discovered'],   15);
  $formVars['iss_user']          = clean($_POST['iss_user'],         10);
  $formVars['iss_subject']       = clean($_POST['iss_subject'],      70);

  logaccess($db, $_SESSION['uid'], $package, "Creating a new record.");

  if (strlen($formVars['iss_subject']) > 0) {

    $query = "insert into inv_issue set iss_id = NULL, " . 
      "iss_companyid  =   " . $formVars['iss_companyid']  . "," . 
      "iss_discovered = \"" . $formVars['iss_discovered'] . "\"," . 
      "iss_user       =   " . $formVars['iss_user']       . "," . 
      "iss_timestamp  = \"" . date("Y-m-d H:i:s")         . "\"," . 
      "iss_subject    = \"" . $formVars['iss_subject']    . "\"";

    logaccess($db, $_SESSION['uid'], $package, "Adding detail: " . $formVars['iss_companyid']);

    $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

    $query = "select last_insert_id($db)";
    $q_result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    $a_result = mysqli_fetch_array($q_result);

    $issue = $a_result['last_insert_id($db)'];

    $url = $Issueroot . "/ticket.php?id=" . $issue . "&server=" . $formVars['iss_companyid'];

  } else {
    $url = $Issueroot . "/issue.php";
  }

  header('Location: ' . $url);

?>
