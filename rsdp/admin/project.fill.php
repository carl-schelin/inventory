<?php
# Script: project.fill.php
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
    $package = "project.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from zones");

      $q_string  = "select prj_name,prj_code,prj_close,prj_product ";
      $q_string .= "from projects ";
      $q_string .= "where prj_id = " . $formVars['id'];
      $q_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_projects = mysqli_fetch_array($q_projects);
      mysqli_free_result($q_projects);

      $product = return_Index($a_projects['prj_product'], "select prod_id from products order by prod_name");

      print "document.dialog.prj_name.value = '"  . mysqli_real_escape_string($a_projects['prj_name']) . "';\n";
      print "document.dialog.prj_code.value  = '" . mysqli_real_escape_string($a_projects['prj_code']) . "';\n";

      print "document.dialog.prj_product['" . $product . "'].selected = true;\n";

      if ($a_projects['prj_close'] == 1) {
        print "document.dialog.prj_close.checked = true;\n";
      } else {
        print "document.dialog.prj_close.checked = false;\n";
      }

      print "document.dialog.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
