<?php
# Script: scanned.php
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

  $package = "scanned.php";

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
  $task[18] = '&gt; ';

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
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>
<script type="text/javascript" src="<?php print $RSDProot; ?>/admin/comments.js"></script>

<script type="text/javascript">

function attach_file( p_script_url, complete ) {
  var af_form = document.rsdp;
  var af_url;
  var answer = false;

  af_url  = '?complete=' + complete;
  af_url += '&rsdp='     + af_form.rsdp.value;
  af_url += '&id='       + af_form.id.value;

  af_url += '&is_ticket='    + encode_URI(af_form.is_ticket.value);
  af_url += '&is_scan='      + af_form.is_scan.checked;
  af_url += '&is_checklist=' + af_form.is_checklist.checked;
  af_url += '&is_verified='  + af_form.is_verified.checked;

  if (complete === 1) {
    var question  = "This InfoSec form is ready to submit.\n\n";
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

  if (document.rsdp.is_checklist.checked === false) {
    set_Class('is_checklist', 'ui-state-error');
    $( "#check-1" ).button( "option", "label", "InfoSec checklist has NOT been completed." );
    vf_submit = 0;
  } else {
    set_Class('is_checklist', 'ui-widget-content');
    $( "#check-1" ).button( "option", "label", "InfoSec checklist HAS been completed." );
  }

  if (document.rsdp.is_scan.checked === false) {
    set_Class('is_scan', 'ui-state-error');
    $( "#check-2" ).button( "option", "label", "InfoSec Scan request has NOT been submitted." );
    vf_submit = 0;
  } else {
    set_Class('is_scan', 'ui-widget-content');
    $( "#check-2" ).button( "option", "label", "InfoSec Scan request HAS been submitted." );
  }

  if (document.rsdp.is_verified.checked === false) {
    set_Class('is_verified', 'ui-state-error');
    $( "#check-3" ).button( "option", "label", "InfoSec Scan results have NOT been reviewed." );
    vf_submit = 0;
  } else {
    set_Class('is_verified', 'ui-widget-content');
    $( "#check-3" ).button( "option", "label", "InfoSec Scan results HAVE been reviewed." );
  }

  if (vf_submit) {
    document.rsdp.addbtn.disabled = false;
  } else {
    document.rsdp.addbtn.disabled = true;
  }
}

function clear_fields() {
  show_file('<?php print $RSDProot; ?>/admin/comments.mysql.php' + '?update=-1&com_rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/infosec/scanned.fill.php' + '?rsdp=<?php print $formVars['rsdp']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );

  $( "#check-1" ).button();
  $( "#check-2" ).button();
  $( "#check-3" ).button();

});

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
  <th class="ui-state-default">InfoSec Scan: <?php print $a_rsdp_osteam['os_sysname']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('scan-help');">Help</a></th>
</tr>
</table>

<div id="scan-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>InfoSec Scan Page</h2>

<p>This task is typically performed by the Platforms Team to finalize the server build and certify it as ready for 
production. There are six tabs providing Project and System information, a list of the IP Addresses associated with 
the Project, a team specific checklist, a list of items that must be checked off before the task can be completed, 
and a comments page for passing information to various teams.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Reminder E-Mail</strong> - Send a reminder email to the team or individual responsible for completing this task.</li>
    <li><strong>Save Changes</strong> - Save any changes to this task.</li>
    <li><strong>Save And Exit</strong> - Save any changes to this task and return to the task list.</li>
    <li><strong>Task Completed</strong> - This button stays disabled until all identified tasks have been completed. Once completed, 
the button is enabled. Clicking it identifies the task as completed. As this is the final task, once this has bee completed, the server 
will drop off of the list of servers to be worked. The server will still be available through the archive page.</li>
  </ul></li>
</ul>

</div>

</div>

<?php print submit_RSDP($db, "$formVars['rsdp'], 18, $RSDProot . "/infosec/scanned.mysql.php", "rsdp_platformspoc", "rsdp_platform", 0); ?>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Project Information</a></li>
  <li><a href="#tabs-2">System Information</a></li>
  <li><a href="#tabs-3">IP Listing</a></li>
  <li><a href="#tabs-4">InfoSec Checklist</a></li>
  <li><a href="#tabs-5">Task Checklist</a></li>
  <li><a href="#tabs-6">Comments<?php
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

<p>This is a list of all the interface names and IPs for this project, not specifically for this server. This makes it easier to create the InfoSec scan ticket.</p>

<?php

# get the project code
  $q_string  = "select rsdp_project ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $formVars['rsdp'];
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_server) > 0) {
    $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);
  } else {
    $a_rsdp_server['rsdp_project'] = 0;
  } 

# using the project code, get all the interfaces for this project. Makes it easier to create the ticket.
  $address = '';
  $hostname = '';
  $comma = '';

  $q_string  = "select if_name,if_ip,os_fqdn ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "left join rsdp_server on rsdp_interface.if_rsdp = rsdp_server.rsdp_id ";
  $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
  $q_string .= "where rsdp_project = " . $a_rsdp_server['rsdp_project'] . " and if_ipcheck = 1 ";
  $q_string .= "order by if_name,if_interface";
  $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_interface) > 0) {

    while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

      $address .= $comma . $a_rsdp_interface['if_ip'];
      $hostname .= $comma . $a_rsdp_interface['if_name'] . "." . $a_rsdp_interface['os_fqdn'];
      $comma = ", ";

    }

    print "<p>List of IP Addresses: " . $address . "</p>";
    print "<p>List of DNS Entries: " . $hostname . "</p>";
  } else {
    print "<p>If you are seeing this, you probably failed to check the box indicating you need an IP.</p>\n";
  }
?>

</div>


<div id="tabs-4">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">InfoSec Checklist</th>
</tr>
<tr>
  <td id="is_checklist"><input type="checkbox" id="check-1" name="is_checklist" onchange="validate_Form();"><label for="check-1"></label></td>
</tr>
<?php print return_Checklist($db, " $formVars['rsdp'], 18); ?>
</table>

</div>


<div id="tabs-5">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Task Checklist</th>
</tr>
<tr>
  <td class="ui-widget-content" id="is_ticket">Magic Ticket #: <input type="input" name="is_ticket" size="20" onchange="validate_Form();"></td>
</tr>
<tr>
  <td class="ui-widget-content" id="is_scan"><input type="checkbox" id="check-2" name="is_scan" onchange="validate_Form();"><label for="check-2"></label></td>
</tr>
<tr>
  <td class="ui-widget-content" id="is_verified"><input type="checkbox" id="check-3" name="is_verified" onchange="validate_Form();"><label for="check-3"></label></td>
</tr>
</table>

</div>


<div id="tabs-6">

<?php include($RSDPpath . "/admin/comments.php"); ?>

</div>

</div>

</div>

</form>


<?php include($RSDPpath . '/admin/comments.dialog.php'); ?>



<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
