<?php
# Script: severity.fill.php
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
    $package = "severity.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from severity");

      $q_string  = "select sev_name ";
      $q_string .= "from severity ";
      $q_string .= "where sev_id = " . $formVars['id'];
      $q_severity = mysql_query($q_string) or die (mysql_error());
      $a_severity = mysql_fetch_array($q_severity);
      mysql_free_result($q_severity);

      print "document.severity.sev_name.value = '"   . mysql_real_escape_string($a_severity['sev_name'])   . "';\n";

      print "document.severity.id.value = " . $formVars['id'] . ";\n";

      print "document.severity.update.disabled = false;\n";
      print "document.severity.sev_name.focus();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
