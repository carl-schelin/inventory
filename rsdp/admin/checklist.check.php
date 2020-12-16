<?php
# Script: checklist.check.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  $package = "checklist.check.php";

  logaccess($db, $_SESSION['uid'], $package, "Setting the checkbox status");

  $formVars['chk_rsdp']    = clean($_GET['rsdp'],          10);
  $formVars['chk_task']    = clean($_GET['chk_task'],      10);
  $formVars['chk_group']   = clean($_GET['chk_group'],     10);
  $formVars['chk_index']   = clean($_GET['chk_index'],     10);
  $formVars['chk_comment'] = clean($_GET['chk_comment'],  100);
  $formVars['chk_checked'] = clean($_GET['chk_checked'],   10);

  if ($formVars['chk_checked'] == 'true') {
    $formVars['chk_checked'] = 1;
  } else {
    $formVars['chk_checked'] = 0;
  }

# see if the box is already in the table
  $q_string  = "select chk_id,chk_checked ";
  $q_string .= "from rsdp_check ";
  $q_string .= "where chk_rsdp = " . $formVars['chk_rsdp'] . " and chk_index = " . $formVars['chk_index'] . " and chk_task = " . $formVars['chk_task'] . " and chk_group = " . $formVars['chk_group'] . " ";
  $q_rsdp_check = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_check = mysqli_fetch_array($q_rsdp_check);

  $q_string = 
    "chk_rsdp    =   " . $formVars['chk_rsdp']    . "," . 
    "chk_index   =   " . $formVars['chk_index']   . "," . 
    "chk_task    =   " . $formVars['chk_task']    . "," . 
    "chk_group   =   " . $formVars['chk_group']   . "," . 
    "chk_comment = \"" . $formVars['chk_comment'] . "\"," . 
    "chk_checked =   " . $formVars['chk_checked'];

# if it doesn't exist, we want to create a new entry. Otherwise we just want to update the existing one.
  if ($a_rsdp_check['chk_checked'] == '') {
    $query = "insert into rsdp_check set chk_id = NULL," . $q_string;
  } else {
    $query = "update rsdp_check set " . $q_string . " where chk_id = " . $a_rsdp_check['chk_id'];
  }

  mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

?>
