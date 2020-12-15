<?php
# Script: device.php
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

  $package = "device.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Device Types</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_device( p_script_url ) {
  var answer = confirm("Delete this Device Type?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_device( p_script_url, update ) {
  var ad_form = document.device;
  var ad_url;

  ad_url  = '?update='   + update;
  ad_url += '&id='       + ad_form.id.value;

  ad_url += "&dev_type="             + encode_URI(ad_form.dev_type.value);
  ad_url += "&dev_description="      + encode_URI(ad_form.dev_description.value);
  ad_url += "&dev_infrastructure="   + ad_form.dev_infrastructure.checked;
  ad_url += "&dev_notes="            + encode_URI(ad_form.dev_notes.value);

  script = document.createElement('script');
  script.src = p_script_url + ad_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('device.mysql.php?update=-1');
}

<?php
  if ($_SESSION['uid'] == 2) {
?>
$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddDevice' ).click(function() {
    $( "#dialogDevice" ).dialog('open');
  });

  $( "#dialogDevice" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogDevice" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('device.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Device",
        click: function() {
          attach_device('device.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Device",
        click: function() {
          attach_device('device.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

<?php
  }
?>
</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="mainform">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Device Type Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('device-help');">Help</a></th>
</tr>
</table>

<div id="device-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Device</strong> - Save any changes to this form.</li>
    <li><strong>Add Device</strong> - Add a new Device Type.</li>
  </ul></li>
</ul>

</div>

</div>

<?php
  if ($_SESSION['uid'] == 2) {
?>
<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddDevice" value="Add Device"></td>
</tr>
</table>
<?php
  }
?>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</div>

</div>

<?php
  if ($_SESSION['uid'] == 2) {
?>
<div id="dialogDevice" title="Device Form">

<form name="device">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Device Type Information</th>
</tr>
<tr>
  <td class="ui-widget-content">Type: <input type="text" name="dev_type" size="10"></td>
  <td class="ui-widget-content">Description: <input type="text" name="dev_description" size="40"></td>
  <td class="ui-widget-content">Infrastructure: <input type="checkbox" name="dev_infrastructure"></td>
  <td class="ui-widget-content">Notes: <input type="text" name="dev_notes" size="60"></td>
</tr>
</table>

</form>

</div>
<?php
  }
?>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
