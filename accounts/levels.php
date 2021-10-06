<?php
# Script: levels.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Admin);

  $package = "levels.php";

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
<title>Level Editor</title>

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
function delete_level( p_script_url ) {
  var question;
  var answer;

  question  = "Making changes to the level titles can be done \n";
  question += "but deleting a level will seriously cause issues \n";
  question += "with user access to the system.\n\n";

  question += "Are you SURE you want to delete this level?";

  answer = confirm(question);

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

  af_url += "&lvl_name="      + encode_URI(af_form.lvl_name.value);
  af_url += "&lvl_level="     + encode_URI(af_form.lvl_level.value);
  af_url += "&lvl_disabled="  + af_form.lvl_disabled.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.updateDialog;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&lvl_name="      + encode_URI(uf_form.lvl_name.value);
  uf_url += "&lvl_level="     + encode_URI(uf_form.lvl_level.value);
  uf_url += "&lvl_disabled="  + uf_form.lvl_disabled.value;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('levels.mysql.php?update=-1');
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
          show_file('levels.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Level",
        click: function() {
          attach_file('levels.mysql.php', 0);
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
          show_file('levels.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Level",
        click: function() {
          update_file('levels.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Level",
        click: function() {
          update_file('levels.mysql.php', 0);
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
  <th class="ui-state-default">Level Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('level-help');">Help</a></th>
</tr>
</table>

<div id="level-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Currently the Inventory uses a simple Level system to provide access to various aspects of the Inventory. Most 
places, to make changes you need Admin level access and Read-Only and Guest access are effectively the same.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Level"></td>
</tr>
</table>


<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Level Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('level-listing-help');">Help</a></th>
</tr>
</table>

<div id="level-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Levels Listing</strong></p>

<p>This page lists all the Levels currently available in the Inventory.</p>

<p>To add a new Level, click the Add Level button. This will bring up a dialog box which you can then 
use to create a new Level.</p>

<p>To edit an existing Level, click on the entry in the listing. A dialog box will be displayed where 
you can edit the current entry, or if there is a small difference, you can make changes and add a 
new Level.

<p>Note that under the Members column is a number which indicates the number of users that are currently 
defined at the listed Level. You cannot remove a Level until this value is zero. The value in parenthese 
is the number of users at that level that are currently disabled.</p>

</div>

</div>


<span id="table_mysql"></span>

</div>

</div>


<div id="dialogCreate" title="Add Level">

<form name="createDialog">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Level Name: <input type="text" name="lvl_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Access Level: <input type="number" name="lvl_level" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Status <select name="lvl_disabled">
<option value="0">Enabled</option>
<option value="1">Disabled</option>
</select></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Add Level">

<form name="updateDialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Level Name: <input type="text" name="lvl_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Access Level: <input type="number" name="lvl_level" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Status <select name="lvl_disabled">
<option value="0">Enabled</option>
<option value="1">Disabled</option>
</select></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
