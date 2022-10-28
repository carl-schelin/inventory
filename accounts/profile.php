<?php
# Script: profile.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Guest);

  $package = "profile.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

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
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
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
  au_url += "&usr_manager="    + au_form.usr_manager.value;
  au_url += "&usr_title="      + au_form.usr_title.value;
  au_url += "&usr_theme="      + au_form.usr_theme.value;
  au_url += "&usr_passwd="     + encode_URI(au_form.usr_passwd.value);
  au_url += "&usr_reenter="    + encode_URI(au_form.usr_reenter.value);
  au_url += "&usr_reset="      + au_form.usr_reset.checked;
  au_url += "&usr_notify="     + encode_URI(au_form.usr_notify.value);
  au_url += "&usr_freq="       + encode_URI(au_form.usr_freq.value);

  script = document.createElement('script');
  script.src = p_script_url + au_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('profile.fill.php');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( );
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

<p>This page displays your information with regards to the Inventory. Certainly most fields are self explanatory.</p>

<p>The Website Certificate Form provides information on if and how you want to be notified if you're identified as an 
owner of a website certificate. It can take some time to get a certificate generated depending on your system so setting 
the first field sufficiently early ensures you will be notified in enough time to get and apply the new certificate. 
The Reminder Increment lists how often you want to be reminded of the certificate's expiration coming due.</p>

<p>Setting the Days Prior to Expiration to -1 will disable notifications.</p>

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
  <th class="ui-state-default"colspan="2">Profile Form</th>
</tr>
<tr>
  <td class="ui-widget-content">First Name <input type="text" name="usr_first" size="20"></td>
  <td class="ui-widget-content">Last Name <input type="text" name="usr_last" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">E-Mail <input type="email" name="usr_email" size="40"></td>
  <td class="ui-widget-content">Phone Number <input type="phone" name="usr_phone" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Select Your Title: <select name="usr_title">
<?php
  $q_string  = "select tit_id,tit_name ";
  $q_string .= "from inv_titles ";
  $q_string .= "order by tit_name ";
  $q_inv_titles = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_titles = mysqli_fetch_array($q_inv_titles)) {
    print "<option value=\"" . $a_inv_titles['tit_id'] . "\">" . $a_inv_titles['tit_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Select Your Manager: <select name="usr_manager">
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
</select></td>
  <td class="ui-widget-content" colspan="2">Inventory Theme <select name="usr_theme">
<?php
  $q_string  = "select theme_id,theme_title ";
  $q_string .= "from themes ";
  $q_string .= "order by theme_title";
  $q_themes = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_themes = mysqli_fetch_array($q_themes)) {
    print "<option value=\"" . $a_themes['theme_id'] . "\">" . $a_themes['theme_title'] . "</option>\n";
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
