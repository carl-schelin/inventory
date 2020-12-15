<?php
# Script: features.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login($AL_Edit);

  $package = "features.mysql.php";

  $formVars['feat_module']        = clean($_POST['feat_module'],       10);
  $formVars['feat_severity']      = clean($_POST['feat_severity'],     10);
  $formVars['feat_priority']      = clean($_POST['feat_priority'],     10);
  $formVars['feat_discovered']    = clean($_POST['feat_discovered'],   15);
  $formVars['feat_openby']        = clean($_POST['feat_openby'],       10);
  $formVars['feat_subject']       = clean($_POST['feat_subject'],      70);

  logaccess($db, $_SESSION['uid'], $package, "Creating a new record.");

  if (strlen($formVars['feat_subject']) > 0) {

    $query = "insert into features set feat_id = NULL, " . 
      "feat_module     =   " . $formVars['feat_module']     . "," . 
      "feat_severity   =   " . $formVars['feat_severity']   . "," . 
      "feat_priority   =   " . $formVars['feat_priority']   . "," . 
      "feat_discovered = \"" . $formVars['feat_discovered'] . "\"," . 
      "feat_openby     =   " . $formVars['feat_openby']     . "," . 
      "feat_timestamp  = \"" . date("Y-m-d H:i:s")          . "\"," . 
      "feat_subject    = \"" . $formVars['feat_subject']    . "\"";

    logaccess($db, $_SESSION['uid'], $package, "Adding detail: " . $formVars['feat_module']);

    $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

    $query = "select last_insert_id($db)";
    $q_result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    $a_result = mysqli_fetch_array($q_result);

    $feature = $a_result['last_insert_id($db)'];

    $q_string = 
      "feat_feat_id   =   " . $feature                  . "," . 
      "feat_text      = \"" . $formVars['feat_subject'] . "\"," . 
      "feat_user      =   " . $formVars['feat_openby'];

    $query = "insert into features_detail set feat_id = NULL," . $q_string;

    $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

    $url = $Featureroot . "/ticket.php?id=" . $feature . "#problem";

  } else {
    $url = $Featureroot . "/features.php";
  }

  header('Location: ' . $url);

?>
