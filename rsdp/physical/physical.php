<?php
# Script: physical.php
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

  $package = "physical.php";

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
  $task[5] = '&gt; ';

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

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>
<script type="text/javascript" src="<?php print $RSDProot; ?>/admin/comments.js"></script>

<script type="text/javascript">

function attach_file( p_script_url, complete ) {
  var af_form = document.rsdp;
  var af_url;
  var answer = false;

  af_url  = '?complete='    + complete;
  af_url += '&rsdp='        + af_form.rsdp.value;
  af_url += '&pf_id='       + af_form.pf_id.value;
  af_url += '&dc_id='       + af_form.dc_id.value;
  af_url += '&if_id='       + af_form.if_id.value;

  af_url += '&pf_row='        + encode_URI(af_form.pf_row.value);
  af_url += '&pf_rack='       + encode_URI(af_form.pf_rack.value);
  af_url += '&pf_unit='       + encode_URI(af_form.pf_unit.value);
  af_url += '&pf_circuita='   + encode_URI(af_form.pf_circuita.value);
  af_url += '&pf_circuitb='   + encode_URI(af_form.pf_circuita.value);

  af_url += '&dc_power='      + af_form.dc_power.checked;
  af_url += '&dc_cables='     + af_form.dc_cables.checked;
  af_url += '&dc_infra='      + af_form.dc_infra.checked;
  af_url += '&dc_received='   + af_form.dc_received.checked;
  af_url += '&dc_installed='  + af_form.dc_installed.checked;
  af_url += '&dc_checklist='  + af_form.dc_checklist.checked;
  af_url += '&dc_path='       + encode_URI(af_form.dc_path.value);

  af_url += '&if_dcrack='     + af_form.if_dcrack.checked;
  af_url += '&if_dccabled='   + af_form.if_dccabled.checked;

  if (complete === 1) {
    var question  = "This Data Center form is ready to submit.\n\n";
        question += "Submitting this request will notify the appropriate teams.\n";
        question += "You will not be able to make further changes to this project once it's done.\n\n";
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

  if (document.rsdp.dc_power.checked === false) {
    set_Class('dc_power', 'ui-state-error');
    $( "#check-1" ).button( "option", "label", "The Power has NOT been ordered." );
    vf_submit = 0;
  } else {
    set_Class('dc_power', 'ui-widget-content');
    $( "#check-1" ).button( "option", "label", "The Power HAS been ordered." );
  }

  if (document.rsdp.dc_cables.checked === false) {
    set_Class('dc_cables', 'ui-state-error');
    $( "#check-2" ).button( "option", "label", "The Cables have NOT been ordered." );
    vf_submit = 0;
  } else {
    set_Class('dc_cables', 'ui-widget-content');
    $( "#check-2" ).button( "option", "label", "The Cables HAVE been ordered." );
  }

  if (document.rsdp.dc_infra.checked === false) {
    set_Class('dc_infra', 'ui-state-error');
    $( "#check-3" ).button( "option", "label", "The Infrastructure has NOT been ordered." );
    vf_submit = 0;
  } else {
    set_Class('dc_infra', 'ui-widget-content');
    $( "#check-3" ).button( "option", "label", "The Infrastructure HAS been ordered." );
  }

  if (document.rsdp.dc_received.checked === false) {
    set_Class('dc_received', 'ui-state-error');
    $( "#check-4" ).button( "option", "label", "The Power has NOT been received." );
    vf_submit = 0;
  } else {
    set_Class('dc_received', 'ui-widget-content');
    $( "#check-4" ).button( "option", "label", "The Power HAS been received." );
  }

  if (document.rsdp.dc_installed.checked === false) {
    set_Class('dc_installed', 'ui-state-error');
    $( "#check-5" ).button( "option", "label", "The Cables have NOT been installed." );
    vf_submit = 0;
  } else {
    set_Class('dc_installed', 'ui-widget-content');
    $( "#check-5" ).button( "option", "label", "The Cables HAVE been installed." );
  }

  if (document.rsdp.dc_checklist.checked === false) {
    set_Class('dc_checklist', 'ui-state-error');
    $( "#check-6" ).button( "option", "label", "The Infrastructure Checklist has NOT been completed." );
    vf_submit = 0;
  } else {
    set_Class('dc_checklist', 'ui-widget-content');
    $( "#check-6" ).button( "option", "label", "The Infrastructure Checklist HAS been completed." );
  }

  if (document.rsdp.if_dcrack.checked === false) {
    set_Class('if_dcrack', 'ui-state-error');
    $( "#check-7" ).button( "option", "label", "The Hardware has NOT been racked." );
    vf_submit = 0;
  } else {
    set_Class('if_dcrack', 'ui-widget-content');
    $( "#check-7" ).button( "option", "label", "The Hardware HAS been racked." );
  }

  if (document.rsdp.if_dccabled.checked === false) {
    set_Class('if_dccabled', 'ui-state-error');
    $( "#check-8" ).button( "option", "label", "The System has NOT been cabled." );
    vf_submit = 0;
  } else {
    set_Class('if_dccabled', 'ui-widget-content');
    $( "#check-8" ).button( "option", "label", "The System HAS been cabled." );
  }

  if (vf_submit) {
    document.rsdp.addbtn.disabled = false;
  } else {
    document.rsdp.addbtn.disabled = true;
  }
}

$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );

  $( "#check-1" ).button();
  $( "#check-2" ).button();
  $( "#check-3" ).button();
  $( "#check-4" ).button();
  $( "#check-5" ).button();
  $( "#check-6" ).button();
  $( "#check-7" ).button();
  $( "#check-8" ).button();
});

