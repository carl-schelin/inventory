<?php
# Script: zones.php
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

  $package = "zones.php";

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
<title>Network Zone Editor</title>

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
  var answer = confirm("Delete this Network Zone?")

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
  var af_form = document.zoneDialog;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&zone_zone="              + encode_URI(af_form.zone_zone.value);
  af_url += "&zone_acronym="           + encode_URI(af_form.zone_acronym.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.updateDialog;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&zone_zone="              + encode_URI(uf_form.zone_zone.value);
  uf_url += "&zone_acronym="           + encode_URI(uf_form.zone_acronym.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('zones.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickAddZone' ).click(function() {
    $( "#dialogZone" ).dialog('open');
  });

  $( "#dialogZone" ).dialog({
    autoOpen: false,
    modal: true,
    height: 175,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogZone" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('zones.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Network Zone",
        click: function() {
          attach_file('zones.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 175,
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
          show_file('zones.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Network Zone",
        click: function() {
          update_file('zones.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Network Zone",
        click: function() {
          update_file('zones.mysql.php', 0);
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

<div class="main">

<form name="zones">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Network Zone Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('zone-help');">Help</a></th>
</tr>
</table>

<div id="zone-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>A Network Zone are the layers of your network. Generally you think of this as an internal network such as the Corporate network or the one facing the public such as a DMZ. Other zones might be added as well depending upon your network design.</p>

<p>Click the Help link at the upper right to open and close any Help window.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddZone" value="Add Network Zone"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogZone" title="Network Zone Form">

<form name="zoneDialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Network Zone <input type="text" name="zone_zone" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Zone Acronym <input type="text" name="zone_acronym" size="5"></td>
</tr>
</table>

</form>

</div>

<div id="dialogUpdate" title="Network Zone Form">

<form name="updateDialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Network Zone <input type="text" name="zone_zone" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Zone Acronym <input type="text" name="zone_acronym" size="5"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
