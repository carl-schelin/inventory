<?php
# Script: message_group.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "message_group.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['sort'])) {
    $_SESSION['sort'] = clean($_GET['sort'], 20);
  } else {
    unset($_SESSION['sort']);
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Message Groups</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_line( p_script_url ) {
  var answer = confirm("Mark this Group as Deleted?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    clear_fields();
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.groups;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&msg_group="       + encode_URI(af_form.msg_group.value);
  af_url += "&msg_deleted="    + af_form.msg_deleted.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('message_group.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddGroup' ).click(function() {
    $( "#dialogGroup" ).dialog('open');
  });

  $( "#dialogGroup" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogGroup" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('message_group.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Message Group",
        click: function() {
          attach_file('message_group.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Message Group",
        click: function() {
          attach_file('message_group.mysql.php', 0);
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

<form name="dialog">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Message Group Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('group-help');">Help</a></th>
</tr>
</table>

<div id="group-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Message Group</strong> - Save any changes to this form.</li>
    <li><strong>Add Message Group</strong> - Add a new Message Group.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddGroup" value="Add Message Group"></td>
</tr>
</table>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</form>


<div id="dialogGroup" title="Message Group Form">

<form name="groups">

<input type="hidden" name="id" value="0">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="8""Message Group Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="msg_group" size="80"></td>
</tr>
<tr>
  <td class="ui-widget-content">Delete? <input type="checkbox" name="msg_deleted"></td>
</tr>
</table>

</form>

</div>


</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
