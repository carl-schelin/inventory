<?php
# Script: service.php
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

  $package = "service.php";

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
<title>Service Class Editor</title>

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
  var answer = confirm("Delete this Service Class?")

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

  af_url += "&svc_name="         + encode_URI(af_form.svc_name.value);
  af_url += "&svc_acronym="      + encode_URI(af_form.svc_acronym.value);
  af_url += "&svc_availability=" + encode_URI(af_form.svc_availability.value);
  af_url += "&svc_downtime="     + encode_URI(af_form.svc_downtime.value);
  af_url += "&svc_mtbf="         + encode_URI(af_form.svc_mtbf.value);
  af_url += "&svc_geographic="   + af_form.svc_geographic.checked;
  af_url += "&svc_mttr="         + encode_URI(af_form.svc_mttr.value);
  af_url += "&svc_resource="     + af_form.svc_resource.checked;
  af_url += "&svc_restore="      + encode_URI(af_form.svc_restore.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&svc_name="         + encode_URI(uf_form.svc_name.value);
  uf_url += "&svc_acronym="      + encode_URI(uf_form.svc_acronym.value);
  uf_url += "&svc_availability=" + encode_URI(uf_form.svc_availability.value);
  uf_url += "&svc_downtime="     + encode_URI(uf_form.svc_downtime.value);
  uf_url += "&svc_mtbf="         + encode_URI(uf_form.svc_mtbf.value);
  uf_url += "&svc_geographic="   + uf_form.svc_geographic.checked;
  uf_url += "&svc_mttr="         + encode_URI(uf_form.svc_mttr.value);
  uf_url += "&svc_resource="     + uf_form.svc_resource.checked;
  uf_url += "&svc_restore="      + encode_URI(uf_form.svc_restore.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('service.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 350,
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
          show_file('service.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Service Class",
        click: function() {
          attach_file('service.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 350,
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
          show_file('service.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Service Class",
        click: function() {
          update_file('service.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Service Class",
        click: function() {
          update_file('service.mysql.php', 0);
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
  <th class="ui-state-default">Service Class Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('service-help');">Help</a></th>
</tr>
</table>

<div id="service-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>A Service Class defines the availability of a service. It provides definition of how often a service can be unavailable 
if how redundant, and if geograpic redundancy is required. These are all driven by contractual requirements.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Service Class"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Service Class Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('service-listing-help');">Help</a></th>
</tr>
</table>

<div id="service-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


<p><strong>Country Listing</strong></p>

<p>This page lists all the Service Class definitions which are used to provide service 
availability requirements.</p>

<p>To edit a Service Class, click on the entry in the listing. A dialog box will be 
displayed where you can edit the current entry, or if there's some change you wish to 
make, you can add a new Service Class.</p>

<p>To add a new Service Class, click the Add Service Class button. A dialog box will 
be displayed where you can add the necessary information and then save the new Service Class.</p>


</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add Service Class">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="svc_name" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Acronym: <input type="text" name="svc_acronym" size="5"></td>
</tr>
<tr>
  <td class="ui-widget-content">Availability: <input type="text" name="svc_availability" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Downtime: <input type="text" name="svc_downtime" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">MTBF: <input type="text" name="svc_mtbf" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Geographically Redundant: <input type="checkbox" name="svc_geographic"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">MTTR: <input type="text" name="svc_mttr" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Resource Sharing: <input type="checkbox" name="svc_resource"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Restore: <input type="text" name="svc_restore" size="12"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Service Class">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="svc_name" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Acronym: <input type="text" name="svc_acronym" size="5"></td>
</tr>
<tr>
  <td class="ui-widget-content">Availability: <input type="text" name="svc_availability" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Downtime: <input type="text" name="svc_downtime" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">MTBF: <input type="text" name="svc_mtbf" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Geographically Redundant: <input type="checkbox" name="svc_geographic"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">MTTR: <input type="text" name="svc_mttr" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Resource Sharing: <input type="checkbox" name="svc_resource"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Restore: <input type="text" name="svc_restore" size="12"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
