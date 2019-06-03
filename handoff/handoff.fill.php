<?php
# Script: handoff.fill.php
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
    $package = "handoff.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from handoff");

      $q_string  = "select off_user,off_group,off_timestamp,off_handoff,off_disabled ";
      $q_string .= "from handoff ";
      $q_string .= "where off_id = " . $formVars['id'];
      $q_handoff = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_handoff = mysql_fetch_array($q_handoff);
      mysql_free_result($q_contacts);

      $user  = return_Index($a_handoff['off_user'],  "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");
      $group = return_Index($a_handoff['off_group'], "select grp_id from groups where grp_disabled = 0 order by grp_name");

      print "document.handoff.off_timestamp.value = '" . mysql_real_escape_string($a_handoff['off_timestamp']) . "';\n";
      print "document.handoff.off_handoff.value = '"   . mysql_real_escape_string($a_handoff['off_handoff'])   . "';\n";

      print "document.handoff.off_user['"  . $user  . "'].selected = true;\n";
      print "document.handoff.off_group['" . $group . "'].selected = true;\n";

      if ($a_contacts['off_disabled']) {
        print "document.handoff.off_disabled.checked = true;\n";
      } else {
        print "document.handoff.off_disabled.checked = false;\n";
      }

      print "document.handoff.id.value = " . $formVars['id'] . ";\n";

      print "document.handoff.update.disabled = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
