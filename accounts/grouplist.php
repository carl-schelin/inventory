<?php
# Script: grouplist.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "grouplist.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing the Grouplist table");

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = $_SESSION['group'];
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
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
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

function attach_grouplist(p_script_url, update) {
  var ag_form = document.grouplist;
  var ag_url;

  ag_url  = '?update='   + update;
  ag_url += "&id="       + ag_form.id.value;

  ag_url += "&gpl_user="       + ag_form.gpl_user.value;
  ag_url += "&gpl_group="      + ag_form.gpl_group.value;
  ag_url += "&gpl_edit="       + ag_form.gpl_edit.checked;

  script = document.createElement('script');
  script.src = p_script_url + ag_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('grouplist.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickAddMember' ).click(function() {
    $( "#dialogMember" ).dialog('open');
  });

  $( "#dialogMember" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogMember" ).hide();
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
        text: "Add To Group",
        click: function() {
          attach_grouplist('grouplist.mysql.php', 0);
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

<form name="mainform">

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Group Membership Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('group-help');">Help</a></th>
</tr>
</table>

<div id="group-help" style="display: none">

<div class="main-help ui-widget-content">
<ul>
  <li><strong>Group Membership Management</strong></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddMember" value="Add Member"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="dialogMember" title="Group Membership Form">

<form name="grouplist">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Group Membership Form</th>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Add <select name="gpl_user"></th>
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
</select> to <select name="gpl_group">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "left join grouplist on grouplist.gpl_group = groups.grp_id ";
  if (check_userlevel($db, $AL_Admin) == 0) {
    $q_string .= "where gpl_user = " . $_SESSION['uid'] . " ";
  }
  $q_string .= "group by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
  }
?>
</select> <input type="checkbox" name="gpl_edit"> Let this user edit your assets? (Doesn't affect changelog)</td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
