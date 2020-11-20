<?php
# Script: initial.platform.php
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
    $package = "initial.platform.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }
    if (isset($_GET['rsdp_platformspoc'])) {
      $formVars['rsdp_platformspoc'] = clean($_GET['rsdp_platformspoc'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from users");

      $q_string  = "select usr_group ";
      $q_string .= "from users ";
      $q_string .= "where usr_id = " . $formVars['rsdp_platformspoc'];
      $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_users) > 0) {
        $a_users = mysqli_fetch_array($q_users);

        $q_string  = "update "; 
        $q_string .= "rsdp_server ";
        $q_string .= "set ";
        $q_string .= "rsdp_platform = " . $a_users['usr_group'] . " ";
        $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# retrieve index values
        $platform     = return_Index($a_users['usr_group'],     "select grp_id from groups where grp_disabled = 0 order by grp_name");

        print "document.rsdp.rsdp_platform['"     . $platform     . "'].selected = true;\n";

      }

      mysqli_free_result($q_users);

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
