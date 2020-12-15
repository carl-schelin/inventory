<?php
# Script: business.php
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

  $package = "business.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Business Units</title>

<style type="text/css" title="currentStyle" media="screen">
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
  var answer = confirm("Delete this Business Unit?")

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
  var af_form = document.business;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&bus_unit=" + encode_URI(af_form.bus_unit.value);
  af_url += "&bus_name=" + encode_URI(af_form.bus_name.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('business.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickAddBusiness' ).click(function() {
    $( "#dialogBusiness" ).dialog('open');
  });

  $( "#dialogBusiness" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogBusiness" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('business.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Business",
        click: function() {
          attach_file('business.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Business",
        click: function() {
          attach_file('business.mysql.php', 0);
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
  <th class="ui-state-default">Business Unit Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('business-unit-help');">Help</a></th>
</tr>
</table>

<div id="business-unit-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Business Unit Form</strong>
  <ul>
    <li><strong>Business Unit ID</strong> - The official company id for the Business Unit</li>
    <li><strong>Business Unit Name</strong> - The name of the company Business Unit</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddBusiness" value="Add Business"></td>
</tr>
</table>

</form>

<span id="table_mysql"></span>

</div>


<div id="dialogBusiness" title="Business Form">

<form name="business">

<input type="hidden" name="id" value="0">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Business Unit Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Business Unit ID: <input type="number" name="bus_unit" size="10"></td>
  <td class="ui-widget-content">Business Unit Name: <input type="text" name="bus_name" size="40"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
