<?php
session_start(); 

if (isset($_SESSION['username'])) {

  include('functions/dbconn.php');
  include('functions/functions.php');

  function check_login($p_db, $p_level) {
    $username_s = $_SESSION['username']; 

    $q_string  = "select usr_id,usr_level,usr_disabled,usr_name,usr_first,usr_last,";
    $q_string .= "usr_group,usr_deptname,usr_reset,usr_disposition,theme_name ";
    $q_string .= "from users ";
    $q_string .= "left join themes on themes.theme_id = users.usr_theme ";
    $q_string .= "where usr_name = '$username_s'"; 
    $q_users = mysqli_query($p_db, $q_string) or die($q_string . ": " . mysqli_error($p_db));
    $a_users = mysqli_fetch_array($q_users);

# get the user level, disable status, and whether a password reset is needed
    $user_level = $a_users['usr_level'];
    $restricted = $a_users['usr_disabled'];
    $pwreset    = $a_users['usr_reset'];

    $q_string  = "select lvl_disabled ";
    $q_string .= "from levels ";
    $q_string .= "where lvl_level = '$user_level'"; 
    $q_levels = mysqli_query($p_db, $q_string);
    $a_levels = mysqli_fetch_array($q_levels);

# see if the user is disabled
    $disabled = $a_levels['lvl_disabled'];

# Set this to 1 to put the system into maintenance mode.
    $maintenance = 0;

    if ($maintenance != 0) {
      include('maintenance.php');
      exit();
    } elseif ($disabled != 0) {
      include('disabled.php');
      exit();
    } elseif ($restricted != 0) {
      include('disabled.php');
      exit();
    } elseif ($pwreset != 0) {
      include('pwreset.inc.php');
      exit();
    } elseif ($user_level <= $p_level) {
// User has authority to view this page.		
      $_SESSION['uid']         = $a_users['usr_id'];
      $_SESSION['username']    = $a_users['usr_name'];
      $_SESSION['name']        = $a_users['usr_first'] . " " . $a_users['usr_last'];
      $_SESSION['group']       = $a_users['usr_group'];
      $_SESSION['dept']        = $a_users['usr_deptname'];
      $_SESSION['theme']       = $a_users['theme_name'];
      $_SESSION['disposition'] = $a_users['usr_disposition'];
    } else {
# reset the changeable environment variables (first/last, group, and department) in case levels and such change.
      include('user_level.php');
      exit();
    }
  }
} else {

// creates an empty function
  function check_login($p_db, $p_level) {
    exit();
  }

// Session doesn't exist (user isn't logged in), include login.
//
// Set the $called variable to determine what will happen
//   if $called == 'yes', the user is on a page and the session has timed out.
//     so rather than sit there dumbly, pop up an alert
//   otherwise include the login page

  if ($called == 'yes') {
    print "alert(\"Your session has timed out and you are now logged out.\\n\\nRefresh this page or click on the Home link to be redirected to the login page.\");\n";
  } else {
    include('login.inc.php');
  }
  exit();

}
?>
