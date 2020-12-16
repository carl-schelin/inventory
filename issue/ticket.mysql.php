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
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['iss_companyid']  = clean($_GET['iss_companyid'],  10);
        $formVars['iss_discovered'] = clean($_GET['iss_discovered'], 15);
        $formVars['iss_closed']     = clean($_GET['iss_closed'],     15);
        $formVars['iss_subject']    = clean($_GET['iss_subject'],    70);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['iss_closed'] == "Current Date") {
          $formVars['iss_closed'] = date('Y-m-d');
        }

        if (strlen($formVars['iss_subject']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "iss_companyid  =   " . $formVars['iss_companyid']  . "," . 
            "iss_discovered = \"" . $formVars['iss_discovered'] . "\"," . 
            "iss_subject    = \"" . $formVars['iss_subject']    . "\"";

          if ($formVars['update'] == 0) {
            $q_string =
              $q_string . "," . 
              "iss_user      =   " . $_SESSION['uid']    . "," . 
              "iss_timestamp = \"" . date("Y-m-d H:i:s") . "\"";

            $query = "insert into issue set iss_id = NULL, " . $q_string;
            $message = "Issue added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update issue set " . $q_string . " where iss_id = " . $formVars['id'];
            $message = "Issue updated.";
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
        $formVars['iss_companyid']  = clean($_GET['iss_companyid'],  10);
        $formVars['iss_discovered'] = clean($_GET['iss_discovered'], 15);
        $formVars['iss_closed']     = clean($_GET['iss_closed'],     15);
        $formVars['iss_subject']    = clean($_GET['iss_subject'],    70);

        if (strlen($formVars['iss_subject']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# it's open, close it
          if ($formVars['iss_closed'] == '0000-00-00' || $formVars['iss_closed'] == 'Current Date') {
            $formVars['iss_closed'] = date('Y-m-d');

            $q_string =
              "iss_companyid  =   " . $formVars['iss_companyid']  . "," . 
              "iss_discovered = \"" . $formVars['iss_discovered'] . "\"," . 
              "iss_subject    = \"" . $formVars['iss_subject']    . "\",".
              "iss_closed     = \"" . $formVars['iss_closed']     . "\"";

            $query = "update issue set " . $q_string . " where iss_id = " . $formVars['id'];

            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

            $q_string = 
              "det_issue =  " . $formVars['id']                              . "," . 
              "det_text  =\"" . "Issue closed by " . $_SESSION['username'] . ".\"," . 
              "det_user  =  " . $_SESSION['uid'];

            $query = "insert into issue_detail set det_id=null," . $q_string;

            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          } else {
            $formVars['iss_closed'] = '0000-00-00';

            $q_string =
              "iss_companyid  =   " . $formVars['iss_companyid']  . "," . 
              "iss_discovered = \"" . $formVars['iss_discovered'] . "\"," . 
              "iss_subject    = \"" . $formVars['iss_subject']    . "\",".
              "iss_closed     = \"" . $formVars['iss_closed']     . "\"";

            $query = "update issue set " . $q_string . " where iss_id = " . $formVars['id'];

            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

            $q_string = 
              "det_issue =  " . $formVars['id']                              . "," . 
              "det_text  =\"" . "Issue reopened by " . $_SESSION['username'] . ".\"," . 
              "det_user  =  " . $_SESSION['uid'];

            $query = "insert into issue_detail set det_id=null," . $q_string;

            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
          }
        }
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>

window.location = 'issue.php?server=<?php print $formVars['iss_companyid']; ?>';

