<?php
# Script: backups.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "backups.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);
    $formVars['copyfrom']       = clean($_GET['copyfrom'],       10);
    $formVars['bu_companyid']   = clean($_GET['bu_companyid'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['bu_companyid'] == '') {
      $formVars['bu_companyid'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['bu_start']       = clean($_GET['bu_start'],       15);
        $formVars['bu_include']     = clean($_GET['bu_include'],     10);
        $formVars['bu_retention']   = clean($_GET['bu_retention'],   10);
        $formVars['bu_sunday']      = clean($_GET['bu_sunday'],      10);
        $formVars['bu_monday']      = clean($_GET['bu_monday'],      10);
        $formVars['bu_tuesday']     = clean($_GET['bu_tuesday'],     10);
        $formVars['bu_wednesday']   = clean($_GET['bu_wednesday'],   10);
        $formVars['bu_thursday']    = clean($_GET['bu_thursday'],    10);
        $formVars['bu_friday']      = clean($_GET['bu_friday'],      10);
        $formVars['bu_saturday']    = clean($_GET['bu_saturday'],    10);
        $formVars['bu_suntime']     = clean($_GET['bu_suntime'],     10);
        $formVars['bu_montime']     = clean($_GET['bu_montime'],     10);
        $formVars['bu_tuetime']     = clean($_GET['bu_tuetime'],     10);
        $formVars['bu_wedtime']     = clean($_GET['bu_wedtime'],     10);
        $formVars['bu_thutime']     = clean($_GET['bu_thutime'],     10);
        $formVars['bu_fritime']     = clean($_GET['bu_fritime'],     10);
        $formVars['bu_sattime']     = clean($_GET['bu_sattime'],     10);
        $formVars['bu_notes']       = clean($_GET['bu_notes'],     1024);
        $formVars['bu_changedby']   = clean($_SESSION['uid'],        10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['bu_include'] == 'true') {
          $formVars['bu_include'] = 1;
        } else {
          $formVars['bu_include'] = 0;
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "bu_companyid =   " . $formVars['bu_companyid'] . "," .
          "bu_start     = \"" . $formVars['bu_start']     . "\"," .
          "bu_include   =   " . $formVars['bu_include']   . "," .
          "bu_retention =   " . $formVars['bu_retention'] . "," .
          "bu_sunday    =   " . $formVars['bu_sunday']    . "," .
          "bu_monday    =   " . $formVars['bu_monday']    . "," .
          "bu_tuesday   =   " . $formVars['bu_tuesday']   . "," .
          "bu_wednesday =   " . $formVars['bu_wednesday'] . "," .
          "bu_thursday  =   " . $formVars['bu_thursday']  . "," .
          "bu_friday    =   " . $formVars['bu_friday']    . "," .
          "bu_saturday  =   " . $formVars['bu_saturday']  . "," .
          "bu_suntime   = \"" . $formVars['bu_suntime']   . "\"," .
          "bu_montime   = \"" . $formVars['bu_montime']   . "\"," .
          "bu_tuetime   = \"" . $formVars['bu_tuetime']   . "\"," .
          "bu_wedtime   = \"" . $formVars['bu_wedtime']   . "\"," .
          "bu_thutime   = \"" . $formVars['bu_thutime']   . "\"," .
          "bu_fritime   = \"" . $formVars['bu_fritime']   . "\"," .
          "bu_sattime   = \"" . $formVars['bu_sattime']   . "\"," .
          "bu_notes     = \"" . $formVars['bu_notes']     . "\"," .
          "bu_changedby =   " . $formVars['bu_changedby'];

        if ($formVars['id'] == 0) {
          $query = "insert into backups set bu_id = NULL," . $q_string;
        }
        if ($formVars['id'] > 0) {
          $query = "update backups set " . $q_string . " where bu_id = " . $formVars['id'];
        }

        logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['id']);

        mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

      }


      if ($formVars['update'] == -2 && $formVars['copyfrom'] > 0) {
        $q_string  = "select bu_start,bu_include,bu_retention,";
        $q_string .= "bu_sunday,bu_monday,bu_tuesday,bu_wednesday,bu_thursday,bu_friday,bu_saturday,";
        $q_string .= "bu_suntime,bu_montime,bu_tuetime,bu_wedtime,bu_thutime,bu_fritime,bu_sattime,";
        $q_string .= "bu_notes ";
        $q_string .= "from backups ";
        $q_string .= "where bu_companyid = " . $formVars['copyfrom'];
        $q_backups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_backups = mysqli_fetch_array($q_backups)) {

          $q_string =
            "bu_companyid =   " . $formVars['bu_companyid']  . "," .
            "bu_start     = \"" . $a_backups['bu_start']     . "\"," .
            "bu_include   =   " . $a_backups['bu_include']   . "," .
            "bu_retention =   " . $a_backups['bu_retention'] . "," .
            "bu_sunday    =   " . $a_backups['bu_sunday']    . "," .
            "bu_monday    =   " . $a_backups['bu_monday']    . "," .
            "bu_tuesday   =   " . $a_backups['bu_tuesday']   . "," .
            "bu_wednesday =   " . $a_backups['bu_wednesday'] . "," .
            "bu_thursday  =   " . $a_backups['bu_thursday']  . "," .
            "bu_friday    =   " . $a_backups['bu_friday']    . "," .
            "bu_saturday  =   " . $a_backups['bu_saturday']  . "," .
            "bu_suntime   = \"" . $a_backups['bu_suntime']   . "\"," .
            "bu_montime   = \"" . $a_backups['bu_montime']   . "\"," .
            "bu_tuetime   = \"" . $a_backups['bu_tuetime']   . "\"," .
            "bu_wedtime   = \"" . $a_backups['bu_wedtime']   . "\"," .
            "bu_thutime   = \"" . $a_backups['bu_thutime']   . "\"," .
            "bu_fritime   = \"" . $a_backups['bu_fritime']   . "\"," .
            "bu_sattime   = \"" . $a_backups['bu_sattime']   . "\"," .
            "bu_notes     = \"" . $a_backups['bu_notes']     . "\"";

          $query = "insert into backups set bu_id = NULL, " . $q_string;
          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
        }
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
