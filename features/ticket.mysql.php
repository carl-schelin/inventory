<?php
# Script: ticket.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "ticket.mysql.php";
    $formVars['update']   = clean($_GET['update'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Edit)) {

# update the issue
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],               10);
        $formVars['feat_module']     = clean($_GET['feat_module'],      10);
        $formVars['feat_severity']   = clean($_GET['feat_severity'],    10);
        $formVars['feat_priority']   = clean($_GET['feat_priority'],    10);
        $formVars['feat_discovered'] = clean($_GET['feat_discovered'],  15);
        $formVars['feat_closed']     = clean($_GET['feat_closed'],      15);
        $formVars['feat_subject']    = clean($_GET['feat_subject'],    255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['feat_closed'] == "Current Date") {
          $formVars['feat_closed'] = date('Y-m-d');
        }

        if (strlen($formVars['feat_subject']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "feat_module     =   " . $formVars['feat_module']     . "," . 
            "feat_severity   =   " . $formVars['feat_severity']   . "," . 
            "feat_priority   =   " . $formVars['feat_priority']   . "," . 
            "feat_discovered = \"" . $formVars['feat_discovered'] . "\"," . 
            "feat_subject    = \"" . $formVars['feat_subject']    . "\"";

          if ($formVars['update'] == 0) {
            $q_string =
              $q_string . "," . 
              "feat_user      =   " . $_SESSION['uid']    . "," . 
              "feat_timestamp = \"" . date("Y-m-d H:i:s") . "\"";

            $query = "insert into features set feat_id = NULL, " . $q_string;
            $message = "Feature added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update features set " . $q_string . " where feat_id = " . $formVars['id'];
            $message = "Feature updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['id']);

          mysql_query($query) or die($query . ": " . mysql_error());

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

# close/reopen the issue
      if ($formVars['update'] == 2) {
        $formVars['id']              = clean($_GET['id'],              10);
        $formVars['feat_module']     = clean($_GET['feat_module'],     10);
        $formVars['feat_severity']   = clean($_GET['feat_severity'],   10);
        $formVars['feat_priority']   = clean($_GET['feat_priority'],   10);
        $formVars['feat_discovered'] = clean($_GET['feat_discovered'], 15);
        $formVars['feat_closed']     = clean($_GET['feat_closed'],     15);
        $formVars['feat_subject']    = clean($_GET['feat_subject'],    70);

        if (strlen($formVars['feat_subject']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

# it's open, close it
          if ($formVars['feat_closed'] == '0000-00-00' || $formVars['feat_closed'] == 'Current Date') {
            $formVars['feat_closed'] = date('Y-m-d');

            $q_string =
              "feat_module     =   " . $formVars['feat_module']     . "," . 
              "feat_severity   =   " . $formVars['feat_severity']   . "," . 
              "feat_priority   =   " . $formVars['feat_priority']   . "," . 
              "feat_discovered = \"" . $formVars['feat_discovered'] . "\"," . 
              "feat_subject    = \"" . $formVars['feat_subject']    . "\",".
              "feat_closed     = \"" . $formVars['feat_closed']     . "\"," .
              "feat_closeby    =   " . $_SESSION['uid'];

            $query = "update features set " . $q_string . " where feat_id = " . $formVars['id'];

            mysql_query($query) or die($query . ": " . mysql_error());

            $q_string = 
              "feat_feat_id =  " . $formVars['id']                            . "," . 
              "feat_text    =\"" . "Issue closed by " . $_SESSION['username'] . ".\"," . 
              "feat_user    =  " . $_SESSION['uid'];

            $query = "insert into features_detail set feat_id=null," . $q_string;

            mysql_query($query) or die($query . ": " . mysql_error());

          } else {
            $formVars['feat_closed'] = '0000-00-00';

            $q_string =
              "feat_module     =   " . $formVars['feat_module']     . "," . 
              "feat_severity   =   " . $formVars['feat_severity']   . "," . 
              "feat_priority   =   " . $formVars['feat_priority']   . "," . 
              "feat_discovered = \"" . $formVars['feat_discovered'] . "\"," . 
              "feat_subject    = \"" . $formVars['feat_subject']    . "\",".
              "feat_closed     = \"" . $formVars['feat_closed']     . "\"," .
              "feat_closeby    =   " . "0";

            $query = "update features set " . $q_string . " where feat_id = " . $formVars['id'];

            mysql_query($query) or die($query . ": " . mysql_error());

            $q_string = 
              "feat_feat_id =  " . $formVars['id']                              . "," . 
              "feat_text    =\"" . "Issue reopened by " . $_SESSION['username'] . ".\"," . 
              "feat_user    =  " . $_SESSION['uid'];

            $query = "insert into features_detail set feat_id=null," . $q_string;

            mysql_query($query) or die($query . ": " . mysql_error());
          }
        }
      }
    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>

window.location = 'features.php';

