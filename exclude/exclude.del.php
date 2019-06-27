<?php
# Script: exclude.del.php
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
    $package = "exclude.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from excludes");

      $q_string  = "select ex_deleted ";
      $q_string .= "from excludes ";
      $q_string .= "where ex_id = " . $formVars['id'] . " ";
      $q_excludes = mysql_query($q_string) or die($q_string . ': ' . mysql_error());
      if (mysql_num_rows($q_excludes) > 0) {
        $q_string  = "delete ";
        $q_string .= "from excludes ";
        $q_string .= "where ex_id = " . $formVars['id'] . " ";
        $insert = mysql_query($q_string) or die($q_string . ": " . mysql_error());

        print "alert('Message Exclude line removed.');\n";
      } else {
        $q_string  = "update ";
        $q_string .= "excludes ";
        $q_string .= "set ex_deleted = " . $_SESSION['uid'] . " ";
        $q_string .= "where ex_id = " . $formVars['id'] . " ";
        $update = mysql_query($q_string) or die($q_string . ": " . mysql_error());

        print "alert('Message Exclude line marked as deleted.');\n";
      }

      print "clear_fields();\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>
