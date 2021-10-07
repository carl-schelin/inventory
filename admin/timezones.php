<?php
# Script: timezones.del.php
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

  $package = "timezones.php";

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
<title>Manage Time Zones</title>

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
  var answer = confirm("Delete this Time Zone?")

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

  af_url += "&zone_name="          + encode_URI(af_form.zone_name.value);
  af_url += "&zone_description="   + encode_URI(af_form.zone_description.value);
  af_url += "&zone_offset="        + encode_URI(af_form.zone_offset.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.updateDialog;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&zone_name="          + encode_URI(uf_form.zone_name.value);
  uf_url += "&zone_description="   + encode_URI(uf_form.zone_description.value);
  uf_url += "&zone_offset="        + encode_URI(uf_form.zone_offset.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('timezones.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
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
          show_file('timezones.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Time Zone",
        click: function() {
          attach_file('timezones.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
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
          show_file('timezones.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Time Zone",
        click: function() {
          update_file('timezones.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Time Zone",
        click: function() {
          update_file('timezones.mysql.php', 0);
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
  <th class="ui-state-default">Time Zone Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('zones-help');">Help</a></th>
</tr>
</table>

<div id="zones-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Time Zone"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Time Zone Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('zones-listing-help');">Help</a></th>
</tr>
</table>

<div id="zones-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add Time Zone">

<form name="createDialog">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Time Zone: <input type="text" name="zone_name" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="zone_description" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Time Offset: <input type="text" name="zone_offset" size="5"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Time Zone">

<form name="updateDialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Time Zone: <input type="text" name="zone_name" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="zone_description" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Time Offset: <input type="text" name="zone_offset" size="5"></td>
</tr>
</table>

</form>

</div>



<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
