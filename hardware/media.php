<?php
# Script: media.php
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

  $package = "media.php";

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
<title>Edit Media Descriptions</title>

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
  var answer = confirm("Delete this Media Description?")

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

  af_url += "&med_text="       + encode_URI(af_form.med_text.value);
  af_url += "&med_default="    + af_form.med_default.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&med_text="       + encode_URI(uf_form.med_text.value);
  uf_url += "&med_default="    + uf_form.med_default.checked;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('media.mysql.php?update=-1');
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
          show_file('media.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Media Description",
        click: function() {
          attach_file('media.mysql.php', 0);
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
          show_file('media.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Media Description",
        click: function() {
          update_file('media.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Media Description",
        click: function() {
          update_file('media.mysql.php', 0);
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
  <th class="ui-state-default">Media Description Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('media-help');">Help</a></th>
</tr>
</table>



<div id="media-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>For a physical system, you can define the media type you wish for the interface.</p>

</div>

</div>



<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Media Description"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Media Description Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('media-listing-help');">Help</a></th>
</tr>
</table>



<div id="media-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Network Media Type Description</strong></p>

<p>This page lists all the defined Media Types.</p>

<p>To add a Media Type, click the Add Media Description button. This will bring up a dialog box which 
you can use to add a Media Description.</p>

<p>To edit an existing Media Description, click on an entry in the listing. A dialog box will be presented where you 
can edit the current entry, or if there is a small difference, you can make changes and add a new Media Description.</p>

<p>The Default setting indicates this is the top displayed item in the menu and generally means that this isn't an
interface that's used to define Media Types. The text can be anything descriptive. In the listing below, a Media Type
configured as a Default will be <span class="ui-state-highlight">highlighted</span>.</p>

<p>Note that under the Members colum is a number which indicates the number of times this Media Description is in use. 
You cannot delete a Description as long as this value is greater than zero.</p>

</div>

</div>


<span id="mysql_table"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add Media Description">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Network Media Description: <input type="text" name="med_text" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Default? <input type="checkbox" name="med_default"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Update Media Description">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Network Media Description: <input type="text" name="med_text" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Default? <input type="checkbox" name="med_default"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
