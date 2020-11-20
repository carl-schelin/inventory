<?php
# Script: text.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login($AL_ReadOnly);

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
  } else {
    $formVars['id'] = 0;
  }

  $package = "text.mysql.php";

  logaccess($_SESSION['uid'], $package, "Generating text report.");

  $q_string  = "select det_id,det_text,det_timestamp,usr_name ";
  $q_string .= "from issue_detail ";
  $q_string .= "left join users on users.usr_id = issue_detail.det_user ";
  $q_string .= "where det_issue = " . $formVars['id'] . " ";
  $q_string .= "order by det_timestamp";
  $q_issue_detail = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
  while ($a_issue_detail = mysqli_fetch_array($q_issue_detail)) {

    print "<br><hr>";
    print "<br>" . $a_issue_detail['det_timestamp'];
    print "<br>" . $a_issue_detail['usr_name'];
    print "<br>" . $a_issue_detail['det_text'];
    print "<br>";
  }

  mysqli_free_result($q_issue_detail);

?>
