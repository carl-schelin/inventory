<?php
# Script: hardware.remove.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Marking a hardware record as deleted

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = "yes";
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "hardware.remove.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['retired'] = 0;
    if (isset($_GET['retired'])) {
      $formVars['retired'] = clean($_GET['retired'], 15);
    }
    if ($formVars['retired'] == '1971-01-01') {
      $formVars['retired'] = date('Y-m-d');
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Marking Record " . $formVars['id'] . " as deleted from hardware");

# get the inv_id
      $q_string  = "select hw_companyid ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_id = " . $formVars['id'] . " ";
      $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_hardware = mysqli_fetch_array($q_hardware);

      $q_string  = "select hw_id ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_companyid = " . $a_hardware['hw_companyid'] . " and hw_id != " . $formVars['id'] . " and hw_primary = 1 ";
      $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_hardware) > 0) {
        $q_string  = "update hardware ";
        $q_string .= "set hw_deleted = 1,hw_primary = 0,hw_hw_id = 0,hw_hd_id = 0,hw_retired = '" . $formVars['retired'] . "' ";
        $q_string .= "where hw_id = " . $formVars['id'] . " ";
        $insert = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        $q_string  = "update hardware ";
        $q_string .= "set hw_hw_id = 0,hw_hd_id = 0 ";
        $q_string .= "where hw_hw_id = " . $formVars['id'] . " or hw_hd_id = " . $formVars['id'] . " ";
        $insert = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        print "alert('Hardware updated.');\n";
      } else {
        print "alert('Error: You must have at least one Server, Chassis,\\nor Array active before deleting this record.\\n\\nRecord not deleted.');\n";
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
