<?php
# Script: service.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "service.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Add/Edit Service Class Levels</title>

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
  var af_form = document.service;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

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

function clear_fields() {
  show_file('service.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddService' ).click(function() {
    $( "#dialogService" ).dialog('open');
  });

  $( "#dialogService" ).dialog({
    autoOpen: false,
    modal: true,
    height: 220,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogService" ).hide();
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
        text: "Update Service",
        click: function() {
          attach_file('service.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Service",
        click: function() {
          attach_file('service.mysql.php', 0);
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
  <th class="ui-state-default">Service Class Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('service-help');">Help</a></th>
</tr>
</table>

<div id="service-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Service Class</strong> - Save any changes to this form.</li>
    <li><strong>Add Service Class</strong> - Add a new Service Class. You can edit an existing one and use this button to save a new Service Class.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Service Class Form</strong> - 
  <ul>
    <li><strong>Name</strong> - Service Class</li>
    <li><strong>Acronym</strong> - Acronym for the Service Class</li>
    <li><strong>Availability</strong> - Percentage of time the <strong>service</strong> is available to the customer.</li>
    <li><strong>Downtime</strong> - Permitted <strong>service</strong> downtime based on the Service Class.</li>
    <li><strong>MTBF</strong> - Mean Time Between Failures - How often a <strong>service</strong> can fail.</li>
    <li><strong>Geographically Redundant</strong> - Do components of the <strong>service</strong> need to in geographically diverse locations.</li>
    <li><strong>MTTR</strong> - Mean Time To Recovery - How much time it takes for a <strong>service</strong> to recover.</li>
    <li><strong>Resource Sharing</strong> - Can other services share the same resources?</li>
    <li><strong>Restore</strong> - Cost/Time to Restore Data and Service.</li>
  </ul></li>
</ul>

<p><a href="http://intradonet/sites/database/Shared%20Documents/Service_Class/Service_Class_Definition.doc">Service Class Documentation.</a></p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddService" value="Add Service Class"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

<div id="dialogService" title="Service Class Form">

<form name="service">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Service Class Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="svc_name" size="30"></td>
  <td class="ui-widget-content">Acronym: <input type="text" name="svc_acronym" size="5"></td>
  <td class="ui-widget-content">Availability: <input type="text" name="svc_availability" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Downtime: <input type="text" name="svc_downtime" size="20"></td>
  <td class="ui-widget-content">MTBF: <input type="text" name="svc_mtbf" size="20"></td>
  <td class="ui-widget-content"><label>Geographically Redundant: <input type="checkbox" name="svc_geographic"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">MTTR: <input type="text" name="svc_mttr" size="12"></td>
  <td class="ui-widget-content"><label>Resource Sharing: <input type="checkbox" name="svc_resource"></label></td>
  <td class="ui-widget-content">Restore: <input type="text" name="svc_restore" size="12"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
