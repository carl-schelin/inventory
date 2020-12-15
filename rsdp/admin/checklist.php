<?php
# Script: checklist.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');
  check_login('2');

  $package = "checklist.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
  } else {
    $formVars['id'] = 0;
  }
  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = $_SESSION['group'];
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage RSDP Checklists</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_line( p_script_url ) {

  var answer = confirm("Delete this Item?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.checklists;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&chk_group="     + af_form.chk_group.value;
  af_url += "&chk_index="     + encode_URI(af_form.chk_index.value);
  af_url += "&chk_text="      + encode_URI(af_form.chk_text.value);
  af_url += "&chk_link="      + encode_URI(af_form.chk_link.value);
  af_url += "&chk_task="      + encode_URI(af_form.chk_task.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('checklist.mysql.php?update=-1&chk_group=<?php print $formVars['group']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
  $( "#platforms-tabs" ).tabs( ).addClass( "tab-shadow" );
  $( "#storage-tabs" ).tabs( ).addClass( "tab-shadow" );
  $( "#monitoring-tabs" ).tabs( ).addClass( "tab-shadow" );
  $( "#applications-tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="checklists">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('checklist-hide');">Checklist Management</a></th>
  <th class="ui-state-default" width="5"><a href="javascript:;" onmousedown="toggleDiv('checklist-help');">Help</a></th>
</tr>
</table>

<div id="checklist-help" style="display: none">

<div class="main-help ui-widget-content">

<p>The <strong>Checklist Manager</strong> gives you the ability to create custom checklists for the available tasks outside of the mandatory ones. For 
instance if you install applications and want to make sure all steps have been followed to do the installation, you would select 
the <strong>Applications</strong> tab and the <strong>Application Installed</strong> sub-tab and create a list of tasks.</p>

<p>These tasks are group defined. This means any member of your team will see the same task list.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Task</strong> - Save any changes to this form.</li>
    <li><strong>Add Task</strong> - Create a new task.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Checklist Form</strong>
  <ul>
    <li><strong>Task</strong> - Select the RSDP task you want to add a checklist to.</li>
    <li><strong>List Order</strong> - This lets you properly order the tasks. If you put in an existing List Number, the remaining tasks are incremented by one. If you leave the List Order field blank, the new task 
is added to the end of the list.</li>
    <li><strong>Text of Checklist</strong> - Description of the task. There are three replaceable variables you can insert in your tasks which are replaced by entered data.
    <ul>
      <li><strong>$host</strong> - The system's Hostname.</li>
      <li><strong>$app</strong> - The system's Application IP.</li>
      <li><strong>$mgt</strong> - The system's Management IP.</li>
    </ul></li>
    <li><strong>Link to Additional Info</strong> - Add a URL link to external documentation such as a HOWTO for installing software. This will be a link associated with the above text.</li>
  </ul></li>
</ul>

</div>

</div>

<div id="checklist-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" disabled="true" name="update" value="Update Checklist" onClick="javascript:attach_file('checklist.mysql.php', 1);hideDiv('checklist-hide');">
<input type="hidden" name="id" value="0">
<input type="hidden" name="chk_group" value="<?php print $formVars['group']; ?>">
<input type="button"                 name="addbtn" value="Add To Checklist" onClick="javascript:attach_file('checklist.mysql.php', 0);"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Checklist Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Task: <select name="chk_task">
<option value="0">Unassigned</option>
<option value="1">Unused</option>
<option value="2">Unused</option>
<option value="3">SAN Configuration</option>
<option value="4">Network Configuration</option>
<option value="5">Data Center</option>
<option value="6">Virtualization</option>
<option value="7">Unused</option>
<option value="8">Unused</option>
<option value="9">Unused</option>
<option value="10">System Installation</option>
<option value="11">SAN Provisioned</option>
<option value="12">System Configured</option>
<option value="13">Backups</option>
<option value="14">Monitoring</option>
<option value="15">App Install</option>
<option value="16">App Monitor</option>
<option value="17">App Configure</option>
<option value="18">InfoSec Scan</option>
</select></td>
  <td class="ui-widget-content">List Order: <input type="text" name="chk_index" value="" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Text of Checklist: <input type="text" name="chk_text" size="80"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Link to Additional Info: <input type="text" name="chk_link" size="80"></td>
</tr>
</table>

</div>

<div id="tabs">

<ul>
  <li><a href="#platforms">Platforms</a></li>
  <li><a href="#networking">Networking</a></li>
  <li><a href="#storage">Storage</a></li>
  <li><a href="#datacenter">Data Center</a></li>
  <li><a href="#virtualization">Virtualization</a></li>
  <li><a href="#backups">Backups</a></li>
  <li><a href="#monitoring">Monitoring</a></li>
  <li><a href="#applications">Applications</a></li>
  <li><a href="#infosec">InfoSec</a></li>
</ul>


<div id="platforms">

<div id="platforms-tabs">

<ul>
  <li><a href="#os_installed">System Installed</a></li>
  <li><a href="#os_configured">System Configured</a></li>
</ul>


<div id="os_installed">

<span id="installed_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="os_configured">

<span id="configured_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

</div>

</div>


<div id="networking">

<span id="networking_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="storage">

<div id="storage-tabs">

<ul>
  <li><a href="#designed">Storage</a></li>
  <li><a href="#provisioned">SAN Provisioned</a></li>
</ul>


<div id="designed">

<span id="storage_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="provisioned">

<span id="provisioned_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

</div>

</div>


<div id="datacenter">

<span id="datacenter_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="virtualization">

<span id="virtualization_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="backups">

<span id="backups_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="monitoring">

<div id="monitoring-tabs">

<ul>
  <li><a href="#monitored">Monitoring</a></li>
  <li><a href="#app_monitored">App Monitored</a></li>
</ul>


<div id="monitored">

<span id="monitored_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="app_monitored">

<span id="app_monitored_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

</div>

</div>



<div id="applications">

<div id="applications-tabs">

<ul>
  <li><a href="#app_installed">Application Installed</a></li>
  <li><a href="#app_configured">Application Configured</a></li>
</ul>


<div id="app_installed">

<span id="app_installed_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="app_configured">

<span id="app_configured_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

</div>

</div>


<div id="infosec">

<span id="infosec_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

</div>

</div>

</form>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
