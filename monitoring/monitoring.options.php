<?php
# Script: montioring.options.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Building a hardware list of a selected type

  header('Content-Type: text/javascript');

  include ('settings.php');
  $called="yes";
  include ($Loginpath . '/check.php');
  include ($Sitepath . '/function.php');

 if (isset($_SESSION['username'])) {
    $package = "montioring.options.php";
    $formVars['grp_id'] = 0;
    if (isset($_GET['grp_id'])) {
      $formVars['grp_id'] = clean($_GET['grp_id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Building a list of users in a group: group=" . $formVars['grp_id']);

      print "var selbox = document.edit.hw_vendorid;\n\n";
      print "selbox.options.length = 0;\n";

// retrieve type list
      $q_string  = "select mod_id,ven_name,mod_name ";
      $q_string .= "from inv_models ";
      $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
      $q_string .= "where mod_type = " . $formVars['hw_type'] . " ";
      $q_string .= "order by ven_name,mod_name";
      $q_inv_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

// create the javascript bit for populating the model dropdown box.
      while ($a_inv_models = mysqli_fetch_array($q_inv_models) ) {
        print "selbox.options[selbox.options.length] = new Option(\"" . htmlspecialchars($a_inv_models['mod_name']) . " (" . htmlspecialchars($a_inv_models['ven_name']) . ")\"," . $a_inv_models['mod_id'] . ");\n";
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }

?>
