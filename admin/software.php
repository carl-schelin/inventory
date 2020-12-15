<?php
# Script: software.php
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

  $package = "software.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Software Lifecycle</title>

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
  var answer = confirm("Delete this Software?")

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
  var af_form = document.software;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&sw_software="   + encode_URI(af_form.sw_software.value);
  af_url += "&sw_eol="        + encode_URI(af_form.sw_eol.value);
  af_url += "&sw_eos="        + encode_URI(af_form.sw_eos.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('software.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddSoftware' ).click(function() {
    $( "#dialogSoftware" ).dialog('open');
  });

  $( "#dialogSoftware" ).dialog({
    autoOpen: false,
    modal: true,
    height: 250,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogSoftware" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('software.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Software",
        click: function() {
          attach_file('software.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Software",
        click: function() {
          attach_file('software.mysql.php', 0);
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
  <th class="ui-state-default">Software Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('software-help');">Help</a></th>
</tr>
</table>

<div id="software-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Support Contract Record</strong> - Save any changes to this form.</li>
    <li><strong>Add Support Contract</strong> - Add new Support Contract details.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddSoftware" value="Add Software"></td>
</tr>
</table>

</form>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</div>

<div id="dialogSoftware" title="Software Form">

<form name="software">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Software Form</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Software: <input type="text" name="sw_software" size="80"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Support: <input type="date" name="sw_eos" size="20"></td>
  <td class="ui-widget-content">End of Life: <input type="date" name="sw_eol" size="20"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
