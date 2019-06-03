<?php
# Script: repos.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "repos.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Red Hat Repositories</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_package( p_script_url ) {
  var answer = confirm("Delete this Package?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_package( p_script_url, update ) {
  var ap_form = document.package;
  var ap_url;

  ap_url  = '?update='   + update;
  ap_url += '&id='       + ap_form.id.value;

  ap_url += "&rep_version="       + encode_URI(ap_form.rep_version.value);
  ap_url += "&rep_group="         + encode_URI(ap_form.rep_group.value);
  ap_url += "&rep_name="          + encode_URI(ap_form.rep_name.value);
  ap_url += "&rep_grpdesc="       + encode_URI(ap_form.rep_grpdesc.value);
  ap_url += "&rep_type="          + encode_URI(ap_form.rep_type.value);
  ap_url += "&rep_package="       + encode_URI(ap_form.rep_package.value);
  ap_url += "&rep_pkgdesc="       + encode_URI(ap_form.rep_pkgdesc.value);
  ap_url += "&rep_included="      + ap_form.rep_group.checked;

  script = document.createElement('script');
  script.src = p_script_url + ap_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('repos.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddRepo' ).click(function() {
    $( "#dialogRepo" ).dialog('open');
  });

  $( "#dialogRepo" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogRepo" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('repos.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Package",
        click: function() {
          attach_package('repos.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Package",
        click: function() {
          attach_package('repos.mysql.php', 0);
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
  <th class="ui-state-default">Package/Repo Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('repo-help');">Help</a></th>
</tr>
</table>

<div id="repo-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Package</strong> - Save any changes to this form.</li>
    <li><strong>Add Package</strong> - Add a new Device Type.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddRepo" value="Add Repo"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</div>

</div>

<div id="dialogRepo" title="Repo Form">

<form name="package">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Device Type Information</th>
</tr>
<tr>
  <td class="ui-widget-content">Red Hat Version: <input type="text" name="rep_version" size="5"></td>
  <td class="ui-widget-content">Group ID: <input type="text" name="rep_group" size="20"></td>
  <td class="ui-widget-content">Group Name: <input type="text" name="rep_name" size="20"></td>
  <td class="ui-widget-content">Group Description: <input type="text" name="rep_grpdesc" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Package Type: <input type="text" name="rep_type" size="20"></td>
  <td class="ui-widget-content">Package Name: <input type="text" name="rep_package" size="20"></td>
  <td class="ui-widget-content">Package Description: <input type="text" name="rep_pkgdesc" size="60"></td>
  <td class="ui-widget-content">Include in Kickstart? <input type="checkbox" name="rep_included"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
