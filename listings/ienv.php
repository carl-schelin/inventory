<?php
# Script: ienv.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login($AL_Edit);

  $package = "ienv.php";

  logaccess($_SESSION['uid'], $package, "Viewing the ienv server listing table");

# If group or an admin, allow access
  if (check_grouplevel($GRP_IENV)) {

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage IENV Listing</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_line( p_script_url ) {
  var answer = confirm("Delete this Line?")

  if (answer) {
    document.getElementById('manual_mysql').innerHTML = '<?php print wait_Process('Waiting...')?>';

    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.changelog;
  var af_url;

  document.getElementById('manual_mysql').innerHTML = '<?php print wait_Process('Waiting...')?>';

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;
  af_url += '&cl_name='  + encode_URI(af_form.cl_name.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('ienv.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

$(function() {

  $( '#clickAddListing' ).click(function() {
    $( "#dialogListing" ).dialog('open');
  });

  $( "#dialogListing" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width:  400,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogListing" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Listing",
        click: function() {
          attach_file('ienv.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Listing",
        click: function() {
          attach_file('ienv.mysql.php', 0);
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

<form name="listing">

<div id="tabs">

<ul>
  <li><a href="#automatic">Automatic Listing</a></li>
  <li><a href="#manual">Manual Listing</a></li>
</ul>


<div id="automatic">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Automatic Listing Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('automatic-help');">Help</a></th>
</tr>
</table>

<div id="automatic-help" style="display: none">

<div class="main-help ui-widget-content">

<p>This listing consists of the systems managed by the IENV Team. The listing is automatically generated. To add a server to 
this listing, either take ownership of a system or add software that you own. This file is used for tools like changelog, 
inventory lookup, and numerous utility scripts used throughout the infrastructure.</p>

<p>The servers file is generated each night at 4pm Mountain. This page shows you the listing in case a review is needed 
plus with the <strong>Refresh Listing</strong> button, when you add a new server, you can rebuild the servers file at the 
touch of a button.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Refresh Listing</strong> - This rebuilds the IENV server listing file.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="hidden" name="id" value="0">
<input type="button"                 name="refresh" value="Refresh Listing" onClick="javascript:attach_file('ienv.mysql.php', 2);">
</td>
</tr>
</table>

<span id="automatic_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="manual">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Manual Listing Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('manual-help');">Help</a></th>
</tr>
</table>

<div id="manual-help" style="display: none">

<div class="main-help ui-widget-content">

<p>This listing contains manually entered applications that you need to enter changelogs for.</p>

<p>The servers file is automatically generated each night at 4pm Mountain. You can add, edit, or delete entries using this page.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Listing</strong> - Save any changes to this form.</li>
    <li><strong>Add Listing</strong> - Add a new item to the list.</li>
    <li><strong>Refresh Listing</strong> - This rebuilds the server listing file.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" name="refresh"                      value="Refresh Listing" onClick="javascript:attach_file('ienv.mysql.php', 2);">
<input type="button" name="addbtn"  id="clickAddListing" value="Add Listing"></td>
</tr>
</table>

<span id="manual_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


</div>

</form>

</div>


<div id="dialogListing" title="Manual List Form">

<form name="changelog">

<input type="hidden" name="id" value="0">
<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Name <input type="text" name="cl_name" size="30"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
<?php
  }
?>
