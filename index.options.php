<?php
# Script: index.options.php
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
    $package = "index.options.php";
    $formVars['product'] = 0;
    if (isset($_GET['product'])) {
      $formVars['product'] = clean($_GET['product'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Building a project list: product=" . $formVars['product']);

      print "var selbox = document.index.project;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"All Servers\",0);\n";

// retrieve type list
      $q_string  = "select prj_id,prj_name ";
      $q_string .= "from projects ";
      $q_string .= "where prj_product = " . $formVars['product'] . " ";
      $q_string .= "order by prj_name ";
      $q_projects = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

// create the javascript bit for populating the model dropdown box.
      while ($a_projects = mysqli_fetch_array($q_projects) ) {
        if ($formVars['product'] > 0) {
          print "selbox.options[selbox.options.length] = new Option(\"" . htmlspecialchars($a_projects['prj_name']) . "\"," . $a_projects['prj_id'] . ");\n";
        }
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }

?>
