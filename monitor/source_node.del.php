<?php
# Script: source_node.del.php
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
    $package = "source_node.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from source_node");

      $q_string  = "update ";
      $q_string .= "source_node ";
      $q_string .= "set src_deleted = 1 ";
      $q_string .= "where src_id = " . $formVars['id'];
      $insert = mysql_query($q_string) or die($q_string . ": " . mysql_error());

      print "alert('Source Node marked as deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>
