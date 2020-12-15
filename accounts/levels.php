<?php
# Script: levels.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('1');

  $package = "levels.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage User Levels</title>

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

  question  = "Making changes to the level titles can be done but deleting a level will seriously\n";
  question += "cause issues with user access to the system.\n\n";

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

function attach_level( p_script_url, update ) {
  var al_form = document.levels;
  var al_url;

  al_url  = '?update='   + update;
  al_url += '&id='       + al_form.id.value;

  al_url += "&lvl_name="      + encode_URI(al_form.lvl_name.value);
  al_url += "&lvl_level="     + encode_URI(al_form.lvl_level.value);
  al_url += "&lvl_disabled="  + al_form.lvl_disabled.value;

  script = document.createElement('script');
  script.src = p_script_url + al_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('levels.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddLevel' ).click(function() {
    $( "#dialogLevel" ).dialog('open');
  });

  $( "#dialogLevel" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogLevel" ).hide();
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
          attach_level('levels.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Level",
        click: function() {
          attach_level('levels.mysql.php', 0);
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

<form name="mainform">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Level Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('level-help');">Help</a></th>
</tr>
</table>

<div id="level-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Level Form</strong>
  <ul>
    <li><strong>Level Name</strong> - The name assigned to an access level. This is displayed in a level selection drop down list.</li>
    <li><strong>Access Level</strong> - Defines the access level of the level. Various parts of the Inventory restrict access to view or edit pages based on this number. Levels for access permit lower numbered levels access. A level 3 access also permits level 2 and level 1 users but denies access to level 4 users.</li>
    <li><strong>Status</strong> - Disables this access level. Disabled levels will not be shown in the levels selection menus and will not have the access the level permits.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddLevel" value="Add Level"></td>
</tr>
</table>

</form>

<span id="table_mysql"></span>

</div>

</div>


<div id="dialogLevel" title="Level Form">

<form name="levels">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Level Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Level Name: <input type="text" name="lvl_name" size="40"></td>
  <td class="ui-widget-content">Access Level: <input type="number" name="lvl_level" size="10"></td>
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
