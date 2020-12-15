<?php
# Script: rights.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath  . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "rights.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Rights</title>

<style type="text/css" title="currentStyle" media="screen">
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
  var answer = confirm("Delete this Rights?")

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
  var af_form = document.rights;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&rgt_type="             + af_form.rgt_type.value;
  af_url += "&rgt_apigroup="         + af_form.rgt_apigroup.value;
  af_url += "&rgt_resource="         + af_form.rgt_resource.value;
  af_url += "&rgt_get="              + af_form.rgt_get.checked;
  af_url += "&rgt_list="             + af_form.rgt_list.checked;
  af_url += "&rgt_watch="            + af_form.rgt_watch.checked;
  af_url += "&rgt_impersonate="      + af_form.rgt_impersonate.checked;
  af_url += "&rgt_create="           + af_form.rgt_create.checked;
  af_url += "&rgt_delete="           + af_form.rgt_delete.checked;
  af_url += "&rgt_deletecollection=" + af_form.rgt_deletecollection.checked;
  af_url += "&rgt_patch="            + af_form.rgt_patch.checked;
  af_url += "&rgt_update="           + af_form.rgt_update.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('rights.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddRights' ).click(function() {
    $( "#dialogRights" ).dialog('open');
  });

  $( "#dialogRights" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogRights" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('rights.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Rights",
        click: function() {
          attach_file('rights.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Rights",
        click: function() {
          attach_file('rights.mysql.php', 0);
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
  <th class="ui-state-default">Rights Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('rights-help');">Help</a></th>
</tr>
</table>

<div id="rights-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Rights</strong> - Save any changes to this form.</li>
    <li><strong>Add Rights</strong> - Add a new Rights to the database. You can edit an existing Rights and click this button to copy a Rights.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddRights" value="Add Rights"></td>
</tr>
</table>

</form>

<span id="rights_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

<div id="dialogRights" title="Rights Form">

<form name="rights">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Rights Form</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2"><select name="rgt_apigroup">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select api_id,api_name ";
  $q_string .= "from apigroups ";
  $q_string .= "order by api_name ";
  $q_apigroups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_apigroups = mysqli_fetch_array($db, $q_apigroups)) {
    print "<option value=\"" . $a_apigroups['api_id'] . "\">" . $a_apigroups['api_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content" colspan="3"><select name="rgt_resource">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select res_id,res_name ";
  $q_string .= "from resources ";
  $q_string .= "order by res_name ";
  $q_resources = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_resources = mysqli_fetch_array($db, $q_resources)) {
    print "<option value=\"" . $a_resources['res_id'] . "\">" . $a_resources['res_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Type: <label><input type="radio" checked value="0" name="rgt_type"> Admin</label> <label><input type="radio" value="1" name="rgt_type"> Edit</label> <label><input type="radio" value="2", name="rgt_type"> View</label></td>
  <td class="ui-widget-content"><label>get: <input type="checkbox" name="rgt_get"></label></td>
  <td class="ui-widget-content"><label>list: <input type="checkbox" name="rgt_list"></label></td>
  <td class="ui-widget-content"><label>watch: <input type="checkbox" name="rgt_watch"></label></td>
  <td class="ui-widget-content"><label>impersonate: <input type="checkbox" name="rgt_impersonate"></label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>create: <input type="checkbox" name="rgt_create"></label></td>
  <td class="ui-widget-content"><label>delete: <input type="checkbox" name="rgt_delete"></label></td>
  <td class="ui-widget-content"><label>deletecollection: <input type="checkbox" name="rgt_deletecollection"></label></td>
  <td class="ui-widget-content"><label>patch: <input type="checkbox" name="rgt_patch"></label></td>
  <td class="ui-widget-content"><label>update: <input type="checkbox" name="rgt_update"></label></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
