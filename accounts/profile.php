<?php
# Script: profile.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('4');

  $package = "profile.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Yourself</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function attach_users(p_script_url, update) {
  var au_form = document.user;
  var au_url;

  au_url  = '?update='   + update;

  au_url += "&usr_first="      + encode_URI(au_form.usr_first.value);
  au_url += "&usr_last="       + encode_URI(au_form.usr_last.value);
  au_url += "&usr_email="      + encode_URI(au_form.usr_email.value);
  au_url += "&usr_phone="      + encode_URI(au_form.usr_phone.value);
  au_url += "&usr_deptname="   + au_form.usr_deptname.value;
  au_url += "&usr_manager="    + au_form.usr_manager.value;
  au_url += "&usr_title="      + au_form.usr_title.value;
  au_url += "&usr_clientid="   + encode_URI(au_form.usr_clientid.value);
  au_url += "&usr_altemail="   + encode_URI(au_form.usr_altemail.value);
  au_url += "&usr_theme="      + au_form.usr_theme.value;
  au_url += "&usr_passwd="     + encode_URI(au_form.usr_passwd.value);
  au_url += "&usr_reenter="    + encode_URI(au_form.usr_reenter.value);
  au_url += "&usr_reset="      + au_form.usr_reset.checked;
  au_url += "&usr_notify="     + encode_URI(au_form.usr_notify.value);
  au_url += "&usr_freq="       + encode_URI(au_form.usr_freq.value);
  au_url += "&usr_report="     + au_form.usr_report.checked;
  au_url += "&usr_confirm="    + au_form.usr_confirm.checked;

  script = document.createElement('script');
  script.src = p_script_url + au_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('profile.fill.php');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . "/topmenu.start.php"); ?>
<?php include($Sitepath . "/topmenu.end.php"); ?>

<form name="user">

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">User Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('user-help');">Help</a></th>
</tr>
</table>

<div id="user-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Profile Form</strong>
  <ul>
    <li><strong>First Name</strong> - The user's first name.</li>
    <li><strong>Last Name</strong> - The user's last name.</li>
    <li><strong>Theme</strong> - Select a theme for the user.</li>
    <li><strong>E-Mail</strong> - The user's official email address. This is important in that several email portions of the system check incoming email against this address.</li>
    <li><strong>Phone Number</strong> - The user's contact phone number. Could be desk phone or cell phone.</li>
    <li><strong>Department</strong> - The Intrado department the user belongs to.
  </ul></li>
  <li><strong>Password Form</strong>
  <ul>
    <li><strong>Reset User Password</strong> - Enter in a new password for the user here.</li>
    <li><strong>Re-Enter Password</strong> - Enter the password in again. If the passwords don't match, the two boxes <span class="ui-state-highlight">change to indicate</span> a mismatch</li>
    <li><strong>Force Password Reset on Next Login</strong> - Check this box if you're resetting a user password or otherwise want to force a password reset.</li>
  </ul></li>
  <li><strong>Changelog Form</strong>
  <ul>
    <li><strong>Remedy ClientID</strong> - This is the user's ID in Remedy which is the same as your Windows Login ID. When changelog's are emailed in, this ID is used to set the owner of the ticket. If this is missing, the email won't be sent and an email will be sent to the user indicating an error.</li>
    <li><strong>Alternate E-Mail</strong> - If the user will be sending changelog emails from other systems such as a jump server, enter that email here.</li>
  </ul></li>
  <li><strong>Morning Report Form</strong>
  <ul>
    <li><strong>Receive the Techops Morning Report</strong> - Select this if you wish to receive the Morning Report. This provides a summary of important issues as provided by selected teams in TechOps.</li>
    <li><strong>cc On Group Responses</strong> - Do you want to receive a copy of the confirmation email that is a reply to the status update sent in by a member of your group? When someone on your team sends a status report in via email, they receive a reply from the system indicating the email was received successfully. When you check this box, you also receive a copy of this confirmation reply.</li>
  </ul></li>
  <li><strong>Website Certificate Form</strong>
  <ul>
    <li><strong>Number of Days Prior to Expiration</strong> - If a user has been identified by the Web Applications team as being responsible for a web site certificate, they will need to be notified when the certificate is going to expire. This should be set to a sufficiently large number to ensure notifications are made in a timely manner. To disable notifications, set to -1.</li>
    <li><strong>Reminder Increment</strong> - Once the initial notification goes out, the user will want to be notified regularly until the certificate has been updated. This value is the number of days after the initial notification you wish to be notified about the pending expiration. This continues every X days until the certificate is renewed. Set to -1 to disable.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="button ui-widget-content">
