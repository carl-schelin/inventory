<?php
# Script: initial.application.php
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
    $package = "initial.application.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }
    if (isset($_GET['rsdp_apppoc'])) {
      $formVars['rsdp_apppoc'] = clean($_GET['rsdp_apppoc'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from users");

      $q_string  = "select usr_group ";
      $q_string .= "from users ";
      $q_string .= "where usr_id = " . $formVars['rsdp_apppoc'];
      $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_users) > 0) {
        $a_users = mysql_fetch_array($q_users);

        $q_string  = "update "; 
        $q_string .= "rsdp_server ";
        $q_string .= "set ";
        $q_string .= "rsdp_application = " . $a_users['usr_group'] . " ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
        $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

# retrieve index values
        $application     = return_Index($a_users['usr_group'],     "select grp_id from groups where grp_disabled = 0 order by grp_name");

        print "document.rsdp.rsdp_application['"     . $application     . "'].selected = true;\n";

      }

      mysql_free_result($q_users);

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
