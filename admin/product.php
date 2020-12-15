<?php
# Script: product.php
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

  $package = "product.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage <?php print $Sitecompany; ?>Products</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_line( p_script_url ) {
  var answer = confirm("Delete this <?php print $Sitecompany; ?>Product?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.products;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&prod_name="     + encode_URI(af_form.prod_name.value);
  af_url += "&prod_desc="     + encode_URI(af_form.prod_desc.value);
  af_url += "&prod_group="    + af_form.prod_group.value;
  af_url += "&prod_type="     + encode_URI(af_form.prod_type.value);
  af_url += "&prod_code="     + encode_URI(af_form.prod_code.value);
  af_url += "&prod_oldcode="  + encode_URI(af_form.prod_oldcode.value);
  af_url += "&prod_citype="   + encode_URI(af_form.prod_citype.value);
  af_url += "&prod_tier1="    + encode_URI(af_form.prod_tier1.value);
  af_url += "&prod_tier2="    + encode_URI(af_form.prod_tier2.value);
  af_url += "&prod_tier3="    + encode_URI(af_form.prod_tier3.value);
  af_url += "&prod_unit="     + af_form.prod_unit.value;
  af_url += "&prod_remedy="   + af_form.prod_remedy.checked;
  af_url += "&prod_service="  + af_form.prod_service.value;



  if (af_form.prod_code.value.length != 2) {
    alert("Product code must be unique and 2 characters.");
  } else {
    script = document.createElement('script');
    script.src = p_script_url + af_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function clear_fields() {
  show_file('product.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddProduct' ).click(function() {
    $( "#dialogProduct" ).dialog('open');
  });

  $( "#dialogProduct" ).dialog({
    autoOpen: false,
    modal: true,
    height: 320,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogProduct" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('product.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Product or Service",
        click: function() {
          attach_file('product.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Product or Service",
        click: function() {
          attach_file('product.mysql.php', 0);
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
  <th class="ui-state-default"><?php print $Sitecompany; ?>Product Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('product-help');">Help</a></th>
</tr>
</table>

<div id="product-help" style="display: none">

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
  <td class="ui-widget-content button"><input type="button" id="clickAddProduct" value="Add Product or Service"></td>
</tr>
</table>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</form>

<div id="dialogProduct" title="Product or Service Form">

<form name="products">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="8">Product or Service Information</th>
</tr>
<tr>
  <td class="ui-widget-content">ID: <input disabled="true" type="text" name="prod_id" size="10"></td>
  <td class="ui-widget-content" colspan="2">Name: <input type="text" name="prod_name" size="40"></td>
  <td class="ui-widget-content">Code: <input type="text" name="prod_code" size="10"></td>
  <td class="ui-widget-content">Old Code: <input type="text" name="prod_oldcode" size="10"></td>
  <td class="ui-widget-content" colspan="3">Description: <input type="text" name="prod_desc" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Group: <select name="prod_group">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
  }
?></select></td>
  <td class="ui-widget-content" colspan="2">Business Unit <select name="prod_unit">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select bus_id,bus_name ";
  $q_string .= "from business_unit ";
  $q_string .= "order by bus_name ";
  $q_business_unit = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_business_unit = mysqli_fetch_array($q_business_unit)) {
    print "<option value=\"" . $a_business_unit['bus_id'] . "\">" . $a_business_unit['bus_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Remedy? <input type="checkbox" name="prod_remedy"></td>
  <td class="ui-widget-content" colspan="2">Service Class <select name="prod_service">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select svc_id,svc_acronym ";
  $q_string .= "from service ";
  $q_string .= "order by svc_id";
  $q_service = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_service = mysqli_fetch_array($q_service)) {
    print "<option value=\"" . $a_service['svc_id'] . "\">" . $a_service['svc_acronym'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

<table>
<tr>
  <th class="ui-state-default" colspan="6">Remedy Import Form</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Product Type: <input type="text" name="prod_type" size="30"></td>
  <td class="ui-widget-content" colspan="3">CI Type: <input type="text" name="prod_citype" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Product Categorization Tier 1: <input type="text" name="prod_tier1" size="30"></td>
  <td class="ui-widget-content" colspan="2">Product Categorization Tier 2: <input type="text" name="prod_tier2" size="30"></td>
  <td class="ui-widget-content" colspan="2">Product Categorization Tier 3: <input type="text" name="prod_tier3" size="30"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
