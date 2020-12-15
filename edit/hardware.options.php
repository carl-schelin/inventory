<?php
# Script: hardware.options.php
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
    $package = "hardware.options.php";
    $formVars['hw_type'] = 0;
    if (isset($_GET['hw_type'])) {
      $formVars['hw_type'] = clean($_GET['hw_type'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Building a hardware type list: type=" . $formVars['hw_type']);

      print "var selbox = document.edit.hw_vendorid;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

// retrieve type list
      $q_string  = "select mod_id,mod_vendor,mod_name ";
      $q_string .= "from models ";
      $q_string .= "where mod_type = " . $formVars['hw_type'] . " ";
      $q_string .= "order by mod_vendor,mod_name";
      $q_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

// create the javascript bit for populating the model dropdown box.
      while ($a_models = mysqli_fetch_array($q_models) ) {
        print "selbox.options[selbox.options.length] = new Option(\"" . htmlspecialchars($a_models['mod_name']) . " (" . htmlspecialchars($a_models['mod_vendor']) . ")\"," . $a_models['mod_id'] . ");\n";
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }

?>
