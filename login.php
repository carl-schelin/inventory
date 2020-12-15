<?php
  include('settings.php');

# if the person hasn't logged in and is using the email login link then set the variable here.
# the check_login() function will manage the magickey variable.
  if (!isset($_SESSION['username'])) {
    if (isset($_GET['magickey'])) {
      $formVars['magickey'] = clean($_GET['magickey'], 60);
      include($Loginpath . '/check.php?magickey=' . $formVars['magickey']);
    } else {
      include($Loginpath . '/check.php');
    }
  } else {
    include($Loginpath . '/check.php');
  }

# connect to the database
  $db = db_connect($DBserver, $DBuser, $DBpassword, $DBname);

  check_login($db, $AL_Guest);

  $formVars['uid']      = $_SESSION['uid'];
  $formVars['username'] = $_SESSION['username'];
  $formVars['group']    = $_SESSION['group'];

  include($Sitepath . '/function.php');
?>
