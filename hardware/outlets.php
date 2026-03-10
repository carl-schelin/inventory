<?php
# Script: outlets.php
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

  $package = "outlets.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  $formVars['sort'] = '';
  if (isset($_GET['sort'])) {
    $formVars['sort'] = clean($_GET['sort'], 30);
  } 
  $formVars['view'] = '';
  if (isset($_GET['view'])) {
    $formVars['view'] = clean($_GET['view'], 30);
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
<title>Manage Power Outlets</title>

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
  var answer = confirm("Delete this Power Outlet?")

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

  af_url += "&out_deviceid="     + af_form.out_deviceid.value;
  af_url += "&out_name="         + encode_URI(af_form.out_name.value);
  af_url += "&out_type="         + af_form.out_type.value;
  af_url += "&out_active="       + af_form.out_active.checked;
  af_url += "&out_desc="         + encode_URI(af_form.out_desc.value);
  af_url += "&out_facing="       + af_form.out_facing.checked;
  af_url += "&out_verified="     + af_form.out_verified.checked;

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

  uf_url += "&out_deviceid="     + uf_form.out_deviceid.value;
  uf_url += "&out_name="         + encode_URI(uf_form.out_name.value);
  uf_url += "&out_type="         + uf_form.out_type.value;
  uf_url += "&out_active="       + uf_form.out_active.checked;
  uf_url += "&out_desc="         + encode_URI(uf_form.out_desc.value);
  uf_url += "&out_facing="       + uf_form.out_facing.checked;
  uf_url += "&out_verified="     + uf_form.out_verified.checked;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
  clear_fields();
}

function clear_fields() {
  show_file('outlets.mysql.php?update=-1&sort=<?php print $formVars['sort']; ?>&view=<?php print $formVars['view']; ?>');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
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
          attach_file('outlets.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Outlet",
        click: function() {
          attach_file('outlets.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
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
          update_file('outlets.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Outlet",
        click: function() {
          update_file('outlets.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Outlet",
        click: function() {
          update_file('outlets.mysql.php', 0);
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
  <th class="ui-state-default">Power Outlet Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('outlet-help');">Help</a></th>
</tr>
</table>

<div id="outlet-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><u><strong>Power Outlet Listing</strong></u></p>

<p>The purpose behind the Power Outlet Listing is to identify the connection between 
devices and ensure the racks are sufficiently powered.</p>

<p>We'll select the device that has one or more outlets, identify the correct outlets, 
and save the information.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Power Outlet"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Power Outlet Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('outlet-listing-help');">Help</a></th>
</tr>
</table>

<div id="outlet-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><u><strong>Power Outlet Listing</strong></u></p>

<p>This is a list of the Power Outlets for all Physical Assets in the Inventory. to be 
perfectly clear, outlets only need to be defined for PDUs and UPSs where we want to 
track connections such as computer rooms. Assigning a outlet to a unit that 
isn't normally in a computer room is unnecessary.</p>

<p>Note that counting outlets starts from the unit Power Port which could be on 
the left or right side of the unit.</p>

<img src="<?php print $Imgsroot . "/powerports.webp"; ?>">

</div>

</div>


<span id="mysql_table"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add Power Outlet Form">

<form name="formCreate">

<?php include('outlets.dialog.php'); ?>

</form>

</div>


<div id="dialogUpdate" title="Edit Power Outlet Form">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<?php include('outlets.dialog.php'); ?>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
