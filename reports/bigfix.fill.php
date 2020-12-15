<?php
# Script: bigfix.fill.php
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
    $package = "bigfix.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['scheduled'] = date('Y-m-d');
    if (isset($_GET['scheduled'])) {
      $formVars['scheduled'] = clean($_GET['scheduled'], 12);
    }
    $formVars['enddate'] = date('Y-m-d');
    if (isset($_GET['enddate'])) {
      $formVars['enddate'] = clean($_GET['enddate'], 12);
    }
    $formVars['anddate'] = date('Y-m-d');
    if (isset($_GET['anddate'])) {
      $formVars['anddate'] = clean($_GET['anddate'], 12);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting all records for " . $formVars['id'] . " from bigfix");

      $q_string  = "select inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['id'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inventory) > 0) {
        $a_inventory = mysqli_fetch_array($q_inventory);

        print "document.getElementById('big_servername').innerHTML = '" . mysqli_real_escape_string($a_inventory['inv_name']) . "';";
      }

      $daterange = "and big_scheduled = \"" . $formVars['scheduled'] . "\" ";
      if ($formVars['scheduled'] != $formVars['enddate']) {
        $daterange = "and big_scheduled >= \"" . $formVars['scheduled'] . "\" and big_scheduled <= \"" . $formVars['enddate'] . "\" ";
      }
      if ($formVars['scheduled'] != $formVars['anddate']) {
        $daterange = "and (big_scheduled = \"" . $formVars['scheduled'] . "\" or big_scheduled = \"" . $formVars['anddate'] . "\") ";
      }
      $patches = '';
      $flagged = '';
      $q_string  = "select big_fixlet,big_severity ";
      $q_string .= "from bigfix ";
      $q_string .= "where big_companyid = " . $formVars['id'] . " " . $daterange;
      $q_string .= "order by big_severity,big_fixlet ";
      $q_bigfix = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_bigfix) > 0) {

        while ($a_bigfix = mysqli_fetch_array($q_bigfix)) {

          if ($a_bigfix['big_severity'] == 1) {
            $severity = "Unspecified";
          }
          if ($a_bigfix['big_severity'] == 2) {
            $severity = "Critical";
          }
          if ($a_bigfix['big_severity'] == 3) {
            $severity = "Important";
          }
          if ($a_bigfix['big_severity'] == 4) {
            $severity = "Moderate";
          }
          if ($a_bigfix['big_severity'] == 5) {
            $severity = "Low";
          }

          if ($flagged != $severity) {
            $patches .= "\n";
            $patches .= "Severity: " . $severity . "\n";
            $patches .= "\n";
            $flagged = $severity;
          }

          $patches .= $a_bigfix['big_fixlet'] . "\n";
        }
      }
      mysqli_free_result($q_bigfix);

      print "document.bigfix.big_patches.value = '" . mysqli_real_escape_string($patches) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
