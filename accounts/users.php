<?php
# Script: users.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('1');

  $package = "users.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Users</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">
<?php

  if (check_userlevel($AL_Admin)) {
?>
function delete_user( p_script_url ) {
  var question;
  var answer;

  question  = "The preference is to change the user access level from Enabled to Disabled\n";
  question += "which prevents the orphaning of user owned or identified information. Deleting\n";
  question += "the user should be done when removing duplicate records or if you know the \n";
  question += "user has no user managed information.\n\n";

  question += "Delete this User anyway?";

  answer = confirm(question);

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    show_file('users.mysql.php?update=-1');
  }
}
<?php
  }
?>

function attach_users(p_script_url, update) {
  var au_form = document.user;
  var au_url;

  au_url  = '?update='   + update;
  au_url += "&id="       + au_form.id.value;

  au_url += "&usr_first="      + encode_URI(au_form.usr_first.value);
  au_url += "&usr_last="       + encode_URI(au_form.usr_last.value);
  au_url += "&usr_name="       + encode_URI(au_form.usr_name.value);
  au_url += "&usr_disabled="   + au_form.usr_disabled.value;
  au_url += "&usr_level="      + au_form.usr_level.value;
  au_url += "&usr_manager="    + au_form.usr_manager.value;
  au_url += "&usr_title="      + au_form.usr_title.value;
  au_url += "&usr_email="      + encode_URI(au_form.usr_email.value);
  au_url += "&usr_phone="      + encode_URI(au_form.usr_phone.value);
  au_url += "&usr_group="      + au_form.usr_group.value;
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
  show_file('users.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddUser' ).click(function() {
    $( "#dialogUser" ).dialog('open');
  });

  $( "#dialogUser" ).dialog({
    autoOpen: false,
    modal: true,
    height: 450,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogUser" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('users.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update User",
        click: function() {
          attach_users('users.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add User",
        click: function() {
          attach_users('users.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . "/topmenu.start.php"); ?>
<?php include($Sitepath . "/topmenu.end.php"); ?>

<form name="mainform">

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
    <li><strong>User Login</strong> - Used by the user to log in to the system. This can be changed but the user needs to know the new name.</li>
    <li><strong>User Access</strong> - It's best to disable a user to maintain any ownerships in the system. Change to Disabled to deny access to an account.</li>
    <li><strong>Edit Level</strong> - There are four levels. The site has restrictions for access. Most users are set to Edit mode since they have parts of the Inventory that they need to be able to edit.</li>
    <li><strong>Theme</strong> - Select a theme for the user.</li>
    <li><strong>First Name</strong> - The user's first name.</li>
    <li><strong>Last Name</strong> - The user's last name.</li>
    <li><strong>E-Mail</strong> - The user's official email address. This is important in that several email portions of the system check incoming email against this address.</li>
    <li><strong>Phone Number</strong> - The user's contact phone number. Could be desk phone or cell phone.</li>
    <li><strong>Group</strong> - The group the user belongs to. This gives the user ownership over editing equipment owned by that group.</li>
    <li><strong>Department</strong> - The department the user belongs to.
  </ul></li>
  <li><strong>Password Form</strong>
  <ul>
    <li><strong>Reset User Password</strong> - Enter in a new password for the user here.</li>
    <li><strong>Re-Enter Password</strong> - Enter the password in again. If the passwords don't match, the two boxes <span class="ui-state-error">change to indicate</span> a mismatch</li>
    <li><strong>Force Password Reset on Next Login</strong> - Check this box if you're resetting a user password or otherwise want to force a password reset.</li>
  </ul></li>
  <li><strong>Changelog Form</strong>
  <ul>
    <li><strong>Remedy ClientID</strong> - This is the user's ID in Remedy and should match your Windows login id. When changelog's are emailed in, this ID is used to set the owner of the ticket. If this is missing, the email won't be sent and an email will be sent to the user indicating an error.</li>
    <li><strong>Alternate E-Mail</strong> - If the user will be sending changelog emails from other systems such as a jump server, enter that email here.</li>
  </ul></li>
  <li><strong>Morning Report Form</strong>
  <ul>
    <li><strong>Receive the Techops Morning Report</strong> - Select this if you wish to receive the Morning Report. This provides a summary of important issues as provided by selected teams in TechOps.</li>
    <li><strong>cc On Group Responses</strong> - Do you want to receive a copy of the confirmation email that is a reply to the status update sent in by a member of your group? When someone on your team sends a status report in via email, they receive a reply from the system indicating the email was received successfully. When you check this box, you also receive a copy of this confirmation reply.</li>
  </ul></li>
  <li><strong>Website Certificate Form</strong>
  <ul>
    <li><strong>Number of Days Prior to Expiration</strong> - If a user has been identified by the Web Applications team as being responsible for a web site certificate, they will need to be notified when the certificate is going to expire. This should be set to a sufficiently large number to ensure notifications are made in a timely manner.</li>
    <li><strong>Reminder Increment</strong> - Once the initial notification goes out, the user will want to be notified regularly until the certificate has been updated.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddUser" value="Add User"></td>
</tr>
</table>

<p></p>

<div id="tabs">

<ul>
  <li><a href="#newuser">New Users</a></li>
  <li><a href="#registered">Registered Users</a></li>
  <li><a href="#admin">Admin Users</a></li>
  <li><a href="#readonly">Read-Only Users</a></li>
  <li><a href="#guest">Guest Users</a></li>
  <li><a href="#disabled">Disabled Users</a></li>
</ul>


<div id="newuser">

<span id="new_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


<div id="registered">

<span id="registered_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


<div id="admin">

<span id="admin_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


<div id="readonly">

<span id="readonly_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


<div id="guest">

<span id="guest_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


<div id="disabled">

<span id="disabled_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


</div>

</div>

</form>

<div id="dialogUser" title="User Form">

<form name="user">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Profile Form</th>
</tr>
<tr>
  <td class="ui-widget-content">User Login <input type="text" name="usr_name" size="10"></td>
  <td class="ui-widget-content">User Access <select name="usr_disabled">
<option value="0">Enabled</option>
<option value="1">Disabled</option>
</select></td>
  <td class="ui-widget-content">Edit Level <select name="usr_level">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select lvl_id,lvl_name ";
  $q_string .= "from levels ";
  $q_string .= "where lvl_disabled = 0 ";
  $q_string .= "order by lvl_id";
  $q_levels = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_levels = mysql_fetch_array($q_levels)) {
    print "<option value=\"" . $a_levels['lvl_id'] . "\">" . $a_levels['lvl_name'] . "</option>\n";
  }
?>
</select></td>
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
</tr>
<tr>
  <td class="ui-widget-content">First Name <input type="text" name="usr_first" size="20"></td>
  <td class="ui-widget-content">Last Name <input type="text" name="usr_last" size="20"></td>
  <td class="ui-widget-content" colspan="2">E-Mail <input type="text" name="usr_email" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Phone Number <input type="text" name="usr_phone" size="20"></td>
  <td class="ui-widget-content">Group <select name="usr_group">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_groups = mysql_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content" colspan="2">Department: <select name="usr_deptname">
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
  <td class="ui-widget-content" colspan="2">Title: <select name="usr_title">
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
  <td class="ui-widget-content" colspan="2">Manager: <select name="usr_manager">
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
</tr>
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
  <td class="ui-widget-content"><label>Receive The Techops Morning Report: <input type="checkbox" checked name="usr_report"></label></td>
  <td class="ui-widget-content"><label>cc On Group Responses: <input type="checkbox" name="usr_confirm"></label></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Website Certificate Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Number of Days Prior to Expiration: <input type="text" name="usr_notify" size="20"></td>
  <td class="ui-widget-content">Reminder Increment: <input type="text" name="usr_freq" size="20"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
