<?php
# Script: hypervisor.php
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

  $package = "hypervisor.php";

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
<title>Hypervisor Editor</title>

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
  var answer = confirm("Delete this Hypervisor?")

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

  af_url += "&hv_name="         + encode_URI(af_form.hv_name.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&hv_name="        + encode_URI(uf_form.hv_name.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('hypervisor.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 150,
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
          show_file('hypervisor.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Hypervisor",
        click: function() {
          attach_file('hypervisor.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 150,
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
          show_file('hypervisor.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Hypervisor",
        click: function() {
          update_file('hypervisor.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Hypervisor",
        click: function() {
          update_file('hypervisor.mysql.php', 0);
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

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Hypervisor Manager</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('hypervisor-help');">Help</a></th>
</tr>
</table>

<div id="hypervisor-help" style="display: none">

<div class="main-help ui-widget-content">

<p>Hypervisor</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Hypervisor"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Hypervisor Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('hyperevisor-listing-help');">Help</a></th>
</tr>
</table>

<div id="hypervisor-listing-help" style="display: none">

<div class="main-help ui-widget-content">

<p><strong>Hypervisor Listing</strong></p>

<p>This page lists details about purchased software licenses.</p>

<p>To add a software license, click the Add License button. This will bring up a dialog box which you can then use to add 
a software license.</p>

<p>To edit a software license, click on an entry in the listing. A dialog box will be displayed where you can edit the 
software license, or if there is a small difference, you can make changes and add a new software license.</p>

</div>

</div>


<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

</div>


<div id="dialogCreate" title="Add Hypervisor">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Hypervisor: <input type="text" name="hv_name" size="65"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Hypervisor">

<form name="formUpdate">
<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Hypervisor: <input type="text" name="hv_name" size="65"></td>
</tr>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
