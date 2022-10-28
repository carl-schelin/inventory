<?php
# Script: users.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Admin);

  $package = "users.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

# if help has not been seen yet,
  if (show_Help($db, $Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>User Editor</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">
<?php

  if (check_userlevel($db, $AL_Admin)) {
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

function attach_file(p_script_url, update) {
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&usr_first="      + encode_URI(af_form.usr_first.value);
  af_url += "&usr_last="       + encode_URI(af_form.usr_last.value);
  af_url += "&usr_name="       + encode_URI(af_form.usr_name.value);
  af_url += "&usr_disabled="   + af_form.usr_disabled.value;
  af_url += "&usr_level="      + af_form.usr_level.value;
  af_url += "&usr_manager="    + af_form.usr_manager.value;
  af_url += "&usr_title="      + af_form.usr_title.value;
  af_url += "&usr_email="      + encode_URI(af_form.usr_email.value);
  af_url += "&usr_phone="      + encode_URI(af_form.usr_phone.value);
  af_url += "&usr_group="      + af_form.usr_group.value;
  af_url += "&usr_theme="      + af_form.usr_theme.value;
  af_url += "&usr_passwd="     + encode_URI(af_form.usr_passwd.value);
  af_url += "&usr_reenter="    + encode_URI(af_form.usr_reenter.value);
  af_url += "&usr_reset="      + af_form.usr_reset.checked;
  af_url += "&usr_notify="     + encode_URI(af_form.usr_notify.value);
  af_url += "&usr_freq="       + encode_URI(af_form.usr_freq.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file(p_script_url, update) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += "&id="       + uf_form.id.value;

  uf_url += "&usr_first="      + encode_URI(uf_form.usr_first.value);
  uf_url += "&usr_last="       + encode_URI(uf_form.usr_last.value);
  uf_url += "&usr_name="       + encode_URI(uf_form.usr_name.value);
  uf_url += "&usr_disabled="   + uf_form.usr_disabled.value;
  uf_url += "&usr_level="      + uf_form.usr_level.value;
  uf_url += "&usr_manager="    + uf_form.usr_manager.value;
  uf_url += "&usr_title="      + uf_form.usr_title.value;
  uf_url += "&usr_email="      + encode_URI(uf_form.usr_email.value);
  uf_url += "&usr_phone="      + encode_URI(uf_form.usr_phone.value);
  uf_url += "&usr_group="      + uf_form.usr_group.value;
  uf_url += "&usr_theme="      + uf_form.usr_theme.value;
  uf_url += "&usr_passwd="     + encode_URI(uf_form.usr_passwd.value);
  uf_url += "&usr_reenter="    + encode_URI(uf_form.usr_reenter.value);
  uf_url += "&usr_reset="      + uf_form.usr_reset.checked;
  uf_url += "&usr_notify="     + encode_URI(uf_form.usr_notify.value);
  uf_url += "&usr_freq="       + encode_URI(uf_form.usr_freq.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('users.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( );

  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 525,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogCreate" ).hide();
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
        text: "Add User",
        click: function() {
          attach_file('users.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 525,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogUpdate" ).hide();
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
          update_file('users.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add User",
        click: function() {
          update_file('users.mysql.php', 0);
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

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">User Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('user-help');">Help</a></th>
</tr>
</table>

<div id="user-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This page lets you manage users for the Inventory. If a user adds an account, it is in the first tab waiting for you to approve the access.

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add User"></td>
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

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">New User Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('newuser-help');">Help</a></th>
</tr>
</table>

<div id="newuser-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>New User Listing</strong></p>

<p>This page lists all the new users requesting access to the system.</p>

<p>To add a New User, click the Add User button. This will bring up a dialog box which you can then use to create a new user.</p>

<p>To edit a New User, click on the user in the listing. A dialog box will be displayed where you can edit the current user.</p>

<p>Every user has the option to receive Certificate Notifications. The second to the last field lets you set the number of days 
before a Certificate expires where the user will receive an email. Generally you want to provide sufficient time to both generate 
a new certificate and the amount of time to get approval to apply the certificate.</p>

<p>In addition, the last field lets you remind the user every few days as defined.</p>

</div>

</div>

<span id="new_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


<div id="registered">

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Registered Users Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('registered-help');">Help</a></th>
</tr>
</table>

<div id="registered-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Registered Users Listing</strong></p>

<p>This page lists all active users in the system.</p>

<p>To add a New User, click the Add User button. This will bring up a dialog box which you can then use to create a new user.</p>

<p>To edit a User, click on the user in the listing. A dialog box will be displayed where you can edit the current user.</p>

<p>Every user has the option to receive Certificate Notifications. The second to the last field lets you set the number of days
before a Certificate expires where the user will receive an email. Generally you want to provide sufficient time to both generate
a new certificate and the amount of time to get approval to apply the certificate.</p>

<p>In addition, the last field lets you remind the user every few days as defined.</p>

</div>

</div>

<span id="registered_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


<div id="admin">

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Admin Users Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('admin-help');">Help</a></th>
</tr>
</table>

<div id="admin-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Admin Users Listing</strong></p>

<p>This page lists all the users that have Administrative privileges.</p>

<p>To add a New User, click the Add User button. This will bring up a dialog box which you can then use to create a new user.</p>

<p>To edit a User, click on the user in the listing. A dialog box will be displayed where you can edit the current user.</p>

<p>Every user has the option to receive Certificate Notifications. The second to the last field lets you set the number of days
before a Certificate expires where the user will receive an email. Generally you want to provide sufficient time to both generate
a new certificate and the amount of time to get approval to apply the certificate.</p>

<p>In addition, the last field lets you remind the user every few days as defined.</p>

</div>

</div>

<span id="admin_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


<div id="readonly">

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Read/Only Users Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('readonly-help');">Help</a></th>
</tr>
</table>

<div id="readonly-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Read/Only Users Listing</strong></p>

<p>This page lists all the users that have been set to Read/Only.</p>

<p>To add a New User, click the Add User button. This will bring up a dialog box which you can then use to create a new user.</p>

<p>To edit a User, click on the user in the listing. A dialog box will be displayed where you can edit the current user.</p>

<p>Every user has the option to receive Certificate Notifications. The second to the last field lets you set the number of days
before a Certificate expires where the user will receive an email. Generally you want to provide sufficient time to both generate
a new certificate and the amount of time to get approval to apply the certificate.</p>

<p>In addition, the last field lets you remind the user every few days as defined.</p>

</div>

</div>

<span id="readonly_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


<div id="guest">

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Guest Users Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('guest-help');">Help</a></th>
</tr>
</table>

<div id="guest-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Guest Users Listing</strong></p>

<p>This page lists all the users that have been identified as Guests.</p>

<p>To add a New User, click the Add User button. This will bring up a dialog box which you can then use to create a new user.</p>

<p>To edit a User, click on the user in the listing. A dialog box will be displayed where you can edit the current user.</p>

<p>Every user has the option to receive Certificate Notifications. The second to the last field lets you set the number of days
before a Certificate expires where the user will receive an email. Generally you want to provide sufficient time to both generate
a new certificate and the amount of time to get approval to apply the certificate.</p>

<p>In addition, the last field lets you remind the user every few days as defined.</p>

</div>

</div>

<span id="guest_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


<div id="disabled">

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Disabled Users Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('disabled-help');">Help</a></th>
</tr>
</table>

<div id="disabled-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Disabled Users Listing</strong></p>

<p>This page lists all the users that have been disabled.</p>

<p>To add a New User, click the Add User button. This will bring up a dialog box which you can then use to create a new user.</p>

<p>To edit a User, click on the user in the listing. A dialog box will be displayed where you can edit the current user.</p>

<p>Every user has the option to receive Certificate Notifications. The second to the last field lets you set the number of days
before a Certificate expires where the user will receive an email. Generally you want to provide sufficient time to both generate
a new certificate and the amount of time to get approval to apply the certificate.</p>

<p>In addition, the last field lets you remind the user every few days as defined.</p>

</div>

</div>

<span id="disabled_users_table"><?php print wait_Process('Loading Users...')?></span>

</div>


</div>

</div>



<div id="dialogCreate" title="Add User">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">User Access <select name="usr_disabled">
<option value="0">Enabled</option>
<option value="1">Disabled</option>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Edit Level <select name="usr_level">
<?php
  $q_string  = "select lvl_id,lvl_name ";
  $q_string .= "from levels ";
  $q_string .= "where lvl_disabled = 0 ";
  $q_string .= "order by lvl_id";
  $q_levels = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_levels = mysqli_fetch_array($q_levels)) {
    print "<option value=\"" . $a_levels['lvl_id'] . "\">" . $a_levels['lvl_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">User Login <input type="text" name="usr_name" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">First Name <input type="text" name="usr_first" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Last Name <input type="text" name="usr_last" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">E-Mail <input type="text" name="usr_email" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Phone Number <input type="text" name="usr_phone" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Title: <select name="usr_title">
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
</tr>
<tr>
  <td class="ui-widget-content">Group <select name="usr_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Manager: <select name="usr_manager">
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
  <td class="ui-widget-content">Inventory Theme <select name="usr_theme">
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
<tr>
  <td class="ui-widget-content" id="password">Reset User Password <input type="password" autocomplete="off" name="usr_passwd" size="30" onKeyDown="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);" onKeyUp="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);"></td>
</tr>
<tr>
  <td class="ui-widget-content" id="reenter">Re-Enter Password <input type="password" name="usr_reenter" size="30" onKeyDown="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);" onKeyUp="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Force Password Reset on Next Login? <input type="checkbox" checked="true" name="usr_reset"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Number of Days Prior to Website Certification Expiration to be Notified: <input type="text" name="usr_notify" size="5"></td>
</tr>
<tr>
  <td class="ui-widget-content">Notification Repeats Every <input type="text" name="usr_freq" size="5"> Days After First Notification</td>
</tr>
</table>

</form>

</div>




<div id="dialogUpdate" title="Edit User">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">User Access <select name="usr_disabled">
<option value="0">Enabled</option>
<option value="1">Disabled</option>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Edit Level <select name="usr_level">
<?php
  $q_string  = "select lvl_id,lvl_name ";
  $q_string .= "from levels ";
  $q_string .= "where lvl_disabled = 0 ";
  $q_string .= "order by lvl_id";
  $q_levels = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_levels = mysqli_fetch_array($q_levels)) {
    print "<option value=\"" . $a_levels['lvl_id'] . "\">" . $a_levels['lvl_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">User Login <input type="text" name="usr_name" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">First Name <input type="text" name="usr_first" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Last Name <input type="text" name="usr_last" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">E-Mail <input type="text" name="usr_email" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Phone Number <input type="text" name="usr_phone" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Title: <select name="usr_title">
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
</tr>
<tr>
  <td class="ui-widget-content">Group <select name="usr_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Manager: <select name="usr_manager">
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
  <td class="ui-widget-content">Inventory Theme <select name="usr_theme">
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
<tr>
  <td class="ui-widget-content" id="password">Reset User Password <input type="password" autocomplete="off" name="usr_passwd" size="30" onKeyDown="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);" onKeyUp="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);"></td>
</tr>
<tr>
  <td class="ui-widget-content" id="reenter">Re-Enter Password <input type="password" name="usr_reenter" size="30" onKeyDown="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);" onKeyUp="javascript:show_file('validate.password.php?password=' + usr_passwd.value + '&reenter=' + usr_reenter.value);"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Force Password Reset on Next Login? <input type="checkbox" checked="true" name="usr_reset"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Number of Days Prior to Website Certification Expiration to be Notified: <input type="text" name="usr_notify" size="5"></td>
</tr>
<tr>
  <td class="ui-widget-content">Notification Repeats Every <input type="text" name="usr_freq" size="5"> Days After First Notification</td>
</tr>
</table>

</form>

</div>



<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
