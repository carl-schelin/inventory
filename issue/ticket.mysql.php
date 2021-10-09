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

            $q_string = "insert into issue set iss_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update issue set " . $q_string . " where iss_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['id']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
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
          if ($formVars['iss_closed'] == '1971-01-01' || $formVars['iss_closed'] == 'Current Date') {
            $formVars['iss_closed'] = date('Y-m-d');

            $q_string =
              "iss_companyid  =   " . $formVars['iss_companyid']  . "," . 
              "iss_discovered = \"" . $formVars['iss_discovered'] . "\"," . 
              "iss_subject    = \"" . $formVars['iss_subject']    . "\",".
              "iss_closed     = \"" . $formVars['iss_closed']     . "\"";

            $q_string = "update issue set " . $q_string . " where iss_id = " . $formVars['id'];

            mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

            $q_string = 
              "det_issue =  " . $formVars['id']                              . "," . 
              "det_text  =\"" . "Issue closed by " . $_SESSION['username'] . ".\"," . 
              "det_user  =  " . $_SESSION['uid'];

            $q_string = "insert into issue_detail set det_id=null," . $q_string;

            mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

          } else {
            $formVars['iss_closed'] = '1971-01-01';

            $q_string =
              "iss_companyid  =   " . $formVars['iss_companyid']  . "," . 
              "iss_discovered = \"" . $formVars['iss_discovered'] . "\"," . 
              "iss_subject    = \"" . $formVars['iss_subject']    . "\",".
              "iss_closed     = \"" . $formVars['iss_closed']     . "\"";

            $q_string = "update issue set " . $q_string . " where iss_id = " . $formVars['id'];

            mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

            $q_string = 
              "det_issue =  " . $formVars['id']                              . "," . 
              "det_text  =\"" . "Issue reopened by " . $_SESSION['username'] . ".\"," . 
              "det_user  =  " . $_SESSION['uid'];

            $q_string = "insert into issue_detail set det_id=null," . $q_string;

            mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          }
        }
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>

window.location = 'issue.php?server=<?php print $formVars['iss_companyid']; ?>';

