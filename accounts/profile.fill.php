<?php
# Script: profile.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "profile.fill.php";
    $formVars['id'] = 0;
    if (isset($_SESSION['uid'])) {
      $formVars['id'] = clean($_SESSION['uid'], 10);
    }

    if (check_userlevel($db, $AL_Guest)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from users");

      $q_string  = "select usr_id,usr_first,usr_last,usr_email,usr_phone,usr_freq,usr_notify,usr_deptname,";
      $q_string .= "usr_altemail,usr_theme,usr_reset,usr_clientid,usr_report,usr_confirm,usr_manager,usr_title,";
      $q_string .= "usr_bigfix,usr_page,usr_pagemail ";
      $q_string .= "from users ";
      $q_string .= "where usr_id = " . $formVars['id'];
      $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_users = mysqli_fetch_array($q_users);
      mysqli_free_result($q_users);

      $theme    = return_Index($db, $a_users['usr_theme'],    "select theme_id from themes order by theme_title") - 1;
      $manager  = return_Index($db, $a_users['usr_manager'],  "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");
      $title    = return_Index($db, $a_users['usr_title'],    "select tit_id from titles order by tit_name");

      $count = 1;
      $deptname = 0;
      $q_string  = "select dep_id ";
      $q_string .= "from department ";
      $q_string .= "left join business_unit on business_unit.bus_unit = department.dep_unit ";
      $q_string .= "order by bus_name,dep_name";
      $q_department = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_department = mysqli_fetch_array($q_department)) {
        if ($a_users['usr_deptname'] == $a_department['dep_id']) {
          $deptname = $count;
        }
        $count++;
      }

      print "document.user.usr_first.value = '"      . mysqli_real_escape_string($db, $a_users['usr_first'])    . "';\n";
      print "document.user.usr_last.value = '"       . mysqli_real_escape_string($db, $a_users['usr_last'])     . "';\n";
      print "document.user.usr_email.value = '"      . mysqli_real_escape_string($db, $a_users['usr_email'])    . "';\n";
      print "document.user.usr_altemail.value = '"   . mysqli_real_escape_string($db, $a_users['usr_altemail']) . "';\n";
      print "document.user.usr_clientid.value = '"   . mysqli_real_escape_string($db, $a_users['usr_clientid']) . "';\n";
      print "document.user.usr_phone.value = '"      . mysqli_real_escape_string($db, $a_users['usr_phone'])    . "';\n";
      print "document.user.usr_notify.value = '"     . mysqli_real_escape_string($db, $a_users['usr_notify'])   . "';\n";
      print "document.user.usr_freq.value = '"       . mysqli_real_escape_string($db, $a_users['usr_freq'])     . "';\n";
      print "document.user.usr_page.value = '"       . mysqli_real_escape_string($db, $a_users['usr_page'])     . "';\n";
      print "document.user.usr_pagemail.value = '"   . mysqli_real_escape_string($db, $a_users['usr_pagemail']) . "';\n";

      print "document.user.usr_theme['"    . $theme     . "'].selected = true;\n";
      print "document.user.usr_deptname['" . $deptname  . "'].selected = true;\n";
      print "document.user.usr_manager['"  . $manager   . "'].selected = true;\n";
      print "document.user.usr_title['"    . $title     . "'].selected = true;\n";

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

      print "document.user.update.disabled = false;\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
