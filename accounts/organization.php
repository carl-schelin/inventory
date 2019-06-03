<?php
# Script: organization.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "organization.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Organizations</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">
<?php
  if (check_userlevel(1)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Delete this Organization?")

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
  var af_form = document.organization;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&org_name=" + encode_URI(af_form.org_name.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('organization.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickAddOrganization' ).click(function() {
    $( "#dialogOrganization" ).dialog('open');
  });

  $( "#dialogOrganization" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogOrganization" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('organization.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Organization",
        click: function() {
          attach_file('organization.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Organization",
        click: function() {
          attach_file('organization.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="mainform">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Organization Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('organization-help');">Help</a></th>
</tr>
</table>

<div id="organization-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Organization Form</strong>
  <ul>
    <li><strong>Organization Name</strong> - The name of the company Organization</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddOrganization" value="Add Organization"></td>
</tr>
</table>

</form>

<span id="table_mysql"></span>

</div>

</div>

<div id="dialogOrganization" title="Organization Form">

<form name="organization">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Organization Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Organization Name: <input type="text" name="org_name" size="60"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
