<?php
# Script: assets.php
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

  $package = "assets.php";

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
<title>Asset Management</title>

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
  var answer = confirm("Delete this Asset?")

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

  af_url += "&ast_name="        + encode_URI(af_form.ast_name.value);
  af_url += "&ast_asset="       + encode_URI(af_form.ast_asset.value);
  af_url += "&ast_serial="      + encode_URI(af_form.ast_serial.value);
  af_url += "&ast_parentid="    + encode_URI(af_form.ast_parentid.value);
  af_url += "&ast_modelid="     + af_form.ast_modelid.value;
  af_url += "&ast_unit="        + af_form.ast_unit.value;
  af_url += "&ast_vendor="      + af_form.ast_vendor.checked;
  af_url += "&ast_managed="     + af_form.ast_managed.checked;
  af_url += "&ast_endsupport="  + encode_URI(af_form.ast_endsupport.value);
  af_url += "&ast_facing="      + af_form.ast_facing.checked;

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

  uf_url += "&ast_name="        + encode_URI(uf_form.ast_name.value);
  uf_url += "&ast_asset="       + encode_URI(uf_form.ast_asset.value);
  uf_url += "&ast_serial="      + encode_URI(uf_form.ast_serial.value);
  uf_url += "&ast_parentid="    + encode_URI(uf_form.ast_parentid.value);
  uf_url += "&ast_modelid="     + uf_form.ast_modelid.value;
  uf_url += "&ast_unit="        + uf_form.ast_unit.value;
  uf_url += "&ast_vendor="      + uf_form.ast_vendor.checked;
  uf_url += "&ast_managed="     + uf_form.ast_managed.checked;
  uf_url += "&ast_endsupport="  + encode_URI(uf_form.ast_endsupport.value);
  uf_url += "&ast_facing="      + uf_form.ast_facing.checked;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
  clear_fields();
}

function clear_fields() {
  show_file('assets.mysql.php?update=-1&sort=<?php print $formVars['sort']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( );

  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 375,
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
          show_file('assets.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Asset",
        click: function() {
          attach_file('assets.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 375,
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
          show_file('assets.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Asset",
        click: function() {
          update_file('assets.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Asset",
        click: function() {
          update_file('assets.mysql.php', 0);
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
  <th class="ui-state-default">Asset Manager</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('asset-help');">Help</a></th>
</tr>
</table>

<div id="asset-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This is one piece of the Asset Management System. You add CPUs here which are then used in the Asset system to configure a server. 
In this situation, you might create a generic CPU vs defining a bunch of specific ones. But you can should you have a system where you 
have a specific purpose and need a specific CPU such as if you have an ARM cluster that hosts VMs, you can define an ARM CPU and then 
create an asset for the CPU and assign it to a device.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Asset"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Asset Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('asset-listing-help');">Help</a></th>
</tr>
</table>

<div id="asset-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Asset Listing</strong></p>

<p>This page lists all the defined CPUs that can be used to create an asset.</p>

<p>To add a CPU, click the <strong>Add CPU</strong> button. This will bring up a dialog box which you can use 
to create a new CPU.</p>

<p>To edit an existing CPU, click on an entry in the listing. A dialog box will be presented where you can edit 
the current entry, or if there is a small difference, you can make changes and add a new CPU.</p>

<p>Note that under the Members colum is a number which indicates the number of times this CPU is in use. You cannot 
delete a CPU as long as this value is greater than zero.</p>

</div>

</div>


<span id="mysql_table"><?php print wait_Process('Waiting...')?></span>

</div>


</div>

</div>


<div id="dialogCreate" title="Add Asset Form">

<form name="formCreate">

<?php include('assets.dialog.php'); ?>

</form>

</div>


<div id="dialogUpdate" title="Update Asset Form">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<?php include('assets.dialog.php'); ?>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
