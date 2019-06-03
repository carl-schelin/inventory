<?php
# Script: checklist.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from checklist");

      $q_string  = "select chk_group,chk_index,chk_text,chk_link,chk_task ";
      $q_string .= "from checklist ";
      $q_string .= "where chk_id = " . $formVars['id'];
      $q_checklist = mysql_query($q_string) or die (mysql_error());
      $a_checklist = mysql_fetch_array($q_checklist);
      mysql_free_result($q_checklist);

#      $group = return_Index($a_checklist['chk_group'], "select grp_id from groups where grp_disabled = 0 order by grp_name");

#      print "document.checklists.chk_group['" . $group . "'].selected = true;\n";
      print "document.checklists.chk_task['" . $a_checklist['chk_task'] . "'].selected = true;\n";

      print "document.checklists.chk_index.value = '" . mysql_real_escape_string($a_checklist['chk_index']) . "';\n";
      print "document.checklists.chk_text.value = '"  . mysql_real_escape_string($a_checklist['chk_text'])  . "';\n";
      print "document.checklists.chk_link.value = '"  . mysql_real_escape_string($a_checklist['chk_link'])  . "';\n";

      print "document.checklists.id.value = " . $formVars['id'] . ";\n";

# you can view any group but only update your group information; unless you're an admin
      if (check_grouplevel($a_checklist['chk_group'])) {
        print "document.checklists.update.disabled = false;\n";
      } else {
        print "document.checklists.update.disabled = true;\n";
      }

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
