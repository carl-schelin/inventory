<?php
# Script: devicetype.php
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

  $package = "devicetype.php";

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
<title>Device Type Editor</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

<?php
  if (check_userlevel($db, $AL_Admin)) {
?>
function delete_device( p_script_url ) {
  var answer = confirm("Delete this Device Type?")

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

  af_url += "&dev_type="             + encode_URI(af_form.dev_type.value);
  af_url += "&dev_description="      + encode_URI(af_form.dev_description.value);
  af_url += "&dev_infrastructure="   + af_form.dev_infrastructure.checked;
  af_url += "&dev_notes="            + encode_URI(af_form.dev_notes.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&dev_type="             + encode_URI(uf_form.dev_type.value);
  uf_url += "&dev_description="      + encode_URI(uf_form.dev_description.value);
  uf_url += "&dev_infrastructure="   + uf_form.dev_infrastructure.checked;
  uf_url += "&dev_notes="            + encode_URI(uf_form.dev_notes.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('devicetype.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 250,
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
          show_file('devicetype.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Device Type",
        click: function() {
          attach_file('devicetype.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 250,
    width: 1100,
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
          show_file('devicetype.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Device Type",
        click: function() {
          update_file('devicetype.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Device Type",
        click: function() {
          update_file('devicetype.mysql.php', 0);
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
  <th class="ui-state-default">Device Type Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('device-help');">Help</a></th>
</tr>
</table>

<div id="device-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


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
  <th class="ui-state-default">Device Type Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('device-listing-help');">Help</a></th>
</tr>
</table>

<div id="device-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</div>



<div id="dialogCreate" title="Add Device Type">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Type: <input type="text" name="dev_type" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="dev_description" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Infrastructure: <input type="checkbox" name="dev_infrastructure"></td>
</tr>
<tr>
  <td class="ui-widget-content">Notes: <input type="text" name="dev_notes" size="60"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Device Type">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Type: <input type="text" name="dev_type" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="dev_description" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Infrastructure: <input type="checkbox" name="dev_infrastructure"></td>
</tr>
<tr>
  <td class="ui-widget-content">Notes: <input type="text" name="dev_notes" size="60"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