function clear_fields() {
  show_file('<?php print $RSDProot; ?>/admin/comments.mysql.php' + '?update=-1&com_rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/physical/physical.fill.php' + '?rsdp=<?php print $formVars['rsdp']; ?>');
}

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($RSDPpath . '/topmenu.rsdp.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<form name="rsdp">

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Data Center Installation: <?php print $a_rsdp_osteam['os_sysname']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('center-help');">Help</a></th>
</tr>
</table>

<div id="center-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>Data Center Installation Page</h2>

<p>This task is typically performed by the Data Center Team. There are five tabs providing Project 
and System information, team checklist, a list of items that must be checked off before the task 
can be completed, and a comments page for passing information to various teams.</p>

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

<p>Click <a href='physical.pdf.php?rsdp=<?php print $formVars['rsdp']; ?>'>Data Center Request</a> for the PDF.</p>

</div>

</div>

<?php print submit_RSDP( $formVars['rsdp'], 5, $RSDProot . "/physical/physical.mysql.php", "rsdp_dcpoc", "", $GRP_DataCenter); ?>

<input type="hidden" name="if_id" value="0">
<input type="hidden" name="pf_id" value="0">
<input type="hidden" name="dc_id" value="0">

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Project Information</a></li>
  <li><a href="#tabs-2">System Information</a></li>
  <li><a href="#tabs-3">Location Form</a></li>
  <li><a href="#tabs-4">Task Checklist</a></li>
  <li><a href="#tabs-5">Comments
<?php
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

<?php print request_Header($formVars['rsdp']); ?>

</div>


<div id="tabs-2">

<?php print request_Server($formVars['rsdp']); ?>

</div>


<div id="tabs-3">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Data Center Location</th>
</tr>
<tr>
  <td class="ui-widget-content" id="pf_row">Row <input type="text" name="pf_row"></td>
  <td class="ui-widget-content" id="pf_rack">Rack <input type="text" name="pf_rack"></td>
  <td class="ui-widget-content" id="pf_unit">Low Unit Number <input type="text" name="pf_unit"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Power Circuit Data</th>
</tr>
<tr>
  <td class="ui-widget-content" id="pf_circuita">Power Circuit A <input type="text" name="pf_circuita"></td>
  <td class="ui-widget-content" id="pf_circuitb">Power Circuit B <input type="text" name="pf_circuitb"></td>
</tr>
</table>

</div>


<div id="tabs-4">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Pre-Setup Checklist</th>
</tr>
<tr>
  <td id="dc_power"><input type="checkbox" id="check-1" name="dc_power" onchange="validate_Form();"> <label for="check-1"></label></td>
</tr>
<tr>
  <td id="dc_cables"><input type="checkbox" id="check-2" name="dc_cables" onchange="validate_Form();"> <label for="check-2"></label></td>
</tr>
<tr>
  <td id="dc_infra"><input type="checkbox" id="check-3" name="dc_infra" onchange="validate_Form();"> <label for="check-3"></label></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Setup Checklist</th>
</tr>
<tr>
  <td id="dc_received"><input type="checkbox" id="check-4" name="dc_received" onchange="validate_Form();"> <label for="check-4"></label></td>
</tr>
<tr>
  <td id="dc_installed"><input type="checkbox" id="check-5" name="dc_installed" onchange="validate_Form();"> <label for="check-5"></label></td>
</tr>
<tr>
  <td id="dc_checklist"><input type="checkbox" id="check-6" name="dc_checklist" onchange="validate_Form();"> <label for="check-6"></label></td>
</tr>
<tr>
  <td id="dc_path" class="ui-widget-content">Cable Path <input type="text" name="dc_path" size=30></td>
</tr>
</table>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Post-Setup Checklist</th>
</tr>
<tr>
  <td id="if_dcrack"><input type="checkbox" id="check-7" name="if_dcrack" onchange="validate_Form();"> <label for="check-7"></label></td>
</tr>
<tr>
  <td id="if_dccabled"><input type="checkbox" id="check-8" name="if_dccabled" onchange="validate_Form();"> <label for="check-8"></label></td>
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
