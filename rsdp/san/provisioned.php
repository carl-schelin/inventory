<?php
# Script: provisioned.php
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

  $package = "provisioned.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

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
  $task[11] = '&gt; ';

  $q_string  = "select os_sysname ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'];
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_osteam) > 0) {
    $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);
  } else {
    $a_rsdp_osteam['os_sysname'] = "New Server";
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

  af_url  = '?complete=' + complete;
  af_url += '&rsdp='     + <?php print $formVars['rsdp']; ?>;
  af_url += '&id='       + af_form.id.value;

  af_url += '&if_sanconf='     + af_form.if_sanconf.checked;
  af_url += '&if_provisioned=' + af_form.if_provisioned.checked;
  af_url += '&if_procheck='    + af_form.if_procheck.checked;

  if (complete === 1) {
    var question  = "This SAN Provisioning form is ready to submit.\n\n";
        question += "Submitting this request will notify the appropriate teams.\n";
        question += "Making changes to the information here after submission will require seperate notification.\n\n";
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

  if (document.rsdp.if_procheck.checked === false) {
    set_Class('if_procheck', 'ui-state-error');
    $( "#check-1" ).button( "option", "label", "The SAN Provisioning checklist has NOT been completed." );
    vf_submit = 0;
  } else {
    set_Class('if_procheck', 'ui-widget-content');
    $( "#check-1" ).button( "option", "label", "The SAN Provisioning checklist HAS been completed." );
  }

  if (document.rsdp.if_sanconf.checked === false) {
    set_Class('if_sanconf', 'ui-state-error');
    $( "#check-2" ).button( "option", "label", "The SAN has NOT been configured." );
    vf_submit = 0;
  } else {
    set_Class('if_sanconf', 'ui-widget-content');
    $( "#check-2" ).button( "option", "label", "The SAN HAS been configured." );
  }

  if (document.rsdp.if_provisioned.checked === false) {
    set_Class('if_provisioned', 'ui-state-error');
    $( "#check-3" ).button( "option", "label", "The SAN has NOT been provisioned." );
    vf_submit = 0;
  } else {
    set_Class('if_provisioned', 'ui-widget-content');
    $( "#check-3" ).button( "option", "label", "The SAN HAS been provisioned." );
  }

  if (vf_submit) {
    document.rsdp.addbtn.disabled = false;
  } else {
    document.rsdp.addbtn.disabled = true;
  }

}

function clear_fields() {
  show_file('<?php print $RSDProot; ?>/admin/comments.mysql.php' + '?update=-1&com_rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/san/provisioned.fill.php' + '?update=-1&rsdp=<?php print $formVars['rsdp']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );

  $( "#check-1" ).button();
  $( "#check-2" ).button();
  $( "#check-3" ).button();

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
  <th class="ui-state-default">SAN Provisioning: <?php print $a_rsdp_osteam['os_sysname']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('san-help');">Help</a></th>
</tr>
</table>

<div id="san-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>SAN Provisioning Page</h2>

<p>This task is typically performed by the Storage Team indicating the allocated Storage has been 
configured and is ready for use. There are five tabs providing Project and System information, team 
checklist, a list of items that must be checked off before the task can be completed, and a comments 
page for passing information to various teams.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Reminder E-Mail</strong> - Send a reminder email to the team or individual responsible for completing this task.</li>
    <li><strong>Save Changes</strong> - Save any changes to this task.</li>
    <li><strong>Save And Exit</strong> - Save any changes to this task and return to the task list.</li>
    <li><strong>Task Completed</strong> - This button stays disabled until all identified tasks have been completed. 
Once completed, the button is enabled. Clicking it identifies the task as completed. If selected, a ticket will be 
created for the next task and an email will be sent notifying the responsible team or individual that their task is 
ready to be worked.</li>
  </ul></li>
</ul>

</div>

</div>

<?php print submit_RSDP($db, " $formVars['rsdp'], 11, $RSDProot . "/san/provisioned.mysql.php", "rsdp_sanpoc", "", 9); ?>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Project Information</a></li>
  <li><a href="#tabs-2">System Information</a></li>
  <li><a href="#tabs-3">SAN Provisioning Checklist</a></li>
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
  <th class="ui-state-default" colspan="3">SAN Provisioning Checklist</th>
</tr>
<tr>
  <td id="if_procheck"><input type="checkbox" id="check-1" name="if_procheck" onchange="validate_Form();"> <label for="check-1"></label></td>
</tr>
<?php print return_Checklist($db, " $formVars['rsdp'], 11); ?>
</table>

</div>


<div id="tabs-4">

<table width="100%" id="table-4" class="full">
<tr>
  <th>Task Checklist</th>
</tr>
<tr>
  <td id="if_sanconf"    ><input type="checkbox" id="check-2" name="if_sanconf"     onclick="validate_Form();"><label for="check-2"></label></td>
</tr>
<tr>
  <td id="if_provisioned"><input type="checkbox" id="check-3" name="if_provisioned" onclick="validate_Form();"><label for="check-3"></label></td>
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
