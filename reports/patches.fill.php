<?php
# Script: patches.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "patches.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['scheduled'] = date('Y-m-d');
    if (isset($_GET['scheduled'])) {
      $formVars['scheduled'] = clean($_GET['scheduled'], 12);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting all records for " . $formVars['id'] . " from bigfix");

      $q_string  = "select big_fixlet,inv_manager,inv_appadmin ";
      $q_string .= "from bigfix ";
      $q_string .= "left join inventory on inventory.inv_id = bigfix.big_companyid ";
      $q_string .= "where big_id = " . $formVars['id'] . " ";
      $q_bigfix = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_bigfix) > 0) {
        $a_bigfix = mysqli_fetch_array($q_bigfix);

        $big_fixlet = $a_bigfix['big_fixlet'];
        $inv_manager = $a_bigfix['inv_manager'];
        $inv_appadmin = $a_bigfix['inv_appadmin'];
#        print "document.getElementById('big_servername').innerHTML = '" . mysqli_real_escape_string($a_inventory['inv_name']) . "';";
      }

      $patches = '';
      $flagged = '';
      $q_string  = "select inv_name ";
      $q_string .= "from bigfix ";
      $q_string .= "left join inventory on inventory.inv_id = bigfix.big_companyid ";
      $q_string .= "where big_fixlet = \"" . $big_fixlet . "\" and big_scheduled = \"" . $formVars['scheduled'] . "\" and (inv_manager = " . $inv_manager . " or inv_appadmin = " . $inv_appadmin . ") ";
      $q_string .= "group by inv_name ";
      $q_bigfix = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_bigfix) > 0) {

        while ($a_bigfix = mysqli_fetch_array($q_bigfix)) {

          $patches .= $a_bigfix['inv_name'] . "\n";
        }
      }
      mysqli_free_result($q_bigfix);

      print "document.bigfix.big_patches.value = '" . mysqli_real_escape_string($patches) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
