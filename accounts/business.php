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
<title>Business Units Editor</title>

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
  var af_form = document.createDialog;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&bus_unit=" + encode_URI(af_form.bus_unit.value);
  af_url += "&bus_name=" + encode_URI(af_form.bus_name.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.updateDialog;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&bus_unit=" + encode_URI(uf_form.bus_unit.value);
  uf_url += "&bus_name=" + encode_URI(uf_form.bus_name.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('business.mysql.php?update=-1');
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
          show_file('business.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Business Unit",
        click: function() {
          attach_file('business.mysql.php', 0);
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
          show_file('business.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Business Unit",
        click: function() {
          update_file('business.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Business Unit",
        click: function() {
          update_file('business.mysql.php', 0);
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

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Business Unit Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('business-unit-help');">Help</a></th>
</tr>
</table>

<div id="business-unit-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>A <strong>business unit</strong> is an entity in an organization that has a specialized function. It develops its own 
strategy within the organization that aligns with company objectives.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Business Unit"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Business Unit Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('business-unit-listing-help');">Help</a></th>
</tr>
</table>

<div id="business-unit-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


<p><strong>Business Unit Listing</strong></p>

<p>This page lists all the currently defined <strong>Business Units</strong> within this organization.</p>

<p>To add a new Business Unit, click the <strong>Add Business Unit</strong> button. This will bring up a dialog box which you can then 
use to create a new business unit.</p>

<p>To editing an existing business unit, click on an entry in the listing. A dialog box will be displayed where you can edit the current 
business unit, or if there's a small difference, you can make changes and add a new business unit.</p>

</div>

</div>


<span id="table_mysql"></span>

</div>


<div id="dialogCreate" title="Add Business Unit">

<form name="createDialog">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Business Unit Name: <input type="text" name="bus_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Business Unit ID: <input type="number" name="bus_unit" size="10"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Business Unit">

<form name="updateDialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Business Unit Name: <input type="text" name="bus_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Business Unit ID: <input type="number" name="bus_unit" size="10"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
