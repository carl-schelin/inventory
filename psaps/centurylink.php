<?php
# Script: psaps.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "psaps.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage PSAP Data</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/FormTables/formTables.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/FormTables/formTables.css">

<script type="text/javascript">

function delete_line( p_script_url ) {
  var answer = confirm("Mark this PSAPs as Deleted?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.psaps;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&psap_customerid="   + encode_URI(af_form.psap_customerid.value);
  af_url += "&psap_parentid="     + encode_URI(af_form.psap_parentid.value);
  af_url += "&psap_ali_id="       + encode_URI(af_form.psap_ali_id.value);
  af_url += "&psap_companyid="    + encode_URI(af_form.psap_companyid.value);
  af_url += "&psap_description="  + encode_URI(af_form.psap_description.value);
  af_url += "&psap_lport="        + encode_URI(af_form.psap_lport.value);
  af_url += "&psap_circuit_id="   + encode_URI(af_form.psap_circuit_id.value);
  af_url += "&psap_pseudo_cid="   + encode_URI(af_form.psap_pseudo_cid.value);
  af_url += "&psap_lec="          + encode_URI(af_form.psap_lec.value);
  af_url += "&psap_texas="        + encode_URI(af_form.psap_texas.value);
  af_url += "&psap_updated="      + encode_URI(af_form.psap_updated.value);
  af_url += "&psap_delete="       + encode_URI(af_form.psap_delete.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('centurylink.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddPSAP' ).click(function() {
    $( "#dialogPSAP" ).dialog('open');
  });

  $( "#dialogPSAP" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogPSAP" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('centurylink.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update PSAP",
        click: function() {
          attach_file('centurylink.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add PSAP",
        click: function() {
          attach_file('centurylink.mysql.php', 0);
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
  <th class="ui-state-default">PSAP Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('psap-help');">Help</a></th>
</tr>
</table>

<div id="psap-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Product</strong> - Save any changes to this form.</li>
    <li><strong>Add Product</strong> - Add a new <?php print $Sitecompany; ?>Product.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddPSAP" value="Add PSAP"></td>
</tr>
</table>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</form>


<div id="dialogPSAP" title="PSAP Form">

<form name="psaps">

<input type="hidden" name="id" value="0">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">PSAP Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Customer ID: <input type="text" name="psap_customerid" size="30"></td>
  <td class="ui-widget-content"><?php print $Sitecompany; ?> ID: <input type="text" name="psap_parentid" size="30"></td>
  <td class="ui-widget-content"><?php print $Sitecompany; ?> ALI ID: <input type="text" name="psap_ali_id" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content"><?php print $Sitecompany; ?> ALI Name: <input type="text" name="psap_companyid" size="30"></td>
  <td class="ui-widget-content"><?php print $Sitecompany; ?> PSAP ID: <input type="text" name="psap_psap_id" size="30"></td>
  <td class="ui-widget-content"><?php print $Sitecompany; ?> PSAP Name: <input type="text" name="psap_description" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">LPort: <input type="text" name="psap_lport" size="30"></td>
  <td class="ui-widget-content">Circuit ID: <input type="text" name="psap_circuit_id" size="30"></td>
  <td class="ui-widget-content">Pseudo Circuit ID: <input type="text" name="psap_pseudo_cid" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">LEC: <input type="text" name="psap_lec" size="30"></td>
  <td class="ui-widget-content">Texas CLEC? <input type="text" name="psap_texas" size="30"></td>
  <td class="ui-widget-content">Last Updated: <input type="text" name="psap_updated" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Delete: <input type="text" name="psap_delete" size="30"></td>
</tr>
</table>

</form>

</div>


</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
