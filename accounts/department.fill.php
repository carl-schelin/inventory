<?php
# Script: department.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "department.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from department");

      $q_string  = "select dep_unit,dep_dept,dep_name ";
      $q_string .= "from department ";
      $q_string .= "where dep_id = " . $formVars['id'];
      $q_department = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_department = mysqli_fetch_array($q_department);
      mysqli_free_result($q_department);

      $depunit = return_Index($db, $a_department['dep_unit'], "select bus_id from business_unit order by bus_name");

      print "document.updateDialog.dep_name.value = '" . mysqli_real_escape_string($db, $a_department['dep_name']) . "';\n";
      print "document.updateDialog.dep_dept.value = '" . mysqli_real_escape_string($db, $a_department['dep_dept']) . "';\n";

      if ($depunit > 0) {
        print "document.updateDialog.dep_unit['" . $depunit  . "'].selected = true;\n";
      }

      print "document.updateDialog.id.value = '" . $formVars['id'] . "'\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
