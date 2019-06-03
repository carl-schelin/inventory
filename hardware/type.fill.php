<?php
# Script: type.fill.php
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
    $package = "type.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inttype");

      $q_string  = "select itp_name,itp_acronym,itp_description ";
      $q_string .= "from inttype ";
      $q_string .= "where itp_id = " . $formVars['id'];
      $q_inttype = mysql_query($q_string) or die ($q_string . ": " . mysql_error());
      $a_inttype = mysql_fetch_array($q_inttype);
      mysql_free_result($q_inttype);

      print "document.interfacetype.itp_name.value = '"        . mysql_real_escape_string($a_inttype['itp_name'])        . "';\n";
      print "document.interfacetype.itp_acronym.value = '"     . mysql_real_escape_string($a_inttype['itp_acronym'])     . "';\n";
      print "document.interfacetype.itp_description.value = '" . mysql_real_escape_string($a_inttype['itp_description']) . "';\n";

      print "document.interfacetype.id.value = " . $formVars['id'] . ";\n";

      print "document.interfacetype.update.disabled = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
