<?php
# Script: logs.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "logs.php";

  logaccess($formVars['uid'], $package, "Accessing script");

  $formVars['sort']= clean($_GET["sort"],    40);

  if (isset($_GET["sort"])) {
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = "order by log_date desc ";
    $_SESSION['sort'] = '';
  }

  if (isset($_POST['search_a'])) {
    $formVars['search_a'] = clean($_POST['search_a'], 40) ;
  } else {
    $formVars['search_a'] = '';
  }

  if (isset($_POST['startdate'])) {
    $formVars['startdate'] = clean($_POST['startdate'], 20);
  } else {
    $formVars['startdate'] = date('Y-m-d', strtotime("-14 days"));
  }

  if (isset($_POST['enddate'])) {
    $formVars['enddate'] = clean($_POST['enddate'], 20);
  } else {
    $formVars['enddate'] = date('Y-m-d', strtotime("+1 day"));
  }

  if (isset($_GET['user'])) {
    $formVars['user'] = clean($_GET['user'], 10);
  } else {
    if (isset($_POST['user'])) {
      $formVars['user'] = clean($_POST['user'], 10);
    } else {
      $formVars['user'] = 0;
    }
  }

  $where = "where ";
  $and = '';
  if ($formVars['search_a'] != '') {
    $where .= "(log_source like '%" . $formVars['search_a'] . "%' or log_detail like '%" . $formVars['search_a'] . "%') ";
    $and = "and ";
  }

  $where .= $and . "log_date >= '" . $formVars['startdate'] . "' and log_date <= '" . $formVars['enddate'] . "' ";

  if ($formVars['user'] != 0) {
    $where .= " and log_user = " . $formVars['user'] . " ";
  }

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
<title>View Logs</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function() {
  $.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd'
  });

  $( "#startpick" ).datepicker();
  $( "#endpick" ).datepicker();

});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Log Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This is the log review page. Logs are created at many stages in the Inventory for bug tracking and statistical 
review of the various parts of the Inventory. It also identifies the person who made a change so we can correct 
should a problem occur.</p>

</div>

</div>

<form name="logs" action="" method="post">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="submit" value="Generate Listing"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Start Date <input type="text" name="startdate" id="startpick" value="<?php print $formVars['startdate']; ?>"></td>
  <td class="ui-widget-content">End Date <input type="text" name="enddate" id="endpick" value="<?php print $formVars['enddate']; ?>"></td>
  <td class="ui-widget-content">Search Criteria: <input type="text" name="search_a" size="60" value="<?php print $formVars['search_a']; ?>"><input type="hidden" name="user" value="<?php print $formVars['user']; ?>"></td>
</tr>
</table>

</form>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="<?php print $package; ?>?sort=log_id">ID</a></th>
  <th class="ui-state-default"><a href="<?php print $package; ?>?sort=log_user">User</a></th>
  <th class="ui-state-default"><a href="<?php print $package; ?>?sort=log_date">Date</a></th>
  <th class="ui-state-default"><a href="<?php print $package; ?>?sort=log_source">Script</a></th>
  <th class="ui-state-default"><a href="<?php print $package; ?>?sort=log_detail">Detail</a></th>
</tr>
<?php

  $q_string  = "select log_id,log_user,log_source,log_date,log_detail,usr_name ";
  $q_string .= "from log ";
  $q_string .= "left join users on users.usr_id = log.log_user ";
  $q_string .= $where;
  $q_string .= $orderby;
  $q_log = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_log = mysql_fetch_array($q_log)) {

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_log['log_id']     . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . "<a href=\"" . $package . "?user=" . $a_log['log_user'] . "\">" . $a_log['usr_name'] . "</a></td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_log['log_date']   . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_log['log_source'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_log['log_detail'] . "</td>\n";
    print "</tr>\n";

  }

?>
</table>

<p>Total: <?php print mysql_num_rows($q_log); ?> logs</p>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
