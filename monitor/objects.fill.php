<?php
# Script: objects.fill.php
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
    $package = "objects.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from objects");

      $q_string  = "select obj_name,obj_deleted ";
      $q_string .= "from objects ";
      $q_string .= "where obj_id = " . $formVars['id'];
      $q_objects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_objects = mysqli_fetch_array($q_objects);
      mysqli_free_result($q_objects);

      print "document.objects.obj_name.value = '"       . mysqli_real_escape_string($a_objects['obj_name'])       . "';\n";

      if ($a_objects['obj_deleted']) {
        print "document.objects.obj_deleted.checked = true;\n";
      } else {
        print "document.objects.obj_deleted.checked = false;\n";
      }

      print "document.objects.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
