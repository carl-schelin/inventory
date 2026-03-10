<?php
# Script: ports.php
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

  $package = "ports.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  $formVars['sort'] = '';
  if (isset($_GET['sort'])) {
    $formVars['sort'] = clean($_GET['sort'], 30);
  } 

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
<title>Manage Ports</title>

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
function delete_line( p_script_url ) {
  var answer = confirm("Delete this Port?")

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

  af_url += "&port_deviceid="     + af_form.port_deviceid.value;
  af_url += "&port_name="         + encode_URI(af_form.port_name.value);
  af_url += "&port_type="         + af_form.port_type.value;
  af_url += "&port_active="       + af_form.port_active.checked;
  af_url += "&port_desc="         + encode_URI(af_form.port_desc.value);
  af_url += "&port_facing="       + af_form.port_facing.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
  clear_fields();
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&port_deviceid="     + uf_form.port_deviceid.value;
  uf_url += "&port_name="         + encode_URI(uf_form.port_name.value);
  uf_url += "&port_type="         + uf_form.port_type.value;
  uf_url += "&port_active="       + uf_form.port_active.checked;
  uf_url += "&port_desc="         + encode_URI(uf_form.port_desc.value);
  uf_url += "&port_facing="       + uf_form.port_facing.checked;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
  clear_fields();
}

function clear_fields() {
  show_file('ports.mysql.php?update=-1&sort=<?php print $formVars['sort']; ?>');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 275,
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
          attach_file('ports.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Port",
        click: function() {
          attach_file('ports.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 275,
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
          update_file('ports.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Port",
        click: function() {
          update_file('ports.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Port",
        click: function() {
          update_file('ports.mysql.php', 0);
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
  <th class="ui-state-default">Power Port Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('port-help');">Help</a></th>
</tr>
</table>

<div id="port-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><u><strong>Power Port Listing</strong></u></p>

<p>The purpose behind the Power Port Listing is to identify the connection between 
devices and ensure the racks are sufficiently powered.</p>

<p>We'll select the device that has one or more ports, identify the correct port, 
and save the information.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Power Port"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Power Port Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('port-listing-help');">Help</a></th>
</tr>
</table>

<div id="port-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><u><strong>Power Port Listing</strong></u></p>

<p>This is a list of the Power Ports for all Assets in the Inventory. to be 
perfectly clear, ports only need to be defined for systems where we want to 
track connections such as computer rooms. Assigning a port to a device that 
isn't normally in a computer room is unnecessary.</p>

<p>In addition, unless the port is actually labeled, as in PSA and PSB, ports 
here are enumerated right to left or bottom to top. For power strips, ps0 
will start from the power cord.</p>

<img src="<?php print $Imgsroot . "/powerports.webp"; ?>">

</div>

</div>


<span id="mysql_table"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add Power Port Form">

<form name="formCreate">

<?php include('ports.dialog.php'); ?>

</form>

</div>


<div id="dialogUpdate" title="Edit Power Port Form">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<?php include('ports.dialog.php'); ?>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
