<?php
# Script: project.mysql.php
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
    $package = "project.mysql.php";
    $formVars['update']        = clean($_GET['update'],       10);
    $formVars['prj_group']     = clean($_GET['prj_group'],    10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['prj_group'] == '') {
      $formVars['prj_group'] = $_SESSION['group'];
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']            = clean($_GET['id'],           10);
        $formVars['prj_name']      = clean($_GET['prj_name'],     30);
        $formVars['prj_code']      = clean($_GET['prj_code'],     10);
        $formVars['prj_close']     = clean($_GET['prj_close'],    10);
        $formVars['prj_product']   = clean($_GET['prj_product'],  10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['prj_code'] == '') {
          $formVars['prj_code'] = 0;
        }
        if ($formVars['prj_close'] == 'true') {
          $formVars['prj_close'] = 1;
        } else {
          $formVars['prj_close'] = 0;
        }

        if (strlen($formVars['prj_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "prj_name      = \"" . $formVars['prj_name']  . "\"," .
            "prj_code      =   " . $formVars['prj_code']  . "," .
            "prj_close     =   " . $formVars['prj_close'] . "," .
            "prj_group     =   " . $formVars['prj_group'] . "," . 
            "prj_product   =   " . $formVars['prj_product'];

          if ($formVars['update'] == 0) {
            $query = "insert into projects set prj_id = NULL, " . $q_string;
            $message = "Project added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update projects set " . $q_string . " where prj_id = " . $formVars['id'];
            $message = "Project updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['prj_name']);

          mysql_query($query) or die($query . ": " . mysql_error());

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      print "document.dialog.prj_name.value = '';\n";
      print "document.dialog.prj_code.value = '';\n";

# rebuild the int_int_id drop down in case of changes in the virtual interface listing
      print "var selbox = document.rsdp.rsdp_project;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"Unassigned\",0);\n";

      $q_string  = "select prj_id,prj_name ";
      $q_string .= "from projects ";
      $q_string .= "where prj_group = " . $_SESSION['group'] . " and prj_close = 0 ";
      $q_string .= "group by prj_name";
      $q_projects = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_projects = mysql_fetch_array($q_projects)) {
        print "selbox.options[selbox.options.length] = new Option(\"" . htmlspecialchars($a_projects['prj_name']) . "\"," . $a_projects['prj_id'] . ");\n";
      }

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
