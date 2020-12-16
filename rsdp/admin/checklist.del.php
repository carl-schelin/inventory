<?php
# Script: checklist.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "checklist.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['task'] = 0;
    if (isset($_GET['task'])) {
      $formVars['task'] = clean($_GET['task'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
# get the group info from the id entry
      $q_string  = "select chk_group ";
      $q_string .= "from checklist ";
      $q_string .= "where chk_id = " . $formVars['id'];
      $q_checklist = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_checklist = mysqli_fetch_array($q_checklist);

      $groupid = $a_checklist['chk_group'];

# now loop through all the checklist items 
# when the id to be deleted is found, delete the entry
# all subsequent entries have their index value reduced by 1
      $delflag = 0;
      $q_string  = "select chk_id,chk_index ";
      $q_string .= "from checklist ";
      $q_string .= "where chk_group = " . $groupid . " and chk_task = " . $formVars['task'] . " ";
      $q_string .= "order by chk_index";
      $q_checklist = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_checklist = mysqli_fetch_array($q_checklist)) {
        if ($delflag) {
          $q_string  = "update checklist ";
          $q_string .= "set chk_index = " . ($a_checklist['chk_index'] - 1) . " ";
          $q_string .= "where chk_id = " . $a_checklist['chk_id'];
          $q_fixindexcl = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        }

        if ($a_checklist['chk_id'] == $formVars['id']) {
          $q_string  = "delete ";
          $q_string .= "from checklist ";
          $q_string .= "where chk_id = " . $formVars['id'];
          $q_delfromcl = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $delflag = 1;
        }
      }

      print "clear_fields();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
