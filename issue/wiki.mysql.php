<?php
# Script: wiki.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

  $package = "wiki.mysql.php";

  logaccess($db, $_SESSION['uid'], $package, "Generating wiki report.");

  print "{| border=1<br>";
  print "!Date/Time!!User!!Detail<br>";

  $q_string  = "select det_id,det_text,det_timestamp,usr_name ";
  $q_string .= "from issue_detail ";
  $q_string .= "left join users on users.usr_id = issue_detail.det_user ";
  $q_string .= "where det_issue = " . $formVars['id'] . " ";
  $q_string .= "order by det_timestamp";
  $q_issue_detail = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
  while ($a_issue_detail = mysqli_fetch_array($q_issue_detail)) {

    print "|-<br>";
    print "|" . $a_issue_detail['det_timestamp'];
    print "||" . $a_issue_detail['usr_name'];
    print "||" . $a_issue_detail['det_text'] . "<br>";
  }

  mysqli_free_result($q_issue_detail);

  print  "|}<br>";

?>
