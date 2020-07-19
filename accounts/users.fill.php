<?php
# Script: users.fill.php
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
    $package = "users.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Admin)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from users");

      $q_string  = "select usr_id,usr_disabled,usr_first,usr_last,usr_name,usr_level,";
      $q_string .= "usr_phone,usr_email,usr_altemail,usr_deptname,usr_group,usr_theme,";
      $q_string .= "usr_reset,usr_clientid,usr_notify,usr_freq,usr_report,usr_confirm,";
      $q_string .= "usr_manager,usr_title,usr_bigfix ";
      $q_string .= "from users ";
      $q_string .= "where usr_id = " . $formVars['id'];
      $q_users = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_users = mysql_fetch_array($q_users);
      mysql_free_result($q_users);

      $groups   = return_Index($a_users['usr_group'],    "select grp_id from groups where grp_disabled = 0 order by grp_name");
      $disabled = $a_users['usr_disabled'];
      $levels   = return_Index($a_users['usr_level'],    "select lvl_id from levels where lvl_disabled = 0 order by lvl_id");
      $theme    = return_Index($a_users['usr_theme'],    "select theme_id from themes order by theme_title") - 1;
      $manager  = return_Index($a_users['usr_manager'],  "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");
      $title    = return_Index($a_users['usr_title'],    "select tit_id from titles order by tit_name");

      $q_string  = "select dep_id,dep_unit,dep_dept,dep_name,bus_name ";
      $q_string .= "from department ";
      $q_string .= "left join business_unit on business_unit.bus_unit = department.dep_unit ";
      $q_string .= "order by bus_name,dep_name";

      $deptname = return_Index($a_users['usr_deptname'], $q_string);

      print "document.user.usr_name.value = '"       . mysql_real_escape_string($a_users['usr_name'])     . "';\n";
      print "document.user.usr_first.value = '"      . mysql_real_escape_string($a_users['usr_first'])    . "';\n";
      print "document.user.usr_last.value = '"       . mysql_real_escape_string($a_users['usr_last'])     . "';\n";
      print "document.user.usr_email.value = '"      . mysql_real_escape_string($a_users['usr_email'])    . "';\n";
      print "document.user.usr_phone.value = '"      . mysql_real_escape_string($a_users['usr_phone'])    . "';\n";
      print "document.user.usr_clientid.value = '"   . mysql_real_escape_string($a_users['usr_clientid']) . "';\n";
      print "document.user.usr_altemail.value = '"   . mysql_real_escape_string($a_users['usr_altemail']) . "';\n";
      print "document.user.usr_notify.value = '"     . mysql_real_escape_string($a_users['usr_notify'])   . "';\n";
      print "document.user.usr_freq.value = '"       . mysql_real_escape_string($a_users['usr_freq'])     . "';\n";

      print "document.user.usr_group['"    . $groups   . "'].selected = true;\n";
      print "document.user.usr_disabled['" . $disabled . "'].selected = true;\n";
      print "document.user.usr_level['"    . $levels   . "'].selected = true;\n";
      print "document.user.usr_theme['"    . $theme    . "'].selected = true;\n";
      print "document.user.usr_deptname['" . $deptname . "'].selected = true;\n";
      print "document.user.usr_manager['"  . $manager  . "'].selected = true;\n";
      print "document.user.usr_title['"    . $title    . "'].selected = true;\n";

      if ($a_users['usr_reset']) {
        print "document.user.usr_reset.checked = true;\n";
      } else {
        print "document.user.usr_reset.checked = false;\n";
      }
      if ($a_users['usr_report']) {
        print "document.user.usr_report.checked = true;\n";
      } else {
        print "document.user.usr_report.checked = false;\n";
      }
      if ($a_users['usr_confirm']) {
        print "document.user.usr_confirm.checked = true;\n";
      } else {
        print "document.user.usr_confirm.checked = false;\n";
      }
      if ($a_users['usr_bigfix']) {
        print "document.user.usr_bigfix.checked = true;\n";
      } else {
        print "document.user.usr_bigfix.checked = false;\n";
      }

      print "document.user.id.value = '" . $formVars['id'] . "'\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
