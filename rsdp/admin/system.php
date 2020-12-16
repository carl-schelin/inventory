<?php
# Script: system.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "system.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
  } else {
    $formVars['id'] = 0;
  }

# if help has not been seen yet,
  if (show_Help($db, 'operatingsystem')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Operating Systems</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_line( p_script_url ) {
  var answer = confirm("Delete this Operating System?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function undelete_line( p_script_url ) {
  var answer = confirm("Undelete this Operating System?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.dialog;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&os_vendor="     + encode_URI(af_form.os_vendor.value);
  af_url += "&os_software="   + encode_URI(af_form.os_software.value);
  af_url += "&os_exception="  + af_form.os_exception.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('system.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

$(function() {

  $( '#clickAddSystem' ).click(function() {
    $( "#dialogSystem" ).dialog('open');
  });

  $( "#dialogSystem" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1000,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogSystem" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          attach_file('system.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update System",
        click: function() {
          attach_file('system.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add System",
        click: function() {
          attach_file('system.mysql.php', 0);
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

<form name="system">

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Operating System Management</th>
  <th class="ui-state-default" width="5"><a href="javascript:;" onmousedown="toggleDiv('system-help');">Help</a></th>
</tr>
</table>

<div id="system-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This is a listing of all the Operating Systems that are available to be selected in RSDP.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Add Operating System</strong> - Add a new Operating System to the listing.</li>
    <li><strong>Remove</strong> - Mark an Operating System as unavailable. Deleted systems are not available to be selected when provisioning a server and are <span class="ui-state-highlight">highlighted</span>.</li>
    <li><strong>Undelete</strong> - Undelete an Operating System. Makes it available for the listing again.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Operating System Dialog</strong>
  <ul>
    <li><strong>Vendor</strong> - The name of the OS vendor.</li>
    <li><strong>Operating System</strong> - The name of the Operating System.</li>
    <li><strong>Exception</strong> - Mark this Operating System as requiring an Exception to be installed. This notifies the user if selected.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" name="addbtn" id="clickAddSystem" value="Add Operating System"></td>
</tr>
</table>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</form>


<div id="dialogSystem" title="Interface Form">

<form name="dialog">

<input type="hidden" name="id" value="0">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Operating System</th>
</tr>
<tr>
  <td class="ui-widget-content">Vendor: <input type="text" name="os_vendor" value="" size="20"></td>
  <td class="ui-widget-content">Operating System: <input type="text" name="os_software" size="40"></td>
  <td class="ui-widget-content">Exception Required? <input type="checkbox" name="os_exception"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
