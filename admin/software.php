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
<title>Support Contract Editor</title>

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
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&sw_software="   + encode_URI(af_form.sw_software.value);
  af_url += "&sw_eol="        + encode_URI(af_form.sw_eol.value);
  af_url += "&sw_eos="        + encode_URI(af_form.sw_eos.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&sw_software="   + encode_URI(uf_form.sw_software.value);
  uf_url += "&sw_eol="        + encode_URI(uf_form.sw_eol.value);
  uf_url += "&sw_eos="        + encode_URI(uf_form.sw_eos.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('software.mysql.php?update=-1');
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
          show_file('software.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Support Contract",
        click: function() {
          attach_file('software.mysql.php', 0);
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
          show_file('software.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Support Contract",
        click: function() {
          update_file('software.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Support Contract",
        click: function() {
          update_file('software.mysql.php', 0);
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
  <th class="ui-state-default">Support Contract Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('software-help');">Help</a></th>
</tr>
</table>

<div id="software-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>The Software Support Contract page provides details on who to contact, what the support level is, and when software 
is determined to be End of Life and End of Support per the software company.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Support Contract"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Support Contract Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('support-listing-help');">Help</a></th>
</tr>
</table>

<div id="support-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


<p><strong>Support Contract Listing</strong></p>

<p>This page lists all the Support Contracts for Software used at this location.</p>

<p>To edit a Support Contract, click on the entry in the listing. A dialog box will be 
displayed where you can edit the current entry, or if there's some change you wish to 
make, you can add a new Support Contract.</p>

<p>To add a new Support Contract, click the Add Support Contract button. A dialog box 
will be displayed where you can add the necessary information and then save the new 
Support Contract.</p>


</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</div>



<div id="dialogCreate" title="Add Support Contract">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Software: <input type="text" name="sw_software" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Support: <input type="date" name="sw_eos" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Life: <input type="date" name="sw_eol" size="20"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Support Contract">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Software: <input type="text" name="sw_software" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Support: <input type="date" name="sw_eos" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Life: <input type="date" name="sw_eol" size="20"></td>
</tr>
</table>

</form>

</div>



<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
