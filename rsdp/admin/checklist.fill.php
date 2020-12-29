<?php
# Script: checklist.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "checklist.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from checklist");

      $q_string  = "select chk_group,chk_index,chk_text,chk_link,chk_task ";
      $q_string .= "from checklist ";
      $q_string .= "where chk_id = " . $formVars['id'];
      $q_checklist = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_checklist = mysqli_fetch_array($q_checklist);
      mysqli_free_result($q_checklist);

#      $group = return_Index($db, $a_checklist['chk_group'], "select grp_id from a_groups where grp_disabled = 0 order by grp_name");

#      print "document.checklists.chk_group['" . $group . "'].selected = true;\n";
      print "document.checklists.chk_task['" . $a_checklist['chk_task'] . "'].selected = true;\n";

      print "document.checklists.chk_index.value = '" . mysqli_real_escape_string($db, $a_checklist['chk_index']) . "';\n";
      print "document.checklists.chk_text.value = '"  . mysqli_real_escape_string($db, $a_checklist['chk_text'])  . "';\n";
      print "document.checklists.chk_link.value = '"  . mysqli_real_escape_string($db, $a_checklist['chk_link'])  . "';\n";

      print "document.checklists.id.value = " . $formVars['id'] . ";\n";

# you can view any group but only update your group information; unless you're an admin
      if (check_grouplevel($db, $a_checklist['chk_group'])) {
        print "document.checklists.update.disabled = false;\n";
      } else {
        print "document.checklists.update.disabled = true;\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
