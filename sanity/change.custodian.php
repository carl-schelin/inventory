<?php
# Script: change.custodian.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  $formVars['id']        = clean($_GET['id'],        10);
  $formVars['custodian'] = clean($_GET['custodian'], 10);

# update the system custodian to match the passed group
  if (check_userlevel($db, $AL_Admin)) {
    $q_string  = "update inventory ";
    $q_string .= "set inv_manager = " . $formVars['custodian'] . " ";
    $q_string .= "where inv_id = " . $formVars['id'] . " ";
    $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

    $q_string  = "update hardware ";
    $q_string .= "set hw_group = " . $formVars['custodian'] . " ";
    $q_string .= "where hw_companyid = " . $formVars['id'] . " ";
    $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  }

?>
