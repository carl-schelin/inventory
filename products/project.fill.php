<?php
# Script: project.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_projects");

      $q_string  = "select prj_name,prj_code,prj_task,prj_desc,prj_directory,prj_group,prj_product ";
      $q_string .= "from inv_projects ";
      $q_string .= "where prj_id = " . $formVars['id'];
      $q_inv_projects = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_projects = mysqli_fetch_array($q_inv_projects);
      mysqli_free_result($q_inv_projects);

      $group   = return_Index($db, $a_inv_projects['prj_group'],   "select grp_id from inv_groups order by grp_name");
      $product = return_Index($db, $a_inv_projects['prj_product'], "select prod_id from inv_products order by prod_name");

      print "document.formUpdate.prj_name.value = '"      . mysqli_real_escape_string($db, $a_inv_projects['prj_name'])      . "';\n";
      print "document.formUpdate.prj_code.value = '"      . mysqli_real_escape_string($db, $a_inv_projects['prj_code'])      . "';\n";
      print "document.formUpdate.prj_task.value = '"      . mysqli_real_escape_string($db, $a_inv_projects['prj_task'])      . "';\n";
      print "document.formUpdate.prj_desc.value = '"      . mysqli_real_escape_string($db, $a_inv_projects['prj_desc'])      . "';\n";
      print "document.formUpdate.prj_directory.value = '" . mysqli_real_escape_string($db, $a_inv_projects['prj_directory']) . "';\n";

      if ($group > 0) {
        print "document.formUpdate.prj_group['"    . $group    . "'].selected = true;\n";
      }
      if ($product > 0) {
        print "document.formUpdate.prj_product['" . $product . "'].selected = true;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
