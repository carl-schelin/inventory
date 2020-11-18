<?php
# Script: rights.fill.php
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
    $package = "rights.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from rights");

      $q_string  = "select rgt_type,rgt_apigroup,rgt_resource,rgt_get,rgt_list,rgt_watch,rgt_impersonate,rgt_create,rgt_delete,rgt_deletecollection,rgt_patch,rgt_update ";
      $q_string .= "from rights ";
      $q_string .= "where rgt_id = " . $formVars['id'];
      $q_rights = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error($db)));
      $a_rights = mysqli_fetch_array($q_rights);
      mysqli_free_result($q_rights);

      $apigroups = return_Index($a_rights['rgt_apigroup'], "select api_id from apigroups order by api_name");
      $resources = return_Index($a_rights['rgt_resource'], "select res_id from resources order by res_name");

      print "document.rights.rgt_apigroup['" . $apigroups . "'].selected = true;\n";
      print "document.rights.rgt_resource['" . $resources . "'].selected = true;\n";

      print "document.rights.rgt_type['" . $a_rights['rgt_type'] . "'].checked = true;\n";

      if ($a_rights['rgt_get']) {
        print "document.rights.rgt_get.checked = true;\n";
      } else {
        print "document.rights.rgt_get.checked = false;\n";
      }
      if ($a_rights['rgt_list']) {
        print "document.rights.rgt_list.checked = true;\n";
      } else {
        print "document.rights.rgt_list.checked = false;\n";
      }
      if ($a_rights['rgt_watch']) {
        print "document.rights.rgt_watch.checked = true;\n";
      } else {
        print "document.rights.rgt_watch.checked = false;\n";
      }
      if ($a_rights['rgt_impersonate']) {
        print "document.rights.rgt_impersonate.checked = true;\n";
      } else {
        print "document.rights.rgt_impersonate.checked = false;\n";
      }
      if ($a_rights['rgt_create']) {
        print "document.rights.rgt_create.checked = true;\n";
      } else {
        print "document.rights.rgt_create.checked = false;\n";
      }
      if ($a_rights['rgt_delete']) {
        print "document.rights.rgt_delete.checked = true;\n";
      } else {
        print "document.rights.rgt_delete.checked = false;\n";
      }
      if ($a_rights['rgt_deletecollection']) {
        print "document.rights.rgt_deletecollection.checked = true;\n";
      } else {
        print "document.rights.rgt_deletecollection.checked = false;\n";
      }
      if ($a_rights['rgt_patch']) {
        print "document.rights.rgt_patch.checked = true;\n";
      } else {
        print "document.rights.rgt_patch.checked = false;\n";
      }
      if ($a_rights['rgt_update']) {
        print "document.rights.rgt_update.checked = true;\n";
      } else {
        print "document.rights.rgt_update.checked = false;\n";
      }

      print "document.rights.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
