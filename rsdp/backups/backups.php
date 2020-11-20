<?php
# Script: backups.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $usethemes = 'yes';
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');
  check_login('2');

  $package = "backups.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

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
  $task[13] = '&gt; ';

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
  af_url += '&rsdp='     + af_form.rsdp.value;
  af_url += '&id='       + af_form.id.value;

  af_url += '&if_backups='       + af_form.if_backups.checked;
  af_url += '&if_buverified='    + af_form.if_buverified.checked;
  af_url += '&if_bucheck='       + af_form.if_bucheck.checked;

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

  if (document.rsdp.if_bucheck.checked === false) {
    set_Class('if_bucheck', 'ui-state-error');
    $( "#check-1" ).button( "option", "label", "The System Backup checklist has NOT been completed." );
    vf_submit = 0;
  } else {
    set_Class('if_bucheck', 'ui-widget-content');
    $( "#check-1" ).button( "option", "label", "The System Backup checklist HAS been completed." );
  }

  if (document.rsdp.if_backups.checked === false) {
    set_Class('if_backups', 'ui-state-error');
    $( "#check-2" ).button( "option", "label", "System Backups have NOT been completed." );
    vf_submit = 0;
  } else {
    set_Class('if_backups', 'ui-widget-content');
    $( "#check-2" ).button( "option", "label", "System Backups HAVE been completed." );
  }

  if (document.rsdp.if_buverified.checked === false) {
    set_Class('if_buverified', 'ui-state-error');
    $( "#check-3" ).button( "option", "label", "System Backups have NOT been verified." );
    vf_submit = 0;
  } else {
    set_Class('if_buverified', 'ui-widget-content');
    $( "#check-3" ).button( "option", "label", "System Backups HAVE been verified." );
  }

  if (vf_submit) {
    document.rsdp.addbtn.disabled = false;
  } else {
    document.rsdp.addbtn.disabled = true;
  }

}

function clear_fields() {
  show_file('<?php print $RSDProot; ?>/admin/comments.mysql.php' + '?update=-1&com_rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/backups/backups.fill.php' + '?update=-1&rsdp=<?php print $formVars['rsdp']; ?>');
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
  <th class="ui-state-default">System Backups: <?php print $a_rsdp_osteam['os_sysname']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('backup-help');">Help</a></th>
</tr>
</table>

<div id="backup-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>System Backups Page</h2>

<p>This task is typically performed by the Backup Team. There are six tabs providing Project and System information, Backup details, team checklist, 
a list of items that must be checked off before the task can be completed, and a comments page for passing information to various teams.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Reminder E-Mail</strong> - Send a reminder email to the team or individual responsible for completing this task.</li>
    <li><strong>Save Changes</strong> - Save any changes to this task.</li>
    <li><strong>Save And Exit</strong> - Save any changes to this task and return to the task list.</li>
    <li><strong>Task Completed</strong> - This button stays disabled until all identified tasks have been completed. Once 
completed, the button is enabled. Clicking it identifies the task as completed. If selected, a ticket will be created for 
the next task and an email will be sent notifying the responsible team or individual that their task is ready to be worked.</li>
  </ul></li>
</ul>

<p>Click <a href='backups.pdf.php?rsdp=<?php print $formVars['rsdp']; ?>'>Backup Request</a> for the PDF.</p>

</div>

</div>

<?php print submit_RSDP( $formVars['rsdp'], 13, $RSDProot . "/backups/backups.mysql.php", "rsdp_backuppoc", "", $GRP_Backups); ?>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Project Information</a></li>
  <li><a href="#tabs-2">System Information</a></li>
  <li><a href="#tabs-3">Backup Information</a></li>
  <li><a href="#tabs-4">Backup Checklist</a></li>
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

<?php print request_Header($formVars['rsdp']); ?>

</div>


<div id="tabs-2">

<?php print request_Server($formVars['rsdp']); ?>

</div>


<div id="tabs-3">

<?php
# begin backup data
  $retention[0] = "None";
  $retention[1] = "Less than 6 Months (Details Required)";
  $retention[2] = "6 Months";
  $retention[3] = "1 Year";
  $retention[4] = "3 Years (Standard)";
  $retention[5] = "7 Years";

  $q_string  = "select loc_name,ct_city,st_acronym ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join locations on locations.loc_id = rsdp_server.rsdp_location ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join states on states.st_id = locations.loc_state ";
  $q_string .= "where rsdp_id = " . $formVars['rsdp'];
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

  $q_string  = "select bu_id,bu_rsdp,bu_start,bu_include,bu_retention,bu_sunday,bu_monday,";
  $q_string .= "bu_tuesday,bu_wednesday,bu_thursday,bu_friday,bu_saturday,bu_suntime,bu_montime,";
  $q_string .= "bu_tuetime,bu_wedtime,bu_thutime,bu_fritime,bu_sattime ";
  $q_string .= "from rsdp_backups ";
  $q_string .= "where bu_rsdp = " . $formVars['rsdp'];
  $q_rsdp_backups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_backups = mysqli_fetch_array($q_rsdp_backups);
?>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Backup Information</th>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Backup Start Date</strong>: <?php print $a_rsdp_backups['bu_start']; ?></td>
  <td class="ui-widget-content"><strong>Retention Length</strong>: <?php print $retention[$a_rsdp_backups['bu_retention']]; ?></td>
  <td class="ui-widget-content"><strong>Data Center Location</strong>: <?php print $a_rsdp_server['loc_name'] . " (" . $a_rsdp_server['ct_city'] . " " . $a_rsdp_server['st_acronym'] . ")"; ?></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3"><strong>Files/drives/volumes to include</strong>:</td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">ALL LOCAL DRIVES </td>
</tr>
<?php
  $q_string  = "select fs_volume,fs_size ";
  $q_string .= "from rsdp_filesystem where ";
  $q_string .= "fs_rsdp = " . $formVars['rsdp'] . " and fs_backup = 1";
  $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
    print "  <tr>\n";
    print "    <td class=\"ui-widget-content\" colspan=\"3\">" . $a_rsdp_filesystem['fs_volume'] . " (" . $a_rsdp_filesystem['fs_size'] . " GB)</td>\n";
    print "  </tr>\n";
  }
