<?php
# Script: cpu.php
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

  $package = "cpu.php";

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
<title>CPU Editor</title>

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
  var answer = confirm("Delete this CPU?")

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
  af_url += "&mod_size="       + encode_URI(af_form.mod_size.value);
  af_url += "&mod_speed="      + encode_URI(af_form.mod_speed.value);
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
  uf_url += "&mod_size="       + encode_URI(uf_form.mod_size.value);
  uf_url += "&mod_speed="      + encode_URI(uf_form.mod_speed.value);
  uf_url += "&mod_eopur="      + encode_URI(uf_form.mod_eopur.value);
  uf_url += "&mod_eoship="     + encode_URI(uf_form.mod_eoship.value);
  uf_url += "&mod_eol="        + encode_URI(uf_form.mod_eol.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('cpu.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( );

  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
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
          show_file('cpu.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add CPU",
        click: function() {
          attach_file('cpu.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
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
          show_file('cpu.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update CPU",
        click: function() {
          update_file('cpu.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add CPU",
        click: function() {
          update_file('cpu.mysql.php', 0);
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
  <th class="ui-state-default">CPU Editor</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('detail-help');">Help</a></th>
</tr>
</table>

<div id="detail-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This is one piece of the Asset Management System. You add CPUs here which are then used in the Asset system to configure a server. 
In this situation, you might create a generic CPU vs defining a bunch of specific ones. But you can should you have a system where you 
have a specific purpose and need a specific CPU such as if you have an ARM cluster that hosts VMs, you can define an ARM CPU and then 
create an asset for the CPU and assign it to a device.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add CPU"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">CPU Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('cpu-listing-help');">Help</a></th>
</tr>
</table>

<div id="cpu-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>CPU Listing</strong></p>

<p>This page lists all the defined CPUs that can be used to create an asset.</p>

<p>To add a CPU, click the <strong>Add CPU</strong> button. This will bring up a dialog box which you can use 
to create a new CPU.</p>

<p>To edit an existing CPU, click on an entry in the listing. A dialog box will be presented where you can edit 
the current entry, or if there is a small difference, you can make changes and add a new CPU.</p>

<p>Note that under the Members colum is a number which indicates the number of times this CPU is in use. You cannot 
delete a CPU as long as this value is greater than zero.</p>

</div>

</div>


<span id="mysql_table"><?php print wait_Process('Waiting...')?></span>

</div>


</div>

</div>


<div id="dialogCreate" title="Add CPU Form">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Vendor: <select name="mod_vendor">
<?php
  $q_string  = "select ven_id,ven_name ";
  $q_string .= "from inv_vendors ";
  $q_string .= "order by ven_name";
  $q_inv_vendors = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_vendors = mysqli_fetch_array($q_inv_vendors)) {
    print "<option value=\"" . $a_inv_vendors['ven_id'] . "\">" . $a_inv_vendors['ven_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Model: <input type="text" name="mod_name" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Number of Cores: <input type="text" name="mod_size" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">CPU Speed: <input type="text" name="mod_speed" size="10"></td>
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


<div id="dialogUpdate" title="Update CPU Form">

<form name="formUpdate">

<input type="hidden" name="id" value="0">
<input type="hidden" name="mod_type" value="8">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Vendor: <select name="mod_vendor">
<?php
  $q_string  = "select ven_id,ven_name ";
  $q_string .= "from inv_vendors ";
  $q_string .= "order by ven_name";
  $q_inv_vendors = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_vendors = mysqli_fetch_array($q_inv_vendors)) {
    print "<option value=\"" . $a_inv_vendors['ven_id'] . "\">" . $a_inv_vendors['ven_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Model: <input type="text" name="mod_name" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Number of Cores: <input type="text" name="mod_size" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">CPU Speed: <input type="text" name="mod_speed" size="10"></td>
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
