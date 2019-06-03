<?php
# Script: parts.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "parts.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Parts</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

<?php
  if (check_userlevel(1)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Deleting a part could orphan hardware parts in the \ninventory. Deleting a part should only be done when \nyou're sure there are no associated equipment.\n\nDelete this Part?")

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
  var af_form = document.parts;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&part_name="     + encode_URI(af_form.part_name.value);
  af_url += "&part_type="     + af_form.part_type.checked;
  af_url += "&part_acronym="  + encode_URI(af_form.part_acronym.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('parts.mysql.php?update=-1');
}

$(document).ready( function() {
});

$(function() {

  $( '#clickAddPart' ).click(function() {
    $( "#dialogPart" ).dialog('open');
  });

  $( "#dialogPart" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
    width:  500,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogPart" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          attach_file('parts.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Part",
        click: function() {
          attach_file('parts.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Part",
        click: function() {
          attach_file('parts.mysql.php', 0);
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

<form name="display">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Part Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('part-help');">Help</a></th>
</tr>
</table>

<div id="part-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Part</strong> - Save any changes to this form.</li>
    <li><strong>Add Part</strong> - If you need to add a new platform, change the Platform Name field then click this button to add the device. This button is only active if there are no devices using the same Server Name.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Part Form</strong>
  <ul>
    <li><strong>Name</strong> - The name of this part type.</li>
    <li><strong>Device Name Acronym</strong> - This provides a part of the device naming convention.</li>
    <li><strong>Primary Device</strong> - Is this a container. For example a server would be considered a Primary Device as would a Storage Array or a Blade Chassis. Primary Devices are required in order to display a server on the main inventory page.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddPart" value="Add Part"></td>
</tr>
</table>

<span id="table_mysql"></span>

</form>

</div>


<div id="dialogPart" title="Part Form">

<form name="parts">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Part Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="part_name" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Device Name Acronym: <input type="text" name="part_acronym" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Primary Device: <input type="checkbox" name="part_type"></label></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
