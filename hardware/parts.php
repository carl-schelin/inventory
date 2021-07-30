<?php
# Script: parts.php
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

  $package = "parts.php";

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
  if (check_userlevel($db, $AL_Admin)) {
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
  var af_form = document.createDialog;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&part_name="     + encode_URI(af_form.part_name.value);
  af_url += "&part_type="     + af_form.part_type.checked;
  af_url += "&part_acronym="  + encode_URI(af_form.part_acronym.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.updateDialog;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&part_name="     + encode_URI(uf_form.part_name.value);
  uf_url += "&part_type="     + uf_form.part_type.checked;
  uf_url += "&part_acronym="  + encode_URI(uf_form.part_acronym.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('parts.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width:  600,
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
          attach_file('parts.mysql.php', -1);
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

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width:  600,
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
          update_file('parts.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Part",
        click: function() {
          update_file('parts.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Part",
        click: function() {
          update_file('parts.mysql.php', 0);
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
  <th class="ui-state-default">Parts Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('part-help');">Help</a></th>
</tr>
</table>

<div id="part-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This page lists all the parts that are available to the Asset management system. This is one piece of the Asset Management System. You add CPUs here which are then used in the Asset system to configure a server.
In this situation, you might create a generic CPU vs defining a bunch of specific ones. But you can should you have a system where you
have a specific purpose and need a specific CPU such as if you have an ARM cluster that hosts VMs, you can define an ARM CPU and then
create an asset for the CPU and assign it to a device.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Part"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Parts Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('part-listing-help');">Help</a></th>
</tr>
</table>

<div id="part-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Part Listing</strong>
  <ul>
    <li><strong>Editing</strong> - Click on a part to edit it.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Click the <strong>Part Management</strong> title bar to toggle the <strong>Part Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>


<span id="table_mysql"></span>

</div>


<div id="dialogCreate" title="Add Part Form">

<form name="createDialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
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


<div id="dialogUpdate" title="Edit Part Form">

<form name="updateDialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
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
