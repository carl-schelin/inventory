<?php
# Script: bugs.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('4');

  $package = "bugs.php";

  logaccess($_SESSION['uid'], $package, "Managing bugs");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
  } else {
    $formVars['id'] = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Bug Tracker</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_bug( p_script_url ) {
  var answer = confirm("Deleting this Bug Report will also delete all associated timeline records.\n\nDelete this Bug?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function clear_fields() {
<?php
  if (isset($_GET['id'])) {
?>
  show_file('bugs.open.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('bugs.closed.mysql.php?id=<?php print $formVars['id']; ?>');
<?php
  } else {
?>
  show_file('bugs.open.mysql.php');
  show_file('bugs.closed.mysql.php');
<?php
  }
?>
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<form name="bugs" action="bugs.mysql.php" method="post">

<div id="main">

<div id="tabs">

<ul>
  <li><a href="#bug">Bug Form</a></li>
  <li><a href="#open">Open Bugs</a></li>
  <li><a href="#closed">Closed Bugs</a></li>
</ul>

<div id="bug">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Bug Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('bug-help');">Help</a></th>
</tr>
</table>

<div id="bug-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Submit A Bug</strong> - Click to submit a new Bug.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Bug Form</strong>
  <ul>
    <li><strong>Module</strong> - Select the Module. This makes it easier to locate the bug.</li>
    <li><strong>Severity</strong> - Select how severe this bug is.
    <ul>
      <li><strong>Note</strong> - A spelling error or formatting issue. Not impacting the operation of the system but irritating.</li>
      <li><strong>Minor</strong> - Module not working as expected. Work can continue.</li>
      <li><strong>Major</strong> - The module is not working as expected. You may be able to continue by using a work around or by bypassing the module.</li>
      <li><strong>Critical</strong> - It's broken and is preventing you from continuing. The module has thrown an error message which you will note in the details.</li>
    </ul></li>
    <li><strong>Priority</strong> - Select a priority level.
    <ul>
      <li><strong>Low</strong> - No hurry. Take your time.</li>
      <li><strong>Medium</strong> - Reasonable delays are okay but sooner would be better than later.</li>
      <li><strong>High</strong> - This is somewhat important. As soon as you can get to it.</li>
    </ul></li>
    <li><strong>Discovery Date</strong> - Enter the date of the discovery.</li>
    <li><strong>Opened by</strong> - Select the name of the person who discovered the bug. Default is you.</li>
    <li><strong>Description</strong> - Brief description of the problem. This will be also be saved as your first problem statement.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="submit" name="clone" value="Submit A Bug">
<input type="hidden" name="id" value="<?php print $formVars['id']; ?>"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Bug Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="system">Module: <select name="bug_module">
<?php
  $q_string  = "select mod_id,mod_name ";
  $q_string .= "from modules ";
  $q_string .= "order by mod_name ";
  $q_modules = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_modules = mysql_fetch_array($q_modules)) {
    if ($formVars['id'] == $a_modules['mod_id']) {
      print "<option selected value=\"" . $a_modules['mod_id'] . "\">" . $a_modules['mod_name'] . "</option>\n";
    } else {
      print "<option value=\"" . $a_modules['mod_id'] . "\">" . $a_modules['mod_name'] . "</option>\n";
    }
  }
?>
</select></td>
  <td class="ui-widget-content">Severity <select name="bug_severity">
<option value="0">Note</option>
<option value="1">Minor</option>
<option value="2">Major</option>
<option value="3">Critical</option>
</select></td>
  <td class="ui-widget-content">Priority <select name="bug_priority">
<option value="0">Low</option>
<option value="1">Medium</option>
<option value="2">High</option>
</select></td>
  <td class="ui-widget-content">Discovery Date: <input type="text" name="bug_discovered" value="<?php print date('Y-m-d'); ?>" size="10"></td>
  <td class="ui-widget-content">Reported By: <select name="bug_openby">
<?php
  $q_string  = "select usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $_SESSION['uid'];
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_users = mysql_fetch_array($q_users);

  print "<option value=\"" . $_SESSION['uid'] . "\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</option>\n";

  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="5">Brief Description: <input type="text" name="bug_subject" size="80"></td>
</tr>
</table>

</div>


<div id="open">

<span id="open_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="closed">

<span id="closed_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


</div>

</div>

</form>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
