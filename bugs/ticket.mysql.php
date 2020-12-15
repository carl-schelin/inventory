<?php
# Script: ticket.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {

# update the issue
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],              10);
        $formVars['bug_module']     = clean($_GET['bug_module'],      10);
        $formVars['bug_severity']   = clean($_GET['bug_severity'],    10);
        $formVars['bug_priority']   = clean($_GET['bug_priority'],    10);
        $formVars['bug_discovered'] = clean($_GET['bug_discovered'],  15);
        $formVars['bug_closed']     = clean($_GET['bug_closed'],      15);
        $formVars['bug_subject']    = clean($_GET['bug_subject'],    255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['bug_closed'] == "Current Date") {
          $formVars['bug_closed'] = date('Y-m-d');
        }

        if (strlen($formVars['bug_subject']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "bug_module     =   " . $formVars['bug_module']     . "," . 
            "bug_severity   =   " . $formVars['bug_severity']   . "," . 
            "bug_priority   =   " . $formVars['bug_priority']   . "," . 
            "bug_discovered = \"" . $formVars['bug_discovered'] . "\"," . 
            "bug_subject    = \"" . $formVars['bug_subject']    . "\"";

          if ($formVars['update'] == 0) {
            $q_string =
              $q_string . "," . 
              "bug_user      =   " . $_SESSION['uid']    . "," . 
              "bug_timestamp = \"" . date("Y-m-d H:i:s") . "\"";

            $query = "insert into bugs set bug_id = NULL, " . $q_string;
            $message = "Bug added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update bugs set " . $q_string . " where bug_id = " . $formVars['id'];
            $message = "Bug updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['id']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

# close/reopen the issue
      if ($formVars['update'] == 2) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['bug_module']     = clean($_GET['bug_module'],     10);
        $formVars['bug_severity']   = clean($_GET['bug_severity'],   10);
        $formVars['bug_priority']   = clean($_GET['bug_priority'],   10);
        $formVars['bug_discovered'] = clean($_GET['bug_discovered'], 15);
        $formVars['bug_closed']     = clean($_GET['bug_closed'],     15);
        $formVars['bug_subject']    = clean($_GET['bug_subject'],    70);

        if (strlen($formVars['bug_subject']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# it's open, close it
          if ($formVars['bug_closed'] == '0000-00-00' || $formVars['bug_closed'] == 'Current Date') {
            $formVars['bug_closed'] = date('Y-m-d');

            $q_string =
              "bug_module     =   " . $formVars['bug_module']     . "," . 
              "bug_severity   =   " . $formVars['bug_severity']   . "," . 
              "bug_priority   =   " . $formVars['bug_priority']   . "," . 
              "bug_discovered = \"" . $formVars['bug_discovered'] . "\"," . 
              "bug_subject    = \"" . $formVars['bug_subject']    . "\",".
              "bug_closed     = \"" . $formVars['bug_closed']     . "\"," .
              "bug_closeby    =   " . $_SESSION['uid'];

            $query = "update bugs set " . $q_string . " where bug_id = " . $formVars['id'];

            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

            $q_string = 
              "bug_bug_id =  " . $formVars['id']                              . "," . 
              "bug_text   =\"" . "Issue closed by " . $_SESSION['username'] . ".\"," . 
              "bug_user   =  " . $_SESSION['uid'];

            $query = "insert into bugs_detail set bug_id=null," . $q_string;

            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          } else {
            $formVars['bug_closed'] = '0000-00-00';

            $q_string =
              "bug_module     =   " . $formVars['bug_module']     . "," . 
              "bug_severity   =   " . $formVars['bug_severity']   . "," . 
              "bug_priority   =   " . $formVars['bug_priority']   . "," . 
              "bug_discovered = \"" . $formVars['bug_discovered'] . "\"," . 
              "bug_subject    = \"" . $formVars['bug_subject']    . "\",".
              "bug_closed     = \"" . $formVars['bug_closed']     . "\"," .
              "bug_closeby    =   " . "0";

            $query = "update bugs set " . $q_string . " where bug_id = " . $formVars['id'];

            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

            $q_string = 
              "bug_bug_id =  " . $formVars['id']                              . "," . 
              "bug_text   =\"" . "Issue reopened by " . $_SESSION['username'] . ".\"," . 
              "bug_user   =  " . $_SESSION['uid'];

            $query = "insert into bugs_detail set bug_id=null," . $q_string;

            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
          }
        }
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>

window.location = 'bugs.php';

