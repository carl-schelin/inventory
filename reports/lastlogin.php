<?php
# Script: lastlogin.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "lastlogin.php";

  logaccess($formVars['uid'], $package, "Viewing the user listing");

# if help has not been seen yet,
  if (show_Help($Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>View User Last Logins</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">User Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This provides a list of all installed Intrado products. Clicking on the link brings up a document with three 
tabs displaying all the hardware associated with the product, the installed software, and all changelog entries.</p>

</div>

</div>

<table class="ui-styled-table">
<?php
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">User Name</a>\n";
  print "  <th class=\"ui-state-default\">Last Login</th>\n";
  print "  <th class=\"ui-state-default\">IP Address</th>\n";
  print "  <th class=\"ui-state-default\">Logs</th>\n";
  print "  <th class=\"ui-state-default\">Logins</th>\n";
  print "</tr>\n";

  $q_string  = "select usr_id,usr_name,usr_first,usr_last,usr_disabled,usr_checkin,usr_ipaddr ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 1 and usr_checkin = '0000-00-00 00:00:00' ";
  $q_string .= "order by usr_last,usr_first";
  $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_users = mysqli_fetch_array($q_users)) {

    $class = 'ui-widget-content';
    if ($a_users['usr_disabled']) {
      $class = "ui-state-error";
    }

    $logs = 0;
    $q_string  = "select log_id ";
    $q_string .= "from log ";
    $q_string .= "where log_user = '" . $a_users['usr_id'] . "' ";
    $q_log = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $logs = mysqli_num_rows($q_log);

    $logins = 0;
    $q_string  = "select log_id ";
    $q_string .= "from log ";
    $q_string .= "where log_detail like '%has logged in.' and log_user = '" . $a_users['usr_id'] . "' ";
    $q_log = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $logins = mysqli_num_rows($q_log);

    if ($logs > 0) {
    print "<tr>\n";
    print "  <td class=\"" . $class . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</a></td>\n";
    print "  <td class=\"" . $class . "\">" . $a_users['usr_checkin'] . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $a_users['usr_ipaddr'] . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $logs . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $logins . "</td>\n";
    print "</tr>\n";
    }

  }

  mysqli_free_result($q_users);

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
