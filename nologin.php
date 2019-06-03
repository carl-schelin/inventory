<?php
  session_start();
# we're trying to show a page without having to use the login system.
# If logged in, then show the standard login screens
# and show the logged in view (editable) of the information.
# really the login stuff should be for event management and
# site management. The other stuff should be readable regardless
  include('settings.php');

  if (isset($_SESSION['username'])) {
    include($Loginpath . '/check.php');
    check_login(4);

    $formVars['uid']      = $_SESSION['uid'];
    $formVars['username'] = $_SESSION['username'];
    $formVars['group']    = $_SESSION['group'];

  } else {

    $formVars['uid']      = 0;
    $formVars['username'] = $_SERVER['REMOTE_ADDR'];
    $formVars['group']    = 0;

# once a database connection is made, all the other data pulls work as expected
# make sure you don't write any changes until someone logs in.
    $db = mysql_connect($DBserver,$DBuser,$DBpassword);
    if (!$db) {
      die('Couldn\'t connect: ' . mysql_error());
    } else {
      $DBlogout = mysql_select_db($DBname,$db);
      if (!$DBlogout) {
        die('Not connected : ' . mysql_error());
      }
    }
  }
  include($Sitepath . '/function.php');
?>
