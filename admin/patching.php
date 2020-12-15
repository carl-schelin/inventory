<?php
# Script: patching.php
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

  $package = "patching.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Patch Descriptions</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_patch( p_script_url ) {
  var answer = confirm("Delete this Patch Description?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_patch( p_script_url, update ) {
  var ap_form = document.patching;
  var ap_url;

  ap_url  = '?update='   + update;
  ap_url += '&id='       + ap_form.id.value;

  ap_url += "&patch_name="           + encode_URI(ap_form.patch_name.value);
  ap_url += "&patch_user="           + ap_form.patch_user.value;
  ap_url += "&patch_group="          + ap_form.patch_group.value;
  ap_url += "&patch_date="           + encode_URI(ap_form.patch_date.value);

  script = document.createElement('script');
  script.src = p_script_url + ap_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('patching.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddPatching' ).click(function() {
    $( "#dialogPatching" ).dialog('open');
  });

  $( "#dialogPatching" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogPatching" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('patching.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Description",
        click: function() {
          attach_patch('patching.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Description",
        click: function() {
          attach_patch('patching.mysql.php', 0);
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
  <th class="ui-state-default">Patch Description Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('patching-help');">Help</a></th>
</tr>
</table>

<div id="patching-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Description</strong> - Save any changes to this form.</li>
    <li><strong>Add Description</strong> - Add a new Patch Description.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddPatching" value="Add Description"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</div>

</div>

<div id="dialogPatching" title="Description Form">

<form name="patching">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Patch Description Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="patch_name" size="60"></td>
  <td class="ui-widget-content">User <select name="patch_user">
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
  <td class="ui-widget-content">Group <select name="patch_group">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Date: <input type="text" name="patch_date" size="12"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
