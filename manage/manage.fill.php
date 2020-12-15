<?php
# Script: manage.fill.php
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
    $package = "manage.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from chkserver");

      $q_string  = "select chk_id,chk_companyid,chk_errorid,chk_userid,chk_status,chk_text,chk_priority,chk_closed ";
      $q_string .= "from chkserver ";
      $q_string .= "where chk_id = " . $formVars['id'];
      $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_chkserver = mysqli_fetch_array($q_chkserver);
      mysqli_free_result($q_chkserver);

      $q_string  = "select usr_id,usr_last,usr_first ";
      $q_string .= "from users ";
      $q_string .= "left join grouplist on grouplist.gpl_user = users.usr_id ";
      $q_string .= "where gpl_group = 1 and usr_disabled = 0 ";
      $q_string .= "order by usr_last ";

      $chkuserid     = return_Index($db, $a_chkserver['chk_userid'], $q_string);

      print "document.error.error_text.value = '"        . mysqli_real_escape_string($a_chkserver['chk_text'])        . "';\n";

      print "document.error.chk_userid['"       . $chkuserid                   . "'].selected = true;\n";
      print "document.error.chk_priority['"     . $a_chkserver['chk_priority'] . "'].selected = true;\n";

      if ($a_chkserver['chk_status'] == 2) {
        print "document.error.chk_status.checked = true;\n";
      } else {
        print "document.error.chk_status.checked = false;\n";
      }
      if ($a_chkserver['chk_closed'] != '0000-00-00 00:00:00') {
        print "document.error.chk_closed.checked = true;\n";
      } else {
        print "document.error.chk_closed.checked = false;\n";
      }

      $q_string  = "select ce_error ";
      $q_string .= "from chkerrors ";
      $q_string .= "where ce_id = " . $a_chkserver['chk_errorid'] . " ";
      $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_chkerrors = mysqli_fetch_array($q_chkerrors);

      print "document.getElementById('error_message').innerHTML = '" . mysqli_real_escape_string($a_chkerrors['ce_error']) . "';\n";

      $q_string  = "select inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $a_chkserver['chk_companyid'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_inventory = mysqli_fetch_array($q_inventory);

      print "document.getElementById('error_server').innerHTML = '" . mysqli_real_escape_string($a_inventory['inv_name']) . "';\n";

      print "document.error.chk_id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