<input type="button" disabled="true" name="update" value="Update"  onClick="javascript:attach_users('profile.mysql.php', 1);">
  </td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Profile Form</th>
</tr>
<tr>
  <td class="ui-widget-content">First Name <input type="text" name="usr_first" size="20"></td>
  <td class="ui-widget-content">Last Name <input type="text" name="usr_last" size="20"></td>
  <td class="ui-widget-content">E-Mail <input type="email" name="usr_email" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Phone Number <input type="phone" name="usr_phone" size="20"></td>
  <td class="ui-widget-content">Theme <select name="usr_theme">
<?php
  $q_string  = "select theme_id,theme_title ";
  $q_string .= "from themes ";
  $q_string .= "order by theme_title";
  $q_themes = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_themes = mysql_fetch_array($q_themes)) {
    print "<option value=\"" . $a_themes['theme_id'] . "\">" . $a_themes['theme_title'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Department: <select name="usr_deptname">
  <option value="0">Unassigned</option>
<?php
  $q_string  = "select dep_id,dep_unit,dep_dept,dep_name,bus_name ";
  $q_string .= "from department ";
  $q_string .= "left join business_unit on business_unit.bus_unit = department.dep_unit ";
  $q_string .= "order by bus_name,dep_name";
  $q_department = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_department = mysql_fetch_array($q_department)) {
    print "  <option value=\"" . $a_department['dep_id'] . "\">" . htmlspecialchars($a_department['bus_name']) . " " . htmlspecialchars($a_department['dep_name']) . " (" . htmlspecialchars($a_department['dep_unit']) . "-" . htmlspecialchars($a_department['dep_dept']) . ")</option>\n";
  }
?>
  </select></td>
</tr>
<tr>
  <td class="ui-widget-content">Select Your Title: <select name="usr_title">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select tit_id,tit_name ";
  $q_string .= "from titles ";
  $q_string .= "order by tit_id ";
  $q_titles = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_titles = mysql_fetch_array($q_titles)) {
    print "<option value=\"" . $a_titles['tit_id'] . "\">" . $a_titles['tit_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content" colspan="3">Select Your Manager: <select name="usr_manager">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Password Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="password">Reset User Password <input type="password" autocomplete="off" name="usr_passwd" size="30" onKeyDown="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);" onKeyUp="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);"></td>
  <td class="ui-widget-content" id="reenter">Re-Enter Password <input type="password" name="usr_reenter" size="30" onKeyDown="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);" 
onKeyUp="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);"></td>
  <td class="ui-widget-content"><label>Force Password Reset on Next Login? <input type="checkbox" checked="true" name="usr_reset"></label></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Changelog Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Remedy ClientID: <input type="text" name="usr_clientid" size="20"></td>
  <td class="ui-widget-content">Alternate E-Mail <input type="text" name="usr_altemail" autocomplete="off" size="80"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Morning Report Form</th>
</tr>
<tr>
  <td class="ui-widget-content"><label>Receive The Techops Morning Report: <input type="checkbox" name="usr_report"></label></td>
  <td class="ui-widget-content"><label>cc On Group Responses: <input type="checkbox" name="usr_confirm"></label></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Website Certificate Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Number of Days Prior to Expiration: <input type="number" name="usr_notify" size="20"></td>
  <td class="ui-widget-content">Reminder Increment: <input type="number" name="usr_freq" size="20"></td>
</tr>
</table>

</div>

</form>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
