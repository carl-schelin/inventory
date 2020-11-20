<?php
# Script: designed.php
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

  $package = "designed.php";

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
  $task[3] = '&gt; ';

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

  af_url  = '?id='       + af_form.id.value;
  af_url += '&complete=' + complete;
  af_url += '&rsdp='     + af_form.rsdp.value;

  af_url += '&san_checklist='   + af_form.san_checklist.checked;

  if (complete === 1) {
    var question  = "The SAN Design Task is ready to submit.\n\n";
        question += "Are you sure you are ready to submit this form?";
    answer = confirm(question);
  }

  if (answer === true || complete < 1 || complete == 2) {
    script = document.createElement('script');
    script.src = p_script_url + af_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_san( p_script_url, update ) {
  var as_form = document.san;
  var as_url;

  as_url  = '?update='   + update;
  as_url += '&id='       + as_form.san_id.value;
  as_url += '&rsdp='     + as_form.san_rsdp.value;

  as_url += "&san_switch="      + encode_URI(as_form.san_switch.value);
  as_url += "&san_port="        + encode_URI(as_form.san_port.value);
  as_url += "&san_media="       + as_form.san_media.value;
  as_url += "&san_wwnnzone="    + encode_URI(as_form.san_wwnnzone.value);

  script = document.createElement('script');
  script.src = p_script_url + as_url;
  document.getElementsByTagName('head')[0].appendChild(script);
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
  var vf_form = document.rsdp;
  var vf_submit = 1;

  if (vf_form.san_checklist.checked === false) {
    set_Class('san_checklist', 'ui-state-error');
    $( "#check-1" ).button( "option", "label", "SAN checklist has NOT been completed." );
    vf_submit = 0;
  } else {
    set_Class('san_checklist', 'ui-widget-content');
    $( "#check-1" ).button( "option", "label", "SAN checklist HAS been completed." );
  }

  if (vf_submit) {
    vf_form.addbtn.disabled = false;
  } else {
    vf_form.addbtn.disabled = true;
  }

}

function clear_fields() {
  show_file('<?php print $RSDProot; ?>/san/designed.fill.php?update=-1&rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/san/san.mysql.php?update=-1&rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/admin/comments.mysql.php?update=-1&com_rsdp=<?php print $formVars['rsdp']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );

  $( "#check-1" ).button();

  $( "#dialogSAN" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1200,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogSAN" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update SAN",
        click: function() {
          attach_san('san.mysql.php', 1);
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
<?php include($RSDPpath . '/topmenu.rsdp.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<form name="rsdp">

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">SAN Design: <?php print $a_rsdp_osteam['os_sysname']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('design-help');">Help</a></th>
</tr>
</table>

<div id="design-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>SAN Design Page</h2>

<p>This task is typically performed by the SAN Team. There are six tabs providing Project and System information, 
a list of group based items that must be checked off, a task checklist, and a comments page for passing information to various teams.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Reminder E-Mail</strong> - Send a reminder email to the team or individual responsible for completing this task.</li>
    <li><strong>Save Changes</strong> - Save any changes to this task.</li>
    <li><strong>Save And Exit</strong> - Save any changes to this task and exit.</li>
    <li><strong>Task Completed</strong> - This button stays disabled until all identified tasks have been completed. 
Once completed, the button is enabled. Clicking it identifies the task as completed. If selected, a ticket will be 
created for the next task and an email will be sent notifying the responsible team or individual that their task is 
ready to be worked.</li>
  </ul></li>
</ul>

<p>Click <a href='designed.pdf.php?rsdp=<?php print $formVars['rsdp']; ?>'>SAN Request</a> for the PDF.</p>

</div>

</div>

<?php print submit_RSDP( $formVars['rsdp'], 3, $RSDProot . "/san/designed.mysql.php", "rsdp_sanpoc", "", $GRP_SAN); ?>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Project Information</a></li>
  <li><a href="#tabs-2">System Information</a></li>
  <li><a href="#tabs-3">SAN Form</a></li>
  <li><a href="#tabs-4">SAN Checklist</a></li>
  <li><a href="#comments">Comments
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
  <th class="ui-state-default">SAN Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('san-help');">Help</a></th>
</tr>
</table>

<div id="san-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>System HBA Slot and Port</strong> - This identifies the slot in the system that holds the Host Bus Adapter (HBA). Identify the port as well if it contains more 
than one port. This information is used by the Data Center folks to run cable. I recommend taking a permanent marker and identifying the ports if they aren't clear.</li>
  <li><strong>WWNN Zone</strong> - The World Wide Port Name assigned by the SAN team.</li>
  <li><strong>Switch</strong> - The switch the cable will be connected to.</li>
  <li><strong>Switch Port</strong> - The port on the switch.</li>
  <li><strong>Media Type</strong> - This will generally be one of the Fiber types in the list.</li>
</ul>

</div>

</div>

<span id="san_mysql"><?php wait_Process("Loading SAN Interfaces, Please Wait"); ?></span>

</div>


<div id="tabs-4">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">SAN Checklist</th>
</tr>
<tr>
  <td id="san_checklist" colspan="2"><input type="checkbox" id="check-1" name="san_checklist" onchange="validate_Form();"><label for="check-1"></label></td>
</tr>
<?php print return_Checklist( $formVars['rsdp'], 3); ?>
</table>

</div>


<div id="comments">

<?php include($RSDPpath . '/admin/comments.php'); ?>

</div>

</div>

</div>

</form>


<?php include($RSDPpath . '/admin/comments.dialog.php'); ?>


<div id="dialogSAN" title="SAN Form">

<form name="san">

<input type="hidden" name="san_id"   value="0">
<input type="hidden" name="san_rsdp" value="<?php print $formVars['rsdp']; ?>">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">SAN Form</th>
</tr>
<tr>
  <td class="ui-widget-content">System HBA Slot and Port: <span id="san_sysport"></span></td>
  <td class="ui-widget-content">WWNN Zone <input type="text" name="san_wwnnzone" size="40"></td>
  <td class="ui-widget-content">Switch <input type="text" name="san_switch" size="30"></td>
  <td class="ui-widget-content">Switch Port <input type="text" name="san_port" size="20"></td>
  <td class="ui-widget-content">Media Type <select name="san_media">
<option value="0">N/A</option>
<?php
  $q_string  = "select med_id,med_text ";
  $q_string .= "from int_media ";
  $q_string .= "order by med_text";
  $q_int_media = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_int_media = mysqli_fetch_array($q_int_media)) {
    print "<option value=\"" . $a_int_media['med_id'] . "\">" . $a_int_media['med_text'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
