<?php
# Script: message_group.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "message_group.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from message_group");

      $q_string  = "select msg_group,msg_deleted ";
      $q_string .= "from message_group ";
      $q_string .= "where msg_id = " . $formVars['id'];
      $q_message_group = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_message_group = mysqli_fetch_array($q_message_group);
      mysqli_free_result($q_message_group);

      print "document.groups.msg_group.value = '"       . mysqli_real_escape_string($db, $a_message_group['msg_group'])       . "';\n";

      if ($a_message_group['msg_deleted']) {
        print "document.groups.msg_deleted.checked = true;\n";
      } else {
        print "document.groups.msg_deleted.checked = false;\n";
      }

      print "document.groups.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
