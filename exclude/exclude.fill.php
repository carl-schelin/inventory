<?php
# Script: exclude.fill.php
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
    $package = "exclude.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from excludes");

      $q_string  = "select ex_id,ex_companyid,ex_text,ex_comments,ex_expiration ";
      $q_string .= "from excludes ";
      $q_string .= "where ex_id = " . $formVars['id'];
      $q_excludes = mysql_query($q_string) or die($q_string . " " . mysql_error());
      $a_excludes = mysql_fetch_array($q_excludes);
      mysql_free_result($q_excludes);

      $server = return_Index($a_excludes['ex_companyid'], "select inv_id from inventory where inv_ssh = 1 and inv_status = 0 and inv_manager = " . $GRP_Unix . " order by inv_name");

      print "document.exclude.ex_text.value = '"       . mysql_real_escape_string($a_excludes['ex_text'])       . "';\n";
      print "document.exclude.ex_comments.value = '"   . mysql_real_escape_string($a_excludes['ex_comments'])   . "';\n";
      print "document.exclude.ex_expiration.value = '" . mysql_real_escape_string($a_excludes['ex_expiration']) . "';\n";

      print "document.exclude.ex_companyid['" . $server . "'].selected = true;\n";

      print "document.exclude.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
