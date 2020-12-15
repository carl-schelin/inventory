<?php
# Script: inventory.security.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Building a hardware list of a selected type

  header('Content-Type: text/javascript');

  include ('settings.php');
  $called="yes";
  include ($Loginpath . '/check.php');
  include ($Sitepath . '/function.php');

 if (isset($_SESSION['username'])) {
    $package = "inventory.security.php";
    $formVars['server'] = 0;
    if (isset($_GET['server'])) {
      $formVars['server'] = clean($_GET['server'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Changing the checkbox status");

      $q_string  = "select inv_appliance ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['server'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inventory = mysqli_fetch_array($q_inventory);

# if it's currently checked, you're here because you're unchecking the box, so check the bigfix and ciscoamp checkboxes.
      if ($a_inventory['inv_appliance']) {
        $q_string = "update inventory set inv_appliance = 0,inv_bigfix = 1,inv_ciscoamp = 1 where inv_id = " . $formVars['server'] . " ";
        $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        print "document.edit.inv_appliance.checked = false;\n";
        print "document.edit.inv_bigfix.checked = true;\n";
        print "document.edit.inv_ciscoamp.checked = true;\n";
      } else {
        $q_string = "update inventory set inv_appliance = 1,inv_bigfix = 0,inv_ciscoamp = 0 where inv_id = " . $formVars['server'] . " ";
        $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        print "document.edit.inv_appliance.checked = true;\n";
        print "document.edit.inv_bigfix.checked = false;\n";
        print "document.edit.inv_ciscoamp.checked = false;\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }

?>
