<?php
# Script: description.php
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

  $package = "description.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing the Interface Type table");

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
<title>Description Editor</title>

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
  var answer = confirm("Delete this Description?")

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

  af_url += "&itp_name="          + encode_URI(af_form.itp_name.value);
  af_url += "&itp_acronym="       + encode_URI(af_form.itp_acronym.value);
  af_url += "&itp_description="   + encode_URI(af_form.itp_description.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&itp_name="          + encode_URI(uf_form.itp_name.value);
  uf_url += "&itp_acronym="       + encode_URI(uf_form.itp_acronym.value);
  uf_url += "&itp_description="   + encode_URI(uf_form.itp_description.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('description.mysql.php?update=-1');
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
          show_file('description.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Description",
        click: function() {
          attach_file('description.mysql.php', 0);
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
          show_file('description.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Description",
        click: function() {
          update_file('description.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Description",
        click: function() {
          update_file('description.mysql.php', 0);
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
  <th class="ui-state-default">Description Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('description-help');">Help</a></th>
</tr>
</table>

<div id="description-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>The purpose of this page is to provide a brief description of what an interface might be used for. This 
is generally what's used to display IPs in the reports. This description is different than the IP Address 
description as that might be assigned by the Networking folks. This provides some additional detail 
and information for interfaces.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Description"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Description Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('description-listing-help');">Help</a></th>
</tr>
</table>

<div id="description-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Interface Description Listing</strong></p>

<p>This page lists the Interface Descriptions which are used to describe server interfaces.</p>

<p>To add a new Description, click on the <strong>Add Description</strong> button. This will bring up a dialog
box which you can use to add a new Interface Description.</p>

<p>To edit an existing Description, click on the entry in the listing. A dialog box will be displayed where you
can edit the current entry, or if there is a small difference, you can make changes and add a new Interface Description</p>

</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add Description">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Name <input type="text" name="itp_name" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Acronym <input type="text" name="itp_acronym" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description <input type="text" name="itp_description" size="50"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Description">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Name <input type="text" name="itp_name" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Acronym <input type="text" name="itp_acronym" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description <input type="text" name="itp_description" size="50"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
