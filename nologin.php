<?php
  session_start();
# we're trying to show a page without having to use the login system.
# If logged in, then show the standard login screens
# and show the logged in view (editable) of the information.
# really the login stuff should be for event management and
# site management. The other stuff should be readable regardless
  include('settings.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    include($Loginpath . '/check.php');

# connect to the database
    $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

    check_login($db, $AL_Guest);

    $formVars['uid']      = $_SESSION['uid'];
    $formVars['username'] = $_SESSION['username'];
    $formVars['group']    = $_SESSION['group'];

  } else {

    $formVars['uid']      = 0;
    $formVars['username'] = $_SERVER['REMOTE_ADDR'];
    $formVars['group']    = 0;

# once a database connection is made, all the other data pulls work as expected
# make sure you don't write any changes until someone logs in.
    $db = mysqli_connect($DBserver,$DBuser,$DBpassword,$DBname);
    if (!$db) {
      die('Couldn\'t connect: ' . mysqli_error($db));
    } else {
      $DBlogout = mysqli_select_db($db, $DBname);
      if (!$DBlogout) {
        die('Not connected : ' . mysqli_error($db));
      }
    }
  }
?>
