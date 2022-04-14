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

  $formVars['type'] = '';
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 40);
  }

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
<title>Software Editor</title>

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

  uf_url += "&type="           + "<?php print $formVars['type']; ?>";
  af_url += "&sw_software="    + encode_URI(af_form.sw_software.value);
  af_url += "&sw_vendor="      + af_form.sw_vendor.value;
  af_url += "&sw_product="     + af_form.sw_product.value;
  af_url += "&sw_licenseid="   + af_form.sw_licenseid.value;
  af_url += "&sw_supportid="   + af_form.sw_supportid.value;
  af_url += "&sw_type="        + af_form.sw_type.value;
  af_url += "&sw_department="  + af_form.sw_department.value;
  af_url += "&sw_tags="        + encode_URI(af_form.sw_tags.value);
  af_url += "&sw_eol="         + encode_URI(af_form.sw_eol.value);
  af_url += "&sw_eos="         + encode_URI(af_form.sw_eos.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&type="           + "<?php print $formVars['type']; ?>";
  uf_url += "&sw_software="    + encode_URI(uf_form.sw_software.value);
  uf_url += "&sw_vendor="      + uf_form.sw_vendor.value;
  uf_url += "&sw_product="     + uf_form.sw_product.value;
  uf_url += "&sw_licenseid="   + uf_form.sw_licenseid.value;
  uf_url += "&sw_supportid="   + uf_form.sw_supportid.value;
  uf_url += "&sw_type="        + uf_form.sw_type.value;
  uf_url += "&sw_department="  + uf_form.sw_department.value;
  uf_url += "&sw_tags="        + encode_URI(uf_form.sw_tags.value);
  uf_url += "&sw_eol="         + encode_URI(uf_form.sw_eol.value);
  uf_url += "&sw_eos="         + encode_URI(uf_form.sw_eos.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('software.mysql.php?update=-1&type=<?php print $formVars['type']; ?>');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 375,
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
        text: "Add Software",
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
    height: 375,
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
        text: "Update Software",
        click: function() {
          update_file('software.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Software",
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

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Software Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('software-help');">Help</a></th>
</tr>
</table>

<div id="software-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update License</strong> - Save any changes to this form.</li>
    <li><strong>Add License</strong> - Add a new License.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Software"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Software Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('software-listing-help');">Help</a></th>
</tr>
</table>

<div id="license-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">
<ul>
  <li><strong>License Key Listing</strong>
  <ul>
    <li><strong>Editing</strong> - Click on a License to edit it.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Click the <strong>License Key Management</strong> title bar to toggle the <strong>License Key Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>


<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

</div>



<div id="dialogCreate" title="Add Software">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Software: <input type="text" name="sw_software" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Vendor: <select name="sw_vendor">
<?php
  $q_string  = "select ven_id,ven_name ";
  $q_string .= "from vendors ";
  $q_string .= "order by ven_name";
  $q_vendors = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_vendors = mysqli_fetch_array($q_vendors)) {
    print "<option value=\"" . $a_vendors['ven_id'] . "\">" . $a_vendors['ven_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Product: <select name="sw_product">
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from products ";
  $q_string .= "order by prod_name";
  $q_products = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_products = mysqli_fetch_array($q_products)) {
    print "<option value=\"" . $a_products['prod_id'] . "\">" . $a_products['prod_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">License: <select name="sw_licenseid">
<?php
  $q_string  = "select lic_id,lic_vendor,lic_product,prj_name ";
  $q_string .= "from licenses ";
  $q_string .= "left join projects on projects.prj_id = licenses.lic_project ";
  $q_string .= "order by prj_name,lic_product,lic_vendor";
  $q_licenses = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_licenses = mysqli_fetch_array($q_licenses)) {
    print "<option value=\"" . $a_licenses['lic_id'] . "\">" . $a_licenses['prj_name'] . " (" . $a_licenses['lic_vendor'] . " " . $a_licenses['lic_product'] . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Support: <select name="sw_supportid">
<?php
  $q_string  = "select sup_id,sup_company ";
  $q_string .= "from support ";
  $q_string .= "order by sup_company";
  $q_support = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_support = mysqli_fetch_array($q_support)) {
    print "<option value=\"" . $a_support['sup_id'] . "\">" . $a_support['sup_company'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Software Type: <select name="sw_type">
<?php
  $q_string  = "select typ_id,typ_name ";
  $q_string .= "from sw_types ";
  $q_string .= "order by typ_name";
  $q_sw_types = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_sw_types = mysqli_fetch_array($q_sw_types)) {
    print "<option value=\"" . $a_sw_types['typ_id'] . "\">" . $a_sw_types['typ_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Department: <select name="sw_department">
<?php
  $q_string  = "select dep_id,dep_name ";
  $q_string .= "from department ";
  $q_string .= "order by dep_name";
  $q_department = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_department = mysqli_fetch_array($q_department)) {
    print "<option value=\"" . $a_department['dep_id'] . "\">" . $a_department['dep_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Software Tags: <input type="text" name="sw_tags" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Life: <input type="date" name="sw_eol" size="15"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Support: <input type="date" name="sw_eos" size="15"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Software">

<form name="formUpdate">
<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Software: <input type="text" name="sw_software" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Vendor: <select name="sw_vendor">
<?php
  $q_string  = "select ven_id,ven_name ";
  $q_string .= "from vendors ";
  $q_string .= "order by ven_name";
  $q_vendors = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_vendors = mysqli_fetch_array($q_vendors)) {
    print "<option value=\"" . $a_vendors['ven_id'] . "\">" . $a_vendors['ven_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Product: <select name="sw_product">
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from products ";
  $q_string .= "order by prod_name";
  $q_products = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_products = mysqli_fetch_array($q_products)) {
    print "<option value=\"" . $a_products['prod_id'] . "\">" . $a_products['prod_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">License: <select name="sw_licenseid">
<?php
  $q_string  = "select lic_id,lic_vendor,lic_product,prj_name ";
  $q_string .= "from licenses ";
  $q_string .= "left join projects on projects.prj_id = licenses.lic_project ";
  $q_string .= "order by prj_name,lic_product,lic_vendor";
  $q_licenses = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_licenses = mysqli_fetch_array($q_licenses)) {
    print "<option value=\"" . $a_licenses['lic_id'] . "\">" . $a_licenses['prj_name'] . " (" . $a_licenses['lic_vendor'] . " " . $a_licenses['lic_product'] . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Support: <select name="sw_supportid">
<?php
  $q_string  = "select sup_id,sup_company ";
  $q_string .= "from support ";
  $q_string .= "order by sup_company";
  $q_support = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_support = mysqli_fetch_array($q_support)) {
    print "<option value=\"" . $a_support['sup_id'] . "\">" . $a_support['sup_company'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Software Type: <select name="sw_type">
<?php
  $q_string  = "select typ_id,typ_name ";
  $q_string .= "from sw_types ";
  $q_string .= "order by typ_name";
  $q_sw_types = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_sw_types = mysqli_fetch_array($q_sw_types)) {
    print "<option value=\"" . $a_sw_types['typ_id'] . "\">" . $a_sw_types['typ_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Department: <select name="sw_department">
<?php
  $q_string  = "select dep_id,dep_name ";
  $q_string .= "from department ";
  $q_string .= "order by dep_name";
  $q_department = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_department = mysqli_fetch_array($q_department)) {
    print "<option value=\"" . $a_department['dep_id'] . "\">" . $a_department['dep_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Software Tags: <input type="text" name="sw_tags" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Life: <input type="date" name="sw_eol" size="15"></td>
</tr>
<tr>
  <td class="ui-widget-content">End of Support: <input type="date" name="sw_eos" size="15"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
