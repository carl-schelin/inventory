<?php
# Script: groups.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('1');

  $package = "groups.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Groups</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">
<?php
  if (check_userlevel($db, $AL_Admin)) {
?>
function delete_line( p_script_url ) {
  var question;
  var answer;

  question  = "The preference is to change the group status from Enabled to Disabled\n";
  question += "which prevents the orphaning of group owned or identified information. Deleting\n";
  question += "the group should be done when removing duplicate records or if you know the \n";
  question += "group has no group managed information.\n\n";

  question += "Delete this Group anyway?";

  answer = confirm(question);

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}
<?php
  }
?>

function attach_file( p_script_url, update ) {
  var af_form = document.groups;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&grp_organization="  + af_form.grp_organization.value;
  af_url += "&grp_name="          + encode_URI(af_form.grp_name.value);
  af_url += "&grp_snow="          + encode_URI(af_form.grp_snow.value);
  af_url += "&grp_manager="       + af_form.grp_manager.value;
  af_url += "&grp_role="          + af_form.grp_role.value;
  af_url += "&grp_email="         + encode_URI(af_form.grp_email.value);
  af_url += "&grp_disabled="      + af_form.grp_disabled.value;
  af_url += "&grp_magic="         + encode_URI(af_form.grp_magic.value);
  af_url += "&grp_category="      + encode_URI(af_form.grp_category.value);
  af_url += "&grp_changelog="     + encode_URI(af_form.grp_changelog.value);
  af_url += "&grp_clfile="        + encode_URI(af_form.grp_clfile.value);
  af_url += "&grp_clserver="      + encode_URI(af_form.grp_clserver.value);
  af_url += "&grp_clscript="      + encode_URI(af_form.grp_clscript.value);
  af_url += "&grp_report="        + encode_URI(af_form.grp_report.value);
  af_url += "&grp_status="        + af_form.grp_status.checked;
  af_url += "&grp_server="        + af_form.grp_server.checked;
  af_url += "&grp_import="        + af_form.grp_import.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('groups.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddGroup' ).click(function() {
    $( "#dialogGroup" ).dialog('open');
  });

  $( "#dialogGroup" ).dialog({
    autoOpen: false,
    modal: true,
    height: 365,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogGroup" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('groups.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Group",
        click: function() {
          attach_file('groups.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Group",
        click: function() {
          attach_file('groups.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="mainform">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Group Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('group-help');">Help</a></th>
</tr>
</table>

<div id="group-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Group Form</strong>
  <ul>
    <li><strong>Group Organization</strong> - The overall company organization this group belongs to.</li>
    <li><strong>Group Name</strong> - The name as presented in any group selection drop down.</li>
    <li><strong>Group Role</strong> - The technical role of this group.</li>
    <li><strong>E-Mail</strong> - The e-mail address for this group. This is used by RSDP for example to send tasks to the group.</li>
    <li><strong>Status</strong> - Change the status of the group here. Disabled groups will not be shown in the group selection menus.</li>
  </ul></li>
  <li><strong>Magic Form</strong>
  <ul>
    <li><strong>Magic ID</strong> - This is the group's ID in Magic. When RSDP tasks are created, this ID is made the owner of the generated ticket. If this is missing, the email won't be sent and an email will be sent to the user indicating an error.</li>
    <li><strong>Category</strong> - For Magic tickets, this sets the ticket Category to ensure proper reporting. If this is missing, the email won't be sent the user will be notified of the error.</li>
  </ul></li>
  <li><strong>Changelog Form</strong>
  <ul>
    <li><strong>E-Mail Address</strong> - This is email address used by users to submit changelogs. This email is located on the incojs01 jumpstart server and managed by the Unix Team.</li>
    <li><strong>Filename Extension</strong> - In the mailbox where the changelog files are stored, this is the file extension so scripts are able to open and read the mail messages.</li>
    <li><strong>Server Filename</strong> - This is the name of the group's servers file. This file is checked by the changelog process to ensure members of the group are able to submit changelogs for servers they manage.</li>
  </ul></li>
  <li><strong>Morning Report Form</strong>
  <ul>
    <li><strong>Morning Report Order</strong> - The location in the Morning Report display.</li>
  </ul></li>
  <li><strong>General Report Form</strong>
  <ul>
    <li><strong>Check Status Report</strong> - Receive a report on the servers that both the Unix team and your team manages. The report details drive space, user issues, and other identified problems.</li>
    <li><strong>Check Server Report</strong> - Receive a report on whether a server is up or down. This only works on Unix servers as the Unix Service account needs to have access to perform the check.</li>
    <li><strong>Import Server Data</strong> - Do you want the inventory to import the data file for the server? This only works on Unix servers as the Unix Service account needs to have access to retrieve the data.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddGroup" value="Add Group"></td>
</tr>
</table>

</form>

<p></p>

<div id="tabs">

<ul>
  <li><a href="#group">Group Details</a></li>
  <li><a href="#magic">Magic Configuration</a></li>
  <li><a href="#changelog">Changelog Configuration</a></li>
</ul>


<div id="group">

<span id="group_mysql"></span>

</div>


<div id="magic">

<span id="magic_mysql"></span>

</div>


<div id="changelog">

<span id="changelog_mysql"></span>

</div>

</div>

</div>

<div id="dialogGroup" title="Group Form">

<form name="groups">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Group Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Group Organization: <select name="grp_organization">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select org_id,org_name ";
  $q_string .= "from organizations ";
  $q_string .= "order by org_name ";
  $q_organizations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_organizations = mysqli_fetch_array($q_organizations)) {
    print "<option value=\"" . $a_organizations['org_id'] . "\">" . $a_organizations['org_name'] . "</option>\n";
  }
?></select></td>
  <td class="ui-widget-content">Group Name: <input type="text" name="grp_name" size="40"></td>
  <td class="ui-widget-content">Service Now Name: <input type="text" name="grp_snow" size="40"></td>
  <td class="ui-widget-content">Group Role: <select name="grp_role">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select role_id,role_name ";
  $q_string .= "from roles ";
  $q_string .= "order by role_name ";
  $q_roles = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_roles = mysqli_fetch_array($q_roles)) {
    print "<option value=\"" . $a_roles['role_id'] . "\">" . $a_roles['role_name'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">E-Mail: <input type="text" name="grp_email" size="40"></td>
  <td class="ui-widget-content">Manager: <select name="grp_manager">
<option value="0">Unassigned</option>
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
  <td class="ui-widget-content">Status <select name="grp_disabled">
<option value="0">Enabled</option>
<option value="1">Disabled</option>
</select></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Magic Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Magic ID: <input type="text" name="grp_magic" size="40"></td>
  <td class="ui-widget-content">Category: <input type="text" name="grp_category" size="40"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Changelog Form</th>
</tr>
<tr>
  <td class="ui-widget-content">E-Mail Address: <input type="text" name="grp_changelog" size="20"></td>
  <td class="ui-widget-content">Filename Extension: <input type="text" name="grp_clfile" size="20"></td>
  <td class="ui-widget-content">Server Filename: <input type="text" name="grp_clserver" size="20"></td>
  <td class="ui-widget-content">Called Script: <input type="text" name="grp_clscript" size="20"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Morning Report Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Morning Report Order: <input type="text" name="grp_report" size="30"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">General Report Form</th>
</tr>
<tr>
  <td class="ui-widget-content"><label>Receive Check Status Report? <input type="checkbox" name="grp_status"></label></td>
  <td class="ui-widget-content"><label>Receive Check Server Report? <input type="checkbox" name="grp_server"></label></td>
  <td class="ui-widget-content"><label>Import Server Data? <input type="checkbox" name="grp_import"></label></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
