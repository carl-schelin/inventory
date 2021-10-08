<?php
# Script: other.php
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

  $package = "other.php";

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
<title>Other Hardware Editor</title>

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
  var answer = confirm("Delete this piece of hardware?")

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

  af_url += "&mod_vendor="     + af_form.mod_vendor.value;
  af_url += "&mod_name="       + encode_URI(af_form.mod_name.value);
  af_url += "&mod_type="       + af_form.mod_type.value;
  af_url += "&mod_eopur="      + encode_URI(af_form.mod_eopur.value);
  af_url += "&mod_eoship="     + encode_URI(af_form.mod_eoship.value);
  af_url += "&mod_eol="        + encode_URI(af_form.mod_eol.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&mod_vendor="     + uf_form.mod_vendor.value;
  uf_url += "&mod_name="       + encode_URI(uf_form.mod_name.value);
  uf_url += "&mod_type="       + uf_form.mod_type.value;
  uf_url += "&mod_eopur="      + encode_URI(uf_form.mod_eopur.value);
  uf_url += "&mod_eoship="     + encode_URI(uf_form.mod_eoship.value);
  uf_url += "&mod_eol="        + encode_URI(uf_form.mod_eol.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('other.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 275,
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
          show_file('other.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Hardware",
        click: function() {
          attach_file('other.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 275,
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
          show_file('other.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Hardware",
        click: function() {
          update_file('other.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Hardware",
        click: function() {
          update_file('other.mysql.php', 0);
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
  <th class="ui-state-default">Other Hardware Editor</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('detail-help');">Help</a></th>
</tr>
</table>

<div id="detail-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This is one piece of the Asset Management System. You add non-standard hardware here which are then used in the Asset 
system to configure a server. In general, the Inventory is used to create servers so additional hardware is probably 
unnecessary however you never know so this page is here.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Hardware"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Other Hardware Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('other-listing-help');">Help</a></th>
</tr>
</table>

<div id="other-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Other Hardware Listing</strong></p>

<p>This page lists any hardware that doesn't fit the standard ones that are to be used to define an asset.</p>

<p>To add a piece of hardware, click the <strong>Add Hardware</strong> button. This will bring up a dialog box which you can use 
to enter a new piece of hardware.</p>

<p>To edit an existing item, click on an entry in the listing. A dialog box will be presented where you can edit 
the current entry, or if there is a small difference, you can make changes and add a new piece of hardware.</p>

<p>Note that under the Members colum is a number which indicates the number of times this piece of hardware is in use. You cannot 
delete an item as long as this value is greater than zero.</p>

</div>

</div>


<span id="mysql_table"><?php print wait_Process('Waiting...')?></span>

</div>


</div>

</div>


<div id="dialogCreate" title="Add Other Hardware Form">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Vendor: <select name="mod_vendor">
<?php
  $q_string  = "select ven_id,ven_name ";
  $q_string .= "from vendors ";
  $q_string .= "order by ven_name";
  $q_vendors = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_vendors = mysqli_fetch_array($q_vendors)) {
    print "<option value=\"" . $a_vendors['ven_id'] . "\">" . $a_vendors['ven_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Model: <input type="text" name="mod_name" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Hardware Type: <select name="mod_type">
<?php
  $q_string  = "select part_id,part_name ";
  $q_string .= "from parts ";
  $q_string .= "order by part_name";
  $q_parts = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_parts = mysqli_fetch_array($q_parts)) {
    print "<option value=\"" . $a_parts['part_id'] . "\">" . $a_parts['part_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Purchase: <input type="date" name="mod_eopur" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Shipping: <input type="date" name="mod_eoship" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Life: <input type="date" name="mod_eol" size="12"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Update Other Hardware Form">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Vendor: <select name="mod_vendor">
<?php
  $q_string  = "select ven_id,ven_name ";
  $q_string .= "from vendors ";
  $q_string .= "order by ven_name";
  $q_vendors = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_vendors = mysqli_fetch_array($q_vendors)) {
    print "<option value=\"" . $a_vendors['ven_id'] . "\">" . $a_vendors['ven_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Model: <input type="text" name="mod_name" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Hardware Type: <select name="mod_type">
<?php
  $q_string  = "select part_id,part_name ";
  $q_string .= "from parts ";
  $q_string .= "order by part_name";
  $q_parts = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_parts = mysqli_fetch_array($q_parts)) {
    print "<option value=\"" . $a_parts['part_id'] . "\">" . $a_parts['part_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Purchase: <input type="date" name="mod_eopur" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Shipping: <input type="date" name="mod_eoship" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Life: <input type="date" name="mod_eol" size="12"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
