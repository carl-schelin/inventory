<?php
# Script: inventory.options.php
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
    $package = "inventory.options.php";
    $formVars['server'] = 0;
    $formVars['product'] = 0;
    if (isset($_GET['server'])) {
      $formVars['server'] = clean($_GET['server'], 10);
    }
    if (isset($_GET['product'])) {
      $formVars['product'] = clean($_GET['product'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Building a project list: product=" . $formVars['product']);

# set the project list to the currently selected one if the user selects the original product

      $q_string  = "select inv_product,inv_project ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['server'] . " ";
      $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_inventory = mysql_fetch_array($q_inventory);

      print "var selbox = document.edit.inv_project;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

// retrieve type list
      $q_string  = "select prj_id,prj_name ";
      $q_string .= "from projects ";
      $q_string .= "where prj_product = " . $formVars['product'] . " ";
      $q_string .= "order by prj_name ";
      $q_projects = mysql_query($q_string) or die($q_string . ": " . mysql_error());

// create the javascript bit for populating the model dropdown box.
      while ($a_projects = mysql_fetch_array($q_projects) ) {
        if ($formVars['product'] > 0) {
          if ($formVars['product'] == $a_inventory['inv_product'] && $a_projects['prj_id'] == $a_inventory['inv_project']) {
            print "selbox.options[selbox.options.length] = new Option(\"" . htmlspecialchars($a_projects['prj_name']) . "\"," . $a_projects['prj_id'] . ", 1);\n";
          } else {
            print "selbox.options[selbox.options.length] = new Option(\"" . htmlspecialchars($a_projects['prj_name']) . "\"," . $a_projects['prj_id'] . ", 0);\n";
          }
        }
      }
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }

?>
