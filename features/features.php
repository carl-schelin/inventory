<?php
# Script: features.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login($AL_Guest);

  $package = "features.php";

  logaccess($db, $_SESSION['uid'], $package, "Managing feature requests");

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
<title>Feature Tracker</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_feature( p_script_url ) {
  var answer = confirm("Deleting this Feature Request will also delete all associated timeline records.\n\nDelete this Feature?")

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
  show_file('features.open.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('features.closed.mysql.php?id=<?php print $formVars['id']; ?>');
<?php
  } else {
?>
  show_file('features.open.mysql.php');
  show_file('features.closed.mysql.php');
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

<form name="features" action="features.mysql.php" method="post">

<div id="main">

<div id="tabs">

<ul>
  <li><a href="#feature">Feature Request Form</a></li>
  <li><a href="#open">Open Features</a></li>
  <li><a href="#closed">Closed Features</a></li>
</ul>

<div id="feature">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Feature Request Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('feature-help');">Help</a></th>
</tr>
</table>

<div id="feature-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Submit A Feature</strong> - Click to submit a new Feature.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Feature Request Form</strong>
  <ul>
    <li><strong>Module</strong> - Select the Module. This makes it easier to locate the request.</li>
    <li><strong>Severity</strong> - Select how severe this feature is.
    <ul>
      <li><strong>Note</strong> - A little request. Change the wording in a help screen for example.</li>
      <li><strong>Minor</strong> - Addition of a field or modification of an existing report.</li>
      <li><strong>Major</strong> - A change that might take a bit of time but might be worthwhile to improve the functionality of the Inventory or other Module.</li>
      <li><strong>Critical</strong> - An urgent request for a report for example.</li>
    </ul></li>
    <li><strong>Priority</strong> - Select a priority level.
    <ul>
      <li><strong>Low</strong> - No hurry. Take your time.</li>
      <li><strong>Medium</strong> - Reasonable delays are okay but sooner would be better than later.</li>
      <li><strong>High</strong> - This is somewhat important. As soon as you can get to it.</li>
    </ul></li>
    <li><strong>Request Date</strong> - Enter the date of the request.</li>
    <li><strong>Opened by</strong> - Select the name of the person who requested the feature. Default is you.</li>
    <li><strong>Description</strong> - Brief description of the request. This will be also be saved as your first request statement.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="submit" name="clone" value="Submit A Feature">
<input type="hidden" name="id" value="<?php print $formVars['id']; ?>"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Feature Request Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="system">Module: <select name="feat_module">
<?php
  $q_string  = "select mod_id,mod_name ";
  $q_string .= "from modules ";
  $q_string .= "order by mod_name ";
  $q_modules = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_modules = mysqli_fetch_array($q_modules)) {
    if ($formVars['id'] == $a_modules['mod_id']) {
      print "<option selected value=\"" . $a_modules['mod_id'] . "\">" . $a_modules['mod_name'] . "</option>\n";
    } else {
      print "<option value=\"" . $a_modules['mod_id'] . "\">" . $a_modules['mod_name'] . "</option>\n";
    }
  }
?>
</select></td>
  <td class="ui-widget-content">Severity <select name="feat_severity">
<option value="0">Note</option>
<option value="1">Minor</option>
<option value="2">Major</option>
<option value="3">Critical</option>
</select></td>
  <td class="ui-widget-content">Priority <select name="feat_priority">
<option value="0">Low</option>
<option value="1">Medium</option>
<option value="2">High</option>
</select></td>
  <td class="ui-widget-content">Request Date: <input type="text" name="feat_discovered" value="<?php print date('Y-m-d'); ?>" size="10"></td>
  <td class="ui-widget-content">Reported By: <select name="feat_openby">
<?php
  $q_string  = "select usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $_SESSION['uid'];
  $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_users = mysqli_fetch_array($q_users);

  print "<option value=\"" . $_SESSION['uid'] . "\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</option>\n";

  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first";
  $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="5">Brief Description: <input type="text" name="feat_subject" size="80"></td>
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
