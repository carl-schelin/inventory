<?php
# Script: servers.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');
  check_login('4');

  $package = "servers.php";

  logaccess($_SESSION['uid'], $package, "Viewing RSDP server listing");

  if (isset($_GET['myrsdp'])) {
    $formVars['myrsdp'] = clean($_GET['myrsdp'], 10);
  } else {
    $formVars['myrsdp'] = 'no';
  }
  if ($formVars['myrsdp'] == '') {
    $formVars['myrsdp'] = 'yes';
  }

  if (isset($_GET['projectid'])) {
    $formVars['projectid'] = clean($_GET['projectid'], 10);
  } else {
    $formVars['projectid'] = 0;
  }

  if (isset($_GET['productid'])) {
    $formVars['productid'] = clean($_GET['productid'], 10);
  } else {
    $formVars['productid'] = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSDP: Servers</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function attach_duplicate( p_script_url) {
  var ad_form = document.duplicate;
  var ad_url;

  ad_url += '?rsdp='          + ad_form.rsdp.value;

  ad_url += "&chk_filesystem="    + ad_form.chk_filesystem.checked;
  ad_url += "&chk_ipaddr="        + ad_form.chk_ipaddr.checked;
  ad_url += "&chk_san1="          + ad_form.chk_san1.checked;
  ad_url += "&chk_net="           + ad_form.chk_net.checked;
  ad_url += "&chk_virt="          + ad_form.chk_virt.checked;
  ad_url += "&chk_sys1="          + ad_form.chk_sys1.checked;
  ad_url += "&chk_san2="          + ad_form.chk_san2.checked;
  ad_url += "&chk_sys2="          + ad_form.chk_sys2.checked;
  ad_url += "&chk_backup="        + ad_form.chk_backup.checked;
  ad_url += "&chk_mon1="          + ad_form.chk_mon1.checked;
  ad_url += "&chk_app1="          + ad_form.chk_app1.checked;
  ad_url += "&chk_mon2="          + ad_form.chk_mon2.checked;
  ad_url += "&chk_app2="          + ad_form.chk_app2.checked;
  ad_url += "&chk_infosec="       + ad_form.chk_infosec.checked;

  script = document.createElement('script');
  script.src = p_script_url + ad_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function delete_line( p_script_url ) {
  var answer = confirm("Delete this Server?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function close_line( p_script_url ) {
  var answer = confirm("Mark this Server as Complete?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function clear_fields() {
  show_file('servers.mysql.php?update=-1&projectid=<?php print $formVars['projectid']; ?>&myrsdp=<?php print $formVars['myrsdp']; ?>&productid=<?php print $formVars['productid']; ?>');
}

$(document).ready( function() {
  $( '#clickDuplicate' ).click(function() {
    $( "#dialogDuplicate" ).dialog('open');
  });

  $( "#dialogDuplicate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 520,
    width:  700,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogDuplicate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Duplicate",
        click: function() {
          attach_duplicate('build/initial.dup.php');
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

<form action="build/initial.php" method="POST">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Server Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="display:none">

<div class="main-help ui-widget-content">


<h2>Instructions</h2>

<p>The <strong>Rapid Server Deployment Process</strong> (RSDP) was designed to help with 90% of the builds in operations. The various groups were gathered and a list of
tasks for provisioning common server deployments performed by each group was compiled. The groups were consulted many times to refine this list which was transformed into
the RSDP module of the Inventory.</p>

<p>This page provides, by default, a listing of all the Servers associated with this Project.</p>

<p>To request a new server, click the <strong>Request New Server</strong> button and fill out the forms.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content" style="text-align: right"><input type="submit" name="clone" id="button" value="Request New Server"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

<div id="dialogDuplicate" title="Duplicate Server Form">

<form name="duplicate">

<input type="hidden" name="id" value="0">

<p>Select the information you want to copy from this server into a new server. By default, the Server Initialization and Server Provisioning steps will be copied. Additional information will be copied based on your selections. Note that none of the tasks will be marked as completed.</p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Duplicate Server Form</th>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_filesystem"> Copy any extra space details noted in the Filesystems tab.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_ipaddr"> Copy all the network details including IP Addresses, Switch Configurations, etc if any.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_san1"> Duplicate the SAN Design task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_net"> Duplicate the Network Configuration Task. Does not include IP Address or Switch details.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_virt"> Duplicate the Virtualization or Data Center Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_sys1"> Duplicate the System Installation Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_san2"> Duplicate the SAN Provisioning Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_sys2"> Duplicate the System Configuration Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_backup"> Duplicate the System Backups Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_mon1"> Duplicate the Monitoring Configuration Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_app1"> Duplicate the Application Installation Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_mon2"> Duplicate the Monitoring Complete Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_app2"> Duplicate the Application Configured Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_infosec"> Duplicate the InfoSec Completed Task.</label></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
