<?php
# Script: configured.php
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

  $package = "configured.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  $formVars['rsdp'] = clean($_GET['rsdp'], 10);
  if (isset($_GET['rsdp'])) {
    $formVars['rsdp'] = clean($_GET['rsdp'], 10);
  } else {
    $formVars['rsdp'] = 0;
  }
  if ($formVars['rsdp'] == '' || $formVars['rsdp'] == 0) {
    include($RSDPpath . "/redirect.php");
    exit(0);
  }

# where are we?
  for ($i = 0; $i < 19; $i++) {
    $task[$i] = '';
  }
  $task[12] = '&gt; ';

  $q_string  = "select os_sysname ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'];
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_osteam) > 0) {
    $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);
  } else {
    $a_rsdp_osteam = "New System";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSDP: <?php print $a_rsdp_osteam['os_sysname']; ?></title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css "           href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" src="<?php print $RSDProot; ?>/admin/comments.js"></script>

<script type="text/javascript">

function attach_file( p_script_url, complete ) {
  var af_form = document.rsdp;
  var af_url;
  var answer = false;

  af_url  = '?complete='    + complete;
  af_url += '&rsdp='        + <?php print $formVars['rsdp']; ?>;
  af_url += '&id='          + af_form.id.value;

  af_url += '&if_sanfs='       + af_form.if_sanfs.checked;
  af_url += '&if_verified='    + af_form.if_verified.checked;
  af_url += '&if_checklist='   + af_form.if_checklist.checked;
  af_url += '&if_wiki='        + af_form.if_wiki.checked;
  af_url += '&if_svrmgt='      + af_form.if_svrmgt.checked;

  if (complete === 1) {
    var question  = "This form is ready to submit.\n\n";
        question += "Once you submit this form, you may not edit the information again.\n\n";
        question += "Are you sure you are ready to submit this form?";
    answer = confirm(question);
  }

  if (answer === true || complete < 1 || complete == 2) {
    script = document.createElement('script');
    script.src = p_script_url + af_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_checklist( p_script_url, p_index, p_task, p_check, p_comment ) {
  var ac_form = document.rsdp;
  var ac_url;
  
  ac_url  = '?id='       + ac_form.id.value;
  ac_url += '&rsdp='     + <?php print $formVars['rsdp']; ?>;
    
  ac_url += '&chk_task='     + encode_URI(p_task);
  ac_url += '&chk_group='    + ac_form.chk_group.value;
  ac_url += '&chk_index='    + encode_URI(p_index);
  ac_url += '&chk_checked='  + encode_URI(p_check);
  ac_url += '&chk_comment='  + encode_URI(p_comment);
  
  script = document.createElement('script');
  script.src = p_script_url + ac_url; 
  document.getElementsByTagName('head')[0].appendChild(script);
}

function validate_Form() {

  var vf_submit = 1;

  if (document.rsdp.if_checklist.checked === false) {
    set_Class('if_checklist', 'ui-state-error');
    $( "#check-1" ).button( "option", "label", "The system checklist has NOT been completed." );
    vf_submit = 0;
  } else {
    set_Class('if_checklist', 'ui-widget-content');
    $( "#check-1" ).button( "option", "label", "The system checklist HAS been completed." );
  }

  if (document.rsdp.if_sanfs.checked === false) {
    set_Class('if_sanfs', 'ui-state-error');
    $( "#check-2" ).button( "option", "label", "The SAN filesystems have NOT been configured." );
    vf_submit = 0;
  } else {
    set_Class('if_sanfs', 'ui-widget-content');
    $( "#check-2" ).button( "option", "label", "The SAN filesystems HAVE been configured." );
  }

  if (document.rsdp.if_verified.checked === false) {
    set_Class('if_verified', 'ui-state-error');
    $( "#check-3" ).button( "option", "label", "The Network interfaces have NOT been verified." );
    vf_submit = 0;
  } else {
    set_Class('if_verified', 'ui-widget-content');
    $( "#check-3" ).button( "option", "label", "The Netork interfaces HAVE been verified." );
  }

  if (document.rsdp.if_wiki.checked === false) {
    set_Class('if_wiki', 'ui-state-error');
    $( "#check-4" ).button( "option", "label", "The system information has NOT been documented." );
    vf_submit = 0;
  } else {
    set_Class('if_wiki', 'ui-widget-content');
    $( "#check-4" ).button( "option", "label", "The system information HAS been documented." );
  }

  if (document.rsdp.if_svrmgt.checked === false) {
    set_Class('if_svrmgt', 'ui-state-error');
    $( "#check-5" ).button( "option", "label", "The server has NOT been added to the Inventory." );
    vf_submit = 0;
  } else {
    set_Class('if_svrmgt', 'ui-widget-content');
    $( "#check-5" ).button( "option", "label", "The server HAS been added to the Inventory." );
  }

  if (vf_submit) {
    document.rsdp.addbtn.disabled = false;
  } else {
    document.rsdp.addbtn.disabled = true;
  }

}

function clear_fields() {
  show_file('<?php print $RSDProot; ?>/admin/comments.mysql.php'   + '?update=-1&com_rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/system/configured.fill.php' + '?update=-1&rsdp=<?php print $formVars['rsdp']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );

  $( "#check-1" ).button();
  $( "#check-2" ).button();
  $( "#check-3" ).button();
  $( "#check-4" ).button();
  $( "#check-5" ).button();
  $( "#check-6" ).button();

});

</script>

</head>
<body onload="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($RSDPpath . '/topmenu.rsdp.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<form name="rsdp">

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">System Configuration: <?php print $a_rsdp_osteam['os_sysname']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('system-help');">Help</a></th>
</tr>
</table>

<div id="system-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>System Configuration Page</h2>

<p>This task is typically performed by the Platforms Team. There are five tabs providing Project 
and System information, team checklist, a list of items that must be checked off before the task 
can be completed, and a comments page for passing information to various teams.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Reminder E-Mail</strong> - Send a reminder email to the team or individual responsible for completing this task.</li>
    <li><strong>Save Changes</strong> - Save any changes to this task.</li>
    <li><strong>Save And Exit</strong> - Save any changes to this task and return to the task list.</li>
    <li><strong>Task Completed</strong> - This button stays disabled until all identified tasks have been 
completed. Once completed, the button is enabled. Clicking it identifies the task as completed. If selected, 
tickets will be created for the next tasks and an email will be sent notifying the responsible teams or 
individuals that their task is ready to be worked.</li>
  </ul></li>
</ul>

</div>

</div>

<?php print submit_RSDP($db, " $formVars['rsdp'], 12, $RSDProot . "/system/configured.mysql.php", "rsdp_platformspoc", "rsdp_platform", 0); ?>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Project Information</a></li>
  <li><a href="#tabs-2">System Information</a></li>
  <li><a href="#tabs-3">System Checklist</a></li>
  <li><a href="#tabs-4">Task Checklist</a></li>
  <li><a href="#tabs-5">Comments<?php
  $q_string  = "select count(*) ";
  $q_string .= "from rsdp_comments ";
  $q_string .= "where com_rsdp = " . $formVars['rsdp'];
  $q_rsdp_comments = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_comments = mysqli_fetch_array($q_rsdp_comments);

  if (mysqli_num_rows($q_rsdp_comments)) {
    print " (" . $a_rsdp_comments['count(*)'] . ")";
  } else {
    print " (0)";
  }
?>
</a></li>
</ul>

<div id="tabs-1">

<?php print request_Header($db, "$formVars['rsdp']); ?>

</div>


<div id="tabs-2">

<?php print request_Server($db, "$formVars['rsdp']); ?>

</div>


<div id="tabs-3">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">System Checklist</th>
</tr>
<tr>
  <td id="if_checklist"><input type="checkbox" id="check-1" name="if_checklist" onchange="validate_Form();"><label for="check-1"></label></td>
</tr>
<?php print return_Checklist($db, " $formVars['rsdp'], 12); ?>
</table>

</div>


<div id="tabs-4">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Task Checklist</th>
</tr>
<tr>
  <td id="if_sanfs"    ><input type="checkbox" id="check-2" name="if_sanfs"    onclick="validate_Form();"> <label for="check-2"></label></td>
</tr>
<tr>
  <td id="if_verified" ><input type="checkbox" id="check-3" name="if_verified" onclick="validate_Form();"> <label for="check-3"></label></td>
</tr>
<tr>
  <td id="if_wiki"     ><input type="checkbox" id="check-4" name="if_wiki"     onclick="validate_Form();"> <label for="check-4"></label></td>
</tr>
<tr>
  <td id="if_svrmgt"   ><input type="checkbox" id="check-5" name="if_svrmgt"   onclick="validate_Form();"> <label for="check-5"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">
<?php
  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_name = \"" . $a_rsdp_osteam['os_sysname'] . "\" and inv_status = 0 ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inventory) > 0) {
    $a_inventory = mysqli_fetch_array($q_inventory);

#    print "To update the Inventory entry for " . $a_inventory['inv_name'] . " ";
    print $a_inventory['inv_name'] . " added to Inventory";
    $update = 1;
  } else {
    print "To add " . $a_rsdp_osteam['os_sysname'] . " to the Inventory, ";
    $update = 0;
    print "<input type=\"button\" name=\"populate\" value=\"Click Here\" onClick=\"javascript:show_file('" . $RSDProot . "/system/populate.php?update=0&rsdp=" . $formVars['rsdp'] . "');\">";
  }
?>
<span id="status"></span>
</td>
</tr>
</table>

</div>

<div id="tabs-5">

<?php include ($RSDPpath . '/admin/comments.php'); ?>

</div>

</div>

</div>

</form>


<?php include($RSDPpath . '/admin/comments.dialog.php'); ?>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
