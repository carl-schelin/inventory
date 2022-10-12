<?php
# Script: duplex.php
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

  $package = "duplex.php";

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
<title>Edit Duplex Descriptions</title>

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
  var answer = confirm("Delete this Duplex Description?")

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

  af_url += "&dup_text="      + encode_URI(af_form.dup_text.value);
  af_url += "&dup_default="   + af_form.dup_default.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&dup_text="      + encode_URI(uf_form.dup_text.value);
  uf_url += "&dup_default="   + uf_form.dup_default.checked;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('duplex.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 175,
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
          show_file('duplex.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Duplex Description",
        click: function() {
          attach_file('duplex.mysql.php', 0);
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
          show_file('duplex.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Duplex Description",
        click: function() {
          update_file('duplex.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Duplex Description",
        click: function() {
          update_file('duplex.mysql.php', 0);
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
  <th class="ui-state-default">Duplex Description Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('duplex-help');">Help</a></th>
</tr>
</table>



<div id="duplex-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>For a physical system, you can define the duplex type you wish for the interface.</p>

</div>

</div>



<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Duplex Description"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Duplex Description Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('duplex-listing-help');">Help</a></th>
</tr>
</table>



<div id="duplex-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Network Duplex Type Description</strong></p>

<p>This page lists all the defined Media Types.</p>

<p>To add a Duplex Type, click the Add Duplex Description button. This will bring up a dialog box which 
you can use to add a Duplex Description.</p>

<p>To edit an existing Duplex Description, click on an entry in the listing. A dialog box will be presented where you 
can edit the current entry, or if there is a small difference, you can make changes and add a new Duplex Description.</p>

<p>The Default setting indicates this is the top displayed item in the menu and generally means that this isn't an
interface that's used to define duplex. The text can be anything descriptive. In the listing below, a Network Duplex
configured as a Default will be <span class="ui-state-highlight">highlighted</span>.</p>

<p>Note that under the Members colum is a number which indicates the number of times this Duplex Description is in use. 
You cannot delete a Description as long as this value is greater than zero.</p>

</div>

</div>


<span id="mysql_table"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add Duplex Description">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Network Duplex Description: <input type="text" name="dup_text" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Default? <input type="checkbox" name="dup_default"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Update Duplex Description">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Network Duplex Description: <input type="text" name="dup_text" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Default? <input type="checkbox" name="dup_default"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
