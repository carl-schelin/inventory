<?php
# Script: groups.php
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

  $package = "groups.php";

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
<title>Group Editor</title>

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
  var af_form = document.createDialog;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&grp_organization="  + af_form.grp_organization.value;
  af_url += "&grp_name="          + encode_URI(af_form.grp_name.value);
  af_url += "&grp_manager="       + af_form.grp_manager.value;
  af_url += "&grp_role="          + af_form.grp_role.value;
  af_url += "&grp_email="         + encode_URI(af_form.grp_email.value);
  af_url += "&grp_disabled="      + af_form.grp_disabled.value;
  af_url += "&grp_status="        + af_form.grp_status.checked;
  af_url += "&grp_server="        + af_form.grp_server.checked;
  af_url += "&grp_import="        + af_form.grp_import.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.updateDialog;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&grp_organization="  + uf_form.grp_organization.value;
  uf_url += "&grp_name="          + encode_URI(uf_form.grp_name.value);
  uf_url += "&grp_manager="       + uf_form.grp_manager.value;
  uf_url += "&grp_role="          + uf_form.grp_role.value;
  uf_url += "&grp_email="         + encode_URI(uf_form.grp_email.value);
  uf_url += "&grp_disabled="      + uf_form.grp_disabled.value;
  uf_url += "&grp_status="        + uf_form.grp_status.checked;
  uf_url += "&grp_server="        + uf_form.grp_server.checked;
  uf_url += "&grp_import="        + uf_form.grp_import.checked;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('groups.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 350,
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
          show_file('groups.mysql.php?update=-1');
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

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 350,
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
          show_file('groups.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Group",
        click: function() {
          update_file('groups.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Group",
        click: function() {
          update_file('groups.mysql.php', 0);
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

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Group Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('group-help');">Help</a></th>
</tr>
</table>

<div id="group-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Groups are an important part of any organization. In the Inventory, you can be members of multiple 
groups. You select from a list and select whether you're a full member or a read-only member.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Group"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Group Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('group-listing-help');">Help</a></th>
</tr>
</table>

<div id="group-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Group Listing</strong></p>

<p>This page lists all of the Groups that have been defined for the Inventory.</p>

<p>To add a new Group, click on the Add Group button. This will bring up a dialog box which you can use to add a 
new Group.</p>

<p>To edit an existing Group, click on the entry in the listing. A dialog box will be displayed where you can edit 
the current entry, or if there is a small difference, you can make changes and add a new Group.</p>

</div>

</div>


<span id="group_mysql"></span>

</div>

</div>


<div id="dialogCreate" title="Add Group">

<form name="createDialog">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Group Organization: <select name="grp_organization">
<?php
  $q_string  = "select org_id,org_name ";
  $q_string .= "from organizations ";
  $q_string .= "order by org_name ";
  $q_organizations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_organizations = mysqli_fetch_array($q_organizations)) {
    print "<option value=\"" . $a_organizations['org_id'] . "\">" . $a_organizations['org_name'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Group Name: <input type="text" name="grp_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Group Role: <select name="grp_role">
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
</tr>
<tr>
  <td class="ui-widget-content">Manager: <select name="grp_manager">
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
  <td class="ui-widget-content">Status <select name="grp_disabled">
<option value="0">Enabled</option>
<option value="1">Disabled</option>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Receive Check Status Report? <input type="checkbox" name="grp_status"></label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Receive Check Server Report? <input type="checkbox" name="grp_server"></label></td>
<tr>
</tr>
  <td class="ui-widget-content"><label>Import Server Data? <input type="checkbox" name="grp_import"></label></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Add Group">

<form name="updateDialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Group Organization: <select name="grp_organization">
<?php
  $q_string  = "select org_id,org_name ";
  $q_string .= "from organizations ";
  $q_string .= "order by org_name ";
  $q_organizations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_organizations = mysqli_fetch_array($q_organizations)) {
    print "<option value=\"" . $a_organizations['org_id'] . "\">" . $a_organizations['org_name'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Group Name: <input type="text" name="grp_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Group Role: <select name="grp_role">
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
</tr>
<tr>
  <td class="ui-widget-content">Manager: <select name="grp_manager">
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
  <td class="ui-widget-content">Status <select name="grp_disabled">
<option value="0">Enabled</option>
<option value="1">Disabled</option>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Receive Check Status Report? <input type="checkbox" name="grp_status"></label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Receive Check Server Report? <input type="checkbox" name="grp_server"></label></td>
<tr>
</tr>
  <td class="ui-widget-content"><label>Import Server Data? <input type="checkbox" name="grp_import"></label></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
