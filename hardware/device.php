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
<title>Device Editor</title>

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
  var answer = confirm("Delete this Device?")

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
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&mod_vendor="     + af_form.mod_vendor.value;
  af_url += "&mod_name="       + encode_URI(af_form.mod_name.value);
  af_url += "&mod_type="       + af_form.mod_type.value;
  af_url += "&mod_virtual="    + af_form.mod_virtual.value;
  af_url += "&mod_eopur="      + encode_URI(af_form.mod_eopur.value);
  af_url += "&mod_eoship="     + encode_URI(af_form.mod_eoship.value);
  af_url += "&mod_eol="        + encode_URI(af_form.mod_eol.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&mod_vendor="     + uf_form.mod_vendor.value;
  uf_url += "&mod_name="       + encode_URI(uf_form.mod_name.value);
  uf_url += "&mod_type="       + uf_form.mod_type.value;
  uf_url += "&mod_virtual="    + uf_form.mod_virtual.value;
  uf_url += "&mod_eopur="      + encode_URI(uf_form.mod_eopur.value);
  uf_url += "&mod_eoship="     + encode_URI(uf_form.mod_eoship.value);
  uf_url += "&mod_eol="        + encode_URI(uf_form.mod_eol.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('device.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 275,
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
          show_file('device.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Device",
        click: function() {
          attach_file('device.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 275,
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
          show_file('device.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Device",
        click: function() {
          update_file('device.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Device",
        click: function() {
          update_file('device.mysql.php', 0);
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
  <th class="ui-state-default">Device Editor</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('detail-help');">Help</a></th>
</tr>
</table>

<div id="detail-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This is one piece of the Asset Management System. Devices are generally considered top level systems. In other words, 
a device isn't a component of a device. It is the device. This should also include virtual machines and any server type 
devices that fit into a blade chassis. Under the Asset management system, you'll be able to associate any piece of 
hardware, virtual or physical, with another piece of hardware (such as a virtual machine with an ESX host).</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Device"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Device Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('device-listing-help');">Help</a></th>
</tr>
</table>

<div id="device-listing-help" style="<?php print $display; ?>">\n";

<div class="main-help ui-widget-content">

<p><strong>Device Listing</strong></p>

<p>This page lists all the defined devices that can be used to create an asset.</p>

<p>To add a device, click the <strong>Add Device</strong> button. This will bring up a dialog box which you can use 
to add a new device.</p>

<p>To edit an existing device, click on an entry in the listing. A dialog box will be presented where you can edit 
the current entry, or if there is a small difference, you can make changes and add a new device.</p>

<p>Note that under the Members colum is a number which indicates the number of times this device is in use. You cannot 
delete a device as long as this value is greater than zero.</p>

</div>

</div>


<span id="mysql_table"><?php print wait_Process('Waiting...')?></span>

</div>


</div>

</div>


<div id="dialogCreate" title="Add Device Form">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Vendor: <select name="mod_vendor">
<?php
  $q_string  = "select ven_id,ven_name ";
  $q_string .= "from vendors ";
  $q_string .= "order by ven_name";
  $q_vendors = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_vendors = mysqli_fetch_array($q_vendors)) {
    print "<option value=\"" . $a_vendors['ven_id'] . "\">" . $a_vendors['ven_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Model: <input type="text" name="mod_name" size="50"> <label>Virtual Machine? <input type="checkbox" name="mod_virtual"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Device Type <select name="mod_type">
<?php
  $q_string  = "select part_id,part_name ";
  $q_string .= "from parts ";
  $q_string .= "order by part_name";
  $q_parts = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_parts = mysqli_fetch_array($q_parts)) {
    print "<option value=\"" . $a_parts['part_id'] . "\">" . $a_parts['part_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Purchase: <input type="date" name="mod_eopur" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Shipping: <input type="date" name="mod_eoship" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Life: <input type="date" name="mod_eol" size="12"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Update Device Form">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Vendor: <select name="mod_vendor">
<?php
  $q_string  = "select ven_id,ven_name ";
  $q_string .= "from vendors ";
  $q_string .= "order by ven_name";
  $q_vendors = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_vendors = mysqli_fetch_array($q_vendors)) {
    print "<option value=\"" . $a_vendors['ven_id'] . "\">" . $a_vendors['ven_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Model: <input type="text" name="mod_name" size="50"> <label>Virtual Machine? <input type="checkbox" name="mod_virtual"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Device Type <select name="mod_type">
<?php
  $q_string  = "select part_id,part_name ";
  $q_string .= "from parts ";
  $q_string .= "order by part_name";
  $q_parts = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_parts = mysqli_fetch_array($q_parts)) {
    print "<option value=\"" . $a_parts['part_id'] . "\">" . $a_parts['part_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Purchase: <input type="date" name="mod_eopur" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Shipping: <input type="date" name="mod_eoship" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Life: <input type="date" name="mod_eol" size="12"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
