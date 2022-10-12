<?php
# Script: grouplist.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "grouplist.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing the Grouplist table");

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = $_SESSION['group'];
  }

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
<title>Manage Group Memberships</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_grouplist( p_script_url ) {
  var answer = confirm("Delete this Group Member?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    clear_fields();
  }
}

function attach_file(p_script_url, update) {
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&gpl_user="       + af_form.gpl_user.value;
  af_url += "&gpl_group="      + af_form.gpl_group.value;
  af_url += "&gpl_edit="       + af_form.gpl_edit.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file(p_script_url, update) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += "&id="       + uf_form.id.value;

  uf_url += "&gpl_user="       + uf_form.gpl_user.value;
  uf_url += "&gpl_group="      + uf_form.gpl_group.value;
  uf_url += "&gpl_edit="       + uf_form.gpl_edit.checked;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('grouplist.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
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
          show_file('grouplist.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Membership",
        click: function() {
          attach_file('grouplist.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
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
          show_file('grouplist.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Membership",
        click: function() {
          update_file('grouplist.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Membership",
        click: function() {
          update_file('grouplist.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Group Membership Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('group-help');">Help</a></th>
</tr>
</table>

<div id="group-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">
<ul>
  <li><strong>Group Membership Management</strong></li>
</ul>

</div>

</div>


<p></p>

<table class="ui-styled-table">
<tr>
<th class="ui-state-default">Group Membership Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('group-listing-help');">Help</a></th>
</tr>
</table>

<div id="group-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Group Membership Listing</strong>
  <ul>
    <li><strong>Delete (x)</strong> - Delete this association.</li>
    <li><strong>Editing</strong> - Click on an association to bring up the form and edit it.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Users that are <span class="ui-state-highlight">highlighted</span> indicate this is 
their <strong>Primary</strong> group. Removing them here may not prevent their access to the group's 
assets if they've only changed organizations within the company. Contact the Inventory Admin to 
correct the Primary group membership.</li>
    <li>Users that are <span class="ui-state-error">highlighted</span> are users who have been 
Disabled in the system and should be removed from the group.</li>
    <li>Users with an asterisk (*) indicate a user who has escalated privileges in the selected group</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Membership"></td>
</tr>
</table>


<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>



<div id="dialogCreate" title="Add Group Membership">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Add user <select name="gpl_user">
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
  <td class="ui-widget-content">To group <select name="gpl_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "left join grouplist on grouplist.gpl_group = a_groups.grp_id ";
  if (check_userlevel($db, $AL_Admin) == 0) {
    $q_string .= "where gpl_user = " . $_SESSION['uid'] . " ";
  }
  $q_string .= "group by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="gpl_edit"> Let this user edit your assets?</label></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Update Group Membership">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Add user <select name="gpl_user">
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
  <td class="ui-widget-content">To group <select name="gpl_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "left join grouplist on grouplist.gpl_group = a_groups.grp_id ";
  if (check_userlevel($db, $AL_Admin) == 0) {
    $q_string .= "where gpl_user = " . $_SESSION['uid'] . " ";
  }
  $q_string .= "group by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="gpl_edit"> Let this user edit your assets?</label></td>
</tr>
</table>

</form>

</div>



<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