?>
<tr>
  <td class="ui-widget-content" colspan="3"><strong>Files/drives/volumes to exclude</strong>:</td>
</tr>
<?php
  $q_string  = "select fs_volume,fs_size ";
  $q_string .= "from rsdp_filesystem ";
  $q_string .= "where fs_rsdp = " . $formVars['rsdp'] . " and fs_backup = 0";
  $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
    print "  <tr>\n";
    print "    <td class=\"ui-widget-content\" colspan=\"3\">" . $a_rsdp_filesystem['fs_volume'] . " (" . $a_rsdp_filesystem['fs_size'] . " GB)</td>\n";
    print "  </tr>\n";
  }
?>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Backup Window</th>
</tr>
<tr>
  <th class="ui-state-default">Day of Week</th>
  <th class="ui-state-default">Backup Type</th>
  <th class="ui-state-default">Backup Window</th>
</tr>
<?php 
  if ($a_rsdp_backups['bu_sunday']) {
    $backups = "<strong>Incremental</strong>";
  } else {
    $backups = "<strong>Full</strong>";
  }
?>
<tr>
  <td class="ui-widget-content">Sunday:</td>
  <td class="ui-widget-content"><?php print $backups; ?></td>
  <td class="ui-widget-content">Times: <?php print $a_rsdp_backups['bu_suntime']; ?></td>
</tr>
<?php
  if ($a_rsdp_backups['bu_monday']) {
    $backups = "<strong>Incremental</strong>";
  } else {
    $backups = "<strong>Full</strong>";
  }
?>
<tr>
  <td class="ui-widget-content">Monday:</td>
  <td class="ui-widget-content"><?php print $backups; ?></td>
  <td class="ui-widget-content">Times: <?php print $a_rsdp_backups['bu_montime']; ?></td>
</tr>
<?php
  if ($a_rsdp_backups['bu_tuesday']) {
    $backups = "<strong>Incremental</strong>";
  } else {
    $backups = "<strong>Full</strong>";
  }
?>
<tr>
  <td class="ui-widget-content">Tuesday:</td>
  <td class="ui-widget-content"><?php print $backups; ?></td>
  <td class="ui-widget-content">Times: <?php print $a_rsdp_backups['bu_montime']; ?></td>
</tr>
<?php
  if ($a_rsdp_backups['bu_wednesday']) {
    $backups = "<strong>Incremental</strong>";
  } else {
    $backups = "<strong>Full</strong>";
  }
?>
<tr>
  <td class="ui-widget-content">Wednesday:</td>
  <td class="ui-widget-content"><?php print $backups; ?></td>
  <td class="ui-widget-content">Times: <?php print $a_rsdp_backups['bu_montime']; ?></td>
</tr>
<?php
  if ($a_rsdp_backups['bu_thursday']) {
    $backups = "<strong>Incremental</strong>";
  } else {
    $backups = "<strong>Full</strong>";
  }
?>
<tr>
  <td class="ui-widget-content">Thusday:</td>
  <td class="ui-widget-content"><?php print $backups; ?></td>
  <td class="ui-widget-content">Times: <?php print $a_rsdp_backups['bu_montime']; ?></td>
</tr>
<?php
  if ($a_rsdp_backups['bu_friday']) {
    $backups = "<strong>Incremental</strong>";
  } else {
    $backups = "<strong>Full</strong>";
  }
?>
<tr>
  <td class="ui-widget-content">Friday:</td>
  <td class="ui-widget-content"><?php print $backups; ?></td>
  <td class="ui-widget-content">Times: <?php print $a_rsdp_backups['bu_montime']; ?></td>
</tr>
<?php
  if ($a_rsdp_backups['bu_saturday']) {
    $backups = "<strong>Incremental</strong>";
  } else {
    $backups = "<strong>Full</strong>";
  }
?>
<tr>
  <td class="ui-widget-content">Saturday:</td>
  <td class="ui-widget-content"><?php print $backups; ?></td>
  <td class="ui-widget-content">Times: <?php print $a_rsdp_backups['bu_montime']; ?></td>
</tr>
</table>

</div>


<div id="tabs-4">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">System Backup Checklist</th>
</tr>
<tr>
  <td id="if_bucheck"  ><input type="checkbox" id="check-1" name="if_bucheck"    onchange="validate_Form();"><label for="check-1"></label></td>
</tr>
<?php print return_Checklist( $formVars['rsdp'], 13); ?>
</table>

</div>


<div id="tabs-5">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Task Checklist</th>
</tr>
<tr>
 <td id="if_backups"   ><input type="checkbox" id="check-2" name="if_backups"    onchange="validate_Form();"><label for="check-2"></label></td>
</tr>
<tr>
 <td id="if_buverified"><input type="checkbox" id="check-3" name="if_buverified" onchange="validate_Form();"><label for="check-3"></label></td>
</tr>
</table>

</div>


<div id="tabs-6">

<?php include ($RSDPpath . '/admin/comments.php'); ?>

</div>

</div>

</div>

</form>


<?php include($RSDPpath . '/admin/comments.dialog.php'); ?>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
