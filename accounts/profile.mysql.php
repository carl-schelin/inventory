<?php
# Script: profile.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "profile.mysql.php";
    $formVars['id']             = clean($_SESSION['uid'],        10);
    $formVars['update']         = clean($_GET['update'],         10);
    $formVars['usr_first']      = clean($_GET['usr_first'],     255);
    $formVars['usr_last']       = clean($_GET['usr_last'],      255);
    $formVars['usr_email']      = clean($_GET['usr_email'],     255);
    $formVars['usr_clientid']   = clean($_GET['usr_clientid'],   30);
    $formVars['usr_deptname']   = clean($_GET['usr_deptname'],   10);
    $formVars['usr_manager']    = clean($_GET['usr_manager'],    10);
    $formVars['usr_title']      = clean($_GET['usr_title'],      10);
    $formVars['usr_altemail']   = clean($_GET['usr_altemail'],  255);
    $formVars['usr_theme']      = clean($_GET['usr_theme'],      10);
    $formVars['usr_passwd']     = clean($_GET['usr_passwd'],     32);
    $formVars['usr_reenter']    = clean($_GET['usr_reenter'],    32);
    $formVars['usr_reset']      = clean($_GET['usr_reset'],      10);
    $formVars['usr_phone']      = clean($_GET['usr_phone'],      15);
    $formVars['usr_notify']     = clean($_GET['usr_notify'],     10);
    $formVars['usr_freq']       = clean($_GET['usr_freq'],       10);
    $formVars['usr_report']     = clean($_GET['usr_report'],     10);
    $formVars['usr_confirm']    = clean($_GET['usr_confirm'],    10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['usr_notify'] == '') {
      $formVars['usr_notify'] = -1;
    }
    if ($formVars['usr_freq'] == '') {
      $formVars['usr_freq'] = -1;
    }
    if ($formVars['usr_reset'] == 'true') {
      $formVars['usr_reset'] = 1;
    } else {
      $formVars['usr_reset'] = 0;
    }
    if ($formVars['usr_report'] == 'true') {
      $formVars['usr_report'] = 1;
    } else {
      $formVars['usr_report'] = 0;
    }
    if ($formVars['usr_confirm'] == 'true') {
      $formVars['usr_confirm'] = 1;
    } else {
      $formVars['usr_confirm'] = 0;
    }

    if (check_userlevel(4)) {
      if ($formVars['update'] == 1) {
        if (strlen($formVars['usr_last']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string = 
            "usr_first       = \"" . $formVars['usr_first']     . "\"," .
            "usr_last        = \"" . $formVars['usr_last']      . "\"," .
            "usr_email       = \"" . $formVars['usr_email']     . "\"," .
            "usr_clientid    = \"" . $formVars['usr_clientid']  . "\"," .
            "usr_deptname    =   " . $formVars['usr_deptname']  . "," .
            "usr_manager     =   " . $formVars['usr_manager']   . "," .
            "usr_title       =   " . $formVars['usr_title']     . "," .
            "usr_altemail    = \"" . $formVars['usr_altemail']  . "\"," .
            "usr_theme       =   " . $formVars['usr_theme']     . "," .
            "usr_reset       =   " . $formVars['usr_reset']     . "," . 
            "usr_phone       = \"" . $formVars['usr_phone']     . "\"," .
            "usr_notify      =   " . $formVars['usr_notify']    . "," .
            "usr_freq        =   " . $formVars['usr_freq']      . "," . 
            "usr_report      =   " . $formVars['usr_report']    . "," . 
            "usr_confirm     =   " . $formVars['usr_confirm'];

          if (strlen($formVars['usr_passwd']) > 0 && $formVars['usr_passwd'] === $formVars['usr_reenter']) {
            logaccess($_SESSION['uid'], $package, "Resetting user " . $formVars['usr_last'] . " password.");
            $q_string .= ",usr_passwd = '" . MD5($formVars['usr_passwd']) . "' ";
          }

          if ($formVars['update'] == 1) {
            $query = "update users set " . $q_string . " where usr_id = " . $formVars['id'];
            $message = "Account settings updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['usr_last']);

          mysql_query($query) or die($query . ": " . mysql_error());

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }
    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }

?>
