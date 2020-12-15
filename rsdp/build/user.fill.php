<?php
# Script: user.fill.php
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
    $package = "user.fill.php";
    $formVars['rsdp_requestor'] = 0;
    if (isset($_GET['rsdp_requestor'])) {
      $formVars['rsdp_requestor'] = clean($_GET['rsdp_requestor'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp_requestor'] . " from users");

      $q_string  = "select usr_phone,usr_email,usr_deptname ";
      $q_string .= "from users ";
      $q_string .= "where usr_id = " . $formVars['rsdp_requestor'];
      $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_users = mysqli_fetch_array($q_users);
      mysqli_free_result($q_users);

      $department = return_Index($db, $a_users['usr_deptname'], "select dep_id from department left join business_unit on business_unit.bus_unit = department.dep_unit order by bus_name,dep_name");

      print "document.rsdp.usr_deptname['" . $department . "'].selected = true;\n";

      print "document.rsdp.usr_phone.value = '" . mysqli_real_escape_string($a_users['usr_phone']) . "';\n";
      print "document.rsdp.usr_email.value = '" . mysqli_real_escape_string($a_users['usr_email']) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access. (" . $formVars['rsdp'] . ")");
    }
  }
?>
