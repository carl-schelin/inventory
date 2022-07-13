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

# if help has not been seen yet,
  if (show_Help($db, $Productpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Product/Service Editor</title>

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
  var answer = confirm("Delete this Product/Service?")

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

  af_url += "&prod_code="     + encode_URI(af_form.prod_code.value);
  af_url += "&prod_name="     + encode_URI(af_form.prod_name.value);
  af_url += "&prod_desc="     + encode_URI(af_form.prod_desc.value);
  af_url += "&prod_tags="     + encode_URI(af_form.prod_tags.value);
  af_url += "&prod_unit="     + af_form.prod_unit.value;
  af_url += "&prod_service="  + af_form.prod_service.value;

  if (af_form.prod_code.value.length != 2) {
    alert("Product code must be unique and 2 characters.");
  } else {
    script = document.createElement('script');
    script.src = p_script_url + af_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&prod_code="     + encode_URI(uf_form.prod_code.value);
  uf_url += "&prod_name="     + encode_URI(uf_form.prod_name.value);
  uf_url += "&prod_desc="     + encode_URI(uf_form.prod_desc.value);
  uf_url += "&prod_tags="     + encode_URI(uf_form.prod_tags.value);
  uf_url += "&prod_unit="     + uf_form.prod_unit.value;
  uf_url += "&prod_service="  + uf_form.prod_service.value;

  if (uf_form.prod_code.value.length != 2) {
    alert("Product code must be unique and 2 characters.");
  } else {
    script = document.createElement('script');
    script.src = p_script_url + uf_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function clear_fields() {
  show_file('product.mysql.php?update=-1');
}

$(document).ready( function() {
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
          show_file('product.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Product/Service",
        click: function() {
          attach_file('product.mysql.php', 0);
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
          show_file('product.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Product/Service",
        click: function() {
          update_file('product.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Product/Service",
        click: function() {
          update_file('product.mysql.php', 0);
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
  <th class="ui-state-default">Product/Service Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('product-help');">Help</a></th>
</tr>
</table>

<div id="product-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>A Product or Service is something that the company is providing. This could be anything from an inhouse built application 
or something like cloud services.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Product/Service"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Product/Service Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('product-listing-help');">Help</a></th>
</tr>
</table>

<div id="product-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Product/Service Listing</strong></p>

<p>This page lists all the Products and Services that are offerred by the company.</p>

<p>To add a Product or Service, click the Add Product/Service button. This will bring up a dialog box which 
you can use to create a new Product or Service.</p>

<p>To edit an existing Product or Service, click on an entry in the listing. A dialog box will be presented 
where you can edit the current entry, or if there is a small difference, you can make changes and add a new 
Product or Service.</p>

<p>Note that under the Members column is a number which indicates the number of servers that are part of 
this project. You cannot remove a product until this value is zero. Clicking on the number will take you 
to a server listing where you can edit the servers and remove them from the product.</p>


</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add Product/Service">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Code: <input type="text" name="prod_code" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="prod_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="prod_desc" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Business Unit Ownership <select name="prod_unit">
<?php
  $q_string  = "select bus_id,bus_name ";
  $q_string .= "from business ";
  $q_string .= "order by bus_name ";
  $q_business = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_business = mysqli_fetch_array($q_business)) {
    print "<option value=\"" . $a_business['bus_id'] . "\">" . $a_business['bus_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Service Class <select name="prod_service">
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
<tr>
  <td class="ui-widget-content">Product Tags: <input type="text" name="prod_tags" size="60"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Product/Service">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Code: <input type="text" name="prod_code" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="prod_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="prod_desc" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Business Unit Ownership <select name="prod_unit">
<?php
  $q_string  = "select bus_id,bus_name ";
  $q_string .= "from business ";
  $q_string .= "order by bus_name ";
  $q_business = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_business = mysqli_fetch_array($q_business)) {
    print "<option value=\"" . $a_business['bus_id'] . "\">" . $a_business['bus_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Service Class <select name="prod_service">
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
<tr>
  <td class="ui-widget-content">Product Tags: <input type="text" name="prod_tags" size="60"></td>
</tr>
</table>

</form>

</div>


</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
