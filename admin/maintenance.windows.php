<?php
# Script: maintenance.windows.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath  . '/function.php');
  check_login('2');

  $package = "maintenance.windows.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Maintenance Windows</title>

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
  var answer = confirm("Delete this Maintenance Window?")

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
  var af_form = document.window;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&win_text=" + encode_URI(af_form.win_text.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('maintenance.windows.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddWindow' ).click(function() {
    $( "#dialogWindow" ).dialog('open');
  });

  $( "#dialogWindow" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogWindow" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('maintenance.windows.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Maintenance Window",
        click: function() {
          attach_file('maintenance.windows.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Maintenance Window",
        click: function() {
          attach_file('maintenance.windows.mysql.php', 0);
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
  <th class="ui-state-default">Maintenance Window Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('window-help');">Help</a></th>
</tr>
</table>

<div id="window-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Maintenance Window</strong> - Save any changes to this form.</li>
    <li><strong>Add Maintenance Window</strong> - Add a new Maintenance Window to the database. You can edit an existing Maintenance Window and click this button to copy a Maintenance Window.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddWindow" value="Add Maintenance Window"></td>
</tr>
</table>

</form>

<span id="window_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

<div id="dialogWindow" title="Maintenance Window Form">

<form name="window">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Maintenance Window Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Maintenance Window: <input type="text" name="win_text" size="100"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
