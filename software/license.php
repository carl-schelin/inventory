<?php
# Script: license.php
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

  $package = "license.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['product'])) {
    $formVars['product'] = clean($_GET['product'], 10);
  } else {
    $formVars['product'] = 0;
  }

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
<title>Software License Editor</title>

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
  var answer = confirm("Delete this License?")

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

  af_url += "&lic_vendor="    + af_form.lic_vendor.value;
  af_url += "&lic_parent="    + af_form.lic_parent.value;
  af_url += "&lic_product="   + encode_URI(af_form.lic_product.value);
  af_url += "&lic_date="      + encode_URI(af_form.lic_date.value);
  af_url += "&lic_expire="    + encode_URI(af_form.lic_expire.value);
  af_url += "&lic_quantity="  + encode_URI(af_form.lic_quantity.value);
  af_url += "&lic_vendorpo="  + encode_URI(af_form.lic_vendorpo.value);
  af_url += "&lic_customer="  + encode_URI(af_form.lic_customer.value);
  af_url += "&lic_po="        + encode_URI(af_form.lic_po.value);
  af_url += "&lic_project="   + encode_URI(af_form.lic_project.value);
  af_url += "&lic_key="       + encode_URI(af_form.lic_key.value);
  af_url += "&lic_serial="    + encode_URI(af_form.lic_serial.value);
  af_url += "&lic_domain="    + encode_URI(af_form.lic_domain.value);
  af_url += "&product="       + <?php print $formVars['product']; ?>;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&lic_vendor="    + uf_form.lic_vendor.value;
  uf_url += "&lic_parent="    + uf_form.lic_parent.value;
  uf_url += "&lic_product="   + encode_URI(uf_form.lic_product.value);
  uf_url += "&lic_date="      + encode_URI(uf_form.lic_date.value);
  uf_url += "&lic_expire="    + encode_URI(uf_form.lic_expire.value);
  uf_url += "&lic_quantity="  + encode_URI(uf_form.lic_quantity.value);
  uf_url += "&lic_vendorpo="  + encode_URI(uf_form.lic_vendorpo.value);
  uf_url += "&lic_po="        + encode_URI(uf_form.lic_po.value);
  uf_url += "&lic_customer="  + encode_URI(uf_form.lic_customer.value);
  uf_url += "&lic_project="   + encode_URI(uf_form.lic_project.value);
  uf_url += "&lic_key="       + encode_URI(uf_form.lic_key.value);
  uf_url += "&lic_serial="    + encode_URI(uf_form.lic_serial.value);
  uf_url += "&lic_domain="    + encode_URI(uf_form.lic_domain.value);
  uf_url += "&product="       + <?php print $formVars['product']; ?>;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('license.mysql.php?update=-1&product=<?php print $formVars['product']; ?>');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 400,
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
          show_file('license.mysql.php?update=-1&product=<?php print $formVars['product']; ?>');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add License",
        click: function() {
          attach_file('license.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 400,
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
          show_file('license.mysql.php?update=-1&product=<?php print $formVars['product']; ?>');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update License",
        click: function() {
          update_file('license.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add License",
        click: function() {
          update_file('license.mysql.php', 0);
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

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Software License Manager</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('license-help');">Help</a></th>
</tr>
</table>

<div id="license-help" style="display: none">

<div class="main-help ui-widget-content">

<p>The License management system lets you track the details about a software license. This includes vendor, project, 
license key, serial number, PO number, and even what domain if it's something like a Certificate.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add License"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Software License Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('license-listing-help');">Help</a></th>
</tr>
</table>

<div id="license-listing-help" style="display: none">

<div class="main-help ui-widget-content">

<p><strong>License Manager Listing</strong></p>

<p>This page lists details about purchased software licenses.</p>

<p>Initially you'll just see the main license file information. Since expiration dates could be different in the 
license file, no dates or quantities are shown. Clicking on the Product will show the selected license and all 
supported products this license was generated for. This includes dates and quantities.</p>

<p>Clicking on the Vendor will present the dialog box for the entry which you can edit or copy.</p>

<p>To add a software license, click the Add License button. This will bring up a dialog box which you can then use to add 
a software license.</p>

<p>To edit a software license, click on an entry in the listing. A dialog box will be displayed where you can edit the 
software license, or if there is a small difference, you can make changes and add a new software license.</p>

<p>Licenses <span class="ui-state-highlight">highlighted like this</span>, are going to expire within 90 days.</p>

<p>Licenses <span class="ui-state-error">highlighted like this</span>, are going to expire within 30 days.</p>

</div>

</div>


<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

</div>


<div id="dialogCreate" title="Add License">

<form name="formCreate">

<?php include("license.dialog.php"); ?>

</form>

</div>


<div id="dialogUpdate" title="Edit License">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<?php include("license.dialog.php"); ?>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
